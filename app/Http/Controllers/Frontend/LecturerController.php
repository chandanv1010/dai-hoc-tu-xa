<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\FrontendController;
use App\Repositories\SystemRepository;
use App\Repositories\LecturerRepository;
use App\Repositories\ProductRepository;
use Illuminate\Http\Request;
use App\Services\ProductService;
use App\Services\ProductCatalogueService;

class LecturerController extends FrontendController
{
    protected $language;
    protected $systemRepository;
    protected $lecturerRepository;
    protected $productRepository;
    protected $productService;
    protected $productCatalogueService;

    public function __construct(
        SystemRepository $systemRepository,
        LecturerRepository $lecturerRepository,
        ProductRepository $productRepository,
        ProductService $productService,
        ProductCatalogueService $productCatalogueService,
    ) {
        $this->systemRepository = $systemRepository;
        $this->lecturerRepository = $lecturerRepository;
        $this->productRepository = $productRepository;
        $this->productService = $productService;
        $this->productCatalogueService = $productCatalogueService;

        parent::__construct();
    }

    public function index(string $canonical = '', Request $request)
    {
        $lecturer = $this->lecturerRepository->findByCondition([
            ['canonical','=', $canonical]
        ]);

        // Kiểm tra nếu không tìm thấy lecturer
        if (is_null($lecturer)) {
            abort(404);
        }

        $allLecturers = $this->lecturerRepository->all();
        $lecturers = $allLecturers;

        $products = $this->productRepository->findByCondition([
            ['lecturer_id','=', $lecturer->id]
        ], true);

        $descendantTrees = null;
        $descendantTrees = $this->productCatalogueService->getChildren();

        $config = $this->config();

        $system = $this->system;

        $seo = [
            'meta_title' => $this->system['seo_meta_title'],
            'meta_keyword' => $this->system['seo_meta_keyword'],
            'meta_description' => $this->system['seo_meta_description'],
            'meta_image' => $this->system['seo_meta_images'],
            'canonical' => config('app.url'),
        ];

        $language = $this->language;

        $template = 'frontend.lecturer.index';

        return view($template, compact(
            'config',
            'seo',
            'system',
            'language',
            'lecturer',
            'products',
            'allLecturers',
            'descendantTrees',
            'lecturers'
        ));
    }

    public function allLecturer(){

        $allLecturers = $this->lecturerRepository->all()->toArray();

        if(!empty($allLecturers)){
            foreach($allLecturers as $k => $lecturer){
                $allLecturers[$k]['courses'] = $this->productRepository->findByCondition([
                    ['lecturer_id','=', $lecturer['id']]
                ], true)->count();
            }
        }

        $config = $this->config();

        $system = $this->system;

        $seo = [
            'meta_title' => $this->system['seo_meta_title'],
            'meta_keyword' => $this->system['seo_meta_keyword'],
            'meta_description' => $this->system['seo_meta_description'],
            'meta_image' => $this->system['seo_meta_images'],
            'canonical' => config('app.url'),
        ];

        $language = $this->language;

        $template = 'frontend.lecturer.list';

        return view($template, compact(
            'config',
            'seo',
            'system',
            'language',
            'allLecturers'
        ));
    }

    private function config()
    {
        return [
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
    }




}