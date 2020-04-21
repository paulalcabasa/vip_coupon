<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\ApprovalService;

class ApprovalController extends Controller
{
    public function get(ApprovalService $approvalService){
        return response()->json($approvalService->getApproval(),200);
    }

    public function approve(Request $request, ApprovalService $approvalService){
        return response()->json($approvalService->handleApprove($request),200);
    }

    public function reject(Request $request, ApprovalService $approvalService){
        return response()->json($approvalService->handleReject($request),200);
    }
}
