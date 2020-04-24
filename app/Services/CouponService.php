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

            $this->insertDenomination(
                $denominations,
                $couponId,
                $createdBy,
                $userSource
            );

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

    public function getCoupons(){
        $coupon = new Coupon();
        $coupons = $coupon->getCoupons();
        return $coupons;
    }

    public function transformCsNumber($denominations){
        $csNumbers = [];
        foreach($denominations as $amount) {
            foreach($amount['csNumbers'] as $csNumber) {
                array_push($csNumbers, $csNumber['text']);
            }
        }
        return $csNumbers;
    }

    public function updateCoupon($request){
        $dealer        = $request->dealerId;
        $denominations = $request->denominations;
        $createdBy     = $request->createdBy;
        $userSource    = $request->userSource;
        $couponId      = $request->couponId;
        $csNumbers     = $this->transformCsNumber($denominations);

        $serialNumber = new SerialNumber;
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
            
            $coupon                     = \App\Models\Coupon::find($couponId);
            $coupon->dealer_id          = $dealer;
            $coupon->updated_by         = $createdBy;
            $coupon->update_user_source = $userSource;
            $coupon->save();

            // delete previous data
            $this->deletePrevDenomination($couponId);
            
            // insert denomination
            $this->insertDenomination(
                $denominations,
                $couponId,
                $createdBy,
                $userSource
            );


            $timeline              = new Timeline;
            $timeline->coupon_id   = $couponId;
            $timeline->action_id   = 10;              // updated
            $timeline->user_id     = $createdBy;
            $timeline->user_source = $userSource;
            $timeline->created_at  = Carbon::now();
            $timeline->save();

            DB::commit();

            return response()->json([
                'message'  => 'Coupon request has been updated.',
                'couponId' => $couponId,
                'error'    => false
            ],200);

        } catch(\Exception $e) {
            DB::rollBack();
            return $e;
            return response()->json([
                'message'  => $e,
                'error'    => true
            ],200);
        }

    }

    public function deletePrevDenomination($couponId){
        $denomination = new Denomination;
        $csNum = new CSNumber;
        $oldDenomination = $denomination->getByCoupon($couponId);
        foreach($oldDenomination as $row) {

            // delete cs numbers first
            $csNum->deleteByDenomination($row['id']);
            
            // delete denomination now
            $denomination->deleteByCoupon($couponId);
        }  
    }

    public function insertDenomination($denominations,$couponId,$createdBy,$userSource){
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
    }

    public function issueCoupon($request){
        $couponId   = $request->couponId;
        $userId     = $request->userId;
        $userSource = $request->userSource;
        $status     = $request->status;
        $action     = $request->action;

        // approve request
        DB::beginTransaction();

        try {

            $coupon = new Coupon;

            $coupon->updateStatus([
                'couponId'   => $couponId,
                'userId'     => $userId,
                'userSource' => $userSource,
                'status'     => $status,
                'updateDate' => Carbon::now()
            ]);
            
            $timeline = new Timeline;

            $timeline->saveTimeline([
                'coupon_id'   => $couponId,
                'action_id'   => $action,
                'user_id'     => $userId,
                'created_at'  => Carbon::now(),
                'user_source' => $userSource
            ]);
            
            DB::commit();
            return [
                'message'  => 'Coupon request has been issued.',
                'error'    => false
            ];
        } catch(\Exception $e) {
            DB::rollBack();
            return [
                'message'  => 'Error :' . $e,
                'error'    => true
            ];

        }
      

    }

    public function receiveFleetCoupon($request){
        $couponId   = $request->couponId;
        $userId     = $request->userId;
        $userSource = $request->userSource;
        $status     = $request->status;
        $action     = $request->action;

        // approve request
        DB::beginTransaction();

        try {

            $coupon = new Coupon;

            $coupon->updateStatus([
                'couponId'   => $couponId,
                'userId'     => $userId,
                'userSource' => $userSource,
                'status'     => $status,
                'updateDate' => Carbon::now()
            ]);
            
            $timeline = new Timeline;

            $timeline->saveTimeline([
                'coupon_id'   => $couponId,
                'action_id'   => $action,
                'user_id'     => $userId,
                'created_at'  => Carbon::now(),
                'user_source' => $userSource
            ]);
            
            DB::commit();
            return [
                'message'  => 'Coupon request has been issued.',
                'error'    => false
            ];
        } catch(\Exception $e) {
            DB::rollBack();
            return [
                'message'  => 'Error :' . $e,
                'error'    => true
            ];

        }
      

    }

    public function receiveDealerCoupon($request){
        $couponId   = $request->couponId;
        $userId     = $request->userId;
        $userSource = $request->userSource;
        $status     = $request->status;
        $action     = $request->action;

        // approve request
        DB::beginTransaction();

        try {

            $coupon = new Coupon;

            $coupon->updateStatus([
                'couponId'   => $couponId,
                'userId'     => $userId,
                'userSource' => $userSource,
                'status'     => $status,
                'updateDate' => Carbon::now()
            ]);
            
            $timeline = new Timeline;

            $timeline->saveTimeline([
                'coupon_id'   => $couponId,
                'action_id'   => $action,
                'user_id'     => $userId,
                'created_at'  => Carbon::now(),
                'user_source' => $userSource
            ]);
            
            DB::commit();
            return [
                'message'  => 'Coupon request has been issued.',
                'error'    => false
            ];
        } catch(\Exception $e) {
            DB::rollBack();
            return [
                'message'  => 'Error :' . $e,
                'error'    => true
            ];
        }
    }
  

}
