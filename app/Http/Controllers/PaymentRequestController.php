<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\PaymentRequestService;


class PaymentRequestController extends Controller
{
    
    public function store(Request $request, PaymentRequestService $paymentRequestService){
        return response()->json($paymentRequestService->handlePaymentSave($request),200);
    }
}
