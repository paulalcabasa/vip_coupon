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
        $approvalId = $request->approval_id;
        $result = $approvalService->approve($approvalId);
        $data = [
            'coupon_id' => $result['module_reference_id'],
            'image_url' => $result['image_url'],
            'message' => $result['message']
        ];
        return view($result['template'], $data);
    }

    public function reject(Request $request){
        $approvalId = $request->approval_id;
        $data = [
            'reject_api' => url('/') . '/api/reject',
            'approvalId' => $approvalId
        ];
        return view('reject-form', $data);
    }

    public function rejectCoupon(Request $request, ApprovalService $approvalService){
        $approvalId = $request->approvalId;
        $remarks = $request->remarks;
        $result = $approvalService->reject($approvalId, $remarks);
        $data = [
            'coupon_id' => $result['module_reference_id'],
            'image_url' => $result['image_url'],
            'message' => $result['message']
        ];
        return view($result['template'], $data);
    }

    public function getByCoupon(Request $request, ApprovalService $approvalService){
        return response()->json($approvalService->getByCoupon($request->coupon_id),200);
    }

    public function resend(Request $request, ApprovalService $approvalService){
        return response()->json($approvalService->resend($request),200);
    }
    
}
