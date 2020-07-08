<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use DB;

class Promo extends Model
{
    protected $connection = "oracle";
    protected $table = "ipc.ipc_vpc_promos";
    protected $primaryKey = 'id';
    public $timestamps = false;

    public function getActive(){
        $sql = "SELECT  prm.id,
                        prm.promo_name,
                        prm.coupon_expiry_date,
                        prm.effective_date_from,
                        prm.effective_date_to,
                        to_char(prm.coupon_expiry_date,'MM/DD/YYYY') coupon_expiry_date_formatted
                FROM ipc.ipc_vpc_promos prm
                WHERE SYSDATE BETWEEN prm.effective_date_from  AND prm.effective_date_to
                    AND prm.status = 1";
        $query = DB::select($sql);
        return $query;
    }

    public function get(){
        $sql = "SELECT  prm.id,
                        prm.promo_name,
                        prm.terms,
                        prm.coupon_type_id,
                        prm.remarks,
                        prm.status status_id,
                        to_char(prm.coupon_expiry_date,'YYYY-MM-DD') coupon_expiry_date_orig,
                        to_char(prm.effective_date_from,'YYYY-MM-DD') effective_date_from_orig,
                        to_char(prm.effective_date_to,'YYYY-MM-DD') effective_date_to_orig,
                        to_char(prm.coupon_expiry_date, 'MM/DD/YYYY') coupon_expiry_date,
                        to_char(prm.effective_date_from, 'MM/DD/YYYY') effective_date_from,
                        to_char(prm.effective_date_to, 'MM/DD/YYYY') effective_date_to,
                        CASE 
                            WHEN st.id = 1 THEN lower(st.status)
                            ELSE CASE WHEN trunc(SYSDATE) BETWEEN prm.effective_date_from  AND prm.effective_date_to THEN lower(st.status) ELSE 'expired' END
                        END status         
                FROM ipc.ipc_vpc_promos prm
                    LEFT JOIN ipc.ipc_vpc_status st
                        ON st.id = prm.status";
        $query = DB::select($sql);
        return $query;
    }

    public function getById($promo_id){
        $sql = "SELECT  prm.id,
                        prm.promo_name,
                        prm.terms,
                        prm.coupon_type_id,
                        to_char(prm.coupon_expiry_date,'YYYY-MM-DD') coupon_expiry_date_orig,
                        to_char(prm.effective_date_from,'YYYY-MM-DD') effective_date_from_orig,
                        to_char(prm.effective_date_to,'YYYY-MM-DD') effective_date_to_orig,
                        trim(to_char(prm.coupon_expiry_date, 'Month')) || ' ' || to_char(prm.coupon_expiry_date, 'D, YYYY') coupon_expiry_date,
                        trim(to_char(prm.effective_date_from, 'Month')) || ' ' || to_char(prm.effective_date_from, 'D, YYYY') effective_date_from,
                        trim(to_char(prm.effective_date_to, 'Month')) || ' ' || to_char(prm.effective_date_to, 'D, YYYY') effective_date_to,
                        CASE WHEN (prm.effective_date_from >= sysdate AND prm.effective_date_to <= sysdate) THEN 'active' ELSE 'inactive' END status
                FROM ipc.ipc_vpc_promos prm
                WHERE prm.id = :promo_id";
        $query = DB::select($sql,['promo_id' => $promo_id]);
        
        return $query[0];
    }

    public function getActiveByCouponType($coupon_type_id){
        $sql = "SELECT  prm.id,
                        prm.promo_name,
                        prm.coupon_expiry_date,
                        prm.effective_date_from,
                        prm.effective_date_to,
                        to_char(prm.coupon_expiry_date,'MM/DD/YYYY') coupon_expiry_date_formatted
                FROM ipc.ipc_vpc_promos prm
                WHERE trunc(SYSDATE) BETWEEN prm.effective_date_from  AND prm.effective_date_to
                    AND prm.coupon_type_id = :coupon_type_id";
        $query = DB::select($sql, ['coupon_type_id' => $coupon_type_id]);
        return $query;
    }

    public function getPending(){
        $sql = "SELECT  prm.id,
                        prm.promo_name,
                        prm.terms,
                        prm.coupon_type_id,
                        to_char(prm.coupon_expiry_date,'YYYY-MM-DD') coupon_expiry_date_orig,
                        to_char(prm.effective_date_from,'YYYY-MM-DD') effective_date_from_orig,
                        to_char(prm.effective_date_to,'YYYY-MM-DD') effective_date_to_orig,
                        to_char(prm.coupon_expiry_date, 'MM/DD/YYYY') coupon_expiry_date,
                        to_char(prm.effective_date_from, 'MM/DD/YYYY') effective_date_from,
                        to_char(prm.effective_date_to, 'MM/DD/YYYY') effective_date_to,
                        CASE 
                            WHEN st.id = 1 THEN lower(st.status)
                            ELSE CASE WHEN trunc(SYSDATE) BETWEEN prm.effective_date_from  AND prm.effective_date_to THEN lower(st.status) ELSE 'expired' END
                        END status,
                        ct.name coupon_type
                FROM ipc.ipc_vpc_promos prm
                    LEFT JOIN ipc.ipc_vpc_status st
                        ON st.id = prm.status
                    LEFT JOIN ipc.ipc_vpc_coupon_types ct
                        ON ct.id = prm.coupon_type_id
                WHERE prm.status = 1
                    AND prm.mail_sent = 'N'";
        $query = DB::select($sql);
        return $query;
    }

    public function getValidated(){
        $sql = "SELECT  prm.id,
                        prm.promo_name,
                        prm.terms,
                        prm.coupon_type_id,
                        prm.remarks,
                        to_char(prm.coupon_expiry_date,'YYYY-MM-DD') coupon_expiry_date_orig,
                        to_char(prm.effective_date_from,'YYYY-MM-DD') effective_date_from_orig,
                        to_char(prm.effective_date_to,'YYYY-MM-DD') effective_date_to_orig,
                        to_char(prm.coupon_expiry_date, 'MM/DD/YYYY') coupon_expiry_date,
                        to_char(prm.effective_date_from, 'MM/DD/YYYY') effective_date_from,
                        to_char(prm.effective_date_to, 'MM/DD/YYYY') effective_date_to,
                        CASE 
                            WHEN st.id = 1 THEN lower(st.status)
                            ELSE CASE WHEN trunc(SYSDATE) BETWEEN prm.effective_date_from  AND prm.effective_date_to THEN lower(st.status) ELSE 'expired' END
                        END status,
                        ct.name coupon_type,
                        lower(st.status) status_name,
                        usr.email_address,
                        usr.first_name || ' ' || usr.last_name requestor
                FROM ipc.ipc_vpc_promos prm
                    LEFT JOIN ipc.ipc_vpc_status st
                        ON st.id = prm.status
                    LEFT JOIN ipc.ipc_vpc_coupon_types ct
                        ON ct.id = prm.coupon_type_id
                    LEFT JOIN ipc_vpc_users_v usr   
                        ON usr.user_id  = prm.created_by
                        AND usr.user_source_id = prm.create_user_source
                WHERE prm.status IN (6,10)
                    AND prm.mail_sent = 'N'";
        $query = DB::select($sql);
        return $query;
    }

   
}
