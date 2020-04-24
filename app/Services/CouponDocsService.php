<?php

namespace App\Services;
use DB;
use Carbon\Carbon;
use App\Models\Coupon;
use App\Models\Denomination;
use App\Models\CSNumber;
use App\Models\Timeline;
use App\Models\CouponDocs;

class CouponDocsService {

    public function generate($request){

        $couponId   = $request->couponId;
        $user       = $request->userId;
        $userSource = $request->userSource;

        $couponDocs = new CouponDocs;
      
        if(count($couponDocs->getByCoupon($couponId)) > 0){
            return response()->json([
                'message'  => 'Document already exists!',
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
                    array_push($docs,[
                        'amount'             => $row['amount'],
                        'cs_number'          => array_shift($csNumbers),
                        'status'             => 3,
                        'created_by'         => $user,
                        'create_user_source' => $userSource,
                        'creation_date'      => Carbon::now(),
                        'print_date'         => Carbon::now(),
                        'denomination_id'    => $row['id'],
                        'coupon_id'         => $couponId
                    ]);
                }
            }


            // insert documents
            $couponDocs->batchInsert($docs);

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
                'message'  => 'Documents has been generated and ready for printing',
                'couponId' => $couponId,
                'error'    => false
            ],200);

        } catch(\Exception $e) {
            DB::rollBack();
            return $e;
        }
    }

  
}

