<?php

namespace App\Repositories;

use App\Models\School;
use App\Repositories\BaseRepository;
use Illuminate\Support\Str;

class SchoolRepository extends BaseRepository
{
    protected $model;

    public function __construct(School $model)
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
                'schools.announce_title',
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
                'schools.form_script',
                'schools.form_tai_lo_trinh_hoc',
                'schools.form_tu_van_mien_phi',
                'schools.form_hoc_thu',
                'schools.graduation_system',
                'schools.training_majors',
                'schools.exam_location',
                'schools.created_at',
            ]
        )
        ->find($id);
        
        // Đảm bảo album được cast đúng nếu chưa được cast (fallback)
        if ($school && isset($school->album) && is_string($school->album) && !empty($school->album)) {
            $decoded = json_decode($school->album, true);
            if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
                $school->setAttribute('album', $decoded);
            }
        }
        
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
                'schools.short_name',
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
                'schools.graduation_system',
                'schools.training_majors',
                'schools.exam_location',
                'schools.created_at',
            ]
        )
        ->where('schools.publish', '=', 2)
        ->whereNull('schools.deleted_at');
        
        // Chuẩn bị filter values cho graduation_system và exam_location (sử dụng slug)
        $graduationSystemFilters = [];
        $examLocationFilters = [];
        
        if ($request->has('graduation_system')) {
            $graduationSystem = $request->input('graduation_system');
            $graduationSystemFilters = is_array($graduationSystem) ? $graduationSystem : [$graduationSystem];
            $graduationSystemFilters = array_filter(array_map('trim', $graduationSystemFilters));
        }
        
        if ($request->has('exam_location')) {
            $examLocation = $request->input('exam_location');
            $examLocationFilters = is_array($examLocation) ? $examLocation : [$examLocation];
            $examLocationFilters = array_filter(array_map('trim', $examLocationFilters));
        }
        
        // Nếu có filter graduation_system hoặc exam_location, filter bằng slug
        if (!empty($graduationSystemFilters) || !empty($examLocationFilters)) {
            $allSchools = $this->model->select('id', 'graduation_system', 'exam_location')
                ->where('publish', '=', 2)
                ->whereNull('deleted_at')
                ->get();
            
            $filteredIds = [];
            foreach ($allSchools as $school) {
                $matchGraduation = empty($graduationSystemFilters);
                $matchLocation = empty($examLocationFilters);
                
                // Kiểm tra graduation_system
                if (!empty($graduationSystemFilters)) {
                    foreach ($graduationSystemFilters as $system) {
                        if ($this->valueMatchesInString($system, $school->graduation_system)) {
                            $matchGraduation = true;
                            break;
                        }
                    }
                }
                
                // Kiểm tra exam_location
                if (!empty($examLocationFilters)) {
                    foreach ($examLocationFilters as $location) {
                        if ($this->valueMatchesInString($location, $school->exam_location)) {
                            $matchLocation = true;
                            break;
                        }
                    }
                }
                
                // Chỉ thêm vào nếu match cả 2 điều kiện (nếu có)
                if ($matchGraduation && $matchLocation) {
                    $filteredIds[] = $school->id;
                }
            }
            
            if (!empty($filteredIds)) {
                $query->whereIn('schools.id', $filteredIds);
            } else {
                $query->whereRaw('1 = 0'); // Không có kết quả
            }
        }
        
        // Filter theo Ngành Đào Tạo (major_id)
        if ($request->has('major_id')) {
            $majorIds = $request->input('major_id');
            if (is_array($majorIds) && count($majorIds) > 0) {
                $majorIds = array_map('intval', $majorIds);
                $query->whereHas('majors', function($q) use ($majorIds) {
                    $q->whereIn('majors.id', $majorIds);
                });
            } elseif (is_numeric($majorIds)) {
                $query->whereHas('majors', function($q) use ($majorIds) {
                    $q->where('majors.id', '=', (int)$majorIds);
                });
            }
        }
        
        $query->orderBy('schools.id', 'asc');

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
    
    /**
     * Lấy tất cả các giá trị filter options từ database
     */
    public function getFilterOptions()
    {
        $schools = $this->model->select('graduation_system', 'exam_location')
            ->where('publish', '=', 2)
            ->whereNull('deleted_at')
            ->get();
        
        $graduationSystemsMap = []; // key: slug, value: giá trị gốc (lấy giá trị đầu tiên hoặc giá trị có format đẹp nhất)
        $examLocationsMap = [];
        
        foreach ($schools as $school) {
            // Xử lý graduation_system (TEXT field) - explode các giá trị cách nhau bởi dấu phẩy
            if ($school->graduation_system && !empty(trim($school->graduation_system))) {
                // Explode bằng dấu phẩy và trim từng giá trị
                $values = array_map('trim', explode(',', $school->graduation_system));
                // Lọc bỏ các giá trị rỗng và thêm vào mảng
                foreach ($values as $value) {
                    if (!empty($value)) {
                        $slug = $this->slugValue($value);
                        if (!empty($slug) && !isset($graduationSystemsMap[$slug])) {
                            // Lưu giá trị gốc đầu tiên tìm thấy (hoặc có thể ưu tiên giá trị có format đẹp hơn)
                            $graduationSystemsMap[$slug] = $value;
                        }
                    }
                }
            }
            
            // Xử lý exam_location (TEXT field) - explode các giá trị cách nhau bởi dấu phẩy
            if ($school->exam_location && !empty(trim($school->exam_location))) {
                // Explode bằng dấu phẩy và trim từng giá trị
                $values = array_map('trim', explode(',', $school->exam_location));
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
        $graduationSystems = array_values($graduationSystemsMap);
        $examLocations = array_values($examLocationsMap);
        
        // Sắp xếp theo thứ tự alphabet (không phân biệt hoa thường)
        usort($graduationSystems, function($a, $b) {
            return strcasecmp($a, $b);
        });
        usort($examLocations, function($a, $b) {
            return strcasecmp($a, $b);
        });
        
        return [
            'graduation_system' => $graduationSystems,
            'exam_location' => $examLocations,
        ];
    }
}


