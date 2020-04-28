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

class VoucherService {


    public function generate($request){

        $couponId   = $request->couponId;
        $user       = $request->userId;
        $userSource = $request->userSource;

        $voucher = new Voucher;
      
        if(count($voucher->getByCoupon($couponId)) > 0){
            return response()->json([
                'message'  => 'Voucher already exists!',
                'couponId' => $couponId,
                'error'    => true
            ],200);
        }


        DB::beginTransaction();

        try {

            // create documents
            $denomination = new Denomination;
            $denominations = $denomination->getByCoupon($couponId);
            $docs = [];

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

                    $voucher->insert([
                        'coupon_id'          => $couponId,
                        'denomination_id'    => $row['id'],
                        'amount'             => $row['amount'],
                        'cs_number'          => array_shift($csNumbers),
                        'status'             => 3,
                        'created_by'         => $user,
                        'create_user_source' => $userSource,
                        'creation_date'      => Carbon::now(),
                        'print_date'         => Carbon::now(),
                        'voucher_code'       => $voucherCode
                    ]);
                }
            }


           

            // update status of coupon
            $coupon = new Coupon;

            $coupon->updateStatus([
                'couponId'   => $couponId,
                'userId'     => $user,
                'userSource' => $userSource,
                'status'     => 3, // printed state
                'updateDate' => Carbon::now()
            ]);
            
            // insert timeline
            $timeline = new Timeline;

            $timeline->saveTimeline([
                'coupon_id'   => $couponId,
                'action_id'   => 2, // printed action
                'user_id'     => $user,
                'created_at'  => Carbon::now(),
                'user_source' => $userSource
            ]);


            DB::commit();

            return response()->json([
                'message'  => 'Voucher has been generated and ready for printing',
                'couponId' => $couponId,
                'error'    => false
            ],200);

        } catch(\Exception $e) {
            DB::rollBack();
            return $e;
        }
    }


    public function getVouchers($couponId){
        $voucher = new Voucher;
        return response()->json($voucher->getByCoupon($couponId),200);
    }

    public function generateVoucherCode($strength = 8) {
        $input = '0123456789BCDFGHJKLMNPQRSTVWXYZ';
        $input_length = strlen($input);
        $random_string = '';
        for($i = 0; $i < $strength; $i++) {
            $random_character = $input[mt_rand(0, $input_length - 1)];
            $random_string .= $random_character;
        }
        
        return $random_string;
    }
  
}

