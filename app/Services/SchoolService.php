<?php

namespace App\Services;

use App\Services\BaseService;
use App\Repositories\SchoolRepository;
use App\Repositories\RouterRepository;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class SchoolService extends BaseService
{
    protected $schoolRepository;
    protected $routerRepository;
    protected $controllerName = 'SchoolController';

    public function __construct(
        SchoolRepository $schoolRepository,
        RouterRepository $routerRepository
    ) {
        $this->schoolRepository = $schoolRepository;
        $this->routerRepository = $routerRepository;
    }

    public function paginate($request, $languageId)
    {
        $perPage = $request->integer('perpage');
        $condition = [
            'keyword' => addslashes($request->input('keyword')),
            'publish' => $request->integer('publish'),
            'where' => [
                ['tb2.language_id', '=', $languageId]
            ]
        ];
        $schools = $this->schoolRepository->pagination(
            $this->paginateSelect(),
            $condition,
            $perPage,
            ['path' => 'school/index'],
            ['schools.id', 'DESC'],
            [
                ['school_language as tb2', 'tb2.school_id', '=', 'schools.id']
            ],
            ['languages']
        );

        return $schools;
    }

    public function create($request, $languageId)
    {
        DB::beginTransaction();
        try {
            $school = $this->createSchool($request);
            if ($school->id > 0) {
                $this->updateLanguageForSchool($school, $request, $languageId);
                $this->updateSchoolMajorRelation($school, $request);
                $this->createRouter($school, $request, $this->controllerName, $languageId);
            }
            DB::commit();
            return $school;
        } catch (\Exception $e) {
            DB::rollBack();
            echo $e->getMessage();
            die();
            return false;
        }
    }

    public function update($id, $request, $languageId)
    {
        DB::beginTransaction();
        try {
            $school = $this->schoolRepository->findById($id);
            if (!$school) {
                DB::rollBack();
                return false;
            }
            $flag = $this->updateSchool($school, $request);
            if ($flag == TRUE) {
                $this->updateLanguageForSchool($school, $request, $languageId);
                $this->updateSchoolMajorRelation($school, $request);
                $this->updateRouter($school, $request, $this->controllerName, $languageId);
            }
            DB::commit();
            return true;
        } catch (\Exception $e) {
            DB::rollBack();
            echo $e->getMessage();
            die();
            return false;
        }
    }

    public function duplicate($id, $languageId)
    {
        DB::beginTransaction();
        try {
            // Lấy bản ghi gốc
            $originalSchool = $this->schoolRepository->getSchoolById($id, $languageId);
            if (!$originalSchool) {
                DB::rollBack();
                return false;
            }

            // Tạo payload từ bản ghi gốc
            $payload = [];
            foreach ($this->payload() as $field) {
                $payload[$field] = $originalSchool->$field;
            }
            $payload['user_id'] = Auth::id();
            $payload['publish'] = 1; // Mặc định là không publish

            // Tạo school mới
            $newSchool = $this->schoolRepository->create($payload);
            $languagePayload = null;

            if ($newSchool->id > 0) {
                // Copy dữ liệu language
                $pivot = $originalSchool->languages->first()->pivot ?? null;
                if ($pivot) {
                    $languagePayload = [];
                    foreach ($this->payloadLanguage() as $field) {
                        $languagePayload[$field] = $pivot->$field ?? '';
                    }
                    
                    // Thêm "- clone" vào tiêu đề
                    $originalName = $languagePayload['name'] ?? '';
                    $languagePayload['name'] = $originalName . ' - clone';
                    
                    // Xử lý canonical với timestamp
                    $timestamp = time();
                    $originalCanonical = $languagePayload['canonical'] ?? '';
                    $languagePayload['canonical'] = $originalCanonical . '-' . $timestamp;
                    
                    // Copy các trường JSON
                    $jsonFields = ['intro', 'announce', 'advantage', 'suitable', 'majors', 'study_method', 'feedback', 'event', 'value'];
                    foreach ($jsonFields as $field) {
                        $languagePayload[$field] = $pivot->$field ?? [];
                    }
                    
                    // Tạo Request object với dữ liệu
                    $request = \Illuminate\Http\Request::create('', 'POST', $languagePayload);
                    $this->updateLanguageForSchool($newSchool, $request, $languageId);
                }

                // Copy quan hệ school_major
                $originalMajors = DB::table('school_major')
                    ->where('school_id', $id)
                    ->get();
                
                if ($originalMajors->count() > 0) {
                    $insertData = [];
                    foreach ($originalMajors as $major) {
                        $insertData[] = [
                            'school_id' => $newSchool->id,
                            'major_id' => $major->major_id,
                            'created_at' => now(),
                            'updated_at' => now(),
                        ];
                    }
                    DB::table('school_major')->insert($insertData);
                }

                // Tạo router mới với canonical mới
                if ($languagePayload && isset($languagePayload['canonical'])) {
                    $routerRequest = \Illuminate\Http\Request::create('', 'POST', [
                        'canonical' => $languagePayload['canonical'],
                    ]);
                    $this->createRouter($newSchool, $routerRequest, $this->controllerName, $languageId);
                }
            }

            DB::commit();
            return $newSchool;
        } catch (\Exception $e) {
            DB::rollBack();
            echo $e->getMessage();
            die();
            return false;
        }
    }

    public function destroy($id)
    {
        DB::beginTransaction();
        try {
            $school = $this->schoolRepository->delete($id);
            $this->routerRepository->forceDeleteByCondition([
                ['module_id', '=', $id],
                ['controllers', '=', 'App\Http\Controllers\Frontend\\' . $this->controllerName],
            ]);
            DB::commit();
            return true;
        } catch (\Exception $e) {
            DB::rollBack();
            echo $e->getMessage();
            die();
            return false;
        }
    }

    private function createSchool($request)
    {
        $payload = [];
        foreach ($this->payload() as $field) {
            if ($field === 'album') {
                // Vì School model có cast 'album' => 'array', nên cần truyền array trực tiếp
                $album = $request->input('album', []);
                $payload[$field] = (!empty($album) && is_array($album)) ? $album : [];
            } elseif (in_array($field, ['form_tai_lo_trinh_hoc', 'form_tu_van_mien_phi', 'form_hoc_thu'])) {
                // Xử lý các field form JSON
                $formJson = $request->input($field, []);
                $hasData = false;
                if (is_array($formJson) && !empty($formJson)) {
                    foreach ($formJson as $key => $value) {
                        if (!empty(trim($value))) {
                            $hasData = true;
                            break;
                        }
                    }
                }
                $payload[$field] = $hasData ? $formJson : null;
            } elseif (in_array($field, ['graduation_system', 'training_majors', 'exam_location'])) {
                // Xử lý các field - lưu như một giá trị string đơn giản (không phải array)
                $fieldData = $request->input($field, '');
                $fieldData = trim($fieldData);
                $payload[$field] = !empty($fieldData) ? $fieldData : null;
            } else {
                $payload[$field] = $request->input($field);
            }
        }
        $payload['user_id'] = Auth::id();
        $school = $this->schoolRepository->create($payload);
        return $school;
    }

    private function updateSchool($school, $request)
    {
        $payload = [];
        foreach ($this->payload() as $field) {
            // Bỏ qua publish - không update publish khi update trường (tránh reset về 1)
            if ($field === 'publish') {
                continue;
            }
            
            if ($field === 'album') {
                // Vì School model có cast 'album' => 'array', nên cần truyền array trực tiếp
                $album = $request->input('album', []);
                $payload[$field] = (!empty($album) && is_array($album)) ? $album : [];
            } elseif (in_array($field, ['form_tai_lo_trinh_hoc', 'form_tu_van_mien_phi', 'form_hoc_thu'])) {
                // Xử lý các field form JSON
                $formJson = $request->input($field, []);
                $hasData = false;
                if (is_array($formJson) && !empty($formJson)) {
                    foreach ($formJson as $key => $value) {
                        if (!empty(trim($value))) {
                            $hasData = true;
                            break;
                        }
                    }
                }
                $payload[$field] = $hasData ? $formJson : null;
            } elseif (in_array($field, ['graduation_system', 'training_majors', 'exam_location'])) {
                // Xử lý các field - lưu như một giá trị string đơn giản (không phải array)
                $fieldData = $request->input($field, '');
                $fieldData = trim($fieldData);
                $payload[$field] = !empty($fieldData) ? $fieldData : null;
            } elseif ($field === 'follow') {
                // Xử lý follow: luôn lấy giá trị từ request nếu có, nếu không có thì giữ nguyên giá trị cũ
                if ($request->has('follow')) {
                    $followValue = $request->input('follow');
                    // Convert sang integer để đảm bảo đúng kiểu dữ liệu
                    $payload[$field] = is_numeric($followValue) ? (int)$followValue : ($followValue ? 1 : 2);
                } else {
                    // Giữ nguyên giá trị cũ nếu không có trong request
                    $payload[$field] = $school->follow ?? 1;
                }
            } else {
                // Luôn lấy giá trị từ request, kể cả null hoặc empty string
                $payload[$field] = $request->input($field);
            }
        }
        $flag = $this->schoolRepository->update($school->id, $payload);
        return $flag;
    }

    private function updateLanguageForSchool($school, $request, $languageId)
    {
        $payload = $this->formatLanguagePayload($school, $request, $languageId);
        
        // Các trường JSON cần json_encode và dùng DB::raw() để tránh double encoding
        $jsonFields = ['intro', 'announce', 'advantage', 'suitable', 'majors', 'study_method', 'feedback', 'event', 'value'];
        $updateData = [];
        foreach ($payload as $key => $value) {
            if (in_array($key, $jsonFields) && is_array($value)) {
                // Dùng DB::raw() với CAST AS JSON để tránh double encoding
                $jsonString = json_encode($value, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
                $updateData[$key] = DB::raw("CAST(" . DB::getPdo()->quote($jsonString) . " AS JSON)");
            } else {
                $updateData[$key] = $value;
            }
        }
        
        // Dùng updateOrInsert để insert hoặc update
        DB::table('school_language')->updateOrInsert(
            [
                'school_id' => $school->id,
                'language_id' => $languageId
            ],
            array_merge($updateData, [
                'updated_at' => now(),
                'created_at' => DB::table('school_language')
                    ->where('school_id', $school->id)
                    ->where('language_id', $languageId)
                    ->value('created_at') ?? now()
            ])
        );
        
        return true;
    }

    private function formatLanguagePayload($school, $request, $languageId)
    {
        $payload = $request->only($this->payloadLanguage());
        $payload['canonical'] = Str::slug($payload['canonical']);
        
        // Lấy array từ request cho các trường JSON
        $payload['intro'] = $request->input('intro', []);
        $payload['announce'] = $request->input('announce', []);
        $payload['advantage'] = $request->input('advantage', []);
        $payload['suitable'] = $request->input('suitable', []);
        $payload['majors'] = $request->input('majors', []);
        $payload['study_method'] = $request->input('study_method', []);
        $payload['feedback'] = $request->input('feedback', []);
        // Event giờ lưu post_catalogue_id thay vì array post_ids
        $eventData = $request->input('event', []);
        $payload['event'] = !empty($eventData['post_catalogue_id']) ? ['post_catalogue_id' => $eventData['post_catalogue_id']] : [];
        $payload['value'] = $request->input('value', []);
        
        return $payload;
    }

    private function paginateSelect()
    {
        return [
            'schools.id',
            'schools.image',
            'schools.publish',
            'schools.graduation_system',
            'schools.exam_location',
            'schools.created_at',
            'tb2.name',
            'tb2.canonical',
        ];
    }

    private function payload()
    {
        return [
            'image',
            'banner',
            'album',
            'intro_image',
            'download_file',
            'announce_image',
            'announce_title',
            'enrollment_quota',
            'short_name',
            'publish',
            'follow',
            'statistics_majors',
            'statistics_students',
            'statistics_courses',
            'statistics_satisfaction',
            'statistics_employment',
            'is_show_statistics',
            'is_show_intro',
            'is_show_announce',
            'is_show_advantage',
            'is_show_suitable',
            'is_show_majors',
            'is_show_study_method',
            'is_show_feedback',
            'is_show_event',
            'is_show_value',
            'form_script',
            'form_tai_lo_trinh_hoc',
            'form_tu_van_mien_phi',
            'form_hoc_thu',
            'graduation_system',
            'training_majors',
            'exam_location',
        ];
    }

    private function payloadLanguage()
    {
        return [
            'name',
            'description',
            'content',
            'meta_title',
            'meta_keyword',
            'meta_description',
            'canonical'
        ];
    }

    private function updateSchoolMajorRelation($school, $request)
    {
        // Lấy danh sách majors từ request
        $majors = $request->input('majors', []);
        $majorIds = [];
        
        // Extract major_id từ JSON majors
        if (is_array($majors) && count($majors) > 0) {
            foreach ($majors as $major) {
                if (isset($major['major_id']) && $major['major_id'] > 0) {
                    $majorIds[] = (int)$major['major_id'];
                }
            }
        }
        
        // Remove duplicates
        $majorIds = array_unique($majorIds);
        
        // Filter out non-existent majors to avoid FK error
        if (count($majorIds) > 0) {
             $existingIds = DB::table('majors')->whereIn('id', $majorIds)->pluck('id')->toArray();
             $majorIds = $existingIds;
        }
        
        // Xóa tất cả quan hệ cũ
        DB::table('school_major')->where('school_id', $school->id)->delete();
        
        // Thêm quan hệ mới
        if (count($majorIds) > 0) {
            $insertData = [];
            foreach ($majorIds as $majorId) {
                $insertData[] = [
                    'school_id' => $school->id,
                    'major_id' => $majorId,
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            }
            DB::table('school_major')->insert($insertData);
        }
        
        return true;
    }
}
