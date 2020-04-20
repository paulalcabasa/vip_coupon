<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use DB;
use App\Models\Coupon;
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
}
