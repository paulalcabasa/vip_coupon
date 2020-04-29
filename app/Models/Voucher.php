<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use DB;

class Voucher extends Model
{   

    protected $connection = "oracle";
    protected $table = "ipc.ipc_vpc_vouchers";
    protected $primaryKey = 'id';
    public $timestamps = false;

    public function batchInsert($params){
        $this->insert($params);
    }

    public function inserVoucher($params){
        $this->insert($params);
    }

    public function getByCoupon($couponId){
        //return $this->where('coupon_id', $couponId)->get();
        $sql = "SELECT cd.id,
                    cd.amount,
                    cd.cs_number,
                    st.status,
                    lpad(cd.id,6,0) voucher_no,
                    cd.voucher_code
                FROM ipc.ipc_vpc_vouchers cd
                    LEFT JOIN ipc.ipc_vpc_status st
                    ON cd.status = st.id
                WHERE cd.coupon_id = :coupon_id";
        $query = DB::select($sql,['coupon_id' => $couponId]);
        return $query;
    }

    public function isExist($voucherCode){
        $query = $this->where('voucher_code', $voucherCode)->count();
      
        if($query > 0){
            return true;
        }
        return false;
    }

    public function getInvalidVouchers($voucherCodes){
        $query = DB::connection('oracle')->table('ipc.ipc_vpc_vouchers vch')
                //    ->leftJoin('ipc.ipc_vpc_payment_lines pl', 'pl.voucher_id','=','vch.id')
                //    ->leftJoin('ipc.ipc_vpc_payment_headers ph', 'ph.id','=','pl.payment_header_id')
                    ->whereIn('vch.voucher_code', $voucherCodes)
                //    ->where('ph.status', 4) // approved
                 //   ->whereRaw('pl.id IS NULL')
                    ->select('vch.voucher_code')
                    ->get();
        $isExist = collect($query)->pluck('voucher_code')->toArray();
        return array_diff($voucherCodes,$isExist);
    }


}
