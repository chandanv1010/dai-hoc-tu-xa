<?php

namespace App\Http\Controllers\Ajax;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Language;
use App\Models\ProductCatalogue;
use Illuminate\Support\Str;
use App\Repositories\PromotionRepository;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Models\Product;

class DashboardController extends Controller
{

    protected $language;
    protected $promotionRepository;

    public function __construct(
        PromotionRepository $promotionRepository
    ){
        $this->middleware(function($request, $next){
            $locale = app()->getLocale(); // vn en cn
            $language = Language::where('canonical', $locale)->first();
            $this->language = $language->id;
            return $next($request);
        });
        $this->promotionRepository = $promotionRepository;
    }

    public function changeStatus(Request $request){
        $post = $request->input();
        $serviceInterfaceNamespace = '\App\Services\\' . ucfirst($post['model']) . 'Service';
        if (class_exists($serviceInterfaceNamespace)) {
            $serviceInstance = app($serviceInterfaceNamespace);
        }
        $flag = $serviceInstance->updateStatus($post);

        return response()->json(['flag' => $flag]); 
        
    }

    public function changeStatusAll(Request $request){
        $post = $request->input();
        $serviceInterfaceNamespace = '\App\Services\\' . ucfirst($post['model']) . 'Service';
        if (class_exists($serviceInterfaceNamespace)) {
            $serviceInstance = app($serviceInterfaceNamespace);
        }
        $flag = $serviceInstance->updateStatusAll($post);
        return response()->json(['flag' => $flag]); 

    }

    public function getMenu(Request $request){
        $model = $request->input('model');
        $page = ($request->input('page')) ?? 1;
        $keyword = ($request->string('keyword')) ?? null;

        $serviceInterfaceNamespace = '\App\Repositories\\' . ucfirst($model) . 'Repository';
        if (class_exists($serviceInterfaceNamespace)) {
            $serviceInstance = app($serviceInterfaceNamespace);
        }
        
        $agruments = $this->paginationAgrument($model, $keyword);
        $object = $serviceInstance->pagination(...array_values($agruments));
        
        return response()->json($object); 
    }

    private function paginationAgrument(string $model = '', string $keyword): array{
        $model = Str::snake($model);
        $join = [
            [$model.'_language as tb2', 'tb2.'.$model.'_id', '=', $model.'s.id'],
        ];
        if(strpos($model, '_catalogue') === false){
            $join[] = [''.$model.'_catalogue_'.$model.' as tb3', ''.$model.'s.id', '=', 'tb3.'.$model.'_id'];
        }

        $condition = [
            'where' => [
                ['tb2.language_id', '=', $this->language],
            ],
        ];
        if(!is_null($keyword)){
            $condition['keyword'] = addslashes($keyword);
        }
        return [
            'column' => ['id','name','canonical'],
            'condition' => $condition,
            'perpage' => 20,
            'extend' => [
                'path' => $model.'.index', 
                'groupBy' => ['id','name']
            ],
            'orderBy' => [$model.'s.id', 'DESC'],
            'join' => $join,
            'relations' => [],
        ];
    }

    public function findModelObject(Request $request){
        $get = $request->input();
        $alias = Str::snake($get['model']).'_language';
        $class = loadClass($get['model']);
        $object = $class->findWidgetItem([
            ['name','LIKE', '%'.$get['keyword'].'%'],
        ], $this->language, $alias);
        return response()->json($object); 
    }

    public function findPromotionObject(Request $request){
        $get = $request->input();
        $model = $get['option']['model'];
        $keyword = $get['search'];
        $alias = Str::snake($model).'_language';
        $class = loadClass($model);
        $object = $class->findWidgetItem([
            ['name','LIKE', '%'.$keyword.'%'],
        ], $this->language, $alias);

        $temp = [];
        if(count($object)){
            foreach($object as $key => $val){
                $temp[] = [
                    'id' => $val->id,
                    'text' => $val->languages->first()->pivot->name,
                ];
            }
        }

        return response()->json(array('items' => $temp)); 
    }


    public function getPromotionConditionValue(Request $request){
        try {
            $get = $request->input();
            switch ($get['value']) {
                case 'staff_take_care_customer':
                    $class = loadClass('User');
                    $object = $class->all()->toArray();
                    break;
                case 'customer_group':
                    $class = loadClass('CustomerCatalogue');
                    $object = $class->all()->toArray();
                    break;
                case 'customer_gender':
                    $object = __('module.gender');
                    break;
                case 'customer_birthday':
                    $object = __('module.day');
                    break;
            }
            $temp = [];
            if(!is_null($object) && count($object)){
                foreach($object as $key => $val){
                    $temp[] = [
                        'id' => $val['id'],
                        'text' => $val['name'],
                    ];
                }
            }
            return response()->json([
                'data' => $temp,
                'error' => false,
            ]);

        }catch(\Exception $e ){
            Log::error($e->getMessage());
            return response()->json([
                'error' => true,
                'messages' =>  $e->getMessage(),
            ]);
        }
        
    }

