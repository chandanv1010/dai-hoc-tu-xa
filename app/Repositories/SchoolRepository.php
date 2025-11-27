<?php

namespace App\Repositories;

use App\Models\School;
use App\Repositories\BaseRepository;

class SchoolRepository extends BaseRepository
{
    protected $model;

    public function __construct(School $model)
    {
        $this->model = $model;
        parent::__construct($model);
    }

    public function getSchoolById(int $id = 0, $language_id = 0)
    {
        $school = $this->model->select([
                'schools.id',
                'schools.image',
                'schools.banner',
                'schools.album',
                'schools.intro_image',
                'schools.download_file',
                'schools.announce_image',
                'schools.enrollment_quota',
                'schools.short_name',
                'schools.publish',
                'schools.statistics_majors',
                'schools.statistics_students',
                'schools.statistics_courses',
                'schools.statistics_satisfaction',
                'schools.statistics_employment',
                'schools.is_show_statistics',
                'schools.is_show_intro',
                'schools.is_show_announce',
                'schools.is_show_advantage',
                'schools.is_show_suitable',
                'schools.is_show_majors',
                'schools.is_show_study_method',
                'schools.is_show_feedback',
                'schools.is_show_event',
                'schools.is_show_value',
                'schools.created_at',
            ]
        )
        ->find($id);
        
        if ($school) {
            // Load languages relationship để có pivot với casts tự động
            $school->load(['languages' => function($query) use ($language_id) {
                $query->where('languages.id', $language_id);
            }]);
            
            // Đảm bảo pivot được cast đúng - decode JSON thủ công nếu cần
            if ($school->languages && $school->languages->count() > 0) {
                $pivot = $school->languages->first()->pivot;
                $jsonFields = ['intro', 'announce', 'advantage', 'suitable', 'majors', 'study_method', 'feedback', 'event', 'value'];
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
        
        return $school;
    }

    public function getAllSchools($language_id = 0, $limit = 0)
    {
        $query = $this->model->select([
                'schools.id',
                'schools.image',
                'schools.publish',
                'schools.statistics_majors',
            ]
        )
        ->where('schools.publish', '=', 2)
        ->whereNull('schools.deleted_at')
        ->orderBy('schools.id', 'asc');

        if ($limit > 0) {
            $query->limit($limit);
        }

        $schools = $query->get();

        // Load languages relationship cho từng school
        foreach ($schools as $school) {
            $school->load(['languages' => function($query) use ($language_id) {
                $query->where('languages.id', $language_id);
            }]);
            
            // Decode JSON fields nếu cần
            if ($school->languages && $school->languages->count() > 0) {
                $pivot = $school->languages->first()->pivot;
                $jsonFields = ['intro', 'announce', 'advantage', 'suitable', 'majors', 'study_method', 'feedback', 'event', 'value'];
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

        return $schools;
    }

    public function paginate($request, $language_id = 0, $perPage = 12, $path = 'cac-truong-dao-tao-tu-xa')
    {
        $query = $this->model->select([
                'schools.id',
                'schools.image',
                'schools.publish',
                'schools.statistics_majors',
                'schools.created_at',
            ]
        )
        ->where('schools.publish', '=', 2)
        ->whereNull('schools.deleted_at')
        ->orderBy('schools.id', 'asc');

        $paginationPath = ($path === 'cac-truong-dao-tao-tu-xa.html') 
            ? config('app.url') . '/cac-truong-dao-tao-tu-xa.html'
            : config('app.url') . '/' . $path;
        
        $schools = $query->paginate($perPage)->withQueryString()->withPath($paginationPath);

        // Load languages relationship cho từng school
        foreach ($schools as $school) {
            $school->load(['languages' => function($query) use ($language_id) {
                $query->where('languages.id', $language_id);
            }]);
            
            // Decode JSON fields nếu cần
            if ($school->languages && $school->languages->count() > 0) {
                $pivot = $school->languages->first()->pivot;
                $jsonFields = ['intro', 'announce', 'advantage', 'suitable', 'majors', 'study_method', 'feedback', 'event', 'value'];
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

        return $schools;
    }

    public function search($keyword, $language_id, $perPage = 10){
        return $this->model->select([
                'schools.id',
                'schools.image',
                'schools.short_name',
                'schools.publish',
                'tb2.name',
                'tb2.description',
                'tb2.canonical',
            ])
            ->join('school_language as tb2', 'tb2.school_id', '=', 'schools.id')
            ->where('tb2.language_id', '=', $language_id)
            ->where('schools.publish', '=', 2)
            ->whereNull('schools.deleted_at')
            ->where(function($query) use ($keyword) {
                $query->where('tb2.name', 'LIKE', '%'.$keyword.'%')
                      ->orWhere('tb2.description', 'LIKE', '%'.$keyword.'%');
            })
            ->orderBy('schools.id', 'desc')
            ->paginate($perPage)->withQueryString()->withPath(config('app.url'). 'tim-kiem');
    }
}

