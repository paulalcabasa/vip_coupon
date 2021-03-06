<?php

namespace App\Services;
use DB;
use Carbon\Carbon;
use App\Models\Coupon;
use App\Models\Denomination;
use App\Models\CSNumber;
use App\Models\Timeline;
use App\Models\Voucher;
use App\Services\CodeService;
use App\Models\CouponType;

class VoucherService {

    public function getVouchers($couponId){
        $voucher = new Voucher;
        return response()->json($voucher->getByCoupon($couponId),200);
    }

     public function generateVoucher($couponId, $user, $userSource){
  
        $voucher = new Voucher;
        $coupon = new Coupon;

        DB::beginTransaction();

        try {

            // create documents
            $denomination = new Denomination;
            $denominations = $denomination->getByCoupon($couponId);
            $docs = [];
            
            $couponDetails = $coupon->getDetails($couponId);
            $couponType = CouponType::where('id', $couponDetails->coupon_type_id)->first();
      
            foreach($denominations as $row){
                $csNumber = new CSNumber;
                
                $csNumbers = $csNumber->getByDenomination($row->id);
                $csNumbers = collect($csNumbers)->pluck('cs_number')->toArray();
            
                for($i = 1; $i <= $row['quantity']; $i++){
                    
                    do {
                        $voucherCode = CodeService::generate([
                            'length' => 8,
                            'numbers' => true,
                            'letters' => true
                        ]); 
                    } while($voucher->isExist($voucherCode));

                    $control_number = $voucher->generateControlNumber($couponType->sequence_object_name);
                    $voucher->insert([
                        'coupon_id'          => $couponId,
                        'denomination_id'    => $row['id'],
                        'amount'             => $row['amount'],
                        'cs_number'          => array_shift($csNumbers),
                        'status'             => 12,
                        'created_by'         => $user,
                        'create_user_source' => $userSource,
                        'creation_date'      => Carbon::now(),
                        'print_date'         => Carbon::now(),
                        'voucher_code'       => $voucherCode,
                        'control_number'     => $control_number,
                        'expiration_date'    => $couponDetails->coupon_expiry_date
                    ]);
                }
            }

            // update status of coupon
            $coupon->updateStatus([
                'couponId'   => $couponId,
                'status'     => 12, // generated state
                'updateDate' => Carbon::now()
            ]);
            
            // insert timeline
            $timeline = new Timeline;

            $timeline->saveTimeline([
                'coupon_id'   => $couponId,
                'action_id'   => 12, // printed action
                'user_id'     => $user,
                'message'     => "Voucher has been generated",   
                'created_at'  => Carbon::now(),
                'user_source' => $userSource,
                'mail_flag'   => 'Y'
            ]);


            DB::commit();


        } catch(\Exception $e) {
            DB::rollBack();
            return $e;
        }
    }

 
  
}