    public function findInformationObject(Request $request)
    {
        $get = $request->input();
        $class = loadClass($get['model']);
        $object = $class->searchInformation([
            ['name', 'LIKE', '%' . $get['keyword'] . '%'],
            ['phone', 'LIKE', '%' . $get['keyword'] . '%', 'orWhere'],
            ['code', 'LIKE', '%' . $get['keyword'] . '%', 'orWhere'],
        ]);
    
        return response()->json($object); 
    }

    public function findProduct(Request $request){
        $keyword = $request->input('keyword');
        $alias = Str::snake(__('module.product_model')).'_language';
        $class = loadClass(__('module.product_model'));
        $object = $class->findWidgetItem([
            ['name','LIKE', '%'.$keyword.'%'],
        ], $this->language, $alias);
        return response()->json($object); 
    }

    // public function findProductObject(Request $request){
    //     $html = '';
    //     $productCatalogueId = $request->input('product_catalogue_id');
    //     if(!$productCatalogueId){
    //         return response()->json(['html' => $html]);
    //     }
    //     $class = loadClass('Product');
    //     $products = $class->getProductByProductCatalogue($productCatalogueId, $this->language);
    //     // dd($products);
    //     $productIds = $products->pluck('id')->all();
    //     if(!count($productIds) &&!is_array($productIds)){
    //         return response()->json(['html' => $html]);
    //     }
    //     $promotions = $this->promotionRepository->findByProduct($productIds);
    //     if($promotions->isNotEmpty()){
    //             $promotionMap = $promotions->keyBy('product_id');
    //             foreach($products as $index => $product){
    //                 if($promotionMap->has($product->id)){
    //                     $products[$index]->promotions = $promotionMap->get($product->id);
    //                 }
    //             }
    //         }
    //     $html = view('frontend.component.product-item-switch', ['products' => $products])->render();
    //     return response()->json(['html' => $html]);
    // }
    
    public function findProductObject(Request $request)
    {
        $html = '';
        $productCatalogueIds = $request->input('product_catalogue_id', []);

        // luôn có 2 id, nếu không có thì trả rỗng
        if (count($productCatalogueIds) < 2) {
            return response()->json(['html' => $html]);
        }

        // Lấy thông tin lft/rgt của 2 danh mục
        $productCatalogues = ProductCatalogue::whereIn('id', $productCatalogueIds)
            ->select('id', 'lft', 'rgt')
            ->get()
            ->keyBy('id');

        // [0] là danh mục phụ, [1] là danh mục chính
        $subRoot  = $productCatalogues->get($productCatalogueIds[0]);
        $mainRoot = $productCatalogues->get($productCatalogueIds[1]);

        if (!$subRoot || !$mainRoot) {
            return response()->json(['html' => $html]);
        }

        // Lấy tất cả danh mục con của từng cây
        $subTree = ProductCatalogue::whereBetween('lft', [$subRoot->lft, $subRoot->rgt])
            ->pluck('id')
            ->toArray();

        $mainTree = ProductCatalogue::whereBetween('lft', [$mainRoot->lft, $mainRoot->rgt])
            ->pluck('id')
            ->toArray();

        $products = Product::query()
            ->withCount(['reviews as review_count'])
            ->withAvg('reviews as review_average', 'score')
            ->join('product_language as tb2', 'tb2.product_id', '=', 'products.id')
            ->leftJoin('lecturers as tb3', 'tb3.id', '=', 'products.lecturer_id')
            ->select([
                'products.id',
                'products.price',
                'products.image',
                'products.total_lesson',
                'products.duration',
                'tb2.name',
                'tb2.canonical',
                'tb2.description',
                'tb2.content',
                'tb2.meta_title',
                'tb3.name as lecturer_name',
                'tb3.image as lecturer_avatar',
                'tb3.canonical as lecturer_canonical',
            ])
            ->where('tb2.language_id', $this->language)
            // nằm trong cây chính
            ->whereIn('products.id', function ($q) use ($mainTree) {
                $q->select('product_id')
                    ->from('product_catalogue_product')
                    ->whereIn('product_catalogue_id', $mainTree);
            })
            // đồng thời nằm trong cây phụ
            ->whereIn('products.id', function ($q) use ($subTree) {
                $q->select('product_id')
                    ->from('product_catalogue_product')
                    ->whereIn('product_catalogue_id', $subTree);
            })
            ->groupBy([
                'products.id',
                'products.price',
                'products.image',
                'products.total_lesson',
                'products.duration',
                'tb2.name',
                'tb2.canonical',
                'tb2.description',
                'tb2.content',
                'tb2.meta_title',
                'tb3.name',
                'tb3.image',
                'tb3.canonical',
            ])
        ->get();
        

        // Không có sản phẩm → trả rỗng
        if ($products->isEmpty()) {
            return response()->json(['html' => $html]);
        }

        // Thêm khuyến mãi nếu có
        $productIds = $products->pluck('id')->all();
        $promotions = $this->promotionRepository->findByProduct($productIds);

        if ($promotions->isNotEmpty()) {
            $promotionMap = $promotions->keyBy('product_id');
            foreach ($products as $index => $product) {
                if ($promotionMap->has($product->id)) {
                    $products[$index]->promotions = $promotionMap->get($product->id);
                }
            }
        }

        // Render HTML
        $html = view('frontend.component.product-item-switch', [
            'products' => $products
        ])->render();

        return response()->json(['html' => $html]);
    }


}
