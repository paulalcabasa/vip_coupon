<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use PDF;
use App\Services\CouponService;
use App\Models\CouponDocs;


class PdfController extends Controller
{   

    private $couponService;

    public function __construct(){
        $this->couponService = new CouponService;    
    }

    public function printCoupon(Request $request){
        $couponDocs = new CouponDocs;
    
        $docs = $couponDocs->getByCoupon($request->coupon_id);
        $data = [
            'docs' => $docs
        ];
        $pdf = PDF::loadView('print_coupon',$data);
        return $pdf->setPaper('a4','portrait')->stream();
    }

}
