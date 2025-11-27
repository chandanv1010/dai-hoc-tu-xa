<?php

namespace App\Http\Controllers\Ajax;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\ReviewService;
use App\Models\Review;
use App\Classes\ReviewNested;


class ReviewController extends Controller
{
    protected $reviewService;
    protected $reviewNestedset;

    public function __construct(
        ReviewService $reviewService,
    ){
        $this->reviewService = $reviewService;
    }

    public function create(Request $request){
        $response = $this->reviewService->create($request);
        return response()->json($response); 
    }

    public function changeStatus(Request $request){
        $id = $request->id;
        $status = $request->status;
        $response = Review::where('id', $id)->update(['status' => $status]);
        return response()->json($response); 
    }
    
    public function delete(Request $request){
        
        $reviewIds = $request->input('array_id'); 
        
        $payload = []; 
        
        foreach($reviewIds as $k => $item){ $payload[] = $item[$k]; } 
        
        $response = Review::whereIn('id', $payload)->delete();

        $this->reviewNestedset = new ReviewNested([
            'table' => 'reviews',
            'reviewable_type' => 'App\Models\Product'
        ]);

        $this->reviewNestedset->Get('level ASC, order ASC');

        $this->reviewNestedset->Recursive(0, $this->reviewNestedset->Set());

        $this->reviewNestedset->Action();

        return response()->json([
            'status' => $response > 0 ? 200 : 404,
            'data' => $response,
        ]);
    }
}
