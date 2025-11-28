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
        $payload = $request->only($this->payload());
        $payload['user_id'] = Auth::id();
        $major = $this->majorRepository->create($payload);
        return $major;
    }

    private function updateMajor($major, $request)
    {
        $payload = $request->only($this->payload());
        $flag = $this->majorRepository->update($major->id, $payload);
        return $flag;
    }

    private function updateLanguageForMajor($major, $request, $languageId)
    {
        $payload = $this->formatLanguagePayload($major, $request, $languageId);
        // dd($payload);
        
        // Các trường JSON cần json_encode và dùng DB::raw() để tránh double encoding
        $jsonFields = ['feature', 'target', 'address', 'overview', 'who', 'priority', 'learn', 'chance', 'school', 'value', 'feedback', 'event'];
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
        $payload['who'] = $request->input('who', []);
        $payload['priority'] = $request->input('priority', []);
        $payload['learn'] = $request->input('learn', []);
        $payload['chance'] = $request->input('chance', []);
        $payload['school'] = $request->input('school', []);
        $payload['value'] = $request->input('value', []);
        $payload['feedback'] = $request->input('feedback', []);
        $payload['event'] = $request->input('event', []);
        
        return $payload;
    }


    private function paginateSelect()
    {
        return [
            'majors.id',
            'majors.subtitle',
            'majors.image',
            'majors.publish',
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
