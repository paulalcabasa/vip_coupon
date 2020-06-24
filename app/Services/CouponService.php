<?php

namespace App\Services;
use DB;
use Carbon\Carbon;
use App\Models\Coupon;
use App\Models\Denomination;
use App\Models\CSNumber;
use App\Models\SerialNumber;
use App\Models\Timeline;
use App\Services\ApprovalService;
use Illuminate\Support\Facades\Storage;
use File;
use App\Models\Approver;
use App\Models\Approval;

class CouponService {

    public function saveCoupon($request){
        $dealer = $request->dealerId;
        $denominations = json_decode($request->denominations);
        $createdBy = $request->createdBy;
        $userSource = $request->userSource;
        $couponType = $request->couponType;
        $description = $request->description;
        $purpose = $request->purpose;
        $promo = $request->promo;
        $emails = json_decode($request->email);
        $attachment = $request->attachment;
        $email_recipients = "";
        $csNumbers = [];
        $filename = "";
        $attachment_path = "";
        $origFilename = "";
        $serialNumber = new SerialNumber;
        $symlink_dir = "";
        foreach($denominations as $amount) {
            foreach($amount->csNumbers as $csNumber) {
                array_push($csNumbers, $csNumber->text);
            }
        }

        foreach($emails as $email) {
            $email_recipients .= $email->text . ';';
        }

        $email_recipients = substr($email_recipients, 0, -1);
        
        if(!empty($_FILES)){
            $filename = Carbon::now()->timestamp . '.' . $attachment->getClientOriginalExtension();
            $origFilename = $attachment->getClientOriginalName();
            $symlink_dir = 'public/storage/uploads/';
            $attachment_path = Storage::putFileAs(
                'public/uploads', $attachment, $filename
            );
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
            $coupon                     = new Coupon;
            $coupon->dealer_id          = $dealer;
            $coupon->created_by         = $createdBy;
            $coupon->create_user_source = $userSource;
            $coupon->coupon_type_id     = $couponType;
            $coupon->promo_id           = $promo;
            $coupon->purpose_id         = $purpose;
            $coupon->description        = $description;
            $coupon->email              = $email_recipients;
            $coupon->attachment         = $symlink_dir . $filename;
            $coupon->filename           = $origFilename;
            $coupon->new_filename       = $filename;
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
            $timeline->message     = 'Coupon has been created';
            $timeline->created_at  = Carbon::now();
            $timeline->save();



            $approvers = Approver::where([
                ['coupon_type_id', $couponType], 
                ['module_id' , 1]
            ])->get();
              
            $approval = new Approval;
            $params = [];
            foreach($approvers as $approver){
                array_push($params, [
                    'approver_id'         => $approver->id,
                    'module_reference_id' => $couponId,
                    'hierarchy'           => $approver->hierarchy,
                    'module_id'           => 1,
                    'status'              => 1,
                    'created_at'          => Carbon::now()
                ]);
            }
            $approval->batchInsert($params);
    
    
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

    public function getCoupons($params){
        $coupon = new Coupon();

        if($params['userType'] == 49 || $params['userType'] == 44){ // administrator
            $coupons = $coupon->getCoupons();
        }
        else {
            unset($params['userType']);
            $coupons = $coupon->getByUser($params);
        }
    
        return $coupons;
    }

    public function transformCsNumber($denominations){
        $csNumbers = [];
        foreach($denominations as $amount) {
            foreach($amount->csNumbers as $csNumber) {
                array_push($csNumbers, $csNumber->text);
            }
        }
        return $csNumbers;
    }

    public function updateCoupon($request){
        $couponId      = $request->couponId;
       
        $denominations = json_decode($request->denominations);
        $csNumbers     = $this->transformCsNumber($denominations);
        $dealer = $request->dealerId;
        $createdBy = $request->createdBy;
        $userSource = $request->userSource;
        $couponType = $request->couponType;
        $description = $request->description;
        $status = $request->status;
        $purpose = $request->purpose;
        $promo = $request->promo;
        $emails = json_decode($request->email);
        $attachment = $request->attachment;
        $email_recipients = "";
        $csNumbers = [];
        $filename = "";
        $attachment_path = "";
        $origFilename = "";
        $serialNumber = new SerialNumber;
        $symlink_dir = "";

        foreach($emails as $email) {
            $email_recipients .= $email->text . ';';
        }

        $email_recipients = substr($email_recipients, 0, -1);


        $serialNumber = new SerialNumber;
        $invalidCsNumbers = $serialNumber->getInvalidCsNumbers($csNumbers);
        
        if(!empty($invalidCsNumbers)){
            return response()->json([
                'message'            => 'There are CS Numbers that does exist',
                'invalid_cs_numbers' => $invalidCsNumbers,
                'error'              => true
            ],200);
        }


        if(!empty($_FILES)){
            $couponC = new Coupon;
            $couponDetails = $couponC->getDetails($couponId); 
            if($couponDetails->attachment != ""){
                $destinationPath = 'public/storage/uploads/';
                File::delete($destinationPath. $couponDetails->new_filename);
            }
            $filename = Carbon::now()->timestamp . '.' . $attachment->getClientOriginalExtension();
            $origFilename = $attachment->getClientOriginalName();
            $symlink_dir = 'public/storage/uploads/';
            $attachment_path = Storage::putFileAs(
                'public/uploads', $attachment, $filename
            );
        }


        DB::beginTransaction();

        try {
            
            $coupon                     = \App\Models\Coupon::find($couponId);
            $coupon->dealer_id          = $dealer;
            $coupon->updated_by         = $createdBy;
            $coupon->update_user_source = $userSource;
            $coupon->promo_id           = $promo;
            $coupon->purpose_id         = $purpose;
            $coupon->description        = $description;
            $coupon->email              = $email_recipients;

            if(!empty($_FILES)){
                $coupon->attachment   = $symlink_dir . $filename;
                $coupon->filename     = $origFilename;
                $coupon->new_filename = $filename;
            }

            if($status == 6){  // if current status is rejected, revert to pending
                $coupon->status = 1;
                // also revert approval statuses and emails
                $approval = new Approval;
                $approval->resetApproval($couponId, 1);

                // revert current approver
                $coupon->current_approval_hierarchy = 1;
            }

            
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
            $timeline->message     = "Coupon request has been updated";
            $timeline->created_at  = Carbon::now();
            $timeline->save();

            $approval = new Approval;
            $approval->resetApproval($couponId, 1);

            DB::commit();

            return [
                'message'  => 'Coupon request has been updated.',
                'couponId' => $couponId,
                'error'    => false
            ];

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

            if($amount->quantity > 0){
                $denomination                     = new Denomination;
                $denomination->coupon_id          = $couponId;
                $denomination->amount             = $amount->amount;
                $denomination->quantity           = $amount->quantity;
                $denomination->created_by         = $createdBy;
                $denomination->create_user_source = $userSource;
                $denomination->save();
                $denominationId = $denomination->id;

                $csNumberParams = [];

                foreach($amount->csNumbers as $csNumber) {
                    array_push($csNumberParams,
                        [
                            'denomination_id'    => $denominationId,
                            'cs_number'          => $csNumber->text,
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
    }

    public function issueCoupon($request){
        $couponId   = $request->couponId;
        $userId     = $request->userId;
        $userSource = $request->userSource;
        $status     = 7;
        $action     = 5;

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
        $status     = 8;
        $action     = 3;

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
                'status'   => 'fleet received',
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
        $status     = 9;
        $action     = 4;

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
                'status'   => 'dealer received',
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

    public function resend($request){

        $coupon_id = $request->coupon_id;
        $user = $request->user;
        DB::beginTransaction();
        
        try {
            // update approval status
          
            $coupon = Coupon::find($coupon_id);
            $coupon->is_sent = 'N';
            $coupon->date_sent = NULL;
            $coupon->save();
            // add to timeline
            $timeline = new Timeline;
            $params = [
                'coupon_id'  => $coupon_id,
                'action_id'  => 12,
                'created_at' => Carbon::now(),
                'message'    => 'Email has been to resent to <strong>' . $coupon->email . '</strong> by ' . $user['first_name'] . ' ' . $user['last_name']
            ];
            $timeline->saveTimeline($params); 

            DB::commit();
            return [
                'message' => 'Email to requestor has been resent!'
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
