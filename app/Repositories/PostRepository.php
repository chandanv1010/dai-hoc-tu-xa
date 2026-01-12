<?php

namespace App\Repositories;

use App\Models\Post;
use App\Repositories\BaseRepository;

/**
 * Class UserService
 * @package App\Services
 */
class PostRepository extends BaseRepository
{
    protected $model;

    public function __construct(
        Post $model
    ){
        $this->model = $model;
        parent::__construct($model);
    }

    

    public function getPostById(int $id = 0, $language_id = 0){
        return $this->model->select([
                'posts.id',
                'posts.post_catalogue_id',
                'posts.image',
                'posts.icon',
                'posts.album',
                'posts.publish',
                'posts.follow',
                'posts.video',
                'posts.template',
                'posts.created_at',
                'posts.viewed',
                'posts.status_menu',
                'posts.short_name',
                'tb2.name',
                'tb2.description',
                'tb2.content',
                'tb2.meta_title',
                'tb2.meta_keyword',
                'tb2.meta_description',
                'tb2.canonical',
            ]
        )
        ->join('post_language as tb2', 'tb2.post_id', '=','posts.id')
        ->with('post_catalogues')
        ->with('tags')
        ->where('tb2.language_id', '=', $language_id)
        ->find($id);
    }

    public function getAllByLanguage($language_id = 0){
        return $this->model->select([
                'posts.id',
                'tb2.name',
            ]
        )
        ->join('post_language as tb2', 'tb2.post_id', '=','posts.id')
        ->where('tb2.language_id', '=', $language_id)
        ->where('posts.publish', '=', 2)
        ->orderBy('tb2.name', 'asc')
        ->get();
    }

    public function getRelated($limit = 6, $postCatalogueId = 0, $postId = 0, $languageId = 0){
        return $this->model->select([
                'posts.id',
                'posts.post_catalogue_id',
                'posts.image',
                'posts.created_at',
                'tb2.name',
                'tb2.description',
                'tb2.canonical',
            ])
            ->join('post_language as tb2', 'tb2.post_id', '=', 'posts.id')
            ->join('post_catalogue_post as tb3', 'posts.id', '=', 'tb3.post_id')
            ->where('tb2.language_id', '=', $languageId)
            ->where('posts.publish', '=', 2)
            ->where('tb3.post_catalogue_id', '=', $postCatalogueId)
            ->where('posts.id', '!=', $postId)
            ->orderBy('posts.id', 'desc')
            ->limit($limit)
            ->get();
    }

    public function search($keyword, $language_id, $perPage = 10){
        return $this->model->select([
                'posts.id',
                'posts.post_catalogue_id',
                'posts.image',
                'posts.created_at',
                'tb2.name',
                'tb2.description',
                'tb2.canonical',
            ])
            ->join('post_language as tb2', 'tb2.post_id', '=', 'posts.id')
            ->where('tb2.language_id', '=', $language_id)
            ->where('posts.publish', '=', 2)
            ->where(function($query) use ($keyword) {
                $query->where('tb2.name', 'LIKE', '%'.$keyword.'%')
                      ->orWhere('tb2.description', 'LIKE', '%'.$keyword.'%');
            })
            ->orderBy('posts.id', 'desc')
            ->paginate($perPage)->withQueryString()->withPath(config('app.url'). 'tim-kiem');
    }

    public function getPostsByTag($tagId, $languageId, $perPage = 15){
        return $this->model->select([
                'posts.id',
                'posts.image',
                'posts.created_at',
            ])
            ->whereHas('tags', function($query) use ($tagId) {
                $query->where('tags.id', $tagId);
            })
            ->where('posts.publish', '=', 2)
            ->with(['languages' => function($query) use ($languageId) {
                $query->where('language_id', $languageId);
            }])
            ->orderBy('posts.id', 'desc')
            ->paginate($perPage);
    }

}
