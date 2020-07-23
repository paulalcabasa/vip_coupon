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

    public function getByCouponType($couponTypeId){
        $promo = new Promo;
        return $promo->getByCouponType($couponTypeId);
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
        $promo->terms = $request->promo['terms'];
        $promo->coupon_type_id = $request->promo['coupon_type'];
        $promo->save();
        $promo_id = $promo->id;
        return [
            'message' => $request->promo['promo_name'] . ' has been created.',
            'promos' => $promo->getByCouponType($request->promo['coupon_type'])
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
        $promo->status = 1;
        $promo->mail_sent = 'N';
        $promo->created_at = Carbon::now();
        $promo->terms = $request->promo['terms'];
        $promo->coupon_type_id = $request->promo['coupon_type'];
        $promo->save();
     
        return [
            'message' => $request->promo['promo_name'] . ' has been updated.',
            'promos' => $promo->getByCouponType($request->promo['coupon_type'])
        ];
    }

    public function getActiveByCouponType($coupon_type_id){
        $promo = new Promo();
        $promos = $promo->getActiveByCouponType($coupon_type_id);
        return $promos;
    }

    public function approve($request){
        $promo = Promo::find($request->promo_id);
        if($promo->status == 1){ // if pending
            $promo->status = 10; // set to active
            $promo->approved_by_id = $request->user_id;
            $promo->approved_by_source = $request->user_id;
            $promo->mail_sent = 'N'; // sent to requestor
            $promo->save();
            return [
                'message' => 'Promo has been approved!',
                'image_url' => url('/') . '/public/images/approval-success.gif',
                'template' => 'promo-message'
            ];
        }
        return [
            'message' => 'It looks like the promo has been already approved.',
            'image_url' => url('/') . '/public/images/approval-error.jpg',
            'template' => 'promo-message'
        ];
    }

    public function reject($request){
        $promo = Promo::find($request->promo_id);
        if($promo->status == 1){ // if pending
            $promo->status = 6; // set to rejected
            $promo->approved_by_id = $request->user_id;
            $promo->approved_by_source = $request->user_source;
            $promo->remarks = $request->remarks;
            $promo->mail_sent = 'N'; // sent to requestor
            $promo->save();
            return [
                'message' => 'Promo has been rejected!',
                'image_url' => url('/') . '/public/images/approval-success.gif',
                'template' => 'promo-message'
            ];
        }
        return [
            'message' => 'It looks like the promo has been already rejected.',
            'image_url' => url('/') . '/public/images/approval-error.jpg',
            'template' => 'promo-message'
        ];
    }

    public function cancelPromo($request){
        $promo = Promo::find($request->promo['id']);
        $user = auth()->user();
        
        $promo->status             = 4;
        $promo->updated_at         = Carbon::now();
        $promo->updated_by         = $user->user_id;
        $promo->update_user_source = $user->user_source_id;
       
        $promo->save();
     
        return [
            'message' => $request->promo['promo_name'] . ' has been cancelled.',
            'promos' => $promo->getByCouponType($request->promo['coupon_type'])
        ];
    }

  
}