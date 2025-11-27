<?php

namespace App\Http\Controllers\Ajax;

use App\Http\Controllers\FrontendController;
use App\Services\CartService;
use App\Repositories\ProductRepository;
use Illuminate\Http\Request;
use Gloudemans\Shoppingcart\Facades\Cart;


class CartController extends FrontendController
{
    protected $cartService;
    protected $productRepository;
    protected $language;

    public function __construct(
        CartService $cartService,
        ProductRepository $productRepository,
    ){
        $this->cartService = $cartService;
        $this->productRepository = $productRepository;
        parent::__construct(); 
    }

    public function create(Request $request){
        $flag = $this->cartService->create($request, $this->language);
        $cart = Cart::instance('shopping')->content();
        $cartCaculate = $this->cartService->reCaculateCart();
        return response()->json([
            'cart' => $cart, 
            'cartCaculate' => $cartCaculate,
            'messages' => 'Thêm sản phẩm vào giỏ hàng thành công',
            'code' => ($flag) ? 10 : 11,
        ]); 
        
    }

    public function update(Request $request){
        $response = $this->cartService->update($request);
        return response()->json([
            'response' => $response, 
            'messages' => 'Cập nhật số lượng thành công',
            'code' => (!$response) ? 11 : 10,
        ]); 
    }

    public function delete(Request $request){
        $response = $this->cartService->delete($request);
        return response()->json([
            'response' => $response, 
            'messages' => 'Xóa sản phẩm khỏi giỏ hàng thành công',
            'code' => (!$response) ? 11 : 10,
        ]);  
    }

    public function pay(Request $request){
        $id = $request->input('id');
        if($product = $this->productRepository->findById($id)){
            $data = [
                'id' => $product->id,
                'image' => $product->image,
                'name' => $product->languages->first()->pivot->name,
                'price' => $product->price,
                'qty' => 1,
            ];
            Cart::instance('pay')->destroy();
            Cart::instance('pay')->add($data);
            $pay = Cart::instance('pay')->content();
            return response()->json([
                'pay' => $pay, 
                'code' => ($product) ? 10 : 11,
            ]);  
        }
    }


    public function applyCartVoucher(Request $request){
        $voucher_id = $request->input('voucher_id');
        $response = $this->cartService->handleApplyCartVoucher($voucher_id);
        $carts = Cart::instance('shopping')->content()->toArray();
        return response()->json([
            'response' => $response, 
            'carts' => $carts,
            'code' => (!$response) ? 11 : 10,
        ]);
    }

    public function unUseVoucher(Request $request){
        $voucher_id = $request->input('voucher_id');
        $response = $this->cartService->handleUnUseVoucher($voucher_id);
        $carts = Cart::instance('shopping')->content()->toArray();
        return response()->json([
            'response' => $response, 
            'carts' => $carts,
            'code' => (!$response) ? 11 : 10,
        ]);
    }

   
}
