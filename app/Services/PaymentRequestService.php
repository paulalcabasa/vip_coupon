<?php

namespace App\Services;

use DB;
use Carbon\Carbon;
use Excel;
use App\Models\Voucher;
use App\Models\PaymentHeader;
use App\Models\PaymentLine;
use App\Models\SerialNumber;

class PaymentRequestService {


    public function savePayment($request){
        $user = $request->user;
        $vouchers = $request->vouchers;
        
        $voucher = new Voucher;
        $paymentHeader = new PaymentHeader;
        $paymentLine = new PaymentLine;

        $voucherCodes = collect($vouchers)->pluck('voucher_code')->toArray();
        
     
        $invalidVoucherCodes = $voucher->getInvalidVouchers($voucherCodes);
        $claimedVoucherCodes = $paymentLine->getClaimedVouchers($voucherCodes);
        
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
                'message'             => 'Payment request fail to upload.',
                'invalidVoucherCodes' => implode(", ", $invalidVoucherCodes),
                'claimedVoucherCodes' => implode("," , $claimedVoucherCodes),
                'error'               => true,
                'errors'              => $errors
            ];
        }



        DB::beginTransaction();
        try {
         
            $paymentHeaderId = $paymentHeader->insertHeader([
                'status'             => 1,
                'created_by'         => $user['user_id'],
                'create_user_source' => $user['user_source_id'],
                'creation_date'      => Carbon::now()
            ]);
            $paymentLineParams = [];
          
            foreach($vouchers as $row){
              
                array_push($paymentLineParams,[
                    'payment_header_id'  => $paymentHeaderId,
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
            $paymentLine->batchInsert($paymentLineParams);
        
            DB::commit();

            return [
                'message'         => 'Payment Request has been created.',
                'error'           => false,
                'paymentHeaderId' => $paymentHeaderId
            ];

            
        
    
        } catch(\Exception $e) {
            DB::rollBack();
            return $e;
        }
        
    }

    public function handlePaymentSave($request){
        $user = $request->userId;
        $userSource = $request->userSource;
        $data = Excel::load($request->voucher_file)->get();

        $voucher = new Voucher;
        $paymentHeader = new PaymentHeader;
        $paymentLine = new PaymentLine;

        $excelHeaders = $data->first()->keys()->toArray();

        $requiredHeaders = array('voucher_code','amount','cs_number','service_invoice_no','service_date','customer_name','dealer_code');
        
        if($excelHeaders !== $requiredHeaders) {
            return [
                'message'             => 'Payment request fail to upload because of invalid headers, please refer to the voucher template.',
                'invalidVoucherCodes' => '',
                'error'               => true
            ];
        }

        $voucherCodes = collect($data)->pluck('voucher_code')->toArray();
      
        $invalidVoucherCodes = $voucher->getInvalidVouchers($voucherCodes);
        $claimedVoucherCodes = $paymentLine->getClaimedVouchers($voucherCodes);
        
        $csNumbers = [];
         /* check validity of cs numbers */
        foreach($data as $row){
            if($row->cs_number != ""){
                array_push($csNumbers, $row->cs_number);
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
        
        foreach($data as $row){
            // voucher details
            $voucherDetails = $voucher->getByCode($row->voucher_code);
            
            if($voucherDetails[0]->voucher_code != $row->voucher_code || $voucherDetails[0]->amount != $row->amount){
                array_push($errors,[
                    'voucher_code' => $row->voucher_code,
                    'message' => 'Invalid amount specified.'
                ]);
            }
         
        }

        if(count($errors) > 0){
            return [
                'message'             => 'Payment request fail to upload.',
                'invalidVoucherCodes' => implode(", ", $invalidVoucherCodes),
                'claimedVoucherCodes' => implode("," , $claimedVoucherCodes),
                'error'               => true,
                'errors'              => $errors
            ];
        }
        

        DB::beginTransaction();
        try {

            $paymentHeaderId = $paymentHeader->insertHeader([
                'status'             => 1,
                'created_by'         => $user,
                'create_user_source' => $userSource,
                'creation_date'      => Carbon::now()
            ]);
            $paymentLineParams = [];

            foreach($data as $row){
                $voucherDetails = $voucher->getByCode($row->voucher_code);
            
                array_push($paymentLineParams,[
                    'payment_header_id'  => $paymentHeaderId,
                    'voucher_code'       => $row->voucher_code,
                    'voucher_id'         => $voucherDetails[0]->id,
                    'cs_number'          => $row->cs_number,
                    'service_invoice_no' => $row->service_invoice_no,
                    'service_date'       => $row->service_date,
                    'dealer_code'        => $row->dealer_code,
                    'customer_name'      => $row->customer_name,
                    'created_by'         => $user,
                    'create_user_source' => $userSource,
                    'creation_date'      => Carbon::now()
                ]);
            }
            $paymentLine->batchInsert($paymentLineParams);

            DB::commit();

            return [
                'message'         => 'Payment Request has been created.',
                'error'           => false,
                'paymentHeaderId' => $paymentHeaderId
            ];
        
    
        } catch(\Exception $e) {
            DB::rollBack();
            return $e;
        }
        
    }

    public function getPayments($request){
        $paymentHeader = new PaymentHeader();
        $status = $request->status;

        if($request->userType == 49 || $request->userType == 44){ // administrator
            return $paymentHeader->getHeaders($status);
        }
        else {
            $params = [
                'userId' => $request->userId,
                'sourceId' => $request->sourceId,
            ];
            return $paymentHeader->getByUser($status, $params);
        }
        
        
    }

    public function getLines($request){
        $paymentLine = new PaymentLine();
        return $paymentLine->get($request->paymentHeaderId);
    }

    public function getHeader($request){
        $paymentHeader = new PaymentHeader();
        return $paymentHeader->get($request->paymentHeaderId);
    }

    public function cancelPayment($request){
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

}