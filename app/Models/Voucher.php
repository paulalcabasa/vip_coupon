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
                    nvl(pl.cs_number,cd.cs_number) cs_number,
                    st.status,
                    lpad(cd.control_number,6,0) voucher_no,
                    cd.voucher_code,
                    to_char(cd.expiration_date,'MM/DD/YYYY') expiration_date
                FROM ipc.ipc_vpc_vouchers cd
                    LEFT JOIN ipc.ipc_vpc_status st
                        ON cd.status = st.id
                    LEFT JOIN ipc.ipc_vpc_claim_lines pl
                        ON pl.voucher_id = cd.id
                WHERE cd.coupon_id = :coupon_id
                    AND cd.status NOT IN (4)
                ORDER BY cd.control_number";
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

    public function getByCode($voucherCode){
        return $this->where('voucher_code', $voucherCode)->get();
    }

    public function getVoucherStats(){
        $sql = "SELECT COUNT(CASE WHEN ph.status = 2 THEN 1 ELSE NULL END) CLAIMED,
                        count(vch.id) printed 
                FROM ipc.ipc_vpc_vouchers vch
                LEFT JOIN ipc.ipc_vpc_claim_lines pl
                    ON pl.voucher_id = vch.id
                LEFT JOIN ipc.ipc_vpc_claim_headers ph
                    ON ph.id = pl.claim_header_id
                WHERE 1 = 1";
        $query = DB::select($sql);
        return $query[0];
    }

    public function getRecentClaims(){
        $sql = "SELECT vch.id,
                        vch.voucher_code,
                        ph.creation_date,
                        vch.amount,
                        dlr.account_name,
                        TRIM(TO_CHAR(ph.creation_date, 'Month')) || ' ' ||  TO_CHAR(ph.creation_date,'DD, YYYY') date_claimed
                FROM ipc.ipc_vpc_vouchers vch
                INNER JOIN ipc.ipc_vpc_claim_lines pl
                    ON pl.voucher_id = vch.id
                INNER JOIN ipc.ipc_vpc_claim_headers ph
                    ON ph.id = pl.claim_header_id
                INNER JOIN ipc.ipc_vpc_coupons cp
                    ON cp.id = vch.coupon_id
                INNER JOIN ipc_portal.dealers dlr
                    ON dlr.id = cp.dealer_id
                WHERE 1 = 1
                    AND ph.status = 2
                    AND rownum <= 15";
        $query = DB::select($sql);
        return $query;
    }

    public function generateControlNumber($sequence_name){
        $sql = "SELECT ".$sequence_name.".nextval control_number FROM dual";
        $query = DB::select($sql);
        return $query[0]->control_number;
    }

}
