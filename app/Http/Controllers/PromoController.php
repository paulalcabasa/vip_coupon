<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\PromoService;


class PromoController extends Controller
{
    public function getActive(){
        $promoService = new PromoService;
        return response()->json($promoService->getActive());
    }

    public function index(){
        $promoService = new PromoService;
        return response()->json($promoService->getPromos());
    }

    public function store(Request $request){
        $promoService = new PromoService;
        return response()->json($promoService->createPromo($request));
    }

    public function update(Request $request){
        $promoService = new PromoService;
        return response()->json($promoService->updatePromo($request));
    }

    public function getActiveByCouponType(Request $request){
        $promoService = new PromoService;
        return response()->json($promoService->getActiveByCouponType($request->coupon_type_id));
    }

    public function approve(Request $request){
        $promoService = new PromoService;
        $result = $promoService->approve($request);
        $data = [
            'image_url' => $result['image_url'],
            'message' => $result['message']
        ];
        return view($result['template'], $data);
    }

    public function reject(Request $request){
     
        $data = [
            'reject_api' => url('/') . '/api/promo/reject',
            'promo_id' => $request->promo_id,
            'user_id' => $request->approver_id,
            'user_source' => $request->approver_source
        ];
        return view('reject-promo-form', $data);
    }

    public function rejectPromo(Request $request){
        $promoService = new PromoService;
        $result = $promoService->reject($request);
        $data = [
            'image_url' => $result['image_url'],
            'message' => $result['message']
        ];
        return view($result['template'], $data);
     
    }
}
