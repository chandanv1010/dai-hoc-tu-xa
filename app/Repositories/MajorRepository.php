<?php

namespace App\Repositories;

use App\Models\Major;
use App\Repositories\BaseRepository;
use Illuminate\Support\Str;

class MajorRepository extends BaseRepository
{
    protected $model;

    public function __construct(Major $model)
    {
        $this->model = $model;
        parent::__construct($model);
    }

    /**
     * Slug hóa giá trị để so sánh (xử lý tiếng Việt)
     */
    private function slugValue($value)
    {
        if (empty($value)) {
            return '';
        }
        
        // Chuẩn hóa: lowercase, trim, loại bỏ khoảng trắng thừa
        $value = mb_strtolower(trim($value), 'UTF-8');
        $value = preg_replace('/\s+/', ' ', $value);
        
        // Bỏ dấu tiếng Việt và chuyển thành slug
        $value = $this->removeVietnameseAccents($value);
        
        return Str::slug($value, '-');
    }

    /**
     * Loại bỏ dấu tiếng Việt
     */
    private function removeVietnameseAccents($str)
    {
        $str = preg_replace("/(à|á|ạ|ả|ã|â|ầ|ấ|ậ|ẩ|ẫ|ă|ằ|ắ|ặ|ẳ|ẵ)/", 'a', $str);
        $str = preg_replace("/(è|é|ẹ|ẻ|ẽ|ê|ề|ế|ệ|ể|ễ)/", 'e', $str);
        $str = preg_replace("/(ì|í|ị|ỉ|ĩ)/", 'i', $str);
        $str = preg_replace("/(ò|ó|ọ|ỏ|õ|ô|ồ|ố|ộ|ổ|ỗ|ơ|ờ|ớ|ợ|ở|ỡ)/", 'o', $str);
        $str = preg_replace("/(ù|ú|ụ|ủ|ũ|ư|ừ|ứ|ự|ử|ữ)/", 'u', $str);
        $str = preg_replace("/(ỳ|ý|ỵ|ỷ|ỹ)/", 'y', $str);
        $str = preg_replace("/(đ)/", 'd', $str);
        $str = preg_replace("/(À|Á|Ạ|Ả|Ã|Â|Ầ|Ấ|Ậ|Ẩ|Ẫ|Ă|Ằ|Ắ|Ặ|Ẳ|Ẵ)/", 'A', $str);
        $str = preg_replace("/(È|É|Ẹ|Ẻ|Ẽ|Ê|Ề|Ế|Ệ|Ể|Ễ)/", 'E', $str);
        $str = preg_replace("/(Ì|Í|Ị|Ỉ|Ĩ)/", 'I', $str);
        $str = preg_replace("/(Ò|Ó|Ọ|Ỏ|Õ|Ô|Ồ|Ố|Ộ|Ổ|Ỗ|Ơ|Ờ|Ớ|Ợ|Ở|Ỡ)/", 'O', $str);
        $str = preg_replace("/(Ù|Ú|Ụ|Ủ|Ũ|Ư|Ừ|Ứ|Ự|Ử|Ữ)/", 'U', $str);
        $str = preg_replace("/(Ỳ|Ý|Ỵ|Ỷ|Ỹ)/", 'Y', $str);
        $str = preg_replace("/(Đ)/", 'D', $str);
        
        return $str;
    }

