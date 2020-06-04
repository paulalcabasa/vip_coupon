<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use PDF;
use App\Services\VoucherService;
use App\Models\Voucher;
use App\Services\CodeService;
use App\Models\Coupon;
use App\Models\CouponType;

class PdfController extends Controller
{   

    public function printCoupon(Request $request){
        $voucher = new Voucher;
        $coupon = new Coupon;
        $couponType = new CouponType;


        $docs = $voucher->getByCoupon($request->coupon_id);
        $header = $coupon->getDetails($request->coupon_id);
      
        $type = CouponType::where('id', $header->coupon_type_id)->first();
       
        $data = [
            'docs' => $docs,
            'header' => $header
        ];
       
        $pdf = PDF::loadView($type->file_template,$data);
        return $pdf->setPaper('a4','portrait')->stream();
    }

}
