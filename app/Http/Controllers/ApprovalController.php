<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\ApprovalService;
use App\Models\Approval;
use App\Models\Coupon;
use App\Models\Denomination;
use App\Models\Promo;

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

    public function getByClaimRequest(Request $request, ApprovalService $approvalService) {
        return response()->json($approvalService->getByClaimRequest($request->claimHeaderId),200);
    }

    public function resend(Request $request, ApprovalService $approvalService){
        return response()->json($approvalService->resend($request),200);
    }

    public function getAllPending(Request $request, ApprovalService $approvalService){
        return response()->json($approvalService->getAllPending($request->employee_number),200);
    }

    public function viewCoupon(Request $request){
      
        $approvalDetails = Approval::where('id' , $request->approval_id)->first();
        $coupon = new Coupon;
        $denomination = new Denomination;

        $couponDetails = $coupon->getDetails($approvalDetails->module_reference_id);
        $denominations = $denomination->getByCoupon($approvalDetails->module_reference_id);
     
        $data =  [
            'message' => 'Coupon Viewing',
            'approval_id' => $request->approval_id,
            'couponDetails' => $couponDetails,
            'denominations' => $denominations,
            'approve_link' => url('/') . '/api/approve/' . $request->approval_id,
            'reject_link' => url('/') . '/api/reject/' . $request->approval_id
        ];

        return view('approval/coupon', $data);
    }

    public function viewPromo(Request $request){

        $promo = new Promo;
        $promo_id = $request->promo_id;
        $promoDetails = $promo->getById($promo_id);
        $approver_user_id = $request->approver_user_id;
        $approver_source_id = $request->approver_user;
        
        $data = [
            'promoDetails' => $promoDetails,
            'approve_link' => url('/') . '/api/promo/approve/' . $promo_id . '/' . $approver_user_id . '/' . $approver_source_id,
            'reject_link'  => url('/') . '/api/promo/reject/' . $promo_id . '/' . $approver_user_id . '/' . $approver_source_id
        ];

        return view('approval/promo', $data);
    }
    
}
