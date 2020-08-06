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

    public function getTotalDenomination($params){
        $sql = "SELECT nvl(sum(quantity),0) quantity
                FROM ipc.ipc_vpc_denominations dn
                    LEFT JOIN ipc.ipc_vpc_coupons cp
                        ON cp.id = dn.coupon_id
                    LEFT JOIN ipc.ipc_vpc_coupon_types ct
                        ON ct.id = cp.coupon_type_id
                WHERE cp.status NOT IN (4,1)
                    AND ct.user_type_id = :user_type_id";
        $query = DB::select($sql, $params);
        return $query[0]->quantity;
    }

      public function getTotalDenominationByDealer($params){
        $sql = "SELECT nvl(sum(quantity),0) quantity
                FROM ipc.ipc_vpc_denominations dn
                    LEFT JOIN ipc.ipc_vpc_coupons cp
                        ON cp.id = dn.coupon_id
                WHERE cp.status NOT IN (4,1)
                    AND cp.dealer_id = :dealer_id";
        $query = DB::select($sql, $params);
        return $query[0]->quantity;
    }
}
