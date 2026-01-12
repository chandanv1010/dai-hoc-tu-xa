<?php

namespace App\Repositories;

use App\Models\Tag;
use App\Repositories\BaseRepository;

class TagRepository extends BaseRepository
{
    protected $model;

    public function __construct(
        Tag $model
    ){
        $this->model = $model;
        parent::__construct($model);
    }

    public function findBySlug($slug){
        return $this->model->where('slug', $slug)->first();
    }

    public function findByName($name){
        return $this->model->where('name', $name)->first();
    }

    public function findOrCreateByName($name){
        $tag = $this->findByName($name);
        if (!$tag) {
            $tag = $this->create(['name' => $name]);
        }
        return $tag;
    }

    public function getAllTags(){
        return $this->model->orderBy('name', 'asc')->get();
    }
}
