<?php

namespace App\Services;

use DB;
use Carbon\Carbon;
use Excel;
use App\Models\Voucher;
use App\Models\ClaimHeader;
use App\Models\ClaimLine;
use App\Models\SerialNumber;
use App\Models\Approver;
use App\Models\Approval;
use App\Services\ApprovalService;
use Illuminate\Support\Facades\Storage;
use File;

class ClaimRequestService {


    public function savePayment($request){
        $user         = auth()->user();
        $vouchers     = json_decode($request->vouchers);
        $dealer_id    = $request->dealer_id;
        $coupon_type  = $request->coupon_type;
        $vehicle_type = $request->vehicle_type;
        
        $voucher     = new Voucher;
        $claimHeader = new ClaimHeader;
        $claimLine   = new ClaimLine;

        $filename         = "";
        $attachment_path  = "";
        $origFilename     = "";
        $symlink_dir      = "";
        $attachment       = $request->attachment;

        $voucherCodes = collect($vouchers)->pluck('voucher_code')->toArray();
        
       

        $invalidVoucherCodes = $voucher->getInvalidVouchers($voucherCodes);
        $claimedVoucherCodes = $claimLine->getClaimedVouchers($voucherCodes);
        
        $csNumbers = [];

      
        foreach($vouchers as $row){
            if($row->cs_number != ""){
                array_push($csNumbers, $row->cs_number);
            }
        }

   
        if(!empty($_FILES)){
            
            $filename = Carbon::now()->timestamp . '.' . $attachment->getClientOriginalExtension();
            $origFilename = $attachment->getClientOriginalName();
            $symlink_dir = 'public/storage/uploads/';
            $attachment_path = Storage::putFileAs(
                'public/uploads', $attachment, $filename
            );
        }

        
        $invalidCSNumbers = [];

        if(count($csNumbers) > 0){
            $serialNumber = new SerialNumber;
            $invalidCSNumbers = $serialNumber->getInvalidCsNumbers($csNumbers);
        }

        if(!empty($invalidVoucherCodes) || !empty($claimedVoucherCodes) || !empty($invalidCSNumbers)) {
            return [
                'message'             => 'Payment request fail to upload.',
                'invalidVoucherCodes' => implode(", ", $invalidVoucherCodes),
                'claimedVoucherCodes' => implode("," , $claimedVoucherCodes),
                'invalidCSNumbers' => implode("," , $invalidCSNumbers),
                'error'               => true,
                'errors' => []
            ];
        }

         /* Validate voucher codes per amount and cs number validity */
        $errors = [];
       
        foreach($vouchers as $row){
            // voucher details
            $voucherDetails = $voucher->getByCode($row->voucher_code);
            
            if($row->amount > $voucherDetails[0]->amount ){
                array_push($errors,[
                    'voucher_code' => $row->voucher_code,
                    'message' => 'Invalid amount specified.'
                ]);
            }
         
        }

        if(count($errors) > 0){
            return [
                'message'             => 'Claim request failed.',
                'invalidVoucherCodes' => implode(", ", $invalidVoucherCodes),
                'claimedVoucherCodes' => implode("," , $claimedVoucherCodes),
                'error'               => true,
                'errors'              => $errors
            ];
        }


       

        DB::beginTransaction();
        try {
         
            $claimHeaderId = $claimHeader->insertHeader([
                'status'             => 1,
                'created_by'         => $user->user_id,
                'create_user_source' => $user->user_source_id,
                'creation_date'      => Carbon::now(),
                'coupon_type'        => $coupon_type,
                'dealer_id'          => $dealer_id,
                'vehicle_type'       => $vehicle_type,
                'attachment'         => $symlink_dir . $filename,
                'filename'           => $origFilename,
            ]);
            
        

                
          
            $approvalService = new ApprovalService;
            $approvers = $approvalService->getClaimApprovers(
                $coupon_type, 
                2,// claim request 
                $user->user_type_id, 
                $vehicle_type,
                $claimHeaderId
            );
            $approval = new Approval;
            $approval->batchInsert($approvers);
            
            $lineParams = [];
          
            foreach($vouchers as $row){
              
                array_push($lineParams,[
                    'claim_header_id'    => $claimHeaderId,
                    'voucher_code'       => $row->voucher_code,
                    'voucher_id'         => $row->voucher_no,
                    'cs_number'          => $row->cs_number,
                    'service_invoice_no' => $row->service_invoice_number,
                    'service_date'       => $row->raw_service_date,
                    'plate_no'           => $row->plate_no,
                    'customer_name'      => $row->customer_name,
                    'amount'             => $row->amount,
                    'created_by'         => $user->user_id,
                    'create_user_source' => $user->user_source_id,
                    'creation_date'      => Carbon::now(),
                    'claim_id'           => $row->id
                ]);
            }

       
          //  return $lineParams;
            $claimLine->batchInsert($lineParams);
            
            DB::commit();
        
            return [
                'message'         => 'Claim Request has been created.',
                'error'           => false,
                'claimHeaderId' => $claimHeaderId
            ];

            
        
    
        } catch(\Exception $e) {
            DB::rollBack();
            return $e;
        }
        
    }

   
    public function getClaimRequests($request){
        $claimHeader = new claimHeader();
       
        $params = [
            'userId'   => $request->userId,
            'sourceId' => $request->sourceId,
        ];
        return $claimHeader->getByUser($params);
        /* if($request->userType == 49 || $request->userType == 44){ // administrator
            return $paymentHeader->getHeaders($status);
        }
        else {
           
            
        } */
    }

