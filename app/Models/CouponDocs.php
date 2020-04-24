<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CouponDocs extends Model
{
    protected $connection = "oracle";
    protected $table = "ipc.ipc_vpc_coupon_docs";
    protected $primaryKey = 'id';
    public $timestamps = false;

    public function batchInsert($params){
        $this->insert($params);
    }

    public function getByCoupon($couponId){
        return $this->where('coupon_id', $couponId)->get();
    }

}
