<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\VoucherService;

class VoucherController extends Controller
{
     private $voucherService;
    
    public function __construct(){
        $this->voucherService = new VoucherService;
    }

    public function generate(Request $request){
        return $this->voucherService->generate($request);
    }

    public function show(Request $request){
        return $this->voucherService->getVouchers($request->couponId);
    }
}
