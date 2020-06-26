<?php

namespace App\Services;

use DB;
use App\Models\Promo;
use Carbon\Carbon;

class PromoService {

    public function getActive(){
        $promo = new Promo();
        $promos = $promo->getActive();
        return $promos;
    }

    public function getPromos(){
        $promo = new Promo;
        return $promo->get();
    }

    public function createPromo($request){
        $promo = new Promo;
        $user = auth()->user();
        $promo->promo_name = $request->promo['promo_name'];
        $promo->effective_date_from = $request->promo['effective_date_from'];
        $promo->effective_date_to = $request->promo['effective_date_to'];
        $promo->coupon_expiry_date = $request->promo['coupon_expiry_date'];
        $promo->created_by = $user->user_id;
        $promo->create_user_source = $user->user_source_id;
        $promo->created_at = Carbon::now();
        $promo->save();
        $promo_id = $promo->id;
        return [
            'message' => $request->promo['promo_name'] . ' has been created.',
            'promos' => $promo->get()
        ];
    }

    public function updatePromo($request){
        $promo = Promo::find($request->promo['id']);
        $user = auth()->user();
        $promo->promo_name = $request->promo['promo_name'];
        $promo->effective_date_from = $request->promo['effective_date_from'];
        $promo->effective_date_to = $request->promo['effective_date_to'];
        $promo->coupon_expiry_date = $request->promo['coupon_expiry_date'];
        $promo->created_by = $user->user_id;
        $promo->create_user_source = $user->user_source_id;
        $promo->created_at = Carbon::now();
        $promo->save();
     
        return [
            'message' => $request->promo['promo_name'] . ' has been updated.',
            'promos' => $promo->get()
        ];
    }
    
}