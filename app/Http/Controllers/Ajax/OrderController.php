<?php

namespace App\Http\Controllers\Ajax;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\OrderService;
use App\Repositories\OrderRepository;
use App\Classes\ViettelPost;
use Illuminate\Support\Facades\Auth;

class OrderController extends Controller
{
    protected $orderService;
    protected $orderRepository;

    public function __construct(
        OrderService $orderService,
        OrderRepository $orderRepository,
    ){
       $this->orderService = $orderService;
       $this->orderRepository = $orderRepository;
    }


    public function update(Request $request){

        $seller =  Auth::guard('customer')->user();


        
        if($this->orderService->update($request)){
            $order = $this->orderRepository->getOrderById($request->input('id'));
            $payload = $request->input('payload');
            if($payload['confirm'] && $payload['confirm'] == 'confirm'){
                $viettelPost = new ViettelPost(
                    $seller->viettelpost_email,
                    $seller->viettelpost_password
                );
                $accessToken = $viettelPost->getToken();

                // #Nếu muốn tạo đơn hàng để viettelpost thu hộ thì bật cái này lên
                // $viettelPost->createOrder($accessToken, $order, $seller, $this->orderRepository);
            }

            // die();

            return response()->json([
                'error' => 10,
                'messages' => 'Cập nhật dữ liệu thành công',
                'order' => $order
            ]);
        }

        return response()->json([
            'error' => 11,
            'messages' => 'Cập nhật dữ liệu không thành công. Hãy thử lại'
        ]);
    }


    public function chart(Request $request){
        $chart = $this->orderService->ajaxOrderChart($request);

        return response()->json($chart);

    }
    
   
}
