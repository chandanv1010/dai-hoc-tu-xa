<?php

namespace App\Services;

use App\Services\BaseService;
use App\Repositories\MajorCatalogueRepository;
use App\Repositories\RouterRepository;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class MajorCatalogueService extends BaseService
{
    protected $majorCatalogueRepository;
    protected $routerRepository;
    protected $language;
    protected $controllerName = 'MajorCatalogueController';

    public function __construct(
        MajorCatalogueRepository $majorCatalogueRepository,
        RouterRepository $routerRepository
    ){
        $this->majorCatalogueRepository = $majorCatalogueRepository;
        $this->routerRepository = $routerRepository;
    }

    public function paginate($request, $languageId){
        $perPage = $request->integer('perpage');
        $condition = [
            'keyword' => addslashes($request->input('keyword')),
            'publish' => $request->integer('publish'),
            'where' => [
                ['tb2.language_id', '=', $languageId]
            ]
        ];
        $majorCatalogues = $this->majorCatalogueRepository->pagination(
            $this->paginateSelect(), 
            $condition, 
            $perPage,
            ['path' => 'major/catalogue/index'],  
            ['major_catalogues.order', 'ASC'],
            [
                ['major_catalogue_language as tb2','tb2.major_catalogue_id', '=' , 'major_catalogues.id']
            ], 
            ['languages']
        );

        return $majorCatalogues;
    }

    public function create($request, $languageId){
        DB::beginTransaction();
        try{
            $majorCatalogue = $this->createCatalogue($request);
            if($majorCatalogue->id > 0){
                $this->updateLanguageForCatalogue($majorCatalogue, $request, $languageId);
                $this->createRouter($majorCatalogue, $request, $this->controllerName, $languageId);
            }
            DB::commit();
            return $majorCatalogue;
        }catch(\Exception $e ){
            DB::rollBack();
            echo $e->getMessage();die();
            return false;
        }
    }

    public function update($id, $request, $languageId){
        DB::beginTransaction();
        try{
            $majorCatalogue = $this->majorCatalogueRepository->findById($id);
            if(!$majorCatalogue){
                DB::rollBack();
                return false;
            }
            $flag = $this->updateCatalogue($majorCatalogue, $request);
            if($flag == TRUE){
                $this->updateLanguageForCatalogue($majorCatalogue, $request, $languageId);
                $this->updateRouter(
                    $majorCatalogue, $request, $this->controllerName, $languageId
                );
            }
            DB::commit();
            return true;
        }catch(\Exception $e ){
            DB::rollBack();
            echo $e->getMessage();die();
            return false;
        }
    }

    public function destroy($id, $languageId){
        DB::beginTransaction();
        try{
            $majorCatalogue = $this->majorCatalogueRepository->delete($id);
            $this->routerRepository->forceDeleteByCondition([
                ['module_id', '=', $id],
                ['controllers', '=', 'App\Http\Controllers\Frontend\MajorCatalogueController'],
            ]);

            DB::commit();
            return true;
        }catch(\Exception $e ){
            DB::rollBack();
            echo $e->getMessage();die();
            return false;
        }
    }

    private function createCatalogue($request){
        $payload = $request->only($this->payload());
        $payload['album'] = $this->formatAlbum($request);
        $payload['user_id'] = Auth::id();
        $majorCatalogue = $this->majorCatalogueRepository->create($payload);
        return $majorCatalogue;
    }

    private function updateCatalogue($majorCatalogue, $request){
        $payload = $request->only($this->payload());
        $payload['album'] = $this->formatAlbum($request);
        // Đảm bảo follow không bị reset nếu không có trong request
        if (!$request->has('follow')) {
            unset($payload['follow']);
        }
        $flag = $this->majorCatalogueRepository->update($majorCatalogue->id, $payload);
        return $flag;
    }

    private function updateLanguageForCatalogue($majorCatalogue, $request, $languageId){
        $payload = $this->formatLanguagePayload($majorCatalogue, $request, $languageId);
        $majorCatalogue->languages()->detach([$languageId, $majorCatalogue->id]);
        $language = $this->majorCatalogueRepository->createPivot($majorCatalogue, $payload, 'languages');
        return $language;
    }

    private function formatLanguagePayload($majorCatalogue, $request, $languageId){
        $payload = $request->only($this->payloadLanguage());
        $payload['canonical'] = Str::slug($payload['canonical']);
        $payload['language_id'] =  $languageId;
        $payload['major_catalogue_id'] = $majorCatalogue->id;
        return $payload;
    }

    private function paginateSelect(){
        return [
            'major_catalogues.id', 
            'major_catalogues.publish',
            'major_catalogues.image',
            'major_catalogues.order',
            'tb2.name', 
            'tb2.canonical',
        ];
    }

    private function payload(){
        return [
            'publish',
            'follow',
            'image',
            'album',
            'order'
        ];
    }

    private function payloadLanguage(){
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
}

