<?php

namespace App\Http\Controllers\Ajax;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Services\ContactService;
use Illuminate\Support\Facades\Validator;

class ContactController extends Controller
{

    protected $contactService;
    
    public function __construct(
        ContactService $contactService
    ){
        $this->contactService = $contactService;
    }

    public function requestConsult(Request $request){
        $flag = $this->contactService->create($request);
        return response()->json([
            'status' => $flag['code'] == 10 ? true : false,
            'messages' => 'Gửi yêu cầu thành công , chúng tôi sẽ sớm liên hệ với bạn',
        ]);
    }

    public function quickConsult(Request $request){
        $flag = $this->contactService->create($request);
        return response()->json([
            'status' => $flag['code'] == 10 ? true : false,
            'messages' => 'Gửi yêu cầu thành công , chúng tôi sẽ sớm liên hệ với bạn',
        ]);
    }

    public function advise(Request $request){
        $rules = [
            'name' => 'required',
            'phone' => 'required',
        ];
        
        $errorMessages = [
            'name.required' => 'Bạn chưa nhập họ tên.',
            'phone.required' => 'Bạn chưa nhập số điện thoại.',
        ];

        $validator = Validator::make($request->all(), $rules, $errorMessages);

        if($validator->fails()) {
            $errors = $validator->errors();
            $response = [
                'status' => 422,
                'messages' => [
                    'name' => $errors->first('name'),
                    'phone' => $errors->first('phone'),
                ],
            ];
        
            return response()->json($response);
        }

        $flag = $this->contactService->create($request);

        return response()->json([
            'response' => $flag, 
            'messages' => 'Đặt hàng thành công',
            'code' => (!$flag) ? 11 : 10,
        ]);  
    }

    
}
