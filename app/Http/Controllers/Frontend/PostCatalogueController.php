<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\FrontendController;
use Illuminate\Http\Request;
use App\Repositories\PostCatalogueRepository;
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

    public function __construct(
        PostCatalogueRepository $postCatalogueRepository,
        PostCatalogueService $postCatalogueService,
        PostService $postService,
        WidgetService $widgetService,
        SlideService $slideService,
    ) {
        $this->postCatalogueRepository = $postCatalogueRepository;
        $this->postCatalogueService = $postCatalogueService;
        $this->postService = $postService;
        $this->widgetService = $widgetService;
        $this->slideService = $slideService;
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

        $config = $this->config();
        $system = $this->system;
        $seo = seo($postCatalogue, $page);
        $introduce = convert_array(Introduce::where('language_id', $this->language)->get(), 'keyword', 'content');
        $schema = $this->schema($postCatalogue, $posts, $breadcrumb);
        return view($template, compact(
            'config',
            'seo',
            'system',
            'breadcrumb',
            'postCatalogue',
            'posts',
            'widgets',
            'schema',
            'slides',
            'introduce'
        ));
    }

    private function schema($postCatalogue, $posts, $breadcrumb)
    {

        $postCatalogueLanguage = $postCatalogue->languages->first();
        if (!$postCatalogueLanguage || !$postCatalogueLanguage->pivot) {
            return '';
        }

        $cat_name = $postCatalogueLanguage->pivot->name;
        $cat_canonical = write_url($postCatalogueLanguage->pivot->canonical);
        $cat_description = strip_tags($postCatalogueLanguage->pivot->description);

        $itemListElements = '';

        $position = 1;

        $authorName = $this->system['homepage_company'] ?? 'An Hưng';
        foreach ($posts as $post) {
            $postLanguage = $post->languages->first();
            if (!$postLanguage || !$postLanguage->pivot) {
                continue;
            }
            $name = $postLanguage->pivot->name;
            $canonical = write_url($postLanguage->pivot->canonical);
            $itemListElements .= "
                {
                    \"@type\": \"BlogPosting\",
                    \"headline\": \"" . addslashes($name) . "\",
                    \"url\": \"" . $canonical . "\",
                    \"datePublished\": \"" . convertDateTime($post->created_at, 'Y-m-d') . "\",
                    \"author\": {
                        \"@type\": \"Person\",
                        \"name\": \"" . addslashes($authorName) . "\"
                    }
                },";
            $position++;
        }

        $itemListElements = rtrim($itemListElements, ',');

        $itemBreadcrumbElements = '';

        $positionBreadcrumb = 2;

        foreach ($breadcrumb as $key => $item) {
            $language = $item->languages->first();
            if (!$language || !$language->pivot) {
                continue;
            }
            $name = $language->pivot->name;
            $canonical = write_url($language->pivot->canonical);
            $itemBreadcrumbElements .= "
                {
                    \"@type\": \"ListItem\",
                    \"position\": $positionBreadcrumb,
                    \"name\": \"" . $name . "\",
                    \"item\": \"" . $canonical . "\",
                },";
            $positionBreadcrumb++;
        }

        $itemBreadcrumbElements = rtrim($itemBreadcrumbElements, ',');

        $authorName = $this->system['homepage_company'] ?? 'An Hưng';
        $logoUrl = isset($this->system['homepage_logo']) && $this->system['homepage_logo'] ? asset($this->system['homepage_logo']) : '';
        
        $schema = "<script type='application/ld+json'>
        [
            {
                \"@context\": \"https://schema.org\",
                \"@type\": \"BreadcrumbList\",
                \"itemListElement\": [
                    {
                        \"@type\": \"ListItem\",
                        \"position\": 1,
                        \"name\": \"Trang chủ\",
                        \"item\": \"" . config('app.url') . "\"
                    }" . ($itemBreadcrumbElements ? ',' . $itemBreadcrumbElements : '') . "
                ]
            },
            {
                \"@context\": \"https://schema.org\",
                \"@type\": \"Blog\",
                \"name\": \"" . addslashes($cat_name) . "\",
                \"description\": \"" . addslashes($cat_description) . "\",
                \"url\": \"" . $cat_canonical . "\",
                \"publisher\": {
                    \"@type\": \"Organization\",
                    \"name\": \"" . addslashes($authorName) . "\"" . ($logoUrl ? ",
                    \"logo\": {
                        \"@type\": \"ImageObject\",
                        \"url\": \"" . $logoUrl . "\"
                    }" : '') . "
                }" . ($itemListElements ? ",
                \"blogPost\": [
                    $itemListElements
                ]" : '') . "
            }
        ]
        </script>";
        return $schema;
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