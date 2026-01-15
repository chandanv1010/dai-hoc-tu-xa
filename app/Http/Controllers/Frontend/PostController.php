<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\FrontendController;
use Illuminate\Http\Request;
use App\Repositories\PostCatalogueRepository;
use App\Services\PostCatalogueService;
use App\Services\PostService;
use App\Repositories\PostRepository;
use App\Services\WidgetService;
use Jenssegers\Agent\Facades\Agent;
use App\Models\Post;
use App\View\Components\TableOfContents;

class postController extends FrontendController
{
    protected $language;
    protected $system;
    protected $postCatalogueRepository;
    protected $postCatalogueService;
    protected $postService;
    protected $postRepository;
    protected $widgetService;

    public function __construct(
        PostCatalogueRepository $postCatalogueRepository,
        PostCatalogueService $postCatalogueService,
        PostService $postService,
        PostRepository $postRepository,
        WidgetService $widgetService,
    ){
        $this->postCatalogueRepository = $postCatalogueRepository;
        $this->postCatalogueService = $postCatalogueService;
        $this->postService = $postService;
        $this->postRepository = $postRepository;
        $this->widgetService = $widgetService;
        parent::__construct(); 
    }


    public function index($id, $request){
        $language = $this->language;
        $post = $this->postRepository->getPostById($id, $this->language, config('apps.general.defaultPublish'));
        $viewed = $post->viewed;
        $updateViewed = Post::where('id', $id)->update(['viewed' => $viewed + 1]); 
        if(is_null($post)){
            abort(404);
        }
        $postCatalogue = $this->postCatalogueRepository->getPostCatalogueById($post->post_catalogue_id, $this->language);
        if($postCatalogue->id == 22 || $postCatalogue->id == 24 || $postCatalogue->id === 44){
            $postCatalogue->children = $this->postCatalogueRepository->findByCondition(
                [
                    ['publish' , '=', 2],
                    ['parent_id', '=', 21]
                ],
                true,
                [],
                ['order', 'desc']
            );
        }

        // dd(123);

        $breadcrumb = $this->postCatalogueRepository->breadcrumb($postCatalogue, $this->language);

        // Lấy bài viết mới trong chuyên mục (limit 19)
        $asidePostsPaginate = $this->postService->paginate(
            $request, 
            $this->language, 
            $postCatalogue, 
            1,
            ['path' => $postCatalogue->canonical],
        );
        $asidePosts = $asidePostsPaginate->items();
        $asidePosts = collect($asidePosts)->take(19);

        // Lấy bài viết liên quan (cùng chuyên mục, khác bài viết hiện tại)
        $relatedPosts = $this->postRepository->getRelated(6, $postCatalogue->id, $post->id, $this->language);


        $widgets = $this->widgetService->getWidget([
            ['keyword' => 'product-catalogue', 'object' => true],
            
        ], $this->language);

        /* ------------------- */
        
        $config = $this->config();
        $system = $this->system;
        $seo = seo($post);
        


        $template = 'frontend.post.post.index';

        $content = $post->languages->first()->pivot->content;
        // dd($content);
        // dd($content, $cont);
        $items = TableOfContents::extract($content);
        $contentWithToc = null;
        $contentWithToc = TableOfContents::injectIds($content, $items);
        // dd($contentWithToc);
        
        // Extract FAQ from content for JSON-LD schema
        $faqs = extractFaqFromContent($content);
        $faqSchema = generateFaqSchema($faqs);

        return view($template, compact(
            'config',
            'seo',
            'system',
            'breadcrumb',
            'postCatalogue',
            'post',
            'asidePosts',
            'relatedPosts',
            'widgets',
            'contentWithToc',
            'faqSchema'
        ));
    }

    private function config(){
        return [
            'language' => $this->language,
            'js' => [
                'frontend/core/library/cart.js',
                'frontend/core/library/product.js',
                'frontend/core/library/review.js',
                'https://prohousevn.com/scripts/fancybox-3/dist/jquery.fancybox.min.js'
            ],
            'css' => [
                'frontend/core/css/product.css',
                'https://prohousevn.com/scripts/fancybox-3/dist/jquery.fancybox.min.css'
            ]
        ];
    }

}
