<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\ClaimRequestService;

class ClaimRequestController extends Controller
{
    
    public function store(Request $request, ClaimRequestService $claimRequestService){
        return response()->json($claimRequestService->savePayment($request),200);
    }

    public function get(Request $request,ClaimRequestService $claimRequestService){
        return response()->json($claimRequestService->getClaimRequests($request),200);
    }

    public function getLines(Request $request, ClaimRequestService $claimRequestService){
        return response()->json($claimRequestService->getLines($request),200);
    }

    public function getHeader(Request $request, ClaimRequestService $claimRequestService){
        return response()->json($claimRequestService->getHeader($request),200);
    }

    public function cancel(Request $request, ClaimRequestService $claimRequestService){
        return response()->json($claimRequestService->cancel($request),200);
    }

    public function updateStatus(Request $request, ClaimRequestService $claimRequestService){
        return response()->json($claimRequestService->updateStatus($request),200);
    }

    public function approval(Request $request, ClaimRequestService $claimRequestService){
        $approval_id = $request->approval_id;
        $result = $claimRequestService->approvalDetails($request);
        return view($result['view'], $result['data']);
    }

    public function approvers(Request $request, ClaimRequestService $claimRequestService){
        return response()->json($claimRequestService->getApprovers($request),200);
    }

    public function approve(Request $request, ClaimRequestService $claimRequestService){
        $result = $claimRequestService->approve($request);
        return view($result['view'], $result['data']);
    }

    public function rejectForm(Request $request, ClaimRequestService $claimRequestService){
        $result = $claimRequestService->rejectForm($request);
        return view($result['view'], $result['data']);
    }

    public function reject(Request $request, ClaimRequestService $claimRequestService){
        $result = $claimRequestService->reject($request);
    
        return view($result['view'], $result['data']);
    }


}
