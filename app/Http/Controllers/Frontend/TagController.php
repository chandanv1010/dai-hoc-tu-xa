<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\FrontendController;
use Illuminate\Http\Request;
use App\Repositories\TagRepository;
use App\Repositories\PostRepository;
use App\Repositories\SchoolRepository;
use App\Services\PostService;

class TagController extends FrontendController
{
    protected $tagRepository;
    protected $postRepository;
    protected $postService;
    protected $schoolRepository;

    public function __construct(
        TagRepository $tagRepository,
        PostRepository $postRepository,
        PostService $postService,
        SchoolRepository $schoolRepository
    ){
        $this->tagRepository = $tagRepository;
        $this->postRepository = $postRepository;
        $this->postService = $postService;
        $this->schoolRepository = $schoolRepository;
        parent::__construct();
    }

    public function index($slug, Request $request){
        $tag = $this->tagRepository->findBySlug($slug);
        
        if (!$tag) {
            abort(404);
        }

        // Lấy posts có tag này
        $posts = $this->postRepository->getPostsByTag($tag->id, $this->language, 15);
        $posts->withQueryString();
        $posts->setPath(route('post.tag', ['slug' => $slug]));

        // Lấy danh sách schools cho sidebar
        $schools = $this->schoolRepository->getAllSchools($this->language, 0);
        
        $config = $this->config();
        $system = $this->system;
        
        $canonicalUrl = route('post.tag', ['slug' => $slug]);
        
        $seo = [
            'meta_title' => 'Bài viết với tag: ' . $tag->name,
            'meta_keyword' => $tag->name,
            'meta_description' => 'Danh sách bài viết được gắn tag: ' . $tag->name,
            'meta_image' => $system['homepage_logo'] ?? '',
            'canonical' => $canonicalUrl,
            'follow' => 1, // Default follow
        ];

        $template = 'frontend.tag.index';

        return view($template, compact(
            'config',
            'seo',
            'system',
            'tag',
            'posts',
            'schools'
        ));
    }

    private function config(){
        return [
            'language' => $this->language,
            'js' => [],
            'css' => []
        ];
    }
}
