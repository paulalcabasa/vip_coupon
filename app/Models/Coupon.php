<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Coupon extends Model
{
    protected $connection = "oracle";
    protected $table = "ipc.ipc_vpc_coupons";
    protected $primaryKey = 'coupon_id';
    const CREATED_AT = 'creation_date';
    const UPDATED_AT = 'update_date';
}
