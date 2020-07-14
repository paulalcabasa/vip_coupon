<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Claim;
use App\Models\Voucher;
use Carbon\Carbon;
use App\Models\Coupon;
use App\Services\ClaimService;

class ClaimController extends Controller
{
    public function claimForm(Request $request){
        $voucher_code = $request->voucher_code;

        $voucher = new Voucher;
        $voucherDetails = $voucher->getByCode($voucher_code);
        
     
    
        if(count($voucherDetails) == 0){
            $data = [
                'message' => 'It seems that ' . $request->voucher_code . ' does not exist.',
                'image_url' => url('/') . '/public/images/approval-error.jpg',
            ];
            return view('claim-message', $data);
        }
        
        $voucherDetails = $voucherDetails[0];
        
        $today = Carbon::today();
        $expiration_date = Carbon::parse($voucherDetails->expiration_date);
        
        if($today->greaterThan($expiration_date)){
            $data = [
                'message' => 'It seems that ' . $request->voucher_code . ' has expired last ' . $expiration_date->format('M d, Y'),
                'image_url' => url('/') . '/public/images/approval-error.jpg',
            ];
            return view('claim-message', $data);
        }
        
        $claimCheck = Claim::where('voucher_id', $voucherDetails->id)->get();

        if(count($claimCheck) > 0){
            $data = [
                'message' => 'It seems that you have already claimed ' . $request->voucher_code . '.',
                'image_url' => url('/') . '/public/images/approval-error.jpg',
            ];
            return view('claim-message', $data);
        }

        $coupon = new Coupon;
        $couponDetails = $coupon->getDetails($voucherDetails->coupon_id);
       

        $data = [
            'voucher_code'   => $voucher_code,
            'claim_api'      => url('/') . '/api/voucher/claim',
            'voucherDetails' => $voucherDetails,
            'couponTypeId'  => $couponDetails->coupon_type_id
        ];  
        return view('claim-form', $data);
    }

    public function store(Request $request){

        $claimCheck = Claim::where('voucher_id', $request->voucher_id)->get();
      
        if(count($claimCheck) > 0){
            $data = [
                'message' => 'It seems that you have already claimed ' . $request->voucher_code . '.',
                'image_url' => url('/') . '/public/images/approval-error.jpg',
            ];
            return view('claim-message', $data);
        }
     

        $claim                = new Claim;
        $claim->voucher_id    = $request->voucher_id;
        $claim->customer_name = $request->customer_name;
        $claim->amount        = $request->amount;
        $claim->creation_date        = Carbon::now();

        if($request->coupon_type_id == 2) { // service
            $claim->cs_number = $request->cs_number;
            $claim->plate_no = $request->plate_number;
            $claim->service_invoice_number = $request->service_invoice_no;
            $claim->service_date = $request->service_date;
        }
        $claim->save();
        
        $data = [
            'message' => 'You have successfully claimed ' . $request->voucher_code . '!',
            'image_url' => url('/') . '/public/images/approval-success.gif',
        ];
        return view('claim-message', $data);
    }

    public function get(Request $request){
        $claimService = new ClaimService;
        return response()->json($claimService->get($request),200);
    }
}
