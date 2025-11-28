<?php

namespace App\Repositories;

use App\Models\Major;
use App\Repositories\BaseRepository;

class MajorRepository extends BaseRepository
{
    protected $model;

    public function __construct(Major $model)
    {
        $this->model = $model;
        parent::__construct($model);
    }

    public function getMajorById(int $id = 0, $language_id = 0)
    {
        // Chỉ select các cột từ majors, không select JSON từ join
        $major = $this->model->select([
                'majors.id',
                'majors.subtitle',
                'majors.banner',
                'majors.career_image',
                'majors.image',
                'majors.publish',
                'majors.major_catalogue_id',
                'majors.study_path_file',
                'majors.is_show_feature',
                'majors.is_show_overview',
                'majors.is_show_who',
                'majors.is_show_priority',
                'majors.is_show_learn',
                'majors.is_show_chance',
                'majors.is_show_school',
                'majors.is_show_value',
                'majors.is_show_feedback',
                'majors.is_show_event',
                'majors.created_at',
            ]
        )
        ->find($id);
        
        if ($major) {
            // Load languages relationship để có pivot với casts tự động
            $major->load(['languages' => function($query) use ($language_id) {
                $query->where('languages.id', $language_id);
            }]);
            
            // Đảm bảo pivot được cast đúng - decode JSON thủ công nếu cần
            if ($major->languages && $major->languages->count() > 0) {
                $pivot = $major->languages->first()->pivot;
                $jsonFields = ['feature', 'target', 'address', 'overview', 'who', 'priority', 'learn', 'chance', 'school', 'value', 'feedback', 'event'];
                foreach ($jsonFields as $field) {
                    if (isset($pivot->$field) && is_string($pivot->$field)) {
                        $decoded = json_decode($pivot->$field, true);
                        if (json_last_error() === JSON_ERROR_NONE) {
                            $pivot->$field = $decoded;
                        }
                    }
                }
            }
        }
        
        return $major;
    }

    public function getAllByLanguage($language_id = 0)
    {
        return $this->model->select([
                'majors.id',
                'tb2.name',
            ]
        )
        ->join('major_language as tb2', 'tb2.major_id', '=', 'majors.id')
        ->where('tb2.language_id', '=', $language_id)
        ->where('majors.publish', '=', 2)
        ->orderBy('tb2.name', 'asc')
        ->get();
    }

    public function getHomeMajors($language_id = 0, $limit = 6)
    {
        $majors = $this->model->select([
                'majors.id',
                'majors.image',
                'majors.publish',
                'majors.is_home',
            ]
        )
        ->where('majors.publish', '=', 2)
        ->where('majors.is_home', '=', 2)
        ->whereNull('majors.deleted_at')
        ->orderBy('majors.id', 'asc')
        ->limit($limit)
        ->get();

        // Load languages relationship cho từng major
        foreach ($majors as $major) {
            $major->load(['languages' => function($query) use ($language_id) {
                $query->where('languages.id', $language_id);
            }]);
            
            // Load schools relationship với language
            $major->load(['schools' => function($query) use ($language_id) {
                $query->with(['languages' => function($q) use ($language_id) {
                    $q->where('languages.id', $language_id);
                }]);
            }]);
            
            // Decode JSON fields nếu cần
            if ($major->languages && $major->languages->count() > 0) {
                $pivot = $major->languages->first()->pivot;
                $jsonFields = ['feature', 'target', 'address', 'overview', 'who', 'priority', 'learn', 'chance', 'school', 'value', 'feedback', 'event'];
                foreach ($jsonFields as $field) {
                    if (isset($pivot->$field) && is_string($pivot->$field)) {
                        $decoded = json_decode($pivot->$field, true);
                        if (json_last_error() === JSON_ERROR_NONE) {
                            $pivot->$field = $decoded;
                        }
                    }
                }
            }
        }

        return $majors;
    }

