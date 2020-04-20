<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use DB;

class Coupon extends Model
{
    protected $connection = "oracle";
    protected $table = "ipc.ipc_vpc_coupons";
    protected $primaryKey = 'id';
    const CREATED_AT = 'creation_date';
    const UPDATED_AT = 'update_date';


    public function getDetails($couponId){
        $sql = "SELECT cp.id coupon_id,
                        dlr.account_name,
                        usr.first_name || ' ' || usr.last_name created_by,
                        TRIM(TO_CHAR(cp.creation_date, 'Month')) || ' ' ||  TO_CHAR(cp.creation_date,'D, YYYY') date_created,
                        st.status
                FROM ipc.ipc_vpc_coupons cp
                    INNER JOIN ipc_portal.dealers dlr
                        ON dlr.id = cp.dealer_id
                    INNER JOIN apps.ipc_vpc_users_v usr
                        ON usr.user_id = cp.created_by
                        AND usr.user_source_id = cp.create_user_source
                    INNER JOIN ipc.ipc_vpc_status st
                        ON st.id = cp.status
                WHERE cp.id = :coupon_id";
        $query = DB::select($sql, [
            'coupon_id' => $couponId
        ]);


        return !empty($query) ? $query[0] : $query;
    }
}
