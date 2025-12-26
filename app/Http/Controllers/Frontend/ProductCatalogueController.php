<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\FrontendController;
use Illuminate\Http\Request;
use App\Repositories\ProductCatalogueRepository;
use App\Repositories\PostRepository;
use App\Repositories\SchoolRepository;
use App\Repositories\MajorRepository;
use App\Services\ProductCatalogueService;
use App\Services\ProductService;
use App\Services\WidgetService;
use App\Repositories\ProductRepository;
use App\Repositories\LecturerRepository;
use Gloudemans\Shoppingcart\Facades\Cart;
use Jenssegers\Agent\Facades\Agent;

class ProductCatalogueController extends FrontendController
{
    protected $language;
    protected $system;
    protected $productCatalogueRepository;
    protected $productCatalogueService;
    protected $productService;
    protected $widgetService;
    protected $productRepository;
    protected $lecturerRepository;
    protected $postRepository;
    protected $schoolRepository;
    protected $majorRepository;

    public function __construct(
        ProductCatalogueRepository $productCatalogueRepository,
        ProductCatalogueService $productCatalogueService,
        ProductService $productService,
        ProductRepository $productRepository,
        LecturerRepository $lecturerRepository,
        PostRepository $postRepository,
        SchoolRepository $schoolRepository,
        MajorRepository $majorRepository,
        WidgetService $widgetService,
    ) {
        $this->productCatalogueRepository = $productCatalogueRepository;
        $this->productCatalogueService = $productCatalogueService;
        $this->productService = $productService;
        $this->widgetService = $widgetService;
        $this->productRepository = $productRepository;
        $this->lecturerRepository = $lecturerRepository;
        $this->postRepository = $postRepository;
        $this->schoolRepository = $schoolRepository;
        $this->majorRepository = $majorRepository;
        parent::__construct();
    }


    public function index($id, $request, $page = 1)
    {

        $productCatalogue = $this->productCatalogueRepository->getProductCatalogueById($id, $this->language);

        $parent = null;

        $descendantTrees = null;

        $descendantTrees = $this->productCatalogueService->getChildren();

        $filters = $this->filter($productCatalogue);

        $breadcrumb = $this->productCatalogueRepository->breadcrumb($productCatalogue, $this->language);

        $products = $this->productService->paginate(
            $request,
            $this->language,
            $productCatalogue,
            $page,
            ['path' => $productCatalogue->canonical],
        );

        $products = $this->combineProductValues($products);

        // Lấy danh sách lecturers đã publish (publish = 2)
        $lecturers = $this->lecturerRepository->findByCondition([
            ['publish', '=', 2]
        ], true);

        $config = $this->config();

        $widgets = $this->widgetService->getWidget([
            ['keyword' => 'news', 'object' => true],
            ['keyword' => 'news-outstanding', 'object' => true],
            ['keyword' => 'projects-feature', 'object' => true],
            ['keyword' => 'design_construction_interior', 'object' => true],
            ['keyword' => 'showroom-system', 'object' => true],
        ], $this->language);

        $config = $this->config();

        $system = $this->system;

        $seo = seo($productCatalogue, $page);

        $template = 'frontend.product.catalogue.index';

        return view($template, compact(
            'descendantTrees',
            'config',
            'seo',
            'system',
            'breadcrumb',
            'productCatalogue',
            'products',
            'filters',
            'widgets',
            'lecturers'
        ));
    }

    private function combineProductValues($products)
    {
        $productId = $products->pluck('id')->toArray();
        if (count($productId) && !is_null($productId)) {
            $products = $this->productService->combineProductAndPromotion($productId, $products);
            $products = $this->productService->combineProductRelation($products);
        }

        return $products;
    }

    private function filter($productCatalogue)
    {
        $filters = null;
        $children = $this->productCatalogueRepository->getChildren($productCatalogue);
        $groupedAttributes = [];
        foreach ($children as $child) {
            if (isset($child->attribute) && !is_null($child->attribute) && count($child->attribute)) {
                foreach ($child->attribute as $key => $value) {
                    if (!isset($groupedAttributes[$key])) {
                        $groupedAttributes[$key] = [];
                    }
                    $groupedAttributes[$key][] = $value;
                }
            }
        }
        foreach ($groupedAttributes as $key => $value) {
            $groupedAttributes[$key] = array_merge(...$value);
        }

        if (isset($groupedAttributes) && !is_null($groupedAttributes) && count($groupedAttributes)) {
            $filters = $this->productCatalogueService->getFilterList($groupedAttributes, $this->language);
        }
        return $filters;
    }


    public function search(Request $request)
    {
        $keyword = $request->input('keyword', '');
        
        // Tìm kiếm trong posts (tin tuc), products (khoa hoc), schools (truong) và majors (nganh hoc)
        $posts = $this->postRepository->search($keyword, $this->language, 10);
        $products = $this->productRepository->search($keyword, $this->language);
        $schools = $this->schoolRepository->search($keyword, $this->language, 10);
        $majors = $this->majorRepository->search($keyword, $this->language, 10);

        // Combine product values for products
        if ($products && $products->count() > 0) {
            $products = $this->combineProductValues($products);
        }

        $config = $this->config();

        $system = $this->system;

        $widgets = $this->widgetService->getWidget([
            ['keyword' => 'news-outstanding', 'object' => true],
        ], $this->language);

        $seo = [
            'meta_title' => 'Tìm kiếm cho từ khóa: ' . $keyword,
            'meta_keyword' => '',
            'meta_description' => '',
            'meta_image' => '',
            'canonical' => write_url('tim-kiem')
        ];

        $template = 'frontend.product.catalogue.search';

        return view($template, compact(
            'config',
            'seo',
            'system',
            'posts',
            'products',
            'schools',
            'majors',
            'keyword',
            'widgets'
        ));
    }

    public function wishlist(Request $request)
    {
        $id = Cart::instance('wishlist')->content()->pluck('id')->toArray();
        $products = $this->productRepository->findByIds($id, $this->language);
        $productId = $products->pluck('id')->toArray();
        if (count($productId) && !is_null($productId)) {
            $products = $this->productService->combineProductAndPromotion($productId, $products);
        }

        $config = $this->config();
        $system = $this->system;
        $seo = [
            'meta_title' => 'Danh sách yêu thích',
            'meta_keyword' => '',
            'meta_description' => '',
            'meta_image' => '',
            'canonical' => write_url('tim-kiem')
        ];
        return view('frontend.product.catalogue.search', compact(
            'config',
            'seo',
            'system',
            'products',
        ));
    }


    private function config()
    {
        return [
            'language' => $this->language,
            'externalJs' => [
                '//code.jquery.com/ui/1.11.4/jquery-ui.js'
            ],
            'css' => [
                'frontend/resources/plugins/OwlCarousel2-2.3.4/dist/assets/owl.carousel.min.css',
                'frontend/resources/plugins/OwlCarousel2-2.3.4/dist/assets/owl.theme.default.min.css',
                'frontend/resources/css/custom.css',
            ],
            'js' => [
                'frontend/core/library/filter.js?'.time(),
                'frontend/resources/plugins/OwlCarousel2-2.3.4/dist/owl.carousel.min.js',
                'frontend/resources/library/js/carousel.js',
            ],

        ];
    }

}