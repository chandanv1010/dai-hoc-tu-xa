<?php

namespace App\Repositories;

use App\Models\PostCatalogue;
use App\Repositories\BaseRepository;


/**
 * Class UserService
 * @package App\Services
 */
class PostCatalogueRepository extends BaseRepository
{
    protected $model;

    public function __construct(
        PostCatalogue $model
    ){
        $this->model = $model;
        parent::__construct($model);
    }

    

    public function getPostCatalogueById(int $id = 0, $language_id = 0){
        return $this->model->select([
                'post_catalogues.id',
                'post_catalogues.parent_id',
                'post_catalogues.image',
                'post_catalogues.icon',
                'post_catalogues.album',
                'post_catalogues.publish',
                'post_catalogues.follow',
                'post_catalogues.lft',
                'post_catalogues.rgt',
                'post_catalogues.created_at',
                'post_catalogues.short_name',
                'tb2.name',
                'tb2.description',
                'tb2.content',
                'tb2.meta_title',
                'tb2.meta_keyword',
                'tb2.meta_description',
                'tb2.canonical',
            ]
        )
        ->join('post_catalogue_language as tb2', 'tb2.post_catalogue_id', '=','post_catalogues.id')
        ->where('tb2.language_id', '=', $language_id)
        ->find($id);
    }

    public function getAllByLanguage($language_id = 0){
        return $this->model->select([
                'post_catalogues.id',
                'tb2.name',
            ]
        )
        ->join('post_catalogue_language as tb2', 'tb2.post_catalogue_id', '=','post_catalogues.id')
        ->where('tb2.language_id', '=', $language_id)
        ->where('post_catalogues.publish', '=', 2)
        ->orderBy('tb2.name', 'asc')
        ->get();
    }

}
