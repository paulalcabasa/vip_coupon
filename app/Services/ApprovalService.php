<?php

namespace App\Services;

use DB;
use App\Models\Coupon;
use App\Models\Timeline;
use Carbon\Carbon;

class ApprovalService {
    
    public function getApproval(){
        $coupon = new Coupon;
        $pending = $coupon->getPending();
        return $pending;
    }

    public function handleApprove($request){
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
                'message'  => 'Coupon request has been approved.',
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

    public function handleReject($request){
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
                'message'  => 'Coupon request has been rejected.',
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