<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\FrontendController;
use Illuminate\Http\Request;
use App\Repositories\PostCatalogueRepository;
use App\Repositories\SchoolRepository;
use App\Services\PostCatalogueService;
use App\Services\PostService;
use App\Services\WidgetService;
use App\Services\SlideService;
use App\Models\System;
use App\Enums\SlideEnum;
use Jenssegers\Agent\Facades\Agent;
use App\Models\Introduce;

class PostCatalogueController extends FrontendController
{
    protected $language;
    protected $system;
    protected $postCatalogueRepository;
    protected $postCatalogueService;
    protected $postService;
    protected $widgetService;
    protected $slideService;
    protected $schoolRepository;

    public function __construct(
        PostCatalogueRepository $postCatalogueRepository,
        PostCatalogueService $postCatalogueService,
        PostService $postService,
        WidgetService $widgetService,
        SlideService $slideService,
        SchoolRepository $schoolRepository,
    ) {
        $this->postCatalogueRepository = $postCatalogueRepository;
        $this->postCatalogueService = $postCatalogueService;
        $this->postService = $postService;
        $this->widgetService = $widgetService;
        $this->slideService = $slideService;
        $this->schoolRepository = $schoolRepository;
        parent::__construct();
    }


    public function index($id, $request, $page = 1)
    {
        $postCatalogue = $this->postCatalogueRepository->getPostCatalogueById($id, $this->language);
        
        if (!$postCatalogue) {
            abort(404);
        }
        $postCatalogue->children = $this->postCatalogueRepository->findByCondition(
            [
                ['publish', '=', 2],
                ['parent_id', '=', $postCatalogue->id]
            ],
            true,
            [],
            ['order', 'desc']
        );
        

        $breadcrumb = $this->postCatalogueRepository->breadcrumb($postCatalogue, $this->language);
        
        // Filter breadcrumb để chỉ giữ lại các item có language data
        $breadcrumb = $breadcrumb->filter(function($item) {
            $language = $item->languages->first();
            return $language && $language->pivot && !empty($language->pivot->name);
        });
        $posts = $this->postService->paginate(
            $request,
            $this->language,
            $postCatalogue,
            $page,
            ['path' => $postCatalogue->canonical],
        );

        $widgets = $this->widgetService->getWidget([
            ['keyword' => 'students', 'object' => true],
            ['keyword' => 'product-catalogue', 'object' => true],
            
        ], $this->language);

        $slides = $this->slideService->getSlide(
            [SlideEnum::MAIN],
            $this->language
        );

        if($postCatalogue->canonical === 'gioi-thieu'){
            $template = 'frontend.post.catalogue.intro';
        }else{
            $template = 'frontend.post.catalogue.index';
        }

        // Lấy danh sách schools cho sidebar
        $schools = $this->schoolRepository->getAllSchools($this->language, 0);
        
        $config = $this->config();
        $system = $this->system;
        $seo = seo($postCatalogue, $page);
        $introduce = convert_array(Introduce::where('language_id', $this->language)->get(), 'keyword', 'content');
        return view($template, compact(
            'config',
            'seo',
            'system',
            'breadcrumb',
            'postCatalogue',
            'posts',
            'widgets',
            'slides',
            'introduce',
            'schools'
        ));
    }


    private function config()
    {
        $config = [
            'language' => $this->language,
            'css' => [
                'frontend/resources/plugins/OwlCarousel2-2.3.4/dist/assets/owl.carousel.min.css',
                'frontend/resources/plugins/OwlCarousel2-2.3.4/dist/assets/owl.theme.default.min.css',
                'frontend/resources/css/custom.css'
            ],
            'js' => [
                'frontend/resources/plugins/OwlCarousel2-2.3.4/dist/owl.carousel.min.js',
                'frontend/resources/library/js/carousel.js',
                'https://getuikit.com/v2/src/js/components/sticky.js'
            ]
        ];

        // Thêm Swiper cho trang intro (feedback slider)
        if(request()->route()->parameter('id')) {
            $postCatalogue = $this->postCatalogueRepository->getPostCatalogueById(request()->route()->parameter('id'), $this->language);
            if($postCatalogue && $postCatalogue->canonical === 'gioi-thieu') {
                $config['js'][] = 'frontend/resources/library/js/swiper.min.js';
                $config['css'][] = 'frontend/resources/library/css/swiper.min.css';
            }
        }

        return $config;
    }

}