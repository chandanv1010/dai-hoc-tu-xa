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

        // Decode event từ JSON và lấy posts
        $eventPosts = collect();
        if ($pivot && isset($pivot->event)) {
            $eventIds = is_array($pivot->event) ? $pivot->event : json_decode($pivot->event, true);
            if (is_array($eventIds) && !empty($eventIds)) {
                // Lấy posts theo IDs
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
                    ->where('tb2.language_id', '=', $this->language)
                    ->whereIn('posts.id', $eventIds)
                    ->where('posts.publish', '=', 2)
                    ->orderByRaw('FIELD(posts.id, ' . implode(',', $eventIds) . ')')
                    ->get();
            }
        }

        $config = $this->config();
        $system = $this->system;
        $seo = seo($major);

        $template = 'frontend.major.index';

        return view($template, compact(
            'config',
            'seo',
            'system',
            'major',
            'allMajors',
            'pivot',
            'feedback',
            'eventPosts'
        ));
    }

    private function config()
    {
        return [
            'language' => $this->language,
            'js' => [],
            'css' => []
        ];
    }
}
