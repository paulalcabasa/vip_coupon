<?php

namespace App\Services;
use DB;
use Carbon\Carbon;
use App\Models\Coupon;
use App\Services\VoucherService;
use App\Services\EmailService;

class InstantService {

    public function instantProcess($request){
        $today = date('Y-m-d');
        $expiry_date = '2020-09-01';

        $today = strtotime($today); 
        $expiry_date = strtotime($expiry_date); 
  
        // Compare the timestamp date  
        if ($today > $expiry_date) {
            return [
                'message' => 'Trial period has expired.'
            ];
        } 

        DB::beginTransaction();
        try{
            DB::statement("UPDATE ipc.ipc_vpc_approval
                            SET status = 2,
                                    mail_sent_flag = 'Y',
                                    date_mail_sent = SYSDATE
                            WHERE module_reference_id = {$request->coupon_id}
                                AND module_id = 1");
            
            
            DB::statement("UPDATE  ipc.ipc_vpc_coupons
                            SET status = 2,
                                    current_approval_hierarchy = (
                                        SELECT max(hierarchy)
                                        FROM ipc.ipc_vpc_approval
                                        WHERE module_reference_id = {$request->coupon_id}
                                            AND module_id = 1
                                    )
                                    WHERE id = {$request->coupon_id}");
            

            // generate coupon
            $this->generateCoupon();

            // send email
            $mailService = new EmailService;
            $mailService->sendGeneratedCoupons();

            // send email notification

            DB::commit();
        } catch(\Exception $e){

            DB::rollBack();
            return ['error' => $e];
        }
        return ['message' => 'You have used the instant service, Coupon No. ' . $request->coupon_id . ' has now been approved, generated and emailed.'];
    }

    public function generateCoupon(){
        $coupon = new Coupon;

        $approvedCoupons = $coupon->getApproved();

        foreach($approvedCoupons as $row){
            $voucherService = new VoucherService;
            $voucherService->generateVoucher(
                $row->coupon_id, 
                -1, 
                -1
            );
        }
    }
  

}
