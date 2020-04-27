<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use PDF;
use App\Services\VoucherService;
use App\Models\Voucher;


class PdfController extends Controller
{   

    public function printCoupon(Request $request){
        $voucher = new Voucher;
        $docs = $voucher->getByCoupon($request->coupon_id);
       
        $data = [
            'docs' => $docs
        ];
        $pdf = PDF::loadView('print_coupon',$data);
        return $pdf->setPaper('a4','portrait')->stream();
    }

}
