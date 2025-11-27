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
                $payload[$field] = $this->formatAlbum($request);
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
            if ($field === 'album') {
                $payload[$field] = $this->formatAlbum($request);
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
        $payload['event'] = $request->input('event', []);
        $payload['value'] = $request->input('value', []);
        
        return $payload;
    }

    private function paginateSelect()
    {
        return [
            'schools.id',
            'schools.image',
            'schools.publish',
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
            'enrollment_quota',
            'short_name',
            'publish',
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
