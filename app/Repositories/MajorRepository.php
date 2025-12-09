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
                'majors.admission_subject',
                'majors.exam_location',
                'majors.form_tai_lo_trinh_json',
                'majors.form_tu_van_mien_phi_json',
                'majors.form_hoc_thu_json',
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

        // Nếu limit = 0 thì lấy tất cả, không giới hạn
        if ($limit > 0) {
            $majors = $query->limit($limit)->get();
        } else {
            $majors = $query->get();
        }

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
                'majors.admission_subject',
                'majors.exam_location',
                'majors.created_at',
            ]
        )
        ->where('majors.publish', '=', 2)
        ->whereNull('majors.deleted_at');

        // Filter theo school_id (có thể là array nếu dùng checkbox)
        if ($request->has('school_id')) {
            $schoolIds = is_array($request->school_id) ? $request->school_id : [$request->school_id];
            $schoolIds = array_filter($schoolIds);
            if (!empty($schoolIds)) {
                $query->whereHas('schools', function($q) use ($schoolIds) {
                    $q->whereIn('schools.id', $schoolIds);
                });
            }
        }

        // Filter theo training_duration (có thể là array nếu dùng checkbox)
        if ($request->has('duration')) {
            $durations = is_array($request->duration) ? $request->duration : [$request->duration];
            $durations = array_filter($durations);
            if (!empty($durations)) {
                $query->whereHas('languages', function($q) use ($language_id, $durations) {
                    $q->where('languages.id', $language_id);
                    $q->where(function($subQuery) use ($durations) {
                        foreach ($durations as $duration) {
                            $subQuery->orWhere('major_language.training_duration', 'LIKE', '%' . $duration . '%');
                        }
                    });
                });
            }
        }

        // Filter theo major_catalogue_id (nhóm ngành)
        if ($request->has('catalogue_id')) {
            $catalogueIds = is_array($request->catalogue_id) ? $request->catalogue_id : [$request->catalogue_id];
            $catalogueIds = array_filter($catalogueIds);
            if (!empty($catalogueIds)) {
                $query->whereIn('majors.major_catalogue_id', $catalogueIds);
            }
        }
        
        // Filter theo Đối Tượng Tuyển Sinh (admission_subject)
        if ($request->has('admission_subject')) {
            $admissionSubjects = $request->input('admission_subject');
            if (is_array($admissionSubjects) && count($admissionSubjects) > 0) {
                $query->where(function($q) use ($admissionSubjects) {
                    foreach ($admissionSubjects as $subject) {
                        $q->orWhere('majors.admission_subject', '=', trim($subject));
                    }
                });
            } elseif (is_string($admissionSubjects) && !empty(trim($admissionSubjects))) {
                $query->where('majors.admission_subject', '=', trim($admissionSubjects));
            }
        }
        
        // Filter theo Địa Điểm Thi (exam_location)
        if ($request->has('exam_location')) {
            $examLocations = $request->input('exam_location');
            if (is_array($examLocations) && count($examLocations) > 0) {
                $query->where(function($q) use ($examLocations) {
                    foreach ($examLocations as $location) {
                        $q->orWhere('majors.exam_location', '=', trim($location));
                    }
                });
            } elseif (is_string($examLocations) && !empty(trim($examLocations))) {
                $query->where('majors.exam_location', '=', trim($examLocations));
            }
        }

        $query->orderBy('majors.id', 'asc');

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

    public function getDistinctDurations($language_id = 0)
    {
        $durations = $this->model->select('major_language.training_duration')
            ->join('major_language', 'major_language.major_id', '=', 'majors.id')
            ->where('major_language.language_id', '=', $language_id)
            ->where('majors.publish', '=', 2)
            ->whereNull('majors.deleted_at')
            ->whereNotNull('major_language.training_duration')
            ->where('major_language.training_duration', '!=', '')
            ->distinct()
            ->pluck('training_duration')
            ->filter()
            ->unique()
            ->sort()
            ->values();
        
        return $durations;
    }

    public function getFilterOptions()
    {
        $majors = $this->model->select('admission_subject', 'exam_location')
            ->where('publish', '=', 2)
            ->whereNull('deleted_at')
            ->get();
        
        $admissionSubjects = [];
        $examLocations = [];
        
        foreach ($majors as $major) {
            // Xử lý admission_subject (TEXT field)
            if ($major->admission_subject && !empty(trim($major->admission_subject))) {
                $admissionSubjects[] = trim($major->admission_subject);
            }
            
            // Xử lý exam_location (TEXT field)
            if ($major->exam_location && !empty(trim($major->exam_location))) {
                $examLocations[] = trim($major->exam_location);
            }
        }
        
        return [
            'admission_subject' => array_values(array_unique(array_filter($admissionSubjects))),
            'exam_location' => array_values(array_unique(array_filter($examLocations))),
        ];
    }
}