    public function getMajorsForAjax($catalogue_id = null, $language_id = 0, $limit = 6)
    {
        $query = $this->model->select([
                'majors.id',
                'majors.image',
                'majors.publish',
                'majors.major_catalogue_id',
            ]
        )
        ->where('majors.publish', '=', 2)
        ->whereNull('majors.deleted_at');

        if ($catalogue_id) {
            $query->where('majors.major_catalogue_id', '=', $catalogue_id)
                  ->orderBy('majors.id', 'desc');
        } else {
            $query->orderBy('majors.id', 'asc');
        }

        $majors = $query->limit($limit)->get();

        // Load languages relationship cho từng major
        foreach ($majors as $major) {
            $major->load(['languages' => function($query) use ($language_id) {
                $query->where('languages.id', $language_id);
            }]);
        }

        return $majors;
    }

    public function getMajorsByCatalogue($catalogue_id, $language_id = 0, $page = 1)
    {
        $perPage = 12;
        
        $majors = $this->model->select([
                'majors.id',
                'majors.image',
                'majors.publish',
                'majors.major_catalogue_id',
                'tb2.name',
                'tb2.canonical',
                'tb2.description',
            ]
        )
        ->join('major_language as tb2', 'tb2.major_id', '=', 'majors.id')
        ->where('tb2.language_id', '=', $language_id)
        ->where('majors.publish', '=', 2)
        ->where('majors.major_catalogue_id', '=', $catalogue_id)
        ->whereNull('majors.deleted_at')
        ->orderBy('majors.id', 'desc')
        ->paginate($perPage, ['*'], 'page', $page);

        return $majors;
    }

    public function paginate($request, $language_id = 0, $perPage = 12, $path = 'cac-nganh-dao-tao-tu-xa')
    {
        $query = $this->model->select([
                'majors.id',
                'majors.image',
                'majors.publish',
                'majors.created_at',
            ]
        )
        ->where('majors.publish', '=', 2)
        ->whereNull('majors.deleted_at')
        ->orderBy('majors.id', 'asc');

        $paginationPath = ($path === 'cac-nganh-dao-tao-tu-xa.html') 
            ? config('app.url') . '/cac-nganh-dao-tao-tu-xa.html'
            : config('app.url') . '/' . $path;
        
        $majors = $query->paginate($perPage)->withQueryString()->withPath($paginationPath);

        // Load languages relationship cho từng major
        foreach ($majors as $major) {
            $major->load(['languages' => function($query) use ($language_id) {
                $query->where('languages.id', $language_id);
            }]);
            
            // Decode JSON fields nếu cần
            if ($major->languages && $major->languages->count() > 0) {
                $pivot = $major->languages->first()->pivot;
                $jsonFields = ['feature', 'target', 'address', 'overview', 'who', 'priority', 'learn', 'chance', 'school', 'value', 'feedback', 'event'];
                foreach ($jsonFields as $field) {
                    if (isset($pivot->$field) && is_string($pivot->$field)) {
                        $decoded = json_decode($pivot->$field, true);
                        if (json_last_error() === JSON_ERROR_NONE) {
                            $pivot->$field = $decoded;
                        }
                    }
                }
            }
        }

        return $majors;
    }

    public function search($keyword, $language_id, $perPage = 10){
        return $this->model->select([
                'majors.id',
                'majors.image',
                'majors.subtitle',
                'majors.publish',
                'tb2.name',
                'tb2.description',
                'tb2.canonical',
            ])
            ->join('major_language as tb2', 'tb2.major_id', '=', 'majors.id')
            ->where('tb2.language_id', '=', $language_id)
            ->where('majors.publish', '=', 2)
            ->whereNull('majors.deleted_at')
            ->where(function($query) use ($keyword) {
                $query->where('tb2.name', 'LIKE', '%'.$keyword.'%')
                      ->orWhere('tb2.description', 'LIKE', '%'.$keyword.'%');
            })
            ->orderBy('majors.id', 'desc')
            ->paginate($perPage)->withQueryString()->withPath(config('app.url'). 'tim-kiem');
    }
}
