<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use PDF;
use App\Services\VoucherService;
use App\Models\Voucher;
use App\Services\CodeService;
use App\Models\Coupon;

class PdfController extends Controller
{   

    public function printCoupon(Request $request){
        $voucher = new Voucher;
        $coupon = new Coupon;
        $docs = $voucher->getByCoupon($request->coupon_id);
        $header = $coupon->getDetails($request->coupon_id);
        $data = [
            'docs' => $docs,
            'header' => $header
        ];
      
        $pdf = PDF::loadView('print-voucher',$data);
        return $pdf->setPaper('a4','portrait')->stream();
    }

}
