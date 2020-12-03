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

    public function setApproval($couponId, $moduleId, $origVehicleType, $newVehicleType, $approvers, $couponType){
        DB::beginTransaction();

        try {
            
            // only delete if vehicle type has been updated
            if($origVehicleType != $newVehicleType && $couponType == 1){
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
                    AND apr.hierarchy = :hierarchy";
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

    public function getAllPending($employee_number){
        $sql = "SELECT *
                FROM (
                
                -- COUPON REQUEST
                SELECT approval.id approval_id,
                            'VIP Coupon' system,
                            'Coupon Request' || ' - ' || coupon.description description,
                            approval.module_reference_id reference_no,
                            to_char(coupon.creation_date,'MM/DD/YYYY') date_requested,
                            portal_users.first_name || ' ' || portal_users.last_name approver_name,
                            request_user.first_name || ' ' || request_user.last_name requestor,
                            portal_users.employee_number approver_employee_number,
                            statuses.status,
                            coupon.coupon_type_id,
                            coupon_type.name coupon_type_name,
                            approval.module_id,
                            portal_users.user_id approver_user_id,
                            portal_users.user_source_id approver_source_id
                FROM ipc.ipc_vpc_approval approval
                    LEFT JOIN ipc.ipc_vpc_coupons coupon
                        ON coupon.id = approval.module_reference_id
                    LEFT JOIN ipc.ipc_vpc_approvers approver
                        ON approver.id = approval.approver_id
                    LEFT JOIN ipc_dms.ipc_portal_users_v portal_users 
                        ON portal_users.user_id = approver.approver_user_id 
                        AND portal_users.user_source_id = approver.approver_source_id
                    LEFT JOIN ipc_dms.ipc_portal_users_v request_user 
                        ON request_user.user_id = coupon.created_by 
                        AND request_user.user_source_id = coupon.create_user_source
                    LEFT JOIN ipc.ipc_vpc_status statuses
                        ON statuses.id = coupon.status
                    LEFT JOIN ipc.ipc_vpc_coupon_types coupon_type
                        ON coupon_type.id = coupon.coupon_type_id
                WHERE approval.module_id = 1
                    AND coupon.current_approval_hierarchy = approver.hierarchy
                    AND approval.status = 1
                    AND coupon.status = 1
                
                UNION
                
                -- CLAIM REQUEST
                SELECT approval.id approval_id,
                            'VIP Coupon' system,
                            'Claim Request - ' || dealer.account_name  description,
                            approval.module_reference_id reference_no,
                            to_char(claim_header.creation_date,'MM/DD/YYYY') date_requested,
                            portal_users.first_name || ' ' || portal_users.last_name approver_name,
                            request_user.first_name || ' ' || request_user.last_name requestor,
                            portal_users.employee_number approver_employee_number,
                            statuses.status,
                            coupon_type.id coupon_type_id,
                            coupon_type.name coupon_type_name,
                            approval.module_id,
                            portal_users.user_id approver_user_id,
                            portal_users.user_source_id approver_source_id
                FROM ipc.ipc_vpc_approval approval
                    LEFT JOIN ipc.ipc_vpc_claim_headers claim_header
                        ON claim_header.id = approval.module_reference_id
                    LEFT JOIN ipc.ipc_vpc_approvers approver
                        ON approver.id = approval.approver_id
                    LEFT JOIN ipc_dms.ipc_portal_users_v portal_users 
                        ON portal_users.user_id = approver.approver_user_id 
                        AND portal_users.user_source_id = approver.approver_source_id
                    LEFT JOIN ipc_dms.ipc_portal_users_v request_user 
                        ON request_user.user_id = claim_header.created_by 
                        AND request_user.user_source_id = claim_header.create_user_source
                    LEFT JOIN ipc.ipc_vpc_status statuses
                        ON statuses.id = claim_header.status
                    LEFT JOIN ipc.ipc_vpc_coupon_types coupon_type
                        ON coupon_type.id = claim_header.coupon_type
                    LEFT JOIN ipc_portal.dealers dealer
                        ON dealer.id = claim_header.dealer_id
                WHERE approval.module_id = 2
                    AND claim_header.current_approval_hierarchy = approver.hierarchy
                    AND approval.status = 1
                    AND claim_header.status = 1
                    
                UNION
                    
                -- PROMO
                SELECT promo.id approval_id,
                            'VIP Coupon' system,
                            'Promo Request' || ' - ' || promo.promo_name description,
                            promo.id reference_no,
                            to_char(promo.created_At,'MM/DD/YYYY') date_requested,
                            approver_user.first_name || ' ' || approver_user.last_name approver_name,
                            request_user.first_name || ' ' || request_user.last_name requestor,
                            approver_user.employee_number approver_employee_number,
                            statuses.status,
                            promo.coupon_type_id,
                            coupon_type.name coupon_type_name,
                            3 module_id,
                            approver_user.user_id approver_user_id,
                            approver_user.user_source_id approver_source_id 
                FROM  ipc.ipc_vpc_promos promo
                    LEFT JOIN ipc_dms.ipc_portal_users_v request_user 
                        ON request_user.user_id = promo.created_by 
                        AND request_user.user_source_id = promo.create_user_source
                    LEFT JOIN ipc.ipc_vpc_status statuses
                        ON statuses.id = promo.status
                    LEFT JOIN ipc.ipc_vpc_coupon_types coupon_type
                        ON coupon_type.id = promo.coupon_type_id
                    LEFT JOIN ipc.ipc_vpc_approvers approver
                        ON approver.module_id = 3
                    LEFT JOIN ipc_dms.ipc_portal_users_v approver_user 
                        ON approver_user.user_id = approver.approver_user_id 
                        AND approver_user.user_source_id = approver.approver_source_id
                
                WHERE 1 = 1
                
                --   AND promo.status = 1
                )
                WHERE approver_employee_number = :approver_employee_number";
        $query = DB::select($sql, ['approver_employee_number' => $employee_number]);
        return $query;
    }
    
}
