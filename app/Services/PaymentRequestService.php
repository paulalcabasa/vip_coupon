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

            foreach($data as $row){
                $paymentLine->insertLine([
                    'payment_header_id' => $paymentHeaderId,
                    'voucher_code'      => $row->voucher_code,
                    'created_by'         => $user,
                    'create_user_source' => $userSource,
                    'creation_date'      => Carbon::now()
                ]);
            }

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

    public function getPayments(){
        $paymentHeader = new PaymentHeader();
        return $paymentHeader->getHeaders();
    }

    public function getLines($request){
        $paymentLine = new PaymentLine();
        return $paymentLine->get($request->paymentHeaderId);
    }

    public function getHeader($request){
        $paymentHeader = new PaymentHeader();
        return $paymentHeader->get($request->paymentHeaderId);
    }

}