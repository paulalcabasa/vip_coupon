<?php

namespace App\models;

use Illuminate\Database\Eloquent\Model;
use DB;

class Approval extends Model
{
    protected $connection = "oracle";
    protected $table = "ipc.ipc_vpc_approval";
    protected $primaryKey = 'id';
    public $timestamps = false; 

    public function batchInsert($params){
        $this->insert($params);
    }

    public function getByCoupon($couponId){
        $sql = "SELECT initcap(usr.first_name)  || ' '  || initcap(usr.last_name) approver_name,
                        lower(st.status) status,
                        to_char(cpa.UPDATED_AT,'MM/DD/YYYY HH24:MI:SS AM') date_approved,
                        cpa.remarks,
                        usr.email_address,
                        cpa.mail_sent_flag,
                        to_char(cpa.date_mail_sent,'MM/DD/YYYY HH24:MI:SS AM') date_sent,
                        va.hierarchy
                FROM ipc.ipc_vpc_approval cpa
                    LEFT JOIN ipc.ipc_vpc_approvers va
                        ON va.id = cpa.approver_id
                    LEFT JOIN ipc_vpc_users_v usr
                        ON usr.user_id = va.approver_user_id
                        AND usr.user_source_id = va.approver_source_id
                    LEFT JOIN ipc.ipc_vpc_approver_types atp
                        ON atp.id = va.approver_type
                    LEFT JOIN ipc.ipc_vpc_status st
                        ON st.id = cpa.status
                WHERE cpa.module_reference_id = :coupon_id
                    AND cpa.module_id = 1
                ORDER BY va.hierarchy";
        $query = DB::select($sql,['coupon_id' => $couponId]);
        return $query;
    }

}
