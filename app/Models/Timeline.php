<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use DB;

class Timeline extends Model
{
    protected $connection = "oracle";
    protected $table = "ipc.ipc_vpc_timeline";
    protected $primaryKey = 'id';
    public $timestamps = false;

    public function getByCoupon($couponId){
        $sql = "SELECT tl.id,
                        tl.created_at,
                        ac.action,
                        usr.first_name || ' ' || usr.last_name created_by
                FROM ipc.ipc_vpc_timeline tl
                    INNER JOIN ipc.ipc_vpc_actions ac
                        ON ac.id = tl.action_id
                    INNER JOIN apps.ipc_vpc_users_v usr
                        ON usr.user_id = tl.user_id
                        AND usr.user_source_id = tl.user_source
                WHERE tl.coupon_id = :coupon_id";
        $query = DB::select($sql,[
            'coupon_id' => $couponId
        ]);
        return $query;
    }
}
