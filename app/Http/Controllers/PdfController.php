<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use PDF;
use App\Services\VoucherService;
use App\Models\Voucher;
use App\Services\CodeService;
use App\Models\Coupon;
use App\Models\Timeline;
use App\Models\CouponType;
use Carbon\Carbon;
use App\Models\Promo;
use App\Models\ClaimHeader;
use App\Models\ClaimLine;

class PdfController extends Controller
{   

    public function printCoupon(Request $request){

        ini_set('max_execution_time',36000); //300 seconds = 5 minutes
        ini_set('memory_limit','2048M');
        $voucher = new Voucher;
        $coupon = new Coupon;
        $couponType = new CouponType;

        $docs = $voucher->getByCoupon($request->coupon_id);
        $header = $coupon->getDetails($request->coupon_id);

       /*  if($header->status_id != 12){
            $data = [
                "message" => "Failed printing! Voucher must be approved before printing and is only printed once.",
                "couponDetails" => $header,
                'image_url' => url('/') . '/public/images/approval-error.jpg'
            ];
            return view('failed-print', $data); 
        } */
        
        $type = CouponType::where('id', $header->coupon_type_id)->first();
       
        $data = [
            'docs' => $docs,
            'header' => $header,
            'claimApiUrl' => url('/') . '/api/voucher/claim/',
            'single' => 'N'
        ];

        // update status to printed
        $coupon = new Coupon;
        $coupon->updateStatus([
            'couponId' => $request->coupon_id,
            'status' => 3,
            'updateDate' => Carbon::now()
        ]);

        // add to timeline and who printed.
        $timeline = new Timeline;
            
        $timeline->saveTimeline([
            'coupon_id'  => $request->coupon_id,
            'action_id'  => 3,
            'created_at' => Carbon::now(),
            'message'    => 'Coupon has been printed by <strong>' . $request->email . '</strong>'
        ]); 
        
        
        $pdf = PDF::loadView($type->file_template,$data);
        return $pdf->setPaper('a4','portrait')->stream();
    }


    public function previewCoupon(Request $request){
     //   return $request->promo_id;
        $promo = new Promo;
        $promoDetail = $promo->getById($request->promo_id);
        $data = [
            'promo' => $promoDetail
        ];

        $template = $promoDetail->coupon_type_id == 1 ? 'preview-coupon' : 'preview-service-coupon';
        $pdf = PDF::loadView($template,$data);
        return $pdf->setPaper('a4','portrait')->stream();
    }

    public function printVoucher(Request $request){
        $voucher = new Voucher;
        $coupon = new Coupon;
        $couponType = new CouponType;

        $docs = $voucher->getByVoucher($request->voucher_id);
        $header = $coupon->getDetails($docs[0]->coupon_id);
        $type = CouponType::where('id', $header->coupon_type_id)->first();
        
     
        $data = [
            'docs' => $docs,
            'header' => $header,
            'claimApiUrl' => url('/') . '/api/voucher/claim/',
            'single' => 'Y'
        ];

        
        $pdf = PDF::loadView($type->file_template,$data);
        return $pdf->setPaper('a4','portrait')->stream(); 
    }

    public function printClaimRequest(Request $request){
        $claimHeader = new ClaimHeader();
        $header =  $claimHeader->get($request->claim_request_id);
        
        $claimLine = new ClaimLine();
        $lines =  $claimLine->get($request->claim_request_id);

        $data = [
            'header' => $header,
            'lines' => $lines
        ];

        
        $pdf = PDF::loadView('print-claim-request',$data);
        return $pdf->setPaper('a4','portrait')->stream(); 
    }

}
