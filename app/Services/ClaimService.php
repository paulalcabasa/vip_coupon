<?php

namespace App\Services;

use DB;
use App\Models\Claim;
use Carbon\Carbon;

class ClaimService 
{
        
    public function get($request)
    {
        $claim = new Claim;
        $params = [
            'start_date' => $request->startDate,
            'end_date' => $request->endDate,
            'dealer_id' => $request->dealerId,
            'coupon_type' => $request->couponType
        ];
        return $claim->getClaims($params);
       
    }
}