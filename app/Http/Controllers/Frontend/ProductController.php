<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\FrontendController;
use Illuminate\Http\Request;
use App\Repositories\ProductCatalogueRepository;
use App\Services\ProductCatalogueService;
use App\Services\ProductService;
use App\Services\VoucherService;
use App\Services\PromotionService;
use App\Repositories\ProductRepository;
use App\Repositories\CustomerRepository;
use App\Repositories\ReviewRepository;
use App\Repositories\VoucherRepository;
use App\Repositories\OrderRepository;
use App\Services\WidgetService;
use Illuminate\Support\Facades\Auth;
use Gloudemans\Shoppingcart\Facades\Cart;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class ProductController extends FrontendController
{
    protected $language;
    protected $system;
    protected $productCatalogueRepository;
    protected $productCatalogueService;
    protected $productService;
    protected $voucherService;
    protected $promotionService;
    protected $productRepository;
    protected $reviewRepository;
    protected $voucherRepository;
    protected $widgetService;
    protected $customerRepository;
    protected $orderRepository;

    public function __construct(
        ProductCatalogueRepository $productCatalogueRepository,
        ProductCatalogueService $productCatalogueService,
        ProductService $productService,
        ProductRepository $productRepository,
        ReviewRepository $reviewRepository,
        VoucherRepository $voucherRepository,
        WidgetService $widgetService,
        VoucherService $voucherService,
        PromotionService $promotionService,
        CustomerRepository $customerRepository,
        OrderRepository $orderRepository,
    ) {
        $this->productCatalogueRepository = $productCatalogueRepository;
        $this->productCatalogueService = $productCatalogueService;
        $this->productService = $productService;
        $this->productRepository = $productRepository;
        $this->reviewRepository = $reviewRepository;
        $this->voucherRepository = $voucherRepository;
        $this->widgetService = $widgetService;
        $this->voucherService = $voucherService;
        $this->promotionService = $promotionService;
        $this->customerRepository = $customerRepository;
        $this->orderRepository = $orderRepository;
        parent::__construct();
    }

    private function promotionLeft($product = null){
        if(empty($product->promotions)){
            return;
        }
        $end = Carbon::parse($product->promotions->endDate);
        $now = Carbon::now();
        $dayLefts = $now->diffInDays($end, false);
        return $dayLefts;
    }

    private function calculateStudent($product){
        $order = DB::table('order_product')->where('product_id', $product->id)->get();
        if ($order->isEmpty()) {
            return;
        }
        $orderIds = $order->pluck('order_id')->toArray();
        $orders = $this->orderRepository->findByCondition(
            condition: [],
            flag: true, 
            relation: [],
            orderBy: ['id', 'desc'],
            param: [
                'whereInField' => 'id',       
                'whereIn' => $orderIds         
            ],
            withCount: []
        );
        $students = $orders->count('customer_id');
        return $students;
    }

    private function getInfoLecturer($lecturer_id){
        $totatStudents = 0;
        $courses = $this->productRepository->findByCondition([
            [
                'lecturer_id', '=', $lecturer_id
            ]
        ], true);
        $totatStudents = $courses->sum('student');
        $reviews = $this->productService->calculateReviewForLecturer($courses);
        $totalCourses = $courses->count();
        $lecturer = [
            'reviews' => $reviews,
            'total_students' => $totatStudents,
            'total_courses' => $totalCourses
        ];
        return $lecturer;
    }

    public function index($id, $request)
    {
        $language = $this->language;
        $product = $this->productRepository->getProductById($id, $this->language, config('apps.general.defaultPublish'));
        if (is_null($product)) {
            abort(404);
        }
        $product = $this->productService->combineProductAndPromotion([$id], $product, true);
        $students = $this->calculateStudent($product);
        
        // Load Lecturer model từ product relationship
        $lecturerModel = null;
        if (!empty($product->lecturer_id)) {
            $lecturerModel = $product->lecturers;
        }
        
        // Lấy thông tin thống kê của lecturer
        $lecturerStats = $this->getInfoLecturer($product->lecturer_id);
        
        // Gộp thông tin: model Lecturer + stats
        $lecturer = $lecturerModel;
        if ($lecturerModel && $lecturerStats) {
            $lecturer->stats = $lecturerStats;
        }
        $promotion_gifts = null;
        $promotion_gifts = $this->promotionService->getProTakeGiftBuyProduct($id);
        $product['promotion_gifts'] = $promotion_gifts;
        $seller = null;
        if (!is_null($product->seller_id)) {
            $seller = $this->customerRepository->findById($product->seller_id);
        }

        $promotionLeft = $this->promotionLeft($product) ?? null;

        $productCatalogue = $this->productCatalogueRepository->getProductCatalogueById($product->product_catalogue_id, $this->language);

        $parent = null;

        $children = null;

        if ($productCatalogue->parent_id != 0) {
            $parent = $this->productCatalogueRepository->getParent($productCatalogue, $this->language);
            $children = $this->productCatalogueRepository->getChildren($parent);
        } else {
            $children = $this->productCatalogueRepository->getChildren($productCatalogue);
        }

        $breadcrumb = $this->productCatalogueRepository->breadcrumb($productCatalogue, $this->language);
        /* ------------------- */
        $product = $this->productService->getAttribute($product, $this->language);
        $category = recursive(
            $this->productCatalogueRepository->all([
                'languages' => function ($query) use ($language) {
                    $query->where('language_id', $language);
                }
            ], categorySelectRaw('product'))
        );

        $wishlist = Cart::instance('wishlist')->content();

        $widgets = $this->widgetService->getWidget([
            ['keyword' => 'news-feature'],
            ['keyword' => 'projects-feature'],
            ['keyword' => 'news', 'object' => true],
            ['keyword' => 'news-outstanding', 'object' => true],
            ['keyword' => 'showroom-system', 'object' => true],
            ['keyword' => 'design_construction_interior', 'object' => true],
            ['keyword' => 'showroom-system', 'object' => true],

        ], $this->language);

        $productSeen = [
            'id' => $product->id,
            'name' => $product->name,
            'price' => $product->price,
            'qty' => 1,
            'options' => [
                'canonical' => $product->languages->first()->pivot->canonical,
                'image' => $product->image,
            ]
        ];

        $productRelated = $this->productRepository->getRelated(6, $product->product_catalogue_id, $product->id);
        
        // Load promotions cho related products
        if ($productRelated && $productRelated->count() > 0) {
            $relatedProductIds = $productRelated->pluck('id')->toArray();
            $productRelated = $this->productService->combineProductAndPromotion($relatedProductIds, $productRelated);
        }


        Cart::instance('seen')->add($productSeen);

        $cartSeen = Cart::instance('seen')->content();


        $carts = Cart::instance('shopping')->content() ?? null;

        $config = $this->config();

        $customer = Auth::guard('customer')->user();

        $voucher_product = (!is_null($customer)) ? $this->voucherService->getVoucherForProduct($id, $carts, $customer->id) : null;

        $system = $this->system;

        $seo = seo($product);

        $template = 'frontend.product.product.index';

        return view($template, compact(
            'config',
            'seo',
            'system',
            'breadcrumb',
            'productCatalogue',
            'customer',
            'voucher_product',
            'product',
            'category',
            'widgets',
            'wishlist',
            'cartSeen',
            'seller',
            'carts',
            'productRelated',
            'children',
            'promotionLeft',
            'students',
            'lecturer', 
        ));
    }

    private function config()
    {
        return [
            'language' => $this->language,
            'js' => [
                'https://prohousevn.com/scripts/fancybox-3/dist/jquery.fancybox.min.js',
                'frontend/resources/plugins/OwlCarousel2-2.3.4/dist/owl.carousel.min.js',
                'frontend/core/library/cart.js',
                'frontend/core/library/product.js',
                'frontend/core/library/review.js',
                'frontend/resources/library/js/carousel.js',
            ],
            'css' => [
                'https://prohousevn.com/scripts/fancybox-3/dist/jquery.fancybox.min.css',
                'frontend/resources/plugins/OwlCarousel2-2.3.4/dist/assets/owl.carousel.min.css',
                'frontend/resources/plugins/OwlCarousel2-2.3.4/dist/assets/owl.theme.default.min.css',
                'frontend/core/css/product.css',
                'frontend/resources/css/custom.css'
            ]
        ];
    }

}