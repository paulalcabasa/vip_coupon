<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\ApprovalService;

class ApprovalController extends Controller
{
    public function get(ApprovalService $approvalService){
        return response()->json($approvalService->get(),200);
    }

    public function approve(Request $request, ApprovalService $approvalService){
        return response()->json($approvalService->handleApprove($request),200);
    }

    public function reject(Request $request, ApprovalService $approvalService){
        return response()->json($approvalService->handleReject($request),200);
    }

    public function getByCoupon(Request $request, ApprovalService $approvalService){
        return response()->json($approvalService->getByCoupon($request->coupon_id),200);
    }
}
