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
        $voucherCodes = collect($data)->pluck('voucher_code')->toArray();
        $voucher = new Voucher;
        $invalidVoucherCodes = $voucher->getInvalidVouchers($voucherCodes);
       
        if(!empty($invalidVoucherCodes)) {
            return [
                'message'             => 'Payment request fail to upload because there are voucher codes that does not exist.',
                'invalidVoucherCodes' => implode(", ", $invalidVoucherCodes),
                'error'               => true
            ];
        }

        DB::beginTransaction();
        try {

            $paymentHeader = new PaymentHeader;

            $paymentLine = new PaymentLine;

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
                'message'  => 'Payment Request has been created.',
                'error'    => false
            ];
        
    
        } catch(\Exception $e) {
            DB::rollBack();
            return $e;
        }
        
    }

}