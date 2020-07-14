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

    public function getByClaimRequest($claimHeaderId){
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
                WHERE cpa.module_reference_id = :claim_header_id
                    AND cpa.module_id = 2
                ORDER BY va.hierarchy";
        $query = DB::select($sql,['claim_header_id' => $claimHeaderId]);
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

    public function setApproval($couponId, $moduleId, $origVehicleType, $newVehicleType, $approvers){
        DB::beginTransaction();

        try {
            
            // only delete if vehicle type has been updated
            if($origVehicleType != $newVehicleType){
                // delete previous approval
                $this->where([
                    'module_reference_id' => $couponId,
                    'module_id' => $moduleId
                ])->delete(); 
                //insert new approval
                $this->batchInsert($approvers);
                
            }
            else {
                // reset only other approvers
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

            

            DB::commit();

            return response()->json([
                'message'  => 'Coupon request has been saved.',
                'couponId' => $couponId,
                'error'    => false
            ],200);

        } catch(\Exception $e) {
            DB::rollBack();
            return response()->json([
                'message'  => $e,
                'couponId' => $couponId,
                'error'    => false
            ],200);
        }
        
       
        
    }

    public function getByCouponHierarchy($params){
        $sql = "SELECT apl.id approval_id,
                        apl.mail_sent_flag,
                        usr.email_address email_address,
                        usr.first_name || ' ' || usr.last_name approver_name,
                        apl.status
                FROM ipc.ipc_vpc_approval apl
                    INNER join ipc.ipc_vpc_approvers apr
                        ON apr.id = apl.approver_id
                    INNER JOIN ipc_vpc_users_v usr
                        ON usr.user_id = apr.approver_user_id
                        AND usr.user_source_id = apr.approver_source_id
                WHERE apl.module_reference_id = :module_reference_id
                        and apl.module_id = :module_id
                    AND apl.hierarchy = :hierarchy";
        $query = DB::select($sql,$params);
        return $query;
    }
    
    public function getPromoApprovers(){
        $sql = "SELECT usr.first_name || ' ' || usr.last_name approver_name,
                    nvl(va.email_address, usr.email_address) email,
                    va.id approver_id,
                    usr.user_id,
                    usr.user_source_id
                FROM ipc.ipc_vpc_approvers va                      
                    INNER JOIN ipc_vpc_users_v usr
                        ON usr.user_id = va.approver_user_id
                        AND usr.user_source_id = va.approver_source_id
                    INNER JOIN ipc.ipc_vpc_modules mdl
                        ON mdl.id = va.module_id
                WHERE 1 = 1
                    AND va.module_id = 3";
        $query = DB::select($sql);
        return $query;
    }
    
}
