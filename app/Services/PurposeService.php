<?php

namespace App\Services;

use DB;
use App\Models\Purpose;
use Carbon\Carbon;

class PurposeService {

    public function getActive(){
        $purpose = new Purpose;
        return $purpose->getActive();
    }

    public function get(){
        $purpose = new Purpose;
        return $purpose->get();
    }

    public function createPurpose($request){
        $purpose = new Purpose;
        $user = auth()->user();
        $purpose->purpose = $request->purpose['purpose'];
        $purpose->status = $request->purpose['status'];
        $purpose->require_cs_no_flag = $request->purpose['require_cs_no_flag'];
        $purpose->created_by = $user->user_id;
        $purpose->create_user_source = $user->user_source_id;
        $purpose->created_at = Carbon::now();
        $purpose->save();
        return [    
            'message' => $request->purpose['purpose'] . ' has been created.',
            'purposes' => $purpose->get()
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