<?php

namespace App\Services;

use DB;
use Carbon\Carbon;
use Excel;
use App\Models\Voucher;
use App\Models\PaymentHeader;
use App\Models\PaymentLine;

class PaymentRequestService {

    public function handlePaymentSave($request){
        $user = $request->userId;
        $userSource = $request->userSource;
        $data = Excel::load($request->voucher_file)->get();

        $voucher = new Voucher;
        $paymentHeader = new PaymentHeader;
        $paymentLine = new PaymentLine;

        $excelHeaders = $data->first()->keys()->toArray();

        $requiredHeaders = array('voucher_code');
        
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

        if(!empty($invalidVoucherCodes) || !empty($claimedVoucherCodes)) {
            return [
                'message'             => 'Payment request fail to upload.',
                'invalidVoucherCodes' => implode(", ", $invalidVoucherCodes),
                'claimedVoucherCodes' => implode("," , $claimedVoucherCodes),
                'error'               => true
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
        return $paymentHeader->getHeaders($status);
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