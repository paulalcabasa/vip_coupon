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

    public function cancelPayment(Request $request, ClaimRequestService $claimRequestService){
        return response()->json($claimRequestService->cancelPayment($request),200);
    }

    public function updateStatus(Request $request, ClaimRequestService $claimRequestService){
        return response()->json($claimRequestService->updateStatus($request),200);
    }
}
