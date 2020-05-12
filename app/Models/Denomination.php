<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use DB;

class Denomination extends Model
{
    protected $connection = "oracle";
    protected $table = "ipc.ipc_vpc_denominations";
    const CREATED_AT = 'creation_date';
    const UPDATED_AT = 'update_date';
    protected $primaryKey = 'id';
    
    public function getByCoupon($couponId){
        return $this
            ->where('coupon_id',$couponId)
            ->get();
    }

    public function deleteByCoupon($couponId){
        $this
            ->where('coupon_id', $couponId)
            ->delete();
    }

    public function getTotalDenomination(){
        $sql = "SELECT nvl(sum(quantity),0) quantity
                FROM ipc.ipc_vpc_denominations dn
                    LEFT JOIN ipc.ipc_vpc_coupons cp
                        ON cp.id = dn.coupon_id
                WHERE cp.status NOT IN (4,1)";
        $query = DB::select($sql);
        return $query[0]->quantity;
    }
}
