<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\CouponDocsService;

class CouponDocsController extends Controller
{
    
    public function generate(Request $request){
        $couponDocsService = new CouponDocsService;
        return $couponDocsService->generate($request);
    }
}
