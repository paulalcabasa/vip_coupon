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

class ClaimRequestService {


    public function savePayment($request){
        $user         = $request->user;
        $vouchers     = $request->vouchers;
        $dealer_id    = $request->dealer_id;
        $coupon_type  = $request->coupon_type;
        $vehicle_type = $request->vehicle_type;
        
        $voucher     = new Voucher;
        $claimHeader = new ClaimHeader;
        $claimLine   = new ClaimLine;

        $voucherCodes = collect($vouchers)->pluck('voucher_code')->toArray();
        
        $invalidVoucherCodes = $voucher->getInvalidVouchers($voucherCodes);
        $claimedVoucherCodes = $claimLine->getClaimedVouchers($voucherCodes);
        
        $csNumbers = [];
        foreach($vouchers as $row){
            if($row['cs_number'] != ""){
                array_push($csNumbers, $row['cs_number']);
            }
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
            $voucherDetails = $voucher->getByCode($row['voucher_code']);
            
            if($row['amount'] > $voucherDetails[0]->amount ){
                array_push($errors,[
                    'voucher_code' => $row['voucher_code'],
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
                'created_by'         => $user['user_id'],
                'create_user_source' => $user['user_source_id'],
                'creation_date'      => Carbon::now(),
                'coupon_type'        => $coupon_type,
                'dealer_id'          => $dealer_id,
                'vehicle_type'       => $vehicle_type
            ]);

            $approvalService = new ApprovalService;
            $approvers = $approvalService->getClaimApprovers(
                $coupon_type, 
                2,// claim request 
                $user['user_type_id'], 
                $vehicle_type,
                $claimHeaderId
            );
            $approval = new Approval;
            $approval->batchInsert($approvers);

            $lineParams = [];
          
            foreach($vouchers as $row){
              
                array_push($lineParams,[
                    'claim_header_id'  => $claimHeaderId,
                    'voucher_code'       => $row['voucher_code'],
                    'voucher_id'         => $row['voucher_no'],
                    'cs_number'          => $row['cs_number'],
                    'service_invoice_no' => $row['service_invoice_number'],
                    'service_date'       => $row['service_date'],
                   // 'dealer_code'        => $row->dealer_code,
                    'customer_name'      => $row['customer_name'],
                    'created_by'         => $user['user_id'],
                    'create_user_source' => $user['user_source_id'],
                    'creation_date'      => Carbon::now()
                ]);
            }
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

   /*  public function cancelPayment($request){
        $paymentHeader = new PaymentHeader();
        $paymentHeader->updateStatus([
            'status'             => 4,
            'updated_by'         => $request->userId,
            'update_user_source' => $request->userSource,
            'payment_header_id'    => $request->paymentHeaderId
        ]);

        return [
            'message' => 'Payment request has been cancelled.'
        ];
    }

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