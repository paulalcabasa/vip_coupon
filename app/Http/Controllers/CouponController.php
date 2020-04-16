<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use DB;
use App\Models\Coupon;
use App\Models\Denomination;
use App\Models\CSNumber;

class CouponController extends Controller
{

    
    public function store(Request $request){
        $dealer = $request->dealer_id;
        $denominations = $request->denominations;
        $created_by = $request->created_by;
        $cs_numbers = [];

        foreach($denominations as $amount) {
            foreach($amount['csNumbers'] as $cs_number) {
                array_push($cs_numbers, $cs_number['text']);
            }
        }

        $check_exist = DB::connection('oracle')->table('mtl_serial_numbers')
                    ->whereIn('serial_number', $cs_numbers)
                    ->select('serial_number')
                    ->get()
                    ->toArray();
        $check_exist = collect($check_exist)->pluck('serial_number')->toArray();
        $invalid_cs_numbers = array_diff($cs_numbers, $check_exist);
        
        if(!empty($invalid_cs_numbers)){
            return response()->json([
                'message'            => 'There are CS Numbers that does exist',
                'invalid_cs_numbers' => $invalid_cs_numbers,
                'error'              => true
            ],200);
        }

        DB::beginTransaction();

        try {
            $coupon = new Coupon;
            $coupon->dealer_id = $dealer;
            $coupon->created_by = $created_by;
            $coupon->save();
            $coupon_id = $coupon->coupon_id;

            foreach($denominations as $amount) {
                $denomination = new Denomination;
                $denomination->coupon_id =  $coupon_id;
                $denomination->amount = $amount['amount'];
                $denomination->quantity = $amount['quantity'];
                $denomination->created_by = $created_by;
                $denomination->save();
                $denomination_id = $denomination->denomination_id;

                $cs_number_params = [];

                foreach($amount['csNumbers'] as $cs_number) {
                    array_push($cs_number_params,
                        [
                            'denomination_id' => $denomination_id,
                            'cs_number'       => $cs_number['text'],
                            'created_by'      => $created_by
                        ]
                    );
                }
                $cs_num = new CSNumber;
                $cs_num->batch_insert($cs_number_params);
                return response()->json($cs_number_params);

            }


            DB::commit();
        } catch(\Exception $e) {
            DB::rollBack();
            return $e;
        }
    
    }
}
