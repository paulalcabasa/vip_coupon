<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use DB;
use App\Services\CouponService;

class CouponController extends Controller
{

    private $couponService;

    public function __construct(){
        $this->couponService = new CouponService;
    }
    
    public function store(Request $request){
        return $this->couponService->saveCoupon($request);
    }

    public function show(Request $request){
        $couponId = $request->couponId;    
        return response()->json($this->couponService->getDetails($couponId),200);
    }

    public function get(Request $request){
        $params = [
            'userId' => $request->userId,
            'sourceId' => $request->sourceId,
            'userType' => $request->userType,
            'salesGroup' => $request->salesGroup
        ];
        return response()->json($this->couponService->getCoupons($params),200);
    }

    public function update(Request $request){
        return response()->json($this->couponService->updateCoupon($request),200);
    }

    public function issue(Request $request){
        return response()->json($this->couponService->issueCoupon($request),200);
    }

    public function receiveFleet(Request $request){
        return response()->json($this->couponService->receiveFleetCoupon($request),200);
    }

    public function receiveDealer(Request $request){
        return response()->json($this->couponService->receiveDealerCoupon($request),200);
    }

    public function resend(Request $request){
        return response()->json($this->couponService->resend($request),200);
    }

   
}