    /**
     * Kiểm tra xem một giá trị có match với một string chứa nhiều giá trị cách nhau bởi dấu phẩy không (so sánh bằng slug)
     */
    private function valueMatchesInString($value, $commaSeparatedString)
    {
        if (empty($commaSeparatedString) || empty($value)) {
            return false;
        }
        
        $valueSlug = $this->slugValue($value);
        if (empty($valueSlug)) {
            return false;
        }
        
        $values = array_map('trim', explode(',', $commaSeparatedString));
        foreach ($values as $dbValue) {
            if (!empty($dbValue) && $this->slugValue($dbValue) === $valueSlug) {
                return true;
            }
        }
        
        return false;
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
            // event không phải JSON nữa, chỉ là số ID
            if ($major->languages && $major->languages->count() > 0) {
                $pivot = $major->languages->first()->pivot;
                $jsonFields = ['feature', 'target', 'address', 'overview', 'who', 'priority', 'learn', 'chance', 'school', 'value', 'feedback'];
                foreach ($jsonFields as $field) {
                    if (isset($pivot->$field) && is_string($pivot->$field)) {
                        $decoded = json_decode($pivot->$field, true);
                        if (json_last_error() === JSON_ERROR_NONE) {
                            $pivot->$field = $decoded;
                        }
                    }
                }
                // Xử lý event riêng - nếu là string JSON cũ thì convert sang số
                if (isset($pivot->event) && is_string($pivot->event)) {
                    $decoded = json_decode($pivot->event, true);
                    if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
                        // Tương thích ngược: nếu là array cũ có post_catalogue_id
                        if (isset($decoded['post_catalogue_id'])) {
                            $pivot->event = (int)$decoded['post_catalogue_id'];
                        } else {
                            $pivot->event = null;
                        }
                    } elseif (is_numeric($pivot->event)) {
                        $pivot->event = (int)$pivot->event;
                    } else {
                        $pivot->event = null;
                    }
                } elseif (isset($pivot->event) && is_array($pivot->event)) {
                    // Tương thích ngược: nếu là array cũ
                    if (isset($pivot->event['post_catalogue_id'])) {
                        $pivot->event = (int)$pivot->event['post_catalogue_id'];
                    } else {
                        $pivot->event = null;
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
        // Join với major_language để chỉ lấy majors có dữ liệu với language_id tương ứng
        $majors = $this->model->select([
                'majors.id',
                'majors.image',
                'majors.publish',
            ]
        )
        ->distinct()
        ->join('major_language as ml', function($join) use ($language_id) {
            $join->on('ml.major_id', '=', 'majors.id')
                 ->where('ml.language_id', '=', $language_id);
        })
        ->where('majors.publish', '=', 2)
        ->whereNull('majors.deleted_at')
        ->whereNotNull('ml.name') // Đảm bảo có tên
        ->orderBy('majors.id', 'asc')
        // ->limit($limit)
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
            // event không phải JSON nữa, chỉ là số ID
            if ($major->languages && $major->languages->count() > 0) {
                $pivot = $major->languages->first()->pivot;
                $jsonFields = ['feature', 'target', 'address', 'overview', 'who', 'priority', 'learn', 'chance', 'school', 'value', 'feedback'];
                foreach ($jsonFields as $field) {
                    if (isset($pivot->$field) && is_string($pivot->$field)) {
                        $decoded = json_decode($pivot->$field, true);
                        if (json_last_error() === JSON_ERROR_NONE) {
                            $pivot->$field = $decoded;
                        }
                    }
                }
                // Xử lý event riêng - nếu là string JSON cũ thì convert sang số
                if (isset($pivot->event) && is_string($pivot->event)) {
                    $decoded = json_decode($pivot->event, true);
                    if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
                        // Tương thích ngược: nếu là array cũ có post_catalogue_id
                        if (isset($decoded['post_catalogue_id'])) {
                            $pivot->event = (int)$decoded['post_catalogue_id'];
                        } else {
                            $pivot->event = null;
                        }
                    } elseif (is_numeric($pivot->event)) {
                        $pivot->event = (int)$pivot->event;
                    } else {
                        $pivot->event = null;
                    }
                } elseif (isset($pivot->event) && is_array($pivot->event)) {
                    // Tương thích ngược: nếu là array cũ
                    if (isset($pivot->event['post_catalogue_id'])) {
                        $pivot->event = (int)$pivot->event['post_catalogue_id'];
                    } else {
                        $pivot->event = null;
                    }
                }
            }
        }
        
        // Filter lại để chỉ giữ majors có languages relationship
        $majors = $majors->filter(function($major) {
            return $major->languages && $major->languages->count() > 0;
        })->values();

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

        // Load languages và schools relationships cho từng major
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
        // Filter theo major_catalogue_id (nhóm ngành)
        if ($request->has('catalogue_id')) {
            $catalogueIds = $request->input('catalogue_id');
            // Đảm bảo là array
            if (!is_array($catalogueIds)) {
                $catalogueIds = [$catalogueIds];
            }
            // Filter giá trị rỗng
            $catalogueIds = array_filter($catalogueIds, function($value) {
                return !is_null($value) && $value !== '';
            });
            
            if (!empty($catalogueIds)) {
                $query->where(function($q) use ($catalogueIds) {
                    $q->whereIn('majors.major_catalogue_id', $catalogueIds);
                });
            }
        }
        
        // Chuẩn bị filter values cho admission_subject và exam_location (sử dụng slug)
        $admissionSubjectFilters = [];
        $examLocationFilters = [];
        
        if ($request->has('admission_subject')) {
            $admissionSubject = $request->input('admission_subject');
            $admissionSubjectFilters = is_array($admissionSubject) ? $admissionSubject : [$admissionSubject];
            $admissionSubjectFilters = array_filter(array_map('trim', $admissionSubjectFilters));
        }
        
        if ($request->has('exam_location')) {
            $examLocation = $request->input('exam_location');
            $examLocationFilters = is_array($examLocation) ? $examLocation : [$examLocation];
            $examLocationFilters = array_filter(array_map('trim', $examLocationFilters));
        }
        
        // Nếu có filter admission_subject hoặc exam_location, filter bằng slug
        if (!empty($admissionSubjectFilters) || !empty($examLocationFilters)) {
            $allMajors = $this->model->select('id', 'admission_subject', 'exam_location')
                ->where('publish', '=', 2)
                ->whereNull('deleted_at')
                ->get();
            
            $filteredIds = [];
            foreach ($allMajors as $major) {
                $matchAdmission = empty($admissionSubjectFilters);
                $matchLocation = empty($examLocationFilters);
                
                // Kiểm tra admission_subject
                if (!empty($admissionSubjectFilters)) {
                    foreach ($admissionSubjectFilters as $subject) {
                        if ($this->valueMatchesInString($subject, $major->admission_subject)) {
                            $matchAdmission = true;
                            break;
                        }
                    }
                }
                
                // Kiểm tra exam_location
                if (!empty($examLocationFilters)) {
                    foreach ($examLocationFilters as $location) {
                        if ($this->valueMatchesInString($location, $major->exam_location)) {
                            $matchLocation = true;
                            break;
                        }
                    }
                }
                
                // Chỉ thêm vào nếu match cả 2 điều kiện (nếu có)
                if ($matchAdmission && $matchLocation) {
                    $filteredIds[] = $major->id;
                }
            }
            
            if (!empty($filteredIds)) {
                $query->whereIn('majors.id', $filteredIds);
            } else {
                $query->whereRaw('1 = 0'); // Không có kết quả
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
            // event không phải JSON nữa, chỉ là số ID
            if ($major->languages && $major->languages->count() > 0) {
                $pivot = $major->languages->first()->pivot;
                $jsonFields = ['feature', 'target', 'address', 'overview', 'who', 'priority', 'learn', 'chance', 'school', 'value', 'feedback'];
                foreach ($jsonFields as $field) {
                    if (isset($pivot->$field) && is_string($pivot->$field)) {
                        $decoded = json_decode($pivot->$field, true);
                        if (json_last_error() === JSON_ERROR_NONE) {
                            $pivot->$field = $decoded;
                        }
                    }
                }
                // Xử lý event riêng - nếu là string JSON cũ thì convert sang số
                if (isset($pivot->event) && is_string($pivot->event)) {
                    $decoded = json_decode($pivot->event, true);
                    if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
                        // Tương thích ngược: nếu là array cũ có post_catalogue_id
                        if (isset($decoded['post_catalogue_id'])) {
                            $pivot->event = (int)$decoded['post_catalogue_id'];
                        } else {
                            $pivot->event = null;
                        }
                    } elseif (is_numeric($pivot->event)) {
                        $pivot->event = (int)$pivot->event;
                    } else {
                        $pivot->event = null;
                    }
                } elseif (isset($pivot->event) && is_array($pivot->event)) {
                    // Tương thích ngược: nếu là array cũ
                    if (isset($pivot->event['post_catalogue_id'])) {
                        $pivot->event = (int)$pivot->event['post_catalogue_id'];
                    } else {
                        $pivot->event = null;
                    }
                }
            }
        }

        return $majors;
    }

