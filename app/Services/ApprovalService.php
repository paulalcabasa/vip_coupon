<?php

namespace App\Services;

use DB;
use App\Models\Coupon;
use App\Models\Timeline;
use App\Models\Approver;
use App\Models\Approval;
use Carbon\Carbon;

class ApprovalService {
    
    public function getApproval(){
        $coupon = new Coupon;
        $pending = $coupon->getPending();
        return $pending;
    }

    public function handleApprove($request){
        $couponId   = $request->couponId;
        $userId     = $request->userId;
        $userSource = $request->userSource;
        $status     = 2;
        $action     = 1;

        // approve request
        DB::beginTransaction();

        try {

            $coupon = new Coupon;

            $coupon->updateStatus([
                'couponId'       => $couponId,
                'userId'         => $userId,
                'userSource'     => $userSource,
                'status'         => $status,
                'approvedBy'     => $userId,
                'approverSource' => $userSource,
                'updateDate'     => Carbon::now()
            ]);
            
            $timeline = new Timeline;

            $timeline->saveTimeline([
                'coupon_id'   => $couponId,
                'action_id'   => $action,
                'user_id'     => $userId,
                'created_at'  => Carbon::now(),
                'user_source' => $userSource
            ]);
            
            DB::commit();
            return [
                'message'  => 'Coupon request has been approved.',
                'status'   => 'approved',
                'error'    => false
            ];
        } catch(\Exception $e) {
            DB::rollBack();
            return [
                'message'  => 'Error :' . $e,
                'error'    => true
            ];

        }
      
    }

    public function handleReject($request){
        $couponId   = $request->couponId;
        $userId     = $request->userId;
        $userSource = $request->userSource;
        $status     = 6;
        $action     = 9;

        // approve request
        DB::beginTransaction();

        try {

            $coupon = new Coupon;

            $coupon->updateStatus([
                'couponId'   => $couponId,
                'userId'     => $userId,
                'userSource' => $userSource,
                'status'     => $status,
                'updateDate' => Carbon::now()
            ]);
            
            $timeline = new Timeline;

            $timeline->saveTimeline([
                'coupon_id'   => $couponId,
                'action_id'   => $action,
                'user_id'     => $userId,
                'created_at'  => Carbon::now(),
                'user_source' => $userSource
            ]);
            
            DB::commit();
            return [
                'message'  => 'Coupon request has been rejected.',
                'status'   => 'rejected',
                'error'    => false
            ];
        } catch(\Exception $e) {
            DB::rollBack();
            return [
                'message'  => 'Error :' . $e,
                'error'    => true
            ];

        }
      
    }

    public function getByCoupon($couponId){
        $approval = new Approval;
        $data = $approval->getByCoupon($couponId);
        return $data;
    }

