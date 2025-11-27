<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\ReviewService;
use App\Repositories\ReviewRepository;
use App\Repositories\ProductRepository;
use App\Http\Requests\Review\StoreReviewRequest;

class ReviewController extends Controller
{
    protected $reviewService;
    protected $reviewRepository;
    protected $productRepository;

    public function __construct(
        ReviewService $reviewService,
        ReviewRepository $reviewRepository,
        ProductRepository $productRepository,
    ){
        $this->reviewService = $reviewService;
        $this->reviewRepository = $reviewRepository;
        $this->productRepository = $productRepository;
    }

    public function index(Request $request){
        $this->authorize('modules', 'review.index');
        $reviews = $this->reviewService->paginate($request);
        $config = [
            'js' => [
                'backend/js/plugins/switchery/switchery.js',
                'https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js'
            ],
            'css' => [
                'backend/css/plugins/switchery/switchery.css',
                'https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css'
            ],
            'model' => 'Review'
        ];
        $config['seo'] = __('messages.review');
        $template = 'backend.review.index';
        return view('backend.dashboard.layout', compact(
            'template',
            'config',
            'reviews'
        ));
    }

    public function create(){
        $this->authorize('modules', 'review.create');
        $products = $this->productRepository->all(['languages']);
        $config = $this->config();
        $config['seo'] = __('messages.review');
        $config['method'] = 'create';
        $template = 'backend.review.store';
        return view('backend.dashboard.layout', compact(
            'products',
            'template',
            'config',
        ));
    }

    public function store(StoreReviewRequest $request){
        if($this->reviewService->create($request)){
            return redirect()->route('review.index')->with('success','Cập nhật bản ghi thành công');
        }
        return redirect()->route('review.index')->with('error','Cập nhật bản ghi không thành công. Hãy thử lại');
    }

    public function delete($id){
        $this->authorize('modules', 'review.destroy');
        $config['seo'] = __('messages.review');
        $review = $this->reviewRepository->findById($id);
        $template = 'backend.review.delete';
        return view('backend.dashboard.layout', compact(
            'template',
            'review',
            'config',
        ));
    }

    public function destroy($id){
        if($this->reviewService->destroy($id)){
            return redirect()->route('review.index')->with('success','Xóa bản ghi thành công');
        }
        return redirect()->route('review.index')->with('error','Xóa bản ghi không thành công. Hãy thử lại');
    }

    
    private function config(){
        return [
            'css' => [
                'https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css'
            ],
            'js' => [
                'https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js',
                'backend/plugins/ckfinder_2/ckfinder.js',
                'backend/library/finder.js',
                'backend/library/widget.js',
                'backend/plugins/ckeditor/ckeditor.js',
            ]
        ];
    }

}
