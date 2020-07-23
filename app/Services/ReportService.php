<?php

namespace App\Services;

use DB;
use App\Models\Report;
use Carbon\Carbon;

class ReportService 
{
        
    public function getVoucherSummary($request)
    {
        $report = new Report;
        $params = [
            'voucher_start_date' => $request->voucherStartDate,
            'voucher_end_date'   => $request->voucherEndDate,
            'dealer_id'          => $request->dealerId,
            'coupon_type'        => $request->couponType,
            'vehicle_type'       => $request->vehicleType,
        ];
        return $report->getVoucherSummary($params);
       
    }
}