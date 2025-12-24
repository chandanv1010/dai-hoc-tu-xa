<?php

namespace App\Services;

use App\Services\BaseService;
use App\Repositories\MajorRepository;
use App\Repositories\RouterRepository;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class MajorService extends BaseService
{
    protected $majorRepository;
    protected $routerRepository;
    protected $controllerName = 'MajorController';

    public function __construct(
        MajorRepository $majorRepository,
        RouterRepository $routerRepository
    ) {
        $this->majorRepository = $majorRepository;
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
        $majors = $this->majorRepository->pagination(
            $this->paginateSelect(),
            $condition,
            $perPage,
            ['path' => 'major/index'],
            ['majors.id', 'DESC'],
            [
                ['major_language as tb2', 'tb2.major_id', '=', 'majors.id']
            ],
            ['languages']
        );

        return $majors;
    }

    public function create($request, $languageId)
    {
        DB::beginTransaction();
        try {
            $major = $this->createMajor($request);
            if ($major->id > 0) {
                $this->updateLanguageForMajor($major, $request, $languageId);
                $this->createRouter($major, $request, $this->controllerName, $languageId);
            }
            DB::commit();
            return $major;
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
            $major = $this->majorRepository->findById($id);
            if (!$major) {
                DB::rollBack();
                return false;
            }
            $flag = $this->updateMajor($major, $request);
            if ($flag == TRUE) {
                $this->updateLanguageForMajor($major, $request, $languageId);
                $this->updateRouter($major, $request, $this->controllerName, $languageId);
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
            $originalMajor = $this->majorRepository->getMajorById($id, $languageId);
            if (!$originalMajor) {
                DB::rollBack();
                return false;
            }

            // Tạo payload từ bản ghi gốc
            $payload = [];
            foreach ($this->payload() as $field) {
                $value = $originalMajor->$field ?? null;
                // Xử lý các trường không được null
                if ($field === 'is_home' && ($value === null || $value === '')) {
                    $value = 0; // Mặc định không hiển thị trang chủ
                } elseif ($field === 'major_catalogue_id' && ($value === null || $value === '')) {
                    $value = 0; // Mặc định không có catalogue
                } elseif ($field === 'publish' && ($value === null || $value === '')) {
                    $value = 1; // Mặc định không publish
                }
                $payload[$field] = $value;
            }
            $payload['user_id'] = Auth::id();
            $payload['publish'] = 1; // Mặc định là không publish
            // Đảm bảo is_home có giá trị nếu vẫn null
            if (!isset($payload['is_home']) || $payload['is_home'] === null) {
                $payload['is_home'] = 0;
            }

            // Tạo major mới
            $newMajor = $this->majorRepository->create($payload);
            $languagePayload = null;

            if ($newMajor->id > 0) {
                // Copy dữ liệu language - copy TẤT CẢ các trường từ pivot
                $pivot = $originalMajor->languages->first()->pivot ?? null;
                if ($pivot) {
                    // Copy trực tiếp vào updateData để tránh mất dữ liệu qua formatLanguagePayload
                    $updateData = [];
                    
                    // Copy tất cả các trường text từ payloadLanguage
                    foreach ($this->payloadLanguage() as $field) {
                        $value = $pivot->$field ?? null;
                        if ($field === 'canonical') {
                            // Xử lý canonical với timestamp
                            $timestamp = time();
                            $originalCanonical = $value ?? '';
                            $updateData[$field] = $originalCanonical . '-' . $timestamp;
                        } elseif ($field === 'name') {
                            // Thêm "- clone" vào tiêu đề
                            $updateData[$field] = ($value ?? '') . ' - clone';
                        } else {
                            // Giữ nguyên giá trị, kể cả null - không convert null thành empty string
                            $updateData[$field] = $value;
                        }
                    }
                    
                    // Copy các trường JSON (decode nếu là string, giữ nguyên nếu là array)
                    $jsonFields = ['feature', 'target', 'address', 'overview', 'who', 'priority', 'learn', 'chance', 'school', 'value', 'feedback'];
                    foreach ($jsonFields as $field) {
                        $value = $pivot->$field ?? null;
                        if ($value !== null) {
                            // Nếu là string thì decode, nếu là array thì dùng trực tiếp
                            if (is_string($value)) {
                                $decoded = json_decode($value, true);
                                $jsonValue = is_array($decoded) ? $decoded : [];
                            } elseif (is_array($value)) {
                                $jsonValue = $value;
                            } else {
                                $jsonValue = [];
                            }
                            
                            // Dùng DB::raw() với CAST AS JSON để tránh double encoding
                            if (!empty($jsonValue)) {
                                $jsonString = json_encode($jsonValue, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
                                $updateData[$field] = DB::raw("CAST(" . DB::getPdo()->quote($jsonString) . " AS JSON)");
                            } else {
                                $updateData[$field] = null;
                            }
                        } else {
                            $updateData[$field] = null;
                        }
                    }
                    
                    // Copy event - xử lý riêng với tương thích ngược
                    // Kiểm tra kiểu cột event trong database (JSON hoặc integer)
                    $eventColumnType = 'json'; // Mặc định là json
                    try {
                        $columnInfo = DB::select("SELECT DATA_TYPE FROM INFORMATION_SCHEMA.COLUMNS 
                            WHERE TABLE_SCHEMA = DATABASE() 
                            AND TABLE_NAME = 'major_language' 
                            AND COLUMN_NAME = 'event'");
                        if (!empty($columnInfo)) {
                            $eventColumnType = strtolower($columnInfo[0]->DATA_TYPE ?? 'json');
                        }
                    } catch (\Exception $e) {
                        // Nếu không kiểm tra được, mặc định là json
                        $eventColumnType = 'json';
                    }
                    
                    $eventValue = $pivot->event ?? null;
                    $finalEventValue = null;
                    
                    if ($eventValue !== null) {
                        // Lấy số ID từ các định dạng khác nhau
                        $eventId = null;
                        if (is_numeric($eventValue)) {
                            $eventId = (int)$eventValue;
                        } elseif (is_string($eventValue)) {
                            // Xử lý trường hợp cũ (JSON string) - tương thích ngược
                            $decoded = json_decode($eventValue, true);
                            if (is_array($decoded) && isset($decoded['post_catalogue_id'])) {
                                $eventId = (int)$decoded['post_catalogue_id'];
                            } elseif (is_numeric($eventValue)) {
                                $eventId = (int)$eventValue;
                            }
                        } elseif (is_array($eventValue) && isset($eventValue['post_catalogue_id'])) {
                            // Xử lý trường hợp cũ (JSON array) - tương thích ngược
                            $eventId = (int)$eventValue['post_catalogue_id'];
                        }
                        
                        // Format theo kiểu cột trong database
                        if ($eventId !== null && $eventId > 0) {
                            if ($eventColumnType === 'json') {
                                // Nếu cột vẫn là JSON (chưa chạy migration), format thành JSON
                                $jsonString = json_encode(['post_catalogue_id' => $eventId], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
                                $updateData['event'] = DB::raw("CAST(" . DB::getPdo()->quote($jsonString) . " AS JSON)");
                            } else {
                                // Nếu cột đã là integer (đã chạy migration), insert số trực tiếp
                                $updateData['event'] = $eventId;
                            }
                        } else {
                            $updateData['event'] = null;
                        }
                    } else {
                        $updateData['event'] = null;
                    }
                    
                    // Lưu trực tiếp vào database
                    DB::table('major_language')->updateOrInsert(
                        [
                            'major_id' => $newMajor->id,
                            'language_id' => $languageId
                        ],
                        array_merge($updateData, [
                            'updated_at' => now(),
                            'created_at' => now()
                        ])
                    );
                    
                    // Lưu lại canonical để tạo router
                    $canonical = $updateData['canonical'] ?? null;
                }

                // Tạo router mới với canonical mới
                if (isset($canonical) && $canonical) {
                    $routerRequest = \Illuminate\Http\Request::create('', 'POST', [
                        'canonical' => $canonical,
                    ]);
                    $this->createRouter($newMajor, $routerRequest, $this->controllerName, $languageId);
                }
            }

            DB::commit();
            return $newMajor;
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
            $major = $this->majorRepository->delete($id);
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

    private function createMajor($request)
    {
        $payload = [];
        foreach ($this->payload() as $field) {
            if (in_array($field, ['admission_subject', 'exam_location'])) {
                // Xử lý các field filter là string
                $fieldData = $request->input($field, '');
                $payload[$field] = !empty(trim($fieldData)) ? trim($fieldData) : null;
            } elseif (in_array($field, ['form_tai_lo_trinh_json', 'form_tu_van_mien_phi_json', 'form_hoc_thu_json'])) {
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
            } else {
                $payload[$field] = $request->input($field);
            }
        }
        $payload['user_id'] = Auth::id();
        $major = $this->majorRepository->create($payload);
        return $major;
    }

    private function updateMajor($major, $request)
    {
        $payload = [];
        foreach ($this->payload() as $field) {
            if (in_array($field, ['admission_subject', 'exam_location'])) {
                // Xử lý các field filter là string
                $fieldData = $request->input($field, '');
                $payload[$field] = !empty(trim($fieldData)) ? trim($fieldData) : null;
            } elseif (in_array($field, ['form_tai_lo_trinh_json', 'form_tu_van_mien_phi_json', 'form_hoc_thu_json'])) {
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
            } elseif ($field === 'follow') {
                // Xử lý follow: luôn lấy giá trị từ request nếu có, nếu không có thì giữ nguyên giá trị cũ
                if ($request->has('follow')) {
                    $followValue = $request->input('follow');
                    // Convert sang integer để đảm bảo đúng kiểu dữ liệu
                    $payload[$field] = is_numeric($followValue) ? (int)$followValue : ($followValue ? 1 : 2);
                } else {
                    // Giữ nguyên giá trị cũ nếu không có trong request
                    $payload[$field] = $major->follow ?? 1;
                }
            } else {
                $payload[$field] = $request->input($field);
            }
        }
        $flag = $this->majorRepository->update($major->id, $payload);
        return $flag;
    }

    private function updateLanguageForMajor($major, $request, $languageId)
    {
        $payload = $this->formatLanguagePayload($major, $request, $languageId);
        // dd($payload);
        
        // Các trường JSON cần json_encode và dùng DB::raw() để tránh double encoding
        // Event không phải JSON nữa, chỉ là số ID - lưu trực tiếp không CAST
        $jsonFields = ['feature', 'target', 'address', 'overview', 'who', 'priority', 'learn', 'chance', 'school', 'value', 'feedback'];
        $updateData = [];
        foreach ($payload as $key => $value) {
            if ($key === 'event') {
                // Event: lưu trực tiếp là số ID (hoặc null) - KHÔNG CAST AS JSON
                $updateData[$key] = is_numeric($value) && $value > 0 ? (int)$value : null;
            } elseif (in_array($key, $jsonFields) && is_array($value)) {
                // Dùng DB::raw() với CAST AS JSON để tránh double encoding
                $jsonString = json_encode($value, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
                $updateData[$key] = DB::raw("CAST(" . DB::getPdo()->quote($jsonString) . " AS JSON)");
            } else {
                $updateData[$key] = $value;
            }
        }
        
        // Dùng updateOrInsert để insert hoặc update
        DB::table('major_language')->updateOrInsert(
            [
                'major_id' => $major->id,
                'language_id' => $languageId
            ],
            array_merge($updateData, [
                'updated_at' => now(),
                'created_at' => DB::table('major_language')
                    ->where('major_id', $major->id)
                    ->where('language_id', $languageId)
                    ->value('created_at') ?? now()
            ])
        );
        
        return true;
    }

    private function formatLanguagePayload($major, $request, $languageId)
    {
        $payload = $request->only($this->payloadLanguage());
        $payload['canonical'] = Str::slug($payload['canonical']);
        
        // Chỉ cần lấy array từ request, Laravel sẽ tự động json_encode khi lưu qua casts
        $payload['feature'] = $request->input('feature', []);
        $payload['target'] = $request->input('target', []);
        $payload['address'] = $request->input('address', []);
        $payload['overview'] = $request->input('overview', []);
        
        // Format who data: tách title và items
        $whoData = $request->input('who', []);
        $whoFormatted = [];
        if (!empty($whoData)) {
            // Lấy title nếu có
            if (isset($whoData['title'])) {
                $whoFormatted['title'] = $whoData['title'];
            }
            // Lấy items (các key số)
            $whoItems = [];
            foreach ($whoData as $key => $value) {
                if (is_numeric($key) && is_array($value)) {
                    $whoItems[] = $value;
                }
            }
            if (!empty($whoItems)) {
                $whoFormatted['items'] = $whoItems;
            }
        }
        $payload['who'] = $whoFormatted;
        
        // Format priority data: tách title và items
        $priorityData = $request->input('priority', []);
        $priorityFormatted = [];
        if (!empty($priorityData)) {
            // Lấy title nếu có
            if (isset($priorityData['title'])) {
                $priorityFormatted['title'] = $priorityData['title'];
            }
            // Lấy items (các key số)
            $priorityItems = [];
            foreach ($priorityData as $key => $value) {
                if (is_numeric($key) && is_array($value)) {
                    $priorityItems[] = $value;
                }
            }
            if (!empty($priorityItems)) {
                $priorityFormatted['items'] = $priorityItems;
            }
        }
        $payload['priority'] = $priorityFormatted;
        
        // Format learn data: tách title, description và items
        $learnData = $request->input('learn', []);
        $learnFormatted = [];
        if (!empty($learnData)) {
            // Lấy title nếu có
            if (isset($learnData['title'])) {
                $learnFormatted['title'] = $learnData['title'];
            }
            // Lấy description nếu có
            if (isset($learnData['description'])) {
                $learnFormatted['description'] = $learnData['description'];
            }
            // Lấy items (các key số hoặc key 'items')
            $learnItems = [];
            if (isset($learnData['items']) && is_array($learnData['items'])) {
                // Xử lý từng category trong items
                foreach ($learnData['items'] as $categoryKey => $category) {
                    if (is_array($category)) {
                        $categoryFormatted = [];
                        // Lấy name của category
                        if (isset($category['name'])) {
                            $categoryFormatted['name'] = $category['name'];
                        }
                        // Lấy items trong category (không giới hạn số lượng)
                        if (isset($category['items']) && is_array($category['items'])) {
                            $categoryFormatted['items'] = [];
                            foreach ($category['items'] as $itemKey => $item) {
                                if (is_array($item) && isset($item['name'])) {
                                    $categoryFormatted['items'][] = $item;
                                }
                            }
                        }
                        if (!empty($categoryFormatted['name'])) {
                            $learnItems[] = $categoryFormatted;
                        }
                    }
                }
            } else {
                // Fallback: nếu không có cấu trúc items thì lấy trực tiếp
                foreach ($learnData as $key => $value) {
                    if (is_numeric($key) && is_array($value)) {
                        $learnItems[] = $value;
                    }
                }
            }
            if (!empty($learnItems)) {
                $learnFormatted['items'] = $learnItems;
            }
        }
        $payload['learn'] = $learnFormatted;
        $payload['chance'] = $request->input('chance', []);
        $payload['school'] = $request->input('school', []);
        $payload['value'] = $request->input('value', []);
        $payload['feedback'] = $request->input('feedback', []);
        // Format event data: event giờ là số ID (post_catalogue_id), không phải JSON array nữa
        $eventInput = $request->input('event');
        if (is_numeric($eventInput) && $eventInput > 0) {
            // Nếu là số trực tiếp
            $payload['event'] = (int)$eventInput;
        } elseif (is_array($eventInput) && isset($eventInput['post_catalogue_id']) && is_numeric($eventInput['post_catalogue_id']) && $eventInput['post_catalogue_id'] > 0) {
            // Tương thích ngược: nếu là array có post_catalogue_id
            $payload['event'] = (int)$eventInput['post_catalogue_id'];
        } else {
            // Mặc định là null
            $payload['event'] = null;
        }
        
        return $payload;
    }


    private function paginateSelect()
    {
        return [
            'majors.id',
            'majors.subtitle',
            'majors.image',
            'majors.publish',
            'majors.admission_subject',
            'majors.exam_location',
            'majors.created_at',
            'tb2.name',
            'tb2.canonical',
        ];
    }

    private function payload()
    {
        return [
            'subtitle',
            'banner',
            'career_image',
            'image',
            'publish',
            'follow',
            'is_home',
            'major_catalogue_id',
            'study_path_file',
            'is_show_feature',
            'is_show_overview',
            'is_show_who',
            'is_show_priority',
            'is_show_learn',
            'is_show_chance',
            'is_show_school',
            'is_show_value',
            'is_show_feedback',
            'is_show_event',
            'admission_subject',
            'exam_location',
            'form_tai_lo_trinh_json',
            'form_tu_van_mien_phi_json',
            'form_hoc_thu_json',
        ];
    }

    private function payloadLanguage()
    {
        return [
            'name',
            'description',
            'content',
            'training_system',
            'study_method',
            'admission_method',
            'enrollment_quota',
            'enrollment_period',
            'admission_type',
            'degree_type',
            'training_duration',
            'total_credits',
            'meta_title',
            'meta_keyword',
            'meta_description',
            'canonical'
        ];
    }

    public function updateStatus($post = [])
    {
        DB::beginTransaction();
        try {
            $model = lcfirst($post['model']) . 'Repository';
            
            // Xử lý riêng cho is_home: 2 = hiển thị, 0/1 = không hiển thị
            if ($post['field'] === 'is_home') {
                $payload[$post['field']] = (($post['value'] == 2) ? 0 : 2);
            } else {
                // Logic mặc định cho các field khác (publish, etc.)
                $payload[$post['field']] = (($post['value'] == 1) ? 2 : 1);
            }
            
            $post = $this->{$model}->update($post['modelId'], $payload);

            DB::commit();
            return true;
        } catch (\Exception $e) {
            DB::rollBack();
            echo $e->getMessage();
            die();
            return false;
        }
    }

}
