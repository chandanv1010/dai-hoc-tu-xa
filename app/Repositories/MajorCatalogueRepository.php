<?php

namespace App\Repositories;

use App\Models\MajorCatalogue;
use App\Repositories\BaseRepository;

class MajorCatalogueRepository extends BaseRepository
{
    protected $model;

    public function __construct(
        MajorCatalogue $model
    ){
        $this->model = $model;
        parent::__construct($model);
    }

    public function getMajorCatalogueById(int $id = 0, $language_id = 0){
        return $this->model->select([
                'major_catalogues.id',
                'major_catalogues.image',
                'major_catalogues.icon',
                'major_catalogues.album',
                'major_catalogues.publish',
                'major_catalogues.order',
                'major_catalogues.created_at',
                'tb2.name',
                'tb2.description',
                'tb2.content',
                'tb2.meta_title',
                'tb2.meta_keyword',
                'tb2.meta_description',
                'tb2.canonical',
            ]
        )
        ->join('major_catalogue_language as tb2', 'tb2.major_catalogue_id', '=', 'major_catalogues.id')
        ->where('tb2.language_id', '=', $language_id)
        ->find($id);
    }

    public function getAllMajorCatalogues($language_id = 0){
        return $this->model->select([
                'major_catalogues.id',
                'major_catalogues.image',
                'major_catalogues.icon',
                'major_catalogues.publish',
                'major_catalogues.order',
                'tb2.name',
                'tb2.canonical',
            ]
        )
        ->join('major_catalogue_language as tb2', 'tb2.major_catalogue_id', '=', 'major_catalogues.id')
        ->withCount(['majors' => function($query) {
            $query->where('publish', 2)->whereNull('deleted_at');
        }])
        ->where('tb2.language_id', '=', $language_id)
        ->where('major_catalogues.publish', '=', 2)
        ->orderBy('major_catalogues.order', 'asc')
        ->orderBy('tb2.name', 'asc')
        ->get();
    }
}

