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

    public function getByCoupon($couponId){
        //return $this->where('coupon_id', $couponId)->get();
        $sql = "SELECT cd.id,
                    cd.amount,
                    cd.cs_number,
                    st.status,
                    lpad(cd.id,6,0) voucher_no
                FROM ipc.ipc_vpc_vouchers cd
                    LEFT JOIN ipc.ipc_vpc_status st
                    ON cd.status = st.id
                WHERE cd.coupon_id = :coupon_id";
        $query = DB::select($sql,['coupon_id' => $couponId]);
        return $query;
    }
}
