<?php

namespace App\Services;
use DB;
use Carbon\Carbon;
use App\Models\Coupon;
use App\Models\Denomination;
use App\Models\CSNumber;
use App\Models\SerialNumber;
use App\Models\Timeline;

class CouponService {

    public function saveCoupon($request){
        $dealer = $request->dealerId;
        $denominations = $request->denominations;
        $createdBy = $request->createdBy;
        $userSource = $request->userSource;
        $csNumbers = [];

        $serialNumber = new SerialNumber;

        foreach($denominations as $amount) {
            foreach($amount['csNumbers'] as $csNumber) {
                array_push($csNumbers, $csNumber['text']);
            }
        }

        $invalidCsNumbers = $serialNumber->getInvalidCsNumbers($csNumbers);
        
        if(!empty($invalidCsNumbers)){
            return response()->json([
                'message'            => 'There are CS Numbers that does exist',
                'invalid_cs_numbers' => $invalidCsNumbers,
                'error'              => true
            ],200);
        }

        DB::beginTransaction();

        try {
            $coupon = new Coupon;
            $coupon->dealer_id = $dealer;
            $coupon->created_by = $createdBy;
            $coupon->create_user_source = $userSource;
            $coupon->save();
            $couponId = $coupon->id;

            foreach($denominations as $amount) {
                $denomination                     = new Denomination;
                $denomination->coupon_id          = $couponId;
                $denomination->amount             = $amount['amount'];
                $denomination->quantity           = $amount['quantity'];
                $denomination->created_by         = $createdBy;
                $denomination->create_user_source = $userSource;
                $denomination->save();
                $denominationId = $denomination->id;

                $csNumberParams = [];

                foreach($amount['csNumbers'] as $csNumber) {
                    array_push($csNumberParams,
                        [
                            'denomination_id'    => $denominationId,
                            'cs_number'          => $csNumber['text'],
                            'created_by'         => $createdBy,
                            'creation_date'      => Carbon::now(),
                            'create_user_source' => $userSource
                        ]
                    );
                }
                $csNum = new CSNumber;
                $csNum->batchInsert($csNumberParams);
                
            }

            $timeline              = new Timeline;
            $timeline->coupon_id   = $couponId;
            $timeline->action_id   = 8;              // created
            $timeline->user_id     = $createdBy;
            $timeline->user_source = $userSource;
            $timeline->created_at  = Carbon::now();
            $timeline->save();

            DB::commit();

            return response()->json([
                'message'  => 'Coupon request has been saved.',
                'couponId' => $couponId,
                'error'    => false
            ],200);

        } catch(\Exception $e) {
            DB::rollBack();
            return $e;
        }
    }

    public function getDetails($couponId){
        $coupon = new Coupon;
        $couponDetails = $coupon->getDetails($couponId);
        
        if(empty($couponDetails)){
            return response()->json([
                'message' => 'Coupon No. does not exist!'
            ],404);
        }

        return $couponDetails;
    }
}
