<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\FrontendController;
use Illuminate\Http\Request;
use App\Repositories\MajorRepository;
use App\Repositories\PostRepository;
use App\Models\Major;
use App\Models\Post;

class MajorController extends FrontendController
{
    protected $language;
    protected $system;
    protected $majorRepository;
    protected $postRepository;

    public function __construct(
        MajorRepository $majorRepository,
        PostRepository $postRepository
    ) {
        $this->majorRepository = $majorRepository;
        $this->postRepository = $postRepository;
        parent::__construct();
    }

    public function index($id, $request)
    {
        $major = $this->majorRepository->getMajorById($id, $this->language);
        
        if (is_null($major)) {
            abort(404);
        }

        // Load schools relationship với languages, image, và decode majors JSON
        $major->load(['schools' => function($query) {
            $query->select('schools.id', 'schools.image', 'schools.short_name', 'schools.publish')
                  ->where('schools.publish', 2)
                  ->whereNull('schools.deleted_at')
                  ->with(['languages' => function($q) {
                      $q->where('languages.id', $this->language);
                  }]);
        }]);
        
        // Decode majors JSON từ school_language pivot
        foreach ($major->schools as $school) {
            if ($school->languages && $school->languages->count() > 0) {
                $pivot = $school->languages->first()->pivot;
                if (isset($pivot->majors)) {
                    if (is_string($pivot->majors)) {
                        $decoded = json_decode($pivot->majors, true);
                        if (json_last_error() === JSON_ERROR_NONE) {
                            $pivot->majors = $decoded;
                        }
                    }
                }
            }
        }

        // Lấy danh sách tất cả majors để hiển thị trong dropdown form đăng ký
        $allMajors = $this->majorRepository->getAllByLanguage($this->language);

        // Lấy pivot để decode feedback và event
        $pivot = $major->languages && $major->languages->count() > 0 
            ? $major->languages->first()->pivot 
            : null;

        // Decode feedback từ JSON
        $feedback = [];
        if ($pivot && isset($pivot->feedback)) {
            $decoded = is_array($pivot->feedback) ? $pivot->feedback : json_decode($pivot->feedback, true);
            if (is_array($decoded)) {
                $feedback = $decoded;
            }
        }

        // Lấy event (post_catalogue_id) và query posts
        $eventPosts = collect();
        if ($pivot && isset($pivot->event)) {
            $eventValue = $pivot->event;
            $postCatalogueId = null;
            
            // Event giờ lưu trực tiếp là số ID, không phải JSON
            if (is_numeric($eventValue)) {
                $postCatalogueId = (int)$eventValue;
            } elseif (is_string($eventValue)) {
                // Xử lý trường hợp cũ (JSON string) - tương thích ngược
                $decoded = json_decode($eventValue, true);
                if (is_array($decoded) && isset($decoded['post_catalogue_id'])) {
                    $postCatalogueId = (int)$decoded['post_catalogue_id'];
                } elseif (is_numeric($eventValue)) {
                    $postCatalogueId = (int)$eventValue;
                }
            } elseif (is_array($eventValue) && isset($eventValue['post_catalogue_id'])) {
                // Xử lý trường hợp cũ (JSON array) - tương thích ngược
                $postCatalogueId = (int)$eventValue['post_catalogue_id'];
            }
            
            // Lấy tất cả posts trong post_catalogue được chọn
            if ($postCatalogueId) {
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
        $seo = seo($major);
        
        // Extract FAQ from entire Major object for JSON-LD schema
        $faqs = extractFaqFromMajor($major, $pivot);
        $faqSchema = generateFaqSchema($faqs);

        $template = 'frontend.major.index';

        return view($template, compact(
            'config',
            'seo',
            'system',
            'major',
            'allMajors',
            'pivot',
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
