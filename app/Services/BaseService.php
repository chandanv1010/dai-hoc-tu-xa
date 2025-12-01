<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use App\Repositories\RouterRepository;
use App\Models\Language;
use Illuminate\Support\Str;

/**
 * Class LanguageService
 * @package App\Services
 */
class BaseService
{
   
    protected $routerRepository;
    protected $controllerName;
    protected $language;
    protected $nestedset;


    public function __construct(
        RouterRepository $routerRepository
    ){
        $this->routerRepository = $routerRepository;
    }

    public function currentLanguage(){
        $locale = app()->getLocale();
        $language = Language::where('canonical', $locale)->first();
        return $language->id;
    }

    public function formatAlbum($request){
        return ($request->input('album') && !empty($request->input('album'))) ? json_encode($request->input('album')) : '';
    }

    public function formatJson($request, $inputName){
        return ($request->input($inputName) && !empty($request->input($inputName))) ? json_encode($request->input($inputName)) : '';
    }

    public function nestedset(){
        $this->nestedset->Get('level ASC, order ASC');
        $this->nestedset->Recursive(0, $this->nestedset->Set());
        $this->nestedset->Action();
    }

    public function formatRouterPayload($model, $request, $controllerName, $languageId){
        $router = [
            'canonical' => Str::slug($request->input('canonical')),
            'module_id' => $model->id,
            'language_id' => $languageId,
            'controllers' => 'App\Http\Controllers\Frontend\\'.$controllerName.'',
        ];
        return $router;
    }

    public function createRouter($model, $request, $controllerName, $languageId){
        $router = $this->formatRouterPayload($model, $request, $controllerName, $languageId);
        
        // Kiểm tra xem có router khác với cùng canonical nhưng module_id khác không
        $duplicateRouter = DB::table('routers')
            ->where('canonical', $router['canonical'])
            ->where('language_id', $languageId)
            ->where('module_id', '!=', $model->id)
            ->first();
        
        if($duplicateRouter){
            // Xóa router trùng canonical với module_id khác
            DB::table('routers')->where('id', $duplicateRouter->id)->delete();
        }
        
        $this->routerRepository->create($router);
    }


    public function updateRouter($model, $request, $controllerName, $languageId){
        $payload = $this->formatRouterPayload($model, $request, $controllerName, $languageId);
        $controller = 'App\Http\Controllers\Frontend\\'.$controllerName;
        
        // Bước 1: Xóa TẤT CẢ router có cùng canonical (dù module_id nào) để tránh duplicate
        // Điều này đảm bảo canonical luôn unique và tránh conflict
        DB::table('routers')
            ->where('canonical', $payload['canonical'])
            ->where('language_id', $languageId)
            ->delete();
        
        // Bước 2: Xóa router cũ với cùng module_id, language_id và controller (nếu còn tồn tại)
        // (Để đảm bảo không còn router cũ nào của module này)
        $this->routerRepository->forceDeleteByCondition([
            ['module_id', '=', $model->id],
            ['language_id', '=', $languageId],
            ['controllers', '=', $controller],
        ]);
        
        // Bước 3: Tạo router mới với canonical
        $res = $this->routerRepository->create($payload);
        return $res;
    }

    public function updateStatus($post = []){
        DB::beginTransaction();
        try{
            $model = lcfirst($post['model']).'Repository';
            $payload[$post['field']] = (($post['value'] == 1)?2:1);
            $post = $this->{$model}->update($post['modelId'], $payload);

            DB::commit();
            return true;
        }catch(\Exception $e ){
            DB::rollBack();
            // Log::error($e->getMessage());
            echo $e->getMessage();die();
            return false;
        }
    }

    public function updateStatusAll($post){
        DB::beginTransaction();
        try{
            $model = lcfirst($post['model']).'Repository';
            $payload[$post['field']] = $post['value'];
            $flag = $this->{$model}->updateByWhereIn('id', $post['id'], $payload);
            // $this->changeUserStatus($post, $post['value']);

            DB::commit();
            return true;
        }catch(\Exception $e ){
            DB::rollBack();
            // Log::error($e->getMessage());
            echo $e->getMessage();die();
            return false;
        }
    }

 

}
