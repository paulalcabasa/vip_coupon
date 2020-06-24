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

    public function updateStatus($params){
        $this->where([
            ['id', $params['approval_id']]
        ])->update([
            'status'     => $params['status'],
            'updated_at' => $params['date_approved'],
            'remarks'    => $params['remarks']
        ]);
    }

    public function checkCoupon($params){
        $sql = "SELECT vpa.hierarchy
                FROM ipc.ipc_vpc_approval vpa
                WHERE vpa.module_reference_id = :module_reference_id
                        AND vpa.module_id = :module_id
                        AND vpa.hierarchy = :hierarchy
                        AND vpa.status IN(2,6)
                GROUP by vpa.hierarchy";
        $query = DB::select($sql, $params);
        return $query;        
    }

    public function getMaxApproval($params){
        $sql = "SELECT max(vpa.hierarchy) hierarchy
                FROM ipc.ipc_vpc_approval vpa
                WHERE vpa.module_reference_id = :module_reference_id
                    AND vpa.module_id = :module_id";
        $query = DB::select($sql,$params);
        return !empty($query) ? $query[0] : $query;
    }

    public function resetApproval($couponId,$moduleId){
        $this->where([
            'module_reference_id' => $couponId,
            'module_id' => $moduleId
        ])->update([
            'status' => 1,
            'mail_sent_flag' => 'N',
            'date_mail_sent' => '',
            'updated_at' => ''
        ]);
    }

    public function getByCouponHierarchy($params){
        $sql = "SELECT apl.id approval_id,
                        apl.mail_sent_flag,
                        usr.email_address email_address,
                        usr.first_name || ' ' || usr.last_name approver_name
                FROM ipc.ipc_vpc_approval apl
                    INNER join ipc.ipc_vpc_approvers apr
                        ON apr.id = apl.approver_id
                    INNER JOIN ipc_vpc_users_v usr
                        ON usr.user_id = apr.approver_user_id
                        AND usr.user_source_id = apr.approver_source_id
                WHERE apl.module_reference_id = :module_reference_id
                        and apl.module_id = :module_id
                    AND apl.hierarchy = 1";
        $query = DB::select($sql,$params);
        return $query;
    }
}
