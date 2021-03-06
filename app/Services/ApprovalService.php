<?php

namespace App\Services;

use DB;
use App\Models\Coupon;
use App\Models\Timeline;
use App\Models\Approver;
use App\Models\Approval;
use Carbon\Carbon;
use App\Services\VoucherService;
use App\Models\DealerApprover;

class ApprovalService {
    
    public function getApproval(){
        $coupon = new Coupon;
        $pending = $coupon->getPending();
        return $pending;
    }

    public function getByCoupon($couponId){
        $approval = new Approval;
        $data = $approval->getByCoupon($couponId);
        return $data;
    }

    public function getByClaimRequest($claimHeaderId){
        $approval = new Approval;
        $data = $approval->getByClaimRequest($claimHeaderId);
        return $data;
    }

    public function approve($approvalId){
        $approval = new Approval;
        $coupon = new Coupon;
        
        DB::beginTransaction();

        try {

            //$approval_data = Approval::where('id', $approvalId)->first();
            $approval_data = DB::table('ipc.ipc_vpc_approval vpa')
                ->where('vpa.id',$approvalId)
                ->leftJoin('ipc.ipc_vpc_status st', 'st.id','=','vpa.status')
                ->leftJoin('ipc.ipc_vpc_approvers apr','apr.id','=','vpa.approver_id')
                ->first();
       
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
                    'message' => 'Coupon No.' . '<strong>' . $approval_data->module_reference_id . '</strong> is already <strong>'.$approval_data->status.'</strong>.'
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
                
                // set coupon status as approved
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
                    'message'    => 'Your coupon request has been approved.',
                    'mail_flag'  => 'Y'
                ]);   
                
                // auto generate voucher once approved
                
                // put voucher generation on a job

