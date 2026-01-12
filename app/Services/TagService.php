<?php

namespace App\Services;

use App\Services\BaseService;
use App\Repositories\TagRepository;
use Illuminate\Support\Facades\DB;

class TagService extends BaseService
{
    protected $tagRepository;
    
    public function __construct(
        TagRepository $tagRepository
    ){
        $this->tagRepository = $tagRepository;
        $this->controllerName = 'TagController';
    }

    public function paginate($request){
        $perPage = 20;
        $condition = [
            'keyword' => addslashes($request->input('keyword')),
            'publish' => $request->integer('publish'),
        ];

        $paginationConfig = [
            'path' => 'tag/index',
        ];

        $orderBy = ['id', 'DESC'];

        $tags = $this->tagRepository->pagination(
            ['*'], 
            $condition, 
            $perPage,
            $paginationConfig,  
            $orderBy,
            [],
            []
        ); 

        return $tags;
    }

    public function create($request){
        DB::beginTransaction();
        try{
            $payload = $request->only(['name']);
            $tag = $this->tagRepository->create($payload);
            DB::commit();
            return $tag;
        }catch(\Exception $e){
            DB::rollBack();
            throw $e;
        }
    }

    public function update($id, $request){
        DB::beginTransaction();
        try{
            $payload = $request->only(['name']);
            $tag = $this->tagRepository->update($id, $payload);
            DB::commit();
            return $tag;
        }catch(\Exception $e){
            DB::rollBack();
            throw $e;
        }
    }

    public function delete($id){
        DB::beginTransaction();
        try{
            $this->tagRepository->delete($id);
            DB::commit();
            return true;
        }catch(\Exception $e){
            DB::rollBack();
            throw $e;
        }
    }

    public function getAllTags(){
        return $this->tagRepository->getAllTags();
    }

    public function findOrCreateTags($tagNames){
        $tags = [];
        if (is_array($tagNames)) {
            foreach ($tagNames as $tagName) {
                if (!empty(trim($tagName))) {
                    $tags[] = $this->tagRepository->findOrCreateByName(trim($tagName));
                }
            }
        }
        return $tags;
    }

    private function paginateSelect(){
        return [
            'id',
            'name',
            'slug',
            'created_at',
            'updated_at',
        ];
    }
}
