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
        $sql = "SELECT  cp.id coupon_id,
                        cp.status status_id,
                        dlr.account_name,
                        usr.first_name || ' ' || usr.last_name created_by,
                        usr.first_name,
                        usr.last_name,
                        TRIM(TO_CHAR(cp.creation_date, 'Month')) || ' ' ||  TO_CHAR(cp.creation_date,'DD, YYYY') date_created,
                        st.status,
                        cp.dealer_id,
                        usr.user_id,
                        usr.user_source_id,
                        ct.name coupon_type,
                        cp.coupon_type_id,
                        cp.description,
                        prm.promo_name,
                        prs.id purpose_id,
                        prs.purpose,
                        prs.require_cs_no_flag,
                        cp.filename,
                        cp.attachment,
                        cp.email,
                        cp.purpose_id,
                        cp.promo_id,
                        cp.new_filename,
                        cp.current_approval_hierarchy,
                        count(vpa.id) approve_ctr,
                        to_char(cp.date_sent, 'MM/DD/YYYY HH24:MI:SS AM') date_sent,
                        cp.is_sent,
                        prm.coupon_expiry_date,
                        to_char(prm.coupon_expiry_date,'MM/DD/YYYY') coupon_expiry_date_formatted,
                        prm.effective_date_from,
                        prm.effective_date_to
                FROM ipc.ipc_vpc_coupons cp
                    INNER JOIN ipc_portal.dealers dlr
                        ON dlr.id = cp.dealer_id
                    INNER JOIN apps.ipc_vpc_users_v usr
                        ON usr.user_id = cp.created_by
                        AND usr.user_source_id = cp.create_user_source
                    INNER JOIN ipc.ipc_vpc_status st
                        ON st.id = cp.status
                    INNER JOIN ipc.ipc_vpc_coupon_types ct
                        ON ct.id = cp.coupon_type_id
                    INNER JOIN ipc.ipc_vpc_promos prm
                        ON prm.id = cp.promo_id
                    INNER JOIN ipc.ipc_vpc_purposes prs
                        ON prs.id = cp.purpose_id
                    LEFT JOIN ipc.ipc_vpc_approval vpa
                        ON vpa.module_reference_id = cp.id
                        AND vpa.module_id = 1
                        AND vpa.status IN (2,6)
                WHERE cp.id = :coupon_id
                GROUP BY 
                        cp.id ,
                        cp.status ,
                        dlr.account_name,
                        usr.first_name,
                        usr.last_name,
                        cp.creation_date,
                        st.status,
                        cp.dealer_id,
                        usr.user_id,
                        usr.user_source_id,
                        ct.name ,
                        cp.coupon_type_id,
                        cp.description,
                        prm.promo_name,
                        prs.id ,
                        prs.purpose,
                        prs.require_cs_no_flag,
                        cp.filename,
                        cp.attachment,
                        cp.email,
                        cp.purpose_id,
                        cp.promo_id,
                        cp.new_filename,
                        cp.current_approval_hierarchy,
                        cp.date_sent,
                        cp.is_sent,
                        prm.coupon_expiry_date,
                        prm.effective_date_from,
                        prm.effective_date_to";
        $query = DB::select($sql, [
            'coupon_id' => $couponId
        ]);
        return !empty($query) ? $query[0] : $query;
    }

    public function getCoupons(){
        $sql = "SELECT cp.id coupon_id,
                    dlr.account_name,
                    usr.first_name || ' ' || usr.last_name created_by,
                    TRIM(TO_CHAR(cp.creation_date, 'Month')) || ' ' ||  TO_CHAR(cp.creation_date,'DD, YYYY') date_created,
                    lower(st.status) status,
                    cp.dealer_id,
                    ct.name coupon_type
                FROM ipc.ipc_vpc_coupons cp
                    LEFT JOIN ipc_portal.dealers dlr
                        ON cp.dealer_id = dlr.id
                    LEFT JOIN apps.ipc_vpc_users_v usr
                        ON usr.user_id = cp.created_by
                        AND usr.user_source_id = cp.create_user_source
                    LEFT JOIN ipc.ipc_vpc_status st
                        ON st.id = cp.status
                    LEFT JOIN ipc.ipc_vpc_coupon_types ct
                        ON ct.id = cp.coupon_type_id";
        $query = DB::select($sql);
        return $query;
    }

    public function getByUser($params){
        $sql = "SELECT cp.id coupon_id,
                    dlr.account_name,
                    usr.first_name || ' ' || usr.last_name created_by,
                    TRIM(TO_CHAR(cp.creation_date, 'Month')) || ' ' ||  TO_CHAR(cp.creation_date,'DD, YYYY') date_created,
                    lower(st.status) status,
                    cp.dealer_id,
                    ct.name coupon_type
                FROM ipc.ipc_vpc_coupons cp
                    LEFT JOIN ipc_portal.dealers dlr
                        ON cp.dealer_id = dlr.id
                    LEFT JOIN apps.ipc_vpc_users_v usr
                        ON usr.user_id = cp.created_by
                        AND usr.user_source_id = cp.create_user_source
                    LEFT JOIN ipc.ipc_vpc_status st
                        ON st.id = cp.status
                    LEFT JOIN ipc.ipc_vpc_coupon_types ct
                        ON ct.id = cp.coupon_type_id
                WHERE usr.user_id = :userId
                    AND usr.user_source_id = :sourceId";
        $query = DB::select($sql,$params);
        return $query;
    }

    public function getPending(){
        $sql = "SELECT cp.id coupon_id,
                    dlr.account_name,
                    usr.first_name || ' ' || usr.last_name created_by,
                    TRIM(TO_CHAR(cp.creation_date, 'Month')) || ' ' ||  TO_CHAR(cp.creation_date,'DD, YYYY') date_created,
                    lower(st.status) status,
                    cp.dealer_id,
                    ct.name coupon_type
                FROM ipc.ipc_vpc_coupons cp
                    LEFT JOIN ipc_portal.dealers dlr
                        ON cp.dealer_id = dlr.id
                    LEFT JOIN apps.ipc_vpc_users_v usr
                        ON usr.user_id = cp.created_by
                        AND usr.user_source_id = cp.create_user_source
                    LEFT JOIN ipc.ipc_vpc_status st
                        ON st.id = cp.status
                    LEFT JOIN ipc.ipc_vpc_coupon_types ct
                        ON ct.id = cp.coupon_type_id
                WHERE cp.status = :status_id";
        $query = DB::select($sql,[
            'status_id' => 1
        ]);
        return $query;
    }

    public function updateStatus($params){
        $this
            ->where([
                [ 'id', '=' , $params['couponId'] ],
            ])
            ->update([
                'status'             => $params['status'],
                 'update_date'        => $params['updateDate'],
            ]);
    }

    public function updateCurrentApprovalHierarchy($params){
        $this
            ->where([
                [ 'id', '=' , $params['couponId'] ],
            ])
            ->update([
                'current_approval_hierarchy'             => $params['next_hierarchy']
            ]);
    }

    public function getGeneratedCoupons(){
        $sql = "SELECT  cp.id coupon_id,
                        cp.status status_id,
                        dlr.account_name,
                        usr.first_name || ' ' || usr.last_name created_by,
                        usr.first_name,
                        usr.last_name,
                        TRIM(TO_CHAR(cp.creation_date, 'Month')) || ' ' ||  TO_CHAR(cp.creation_date,'DD, YYYY') date_created,
                        st.status,
                        cp.dealer_id,
                        usr.user_id,
                        usr.user_source_id,
                        ct.name coupon_type,
                        cp.coupon_type_id,
                        cp.description,
                        prm.promo_name,
                        prs.id purpose_id,
                        prs.purpose,
                        prs.require_cs_no_flag,
                        cp.filename,
                        cp.attachment,
                        cp.email,
                        cp.purpose_id,
                        cp.promo_id,
                        cp.new_filename,
                        cp.current_approval_hierarchy
  
                FROM ipc.ipc_vpc_coupons cp
                    INNER JOIN ipc_portal.dealers dlr
                        ON dlr.id = cp.dealer_id
                    INNER JOIN apps.ipc_vpc_users_v usr
                        ON usr.user_id = cp.created_by
                        AND usr.user_source_id = cp.create_user_source
                    INNER JOIN ipc.ipc_vpc_status st
                        ON st.id = cp.status
                    INNER JOIN ipc.ipc_vpc_coupon_types ct
                        ON ct.id = cp.coupon_type_id
                    INNER JOIN ipc.ipc_vpc_promos prm
                        ON prm.id = cp.promo_id
                    INNER JOIN ipc.ipc_vpc_purposes prs
                        ON prs.id = cp.purpose_id
                
                WHERE cp.STATUS = 12
                        and cp.is_sent = 'N'";
        $query = DB::select($sql);
        return $query;
    }
    
    public function updateMailStatus($params){
        $this
            ->where([
                [ 'id', '=' , $params['coupon_id']],
            ])
            ->update([
                'is_sent'             => $params['sent_flag'],
                'date_sent'        => $params['date_sent'],
            ]);
    }

    
}
