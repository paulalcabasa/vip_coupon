<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CouponType extends Model
{
    protected $connection = "oracle";
    protected $table = "ipc.ipc_vpc_coupon_types";
    protected $primaryKey = 'id';
    public $timestamps = false;

}
