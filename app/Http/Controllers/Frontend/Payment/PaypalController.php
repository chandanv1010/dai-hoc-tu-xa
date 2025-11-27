<?php

namespace App\Http\Controllers\Frontend\Payment;

use App\Http\Controllers\FrontendController;
use Illuminate\Http\Request;
use App\Repositories\OrderRepository;
use App\Services\OrderService;
use Srmklive\PayPal\Services\PayPal as PayPalClient;


class PaypalController extends FrontendController
{
  
    protected $orderRepository;
    protected $orderService;

    public function __construct(
        OrderRepository $orderRepository,
        OrderService $orderService,
    ){
       
        $this->orderRepository = $orderRepository;
        $this->orderService = $orderService;
        parent::__construct();
    }


    public function success(Request $request){
        $system = $this->system;
        $provider = new PayPalClient();
        $provider->setApiCredentials(config('paypal'));
        $paypalToken = $provider->getAccessToken();
        $response = $provider->capturePaymentOrder($request->input('token'));

        $orderId = $request->input('orderId');

       

        if(isset($response['status']) && $response['status'] == 'COMPLETED'){
            $payload['payment'] = 'paid';
            $payload['confirm'] = 'confirm';
            $order = $this->orderRepository->findByCondition([
                ['id', '=', $orderId],
            ], false, ['products']);
            if($order){
                $flag = $this->orderService->updatePaymentOnline($payload, $order);
            } else {
                $flag = false;
            }
            $seo = [
                'meta_title' => 'Thông tin thanh toán mã đơn hàng #'.$orderId,
                'meta_keyword' => '',
                'meta_description' => '',
                'meta_image' => '',
                'canonical' => write_url('cart/success', TRUE, TRUE),
            ];
            $template = 'frontend.cart.component.paypal';
            return view('frontend.cart.success', compact(
                'seo',
                'system',
                'order',
                'template',
            ));
        }
        // If payment not completed, redirect back with error
        return redirect()->route('cart.checkout')->with('error', 'Thanh toán PayPal không thành công.');
    }

    public function cancel(Request $request){
        echo 'Hủy thanh toán thành công';die();
    }

  

}