    public function getAll($request, $language_id = 0)
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
            $catalogueIds = $request->input('catalogue_id');
            // Đảm bảo là array
            if (!is_array($catalogueIds)) {
                $catalogueIds = [$catalogueIds];
            }
            // Filter giá trị rỗng
            $catalogueIds = array_filter($catalogueIds, function($value) {
                return !is_null($value) && $value !== '';
            });
            
            if (!empty($catalogueIds)) {
                $query->where(function($q) use ($catalogueIds) {
                    $q->whereIn('majors.major_catalogue_id', $catalogueIds);
                });
            }
        }
        
        // Chuẩn bị filter values cho admission_subject và exam_location (sử dụng slug)
        $admissionSubjectFilters = [];
        $examLocationFilters = [];
        
        if ($request->has('admission_subject')) {
            $admissionSubject = $request->input('admission_subject');
            $admissionSubjectFilters = is_array($admissionSubject) ? $admissionSubject : [$admissionSubject];
            $admissionSubjectFilters = array_filter(array_map('trim', $admissionSubjectFilters));
        }
        
        if ($request->has('exam_location')) {
            $examLocation = $request->input('exam_location');
            $examLocationFilters = is_array($examLocation) ? $examLocation : [$examLocation];
            $examLocationFilters = array_filter(array_map('trim', $examLocationFilters));
        }
        
        // Nếu có filter admission_subject hoặc exam_location, filter bằng slug
        if (!empty($admissionSubjectFilters) || !empty($examLocationFilters)) {
            $allMajors = $this->model->select('id', 'admission_subject', 'exam_location')
                ->where('publish', '=', 2)
                ->whereNull('deleted_at')
                ->get();
            
            $filteredIds = [];
            foreach ($allMajors as $major) {
                $matchAdmission = empty($admissionSubjectFilters);
                $matchLocation = empty($examLocationFilters);
                
                // Kiểm tra admission_subject
                if (!empty($admissionSubjectFilters)) {
                    foreach ($admissionSubjectFilters as $subject) {
                        if ($this->valueMatchesInString($subject, $major->admission_subject)) {
                            $matchAdmission = true;
                            break;
                        }
                    }
                }
                
                // Kiểm tra exam_location
                if (!empty($examLocationFilters)) {
                    foreach ($examLocationFilters as $location) {
                        if ($this->valueMatchesInString($location, $major->exam_location)) {
                            $matchLocation = true;
                            break;
                        }
                    }
                }
                
                // Chỉ thêm vào nếu match cả 2 điều kiện (nếu có)
                if ($matchAdmission && $matchLocation) {
                    $filteredIds[] = $major->id;
                }
            }
            
            if (!empty($filteredIds)) {
                $query->whereIn('majors.id', $filteredIds);
            } else {
                $query->whereRaw('1 = 0'); // Không có kết quả
            }
        }

        $query->orderBy('majors.id', 'asc');
        
        $majors = $query->get();

        // Load languages relationship cho từng major
        foreach ($majors as $major) {
            $major->load(['languages' => function($query) use ($language_id) {
                $query->where('languages.id', $language_id);
            }]);
            
            // Decode JSON fields nếu cần
            // event không phải JSON nữa, chỉ là số ID
            if ($major->languages && $major->languages->count() > 0) {
                $pivot = $major->languages->first()->pivot;
                $jsonFields = ['feature', 'target', 'address', 'overview', 'who', 'priority', 'learn', 'chance', 'school', 'value', 'feedback'];
                foreach ($jsonFields as $field) {
                    if (isset($pivot->$field) && is_string($pivot->$field)) {
                        $decoded = json_decode($pivot->$field, true);
                        if (json_last_error() === JSON_ERROR_NONE) {
                            $pivot->$field = $decoded;
                        }
                    }
                }
                // Xử lý event riêng - nếu là string JSON cũ thì convert sang số
                if (isset($pivot->event) && is_string($pivot->event)) {
                    $decoded = json_decode($pivot->event, true);
                    if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
                        // Tương thích ngược: nếu là array cũ có post_catalogue_id
                        if (isset($decoded['post_catalogue_id'])) {
                            $pivot->event = (int)$decoded['post_catalogue_id'];
                        } else {
                            $pivot->event = null;
                        }
                    } elseif (is_numeric($pivot->event)) {
                        $pivot->event = (int)$pivot->event;
                    } else {
                        $pivot->event = null;
                    }
                } elseif (isset($pivot->event) && is_array($pivot->event)) {
                    // Tương thích ngược: nếu là array cũ
                    if (isset($pivot->event['post_catalogue_id'])) {
                        $pivot->event = (int)$pivot->event['post_catalogue_id'];
                    } else {
                        $pivot->event = null;
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
        
        $admissionSubjectsMap = []; // key: slug, value: giá trị gốc
        $examLocationsMap = [];
        
        foreach ($majors as $major) {
            // Xử lý admission_subject (TEXT field) - explode các giá trị cách nhau bởi dấu phẩy
            if ($major->admission_subject && !empty(trim($major->admission_subject))) {
                // Explode bằng dấu phẩy và trim từng giá trị
                $values = array_map('trim', explode(',', $major->admission_subject));
                // Lọc bỏ các giá trị rỗng và thêm vào mảng
                foreach ($values as $value) {
                    if (!empty($value)) {
                        $slug = $this->slugValue($value);
                        if (!empty($slug) && !isset($admissionSubjectsMap[$slug])) {
                            // Lưu giá trị gốc đầu tiên tìm thấy
                            $admissionSubjectsMap[$slug] = $value;
                        }
                    }
                }
            }
            
            // Xử lý exam_location (TEXT field) - explode các giá trị cách nhau bởi dấu phẩy
            if ($major->exam_location && !empty(trim($major->exam_location))) {
                // Explode bằng dấu phẩy và trim từng giá trị
                $values = array_map('trim', explode(',', $major->exam_location));
                // Lọc bỏ các giá trị rỗng và thêm vào mảng
                foreach ($values as $value) {
                    if (!empty($value)) {
                        $slug = $this->slugValue($value);
                        if (!empty($slug) && !isset($examLocationsMap[$slug])) {
                            // Lưu giá trị gốc đầu tiên tìm thấy
                            $examLocationsMap[$slug] = $value;
                        }
                    }
                }
            }
        }
        
        // Chuyển từ map về array và sắp xếp
        $admissionSubjects = array_values($admissionSubjectsMap);
        $examLocations = array_values($examLocationsMap);
        
        // Sắp xếp theo thứ tự alphabet (không phân biệt hoa thường)
        usort($admissionSubjects, function($a, $b) {
            return strcasecmp($a, $b);
        });
        usort($examLocations, function($a, $b) {
            return strcasecmp($a, $b);
        });
        
        return [
            'admission_subject' => $admissionSubjects,
            'exam_location' => $examLocations,
        ];
    }
}