    public function approve($approvalId){
        $approval = new Approval;
        $coupon = new Coupon;

        $approval_data = Approval::where('id', $approvalId)->first();

        
        $check_approval = $approval->checkCoupon([
            'module_reference_id' => $approval_data->module_reference_id,
            'module_id' => $approval_data->module_id,
            'hierarchy' => $approval_data->hierarchy
        ]);

        if(!empty($check_approval)){
            return [
                'module_reference_id' => $approval_data->module_reference_id,
                'template' => 'warning',
                'image_url' => url('/') . '/public/images/approval-error.jpg',
                'message' => 'Coupon No.' . '<strong>' . $approval_data->module_reference_id . '</strong> is already approved.'
            ];
        }
  
        // check if it is his/her turn on approval
        $couponDetails = $coupon->getDetails($approval_data->module_reference_id);
    
        if($couponDetails->current_approval_hierarchy != $approval_data->hierarchy){
           
            return [
                'module_reference_id' => $approval_data->module_reference_id,
                'template' => 'warning',
                'image_url' => url('/') . '/public/images/approval-error.jpg',
                'message' => 'It is not yet your turn to approve Coupon No. <strong>' . $approval_data->module_reference_id . '</strong>.'
            ];
        }

        $approval->updateStatus([
            'approval_id'   => $approvalId,
            'status'        => 2, // approved
            'date_approved' => Carbon::now(),
            'remarks'       => ''
        ]);
        

        // get max approver
        $max_hierarchy = $approval->getMaxApproval([
            'module_reference_id' => $approval_data->module_reference_id,
            'module_id' => $approval_data->module_id,
        ]);
        

        // update next approver is already the last, update the coupon to approved
       
        if($approval_data->hierarchy == $max_hierarchy->hierarchy){
          
            $coupon->updateStatus([
                'couponId' => $approval_data->module_reference_id,
                'status' => 2,
                'updateDate' => Carbon::now()
            ]);

            // add to timeline and email to dealer and approvers
            $timeline = new Timeline;

            $timeline->saveTimeline([
                'coupon_id'  => $approval_data->module_reference_id,
                'action_id'  => 2,
                'created_at' => Carbon::now(),
                'message'    => 'Your coupon request has been approved.'
            ]);    
                
        }
        // update the current hierarchy of coupon approver
        else {
            $next_hierarchy = $approval_data->hierarchy + 1;
            $coupon->updateCurrentApprovalHierarchy([
                'couponId' => $approval_data->module_reference_id,
                'next_hierarchy' => $next_hierarchy
            ]);
        }
       


        return [
            'module_reference_id' => $approval_data->module_reference_id,
            'template' => 'approve',
            'image_url' => url('/') . '/public/images/approval-success.gif',
            'message' => 'You have successfully approved Coupon No. <strong>' . $approval_data->module_reference_id . '</strong>!'
        ];
    }

    public function reject($approvalId,$remarks){
        $approval = new Approval;
        $coupon = new Coupon;
        
     //   $approval_data = Approval::where('id', $approvalId)->first();
        
        $approval_data = DB::table('ipc.ipc_vpc_approval vpa')
            ->leftJoin('ipc.ipc_vpc_status st', 'st.id','=','vpa.status')->first();
     
        $check_approval = $approval->checkCoupon([
            'module_reference_id' => $approval_data->module_reference_id,
            'module_id' => $approval_data->module_id,
            'hierarchy' => $approval_data->hierarchy
        ]);
             
        if(!empty($check_approval)){

            return [
                'module_reference_id' => $approval_data->module_reference_id,
                'template' => 'reject-message',
                'message' => 'You have already '.strtolower($approval_data->status).' Coupon No. <strong>' . $approval_data->module_reference_id . '</strong>.',
                'image_url' => url('/') . '/public/images/approval-error.jpg'
            ];
        }

        
        $couponDetails = $coupon->getDetails($approval_data->module_reference_id);
        if($couponDetails->current_approval_hierarchy != $approval_data->hierarchy){
            return [
                'module_reference_id' => $approval_data->module_reference_id,
                'template' => 'warning',
                'image_url' => url('/') . '/public/images/approval-error.jpg',
                'message' => 'It is not yet your turn to reject Coupon No. <strong>' . $approval_data->module_reference_id . '</strong>.'
            ];
        }

 
        // update approval status
        $approval->updateStatus([
            'approval_id'   => $approvalId,
            'status'        => 6, // rejected
            'date_approved' => Carbon::now(),
            'remarks'       => $remarks
        ]);
        
        // update coupon status
        $coupon->updateStatus([
            'couponId' => $approval_data->module_reference_id,
            'status' => 6,
            'updateDate' => Carbon::now()
        ]);
        
        // add to timeline and email to dealer and approvers
        $timeline = new Timeline;

        $timeline->saveTimeline([
            'coupon_id'  => $approval_data->module_reference_id,
            'action_id'  => 9,
            'created_at' => Carbon::now(),
            'message'    => 'Your coupon request has been rejected. Reason is : <strong>' . $remarks .'</strong>'
        ]); 
        
        return [
            'module_reference_id' => $approval_data->module_reference_id,
            'template' => 'reject-message',
            'message' => 'You have successfully rejected Coupon No. <strong>' . $approval_data->module_reference_id . '</strong>!',
            'image_url' => url('/') . '/public/images/approval-success.gif'
        ];
    }


}
