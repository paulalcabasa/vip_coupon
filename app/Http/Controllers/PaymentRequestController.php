<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\PaymentRequestService;


class PaymentRequestController extends Controller
{
    
    public function store(Request $request, PaymentRequestService $paymentRequestService){
        return response()->json($paymentRequestService->handlePaymentSave($request),200);
    }

    public function get(PaymentRequestService $paymentRequestService){
        return response()->json($paymentRequestService->getPayments(),200);
    }

    public function getLines(Request $request, PaymentRequestService $paymentRequestService){
        return response()->json($paymentRequestService->getLines($request),200);
    }

    public function getHeader(Request $request, PaymentRequestService $paymentRequestService){
        return response()->json($paymentRequestService->getHeader($request),200);
    }
}