                /* $voucherService = new VoucherService;
                $voucherService->generateVoucher(
                    $approval_data->module_reference_id, 
                    $approval_data->approver_user_id, 
                    $approval_data->approver_source_id
                ); */
            }
            // update the current hierarchy of coupon approver
            else {
                $next_hierarchy = $approval_data->hierarchy + 1;
                $coupon->updateCurrentApprovalHierarchy([
                    'couponId' => $approval_data->module_reference_id,
                    'next_hierarchy' => $next_hierarchy
                ]);
            }
        
            DB::commit();
            
            return [
                'module_reference_id' => $approval_data->module_reference_id,
                'template' => 'approve',
                'image_url' => url('/') . '/public/images/approval-success.gif',
                'message' => 'You have successfully approved Coupon No. <strong>' . $approval_data->module_reference_id . '</strong>!'
            ];
            
        } catch(\Exception $e) {
            DB::rollBack();
            return $e;
        }        
    }

    public function reject($approvalId,$remarks){
        $approval = new Approval;
        $coupon = new Coupon;
        
     //   $approval_data = Approval::where('id', $approvalId)->first();
        
        $approval_data = DB::table('ipc.ipc_vpc_approval vpa')
            ->where('vpa.id',$approvalId)
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

    public function resend($request){

        $coupon_id = $request->coupon_id;
        $user = $request->user;

        $coupon = new Coupon;

        $couponDetails = $coupon->getDetails($coupon_id);

        if($couponDetails->status_id == 12){
            return [
                'message' => 'Coupon is already approved, email has not been resent.'
            ];
        }

        DB::beginTransaction();
        
        try {
            // update approval status
            $approval = new Approval;
            $approvers = $approval->getByCouponHierarchy([
                'module_reference_id' => $coupon_id,
                'module_id' => 1, // coupon module
                'hierarchy' => $couponDetails->current_approval_hierarchy
            ]);
            
   
            
            $approver_emails = "";
            foreach($approvers as $row){
            
                $approver =  Approval::find($row->approval_id);
                $approver->mail_sent_flag = 'N';
                $approver->date_mail_sent = NULL;
                $approver->updated_at = NULL;
                $approver->save();
                $approver_emails .= $row->email_address . ';';
               
            }
         
            // add to timeline
            $timeline = new Timeline;
            $params = [
                'coupon_id'  => $coupon_id,
                'action_id'  => 12,
                'created_at' => Carbon::now(),
                'message'    => 'Email has been to resent to <strong>' . $approver_emails . '</strong> by ' . $user['first_name'] . ' ' . $user['last_name']
            ];

            $timeline->saveTimeline($params); 

            DB::commit();
            return [
                'message' => 'Email to approver has been resent!'
            ];
        } catch(\Exception $e) {
            DB::rollBack();
            return [
                'message'  => 'Error :' . $e,
                'error'    => true
            ];

        }

    }

    public function getCouponApprovers($coupon_type, $module_id, $user_type_id, $vehicle_type, $coupon_id, $dealer_id){
        $params = [];

        if($vehicle_type == "LCV"){
            $approvers = Approver::where([
                ['coupon_type_id', $coupon_type], 
                ['module_id' , $module_id],
                ['user_type_id', $user_type_id],
                ['vehicle_type', $vehicle_type]
            ]) ->orderBy('hierarchy', 'asc')->get();
        }
        else if ($vehicle_type == "CV"){
            $approvers = Approver::where([
                ['coupon_type_id', $coupon_type], 
                ['module_id' , $module_id],
                ['user_type_id', $user_type_id],
                ['vehicle_type', $vehicle_type]
            ])
            ->where("hierarchy", ">", 1)
            ->orderBy('hierarchy', 'asc')->get();

            // get tier 1 approver
            $dealerSalesAdmin = Approver::where([
                ['coupon_type_id', $coupon_type], 
                ['module_id' , $module_id],
                ['user_type_id', $user_type_id],
                ['vehicle_type', $vehicle_type],
                ['hierarchy', 1]
            ])->get();

         
             
            $dealerCoveredArr = [];
            foreach($dealerSalesAdmin as $dealerSales){
          
                $dealerCovered = DealerApprover::where([
                    ['approver_id', $dealerSales->id],
                    ['dealer_id', $dealer_id],
                ])->get();

     
                foreach($dealerCovered as $row){
                    array_push($params, [
                        'approver_id'         => $dealerSales->id,
                        'module_reference_id' => $coupon_id,
                        'hierarchy'           => $dealerSales->hierarchy,
                        'module_id'           => 1,
                        'status'              => 1,
                        'created_at'          => Carbon::now()
                    ]);
                } 
            }
        }
  
        foreach($approvers as $approver){
            array_push($params, [
                'approver_id'         => $approver->id,
                'module_reference_id' => $coupon_id,
                'hierarchy'           => $approver->hierarchy,
                'module_id'           => 1,
                'status'              => 1,
                'created_at'          => Carbon::now()
            ]);
        }
    
        return $params;

    }

    public function getClaimApprovers($coupon_type, $module_id, $user_type_id, $vehicle_type, $module_reference_id){
        $params = [];
        $approvers = Approver::where([
                ['coupon_type_id', $coupon_type], 
                ['module_id' , $module_id],
                ['user_type_id', $user_type_id],
                ['vehicle_type', $vehicle_type]
            ]) ->orderBy('hierarchy', 'asc')->get();
        foreach($approvers as $approver){
    
            array_push($params, [
                'approver_id'         => $approver->id,
                'module_reference_id' => $module_reference_id,
                'hierarchy'           => $approver->hierarchy,
                'module_id'           => $module_id,
                'status'              => 1,
                'created_at'          => Carbon::now()
            ]);
          
        }
        return $params;

    }

    public function getAllPending($employee_number){
        $approval = new Approval;
        $pending =  $approval->getAllPending($employee_number);
        $base_url = url('/');
        $apiData = [];

        foreach($pending as $row){

            $view_url = "";
            if($row->module_id == 1){
                $view_url = $base_url . "/api/approval/coupon/" . $row->approval_id;
            }
            else if($row->module_id == 2){
                $view_url = $base_url . "/api/claim-request/approval/details/" . $row->approval_id;
            }
            else if($row->module_id == 3){
                $view_url = $base_url . "/api/approval/promo/" . $row->reference_no . '/' . $row->approver_user_id . '/' . $row->approver_source_id;
            }
            array_push($apiData, [
                'description'  => $row->description,
                'reference_no' => $row->reference_no,
                'status'       => $row->status,
                'request_date' => $row->date_requested,
                'requested_by' => $row->requestor,
                'view_url'     => $view_url
            ]);
        }

        return $apiData;
    }

}