    public function getLines($request){
        $claimLine = new ClaimLine();
        return $claimLine->get($request->claimHeaderId);
    }

    public function getHeader($request){
        $claimHeader = new ClaimHeader();
        return $claimHeader->get($request->claimHeaderId);
    }

    public function approvalDetails($request){
        $approval_id = $request->approval_id;
        $approvalDetails = Approval::where('id',$approval_id)->get();
        $claimHeader = new ClaimHeader;
        $header = $claimHeader->get($approvalDetails[0]->module_reference_id);
        $claimLine = new ClaimLine;
        $lines = $claimLine->get($header->id);
     
        $data = [
            'approval_details' => [],
            'header'           => $header,
            'lines'            => $lines,
            'approve_link'     => url('/') . '/api/claim-request/approve/' . $approval_id,
            'reject_link'      => url('/') . '/api/claim-request/reject/' . $approval_id,
        ];

        return [
            'data' => $data,
            'view' => 'email/claim-details'
        ];
    }

    public function approve($request){
        $approval_id = $request->approval_id;
        $approval_data = Approval::where('id',$approval_id)->first();
        $claimHeader = new ClaimHeader;
        $header = $claimHeader->get($approval_data->module_reference_id);
        
        if($approval_data->status != 1){
            $data = [
                'message' => 'It seems like you have already approved Claim request no.' . $approval_data->module_reference_id,
                'image_url' => url('/') . '/public/images/approval-error.jpg',
            ];

            return [
                'data' => $data,
                'view' => 'claim-message'
            ];
        }

        $approval = new Approval;
        DB::beginTransaction();
        try {
            // update status of approval
            $approval->updateStatus([
                'approval_id'   => $approval_id,
                'status'        => 2, // approved
                'date_approved' => Carbon::now(),
                'remarks'       => ''
            ]);

            // check if already max approver
            $max_hierarchy = $approval->getMaxApproval([
                'module_reference_id' => $approval_data->module_reference_id,
                'module_id' => $approval_data->module_id,
            ]);

            if($approval_data->hierarchy == $max_hierarchy->hierarchy){
                // update status of claim request
                $claimHeader->updateStatus([
                    'claim_header_id'    => $header->id,
                    'status'             => 2,
                    'updated_by'         => -1,
                    'update_user_source' => -1
                ]);
            }
            else {
                $next_hierarchy = $header->current_approval_hierarchy + 1;
                $claimHeader->updateCurrentApprovalHierarchy([
                    'claim_header_id' => $header->id,
                    'next_hierarchy' => $next_hierarchy
                ]);
            }

            DB::commit();

            $data = [
                'message' => 'You have successfully approved Claim request no. ' . $approval_data->module_reference_id,
                'image_url' => url('/') . '/public/images/approval-success.gif',
            ];

            return [
                'data' => $data,
                'view' => 'claim-message'
            ];

        } catch(\Exception $e){
            DB::rollback();
            return $e;
        }

        
    }

