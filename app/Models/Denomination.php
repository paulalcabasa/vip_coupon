<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

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
}
