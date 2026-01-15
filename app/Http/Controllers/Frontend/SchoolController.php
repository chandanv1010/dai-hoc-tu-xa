<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\FrontendController;
use Illuminate\Http\Request;
use App\Repositories\SchoolRepository;
use App\Repositories\SystemRepository;
use App\Repositories\MajorRepository;
use App\Repositories\PostRepository;
use App\Services\WidgetService;
use App\Models\Post;

class SchoolController extends FrontendController
{
    protected $language;
    protected $system;
    protected $schoolRepository;
    protected $systemRepository;
    protected $majorRepository;
    protected $postRepository;
    protected $widgetService;

    public function __construct(
        SchoolRepository $schoolRepository,
        SystemRepository $systemRepository,
        MajorRepository $majorRepository,
        PostRepository $postRepository,
        WidgetService $widgetService,
    ) {
        $this->schoolRepository = $schoolRepository;
        $this->systemRepository = $systemRepository;
        $this->majorRepository = $majorRepository;
        $this->postRepository = $postRepository;
        $this->widgetService = $widgetService;
        parent::__construct();
    }

    public function index($id, Request $request)
    {
        // Lấy school theo ID và language
        $school = $this->schoolRepository->getSchoolById($id, $this->language);

        if (!$school) {
            abort(404);
        }

        // Lấy pivot data
        $pivot = $school->languages && $school->languages->count() > 0 
            ? $school->languages->first()->pivot 
            : null;

        if (!$pivot) {
            abort(404);
        }

        // Decode album từ JSON hoặc lấy trực tiếp nếu đã là array
        $album = [];
        if (!empty($school->album)) {
            if (is_array($school->album)) {
                // Đã được cast thành array rồi
                $album = $school->album;
            } elseif (is_string($school->album)) {
                // Nếu vẫn là string, decode
                $decoded = json_decode($school->album, true);
                if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
                    $album = $decoded;
                }
            }
        }

        // dd($school);

        // Lấy SEO từ pivot
        $seo = [
            'meta_title' => $pivot->meta_title ?? $pivot->name ?? '',
            'meta_keyword' => $pivot->meta_keyword ?? '',
            'meta_description' => $pivot->meta_description ?? $pivot->description ?? '',
            'meta_image' => !empty($school->image) ? image($school->image) : ($this->system['homepage_logo'] ?? ''),
            'canonical' => write_url($pivot->canonical, true, true),
            'follow' => $school->follow ?? 1, // 1 = Follow, 2 = Nofollow, default = 1
        ];

        // Lấy widgets (giống như homepage)
        $widgets = $this->widgetService->getWidget([
            ['keyword' => 'distance-learning', 'object' => true],
        ], $this->language);

        // Lấy danh sách majors từ school
        $schoolMajors = [];
        $majorsData = [];
        
        // Decode majors từ JSON
        if ($pivot && isset($pivot->majors)) {
            $majorsJson = is_array($pivot->majors) ? $pivot->majors : json_decode($pivot->majors, true);
            if (is_array($majorsJson)) {
                $majorsData = $majorsJson;
            }
        }
        
        // Lấy thông tin chi tiết của từng major
        if (!empty($majorsData)) {
            foreach ($majorsData as $majorData) {
                if (isset($majorData['major_id'])) {
                    $major = $this->majorRepository->getMajorById($majorData['major_id'], $this->language);
                    if ($major && $major->languages && $major->languages->count() > 0) {
                        $majorPivot = $major->languages->first()->pivot;
                        $schoolMajors[] = [
                            'major' => $major,
                            'majorPivot' => $majorPivot,
                            'data' => $majorData, // Dữ liệu từ majors JSON (credits, duration, etc.)
                        ];
                    }
                }
            }
        }

        // Decode feedback từ JSON
        $feedback = [];
        if ($pivot && isset($pivot->feedback)) {
            $decoded = is_array($pivot->feedback) ? $pivot->feedback : json_decode($pivot->feedback, true);
            if (is_array($decoded)) {
                $feedback = $decoded;
            }
        }

        // Decode event từ JSON và lấy posts
        $eventPosts = collect();
        if ($pivot && isset($pivot->event)) {
            $eventData = is_array($pivot->event) ? $pivot->event : json_decode($pivot->event, true);
            if (is_array($eventData) && !empty($eventData['post_catalogue_id'])) {
                $postCatalogueId = $eventData['post_catalogue_id'];
                // Lấy tất cả posts trong post_catalogue được chọn
                $eventPosts = Post::select([
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
                    ->where('tb2.language_id', '=', $this->language)
                    ->where('tb3.post_catalogue_id', '=', $postCatalogueId)
                    ->where('posts.publish', '=', 2)
                    ->orderBy('posts.id', 'desc')
                    ->get();
            }
        }

        $config = $this->config();
        $system = $this->system;
        
        // Extract FAQ from entire School object for JSON-LD schema
        $faqs = extractFaqFromSchool($school, $pivot);
        $faqSchema = generateFaqSchema($faqs);
        
        $template = 'frontend.school.index';
        
        return view($template, compact(
            'config',
            'seo',
            'system',
            'school',
            'pivot',
            'album',
            'widgets',
            'schoolMajors',
            'feedback',
            'eventPosts',
            'faqSchema'
        ));
    }

    private function config()
    {
        return [
            'language' => $this->language,
            'js' => [
                'frontend/resources/library/js/swiper.min.js',
            ],
            'css' => [
                'frontend/resources/library/css/swiper.min.css',
            ],
        ];
    }
}