    public function getApprovers($request){
        $approval = new Approval;
        return $approval->getByClaimRequest($request->claimHeaderId);
    }

    public function rejectForm($request){
        $approval_id = $request->approval_id;
        $reject_url = url('/') . '/api/claim-request/reject-state';
        $data = [
            'approval_id' => $approval_id,
            'reject_url' => $reject_url
        ];

        return [
            'data' => $data,
            'view' => 'reject-claim-form'
        ];
    }

    public function reject($request){
        $approval_id = $request->approval_id;
        $approval_data = Approval::where('id',$approval_id)->first();
        $remarks = $request->remarks;
        if($approval_data->status != 1){
            $data = [
                'message' => 'It seems like you have already rejected Claim request no.' . $approval_data->module_reference_id,
                'image_url' => url('/') . '/public/images/approval-error.jpg',
            ];

            return [
                'data' => $data,
                'view' => 'claim-message'
            ];
        }

        $claimHeader = new ClaimHeader;
        $approval = new Approval;
        DB::beginTransaction();
        try {
            // update status of approval
            $approval->updateStatus([
                'approval_id'   => $approval_id,
                'status'        => 6, // rejected
                'date_approved' => Carbon::now(),
                'remarks'       => $remarks
            ]);
            
            $claimHeader->updateStatus([
                'claim_header_id'    => $approval_data->module_reference_id,
                'status'             => 6,
                'updated_by'         => -1,
                'update_user_source' => -1
            ]);

            DB::commit();

            $data = [
                'message' => 'You have successfully rejected Claim request no. ' . $approval_data->module_reference_id,
                'image_url' => url('/') . '/public/images/approval-success.gif',
            ];

            return [
                'data' => $data,
                'view' => 'claim-message'
            ];

        } catch(\Exception $e){
            DB::rollback();
            return $e;
        }

        
    }
     public function cancel($request){

        $user = auth()->user();
        DB::beginTransaction();
        try{

            $claimHeader = ClaimHeader::findOrFail($request->claimHeader['id']);
            $claimHeader->status = 4;
            $claimHeader->updated_by = $user->user_id;
            $claimHeader->update_user_source = $user->user_source_id;
            $claimHeader->save();
            
            DB::table('ipc.ipc_vpc_claim_lines')
                ->where('claim_header_id', $request->claimHeader['id'])
                ->delete();

            DB::commit();

            return [
                'message' => 'Claim request has been cancelled.',
                'status' => 'cancelled'
            ];

        }catch(Exception $e){
            DB::rollBack();
        }
        
        
       /*  $claimHeader->updateStatus([
            'status'             => 4,
            'updated_by'         => $user->user_id,
            'update_user_source' => $user->user_source_id,
            'payment_header_id'    => $claimHeader->paymentHeaderId
        ]); */

        return [
            'message' => 'Payment request has been cancelled.'
        ];
    }
    /*
    public function updateStatus($request){
        $paymentHeader = new PaymentHeader();
        $paymentHeader->updateStatus([
            'status'             => $request->status,
            'updated_by'         => $request->userId,
            'update_user_source' => $request->userSource,
            'payment_header_id'    => $request->paymentHeaderId
        ]);

        return [
            'message' => 'Payment request has been ' . $request->statusVerb . ".",
            'status' => $request->statusVerb
        ];
    }
 */
}