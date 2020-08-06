<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use DB;

class ClaimHeader extends Model
{
    protected $connection = "oracle";
    protected $table = "ipc.ipc_vpc_claim_headers";
    const CREATED_AT = 'creation_date';
    const UPDATED_AT = 'update_date';
    protected $primaryKey = 'id';

    public function insertHeader($params){
        return $this->insertGetId($params);
    }

    public function getHeaders($status){

        $where = "";

        if($status == "pending"){
            $where .= 'AND ph.status = 1';
        }

        $sql = "SELECT ph.id,
                    st.status,
                    ph.creation_date,
                    usr.first_name || ' ' || usr.last_name created_by,
                    TRIM(TO_CHAR(ph.creation_date, 'Month')) || ' ' ||  TO_CHAR(ph.creation_date,'DD, YYYY') date_created,
                    lower(st.status) status
                FROM ipc.ipc_vpc_claim_headers ph
                LEFT JOIN ipc.ipc_vpc_status st
                    ON ph.status = st.id
                LEFT JOIN apps.ipc_vpc_users_v usr
                    ON usr.user_id = ph.created_by
                    AND usr.user_source_id = ph.create_user_source
                WHERE 1 = 1";
        
        $sql .= $where;
        $query = DB::select($sql);
        return $query;
    }

    public function getByUser($params){

        $sql = "SELECT ph.id,
                    st.status,
                    ph.creation_date,
                    usr.first_name || ' ' || usr.last_name created_by,
                    TRIM(TO_CHAR(ph.creation_date, 'Month')) || ' ' ||  TO_CHAR(ph.creation_date,'DD, YYYY') date_created,
                    lower(st.status) status
                FROM ipc.ipc_vpc_claim_headers ph
                LEFT JOIN ipc.ipc_vpc_status st
                    ON ph.status = st.id
                LEFT JOIN apps.ipc_vpc_users_v usr
                    ON usr.user_id = ph.created_by
                    AND usr.user_source_id = ph.create_user_source
                WHERE 1 = 1
                    AND usr.user_id = :userId
                    AND usr.user_source_id = :sourceId";
        
 
        $query = DB::select($sql, $params);
        return $query;
    }

    public function get($claimHeaderId){
        $sql = "SELECT ph.id,
                        ph.creation_date,
                        usr.first_name || ' ' || usr.last_name created_by,
                        TRIM(TO_CHAR(ph.creation_date, 'Month')) || ' ' ||  TO_CHAR(ph.creation_date,'DD, YYYY') date_created,
                        lower(st.status) status,
                        ph.vehicle_type,
                        ct.name coupon_type,
                        ph.dealer_id,
                        dlr.account_name dealer,
                        sum(cl.amount) total_amount,
                        ct.id coupon_type_id,
                        ph.current_approval_hierarchy
                FROM ipc.ipc_vpc_claim_headers ph
                    LEFT JOIN ipc.ipc_vpc_status st
                        ON ph.status = st.id
                    LEFT JOIN apps.ipc_vpc_users_v usr
                        ON usr.user_id = ph.created_by
                        AND usr.user_source_id = ph.create_user_source
                    LEFT JOIN ipc.ipc_vpc_coupon_types ct
                        ON ct.id = ph.coupon_type
                    LEFT JOIN ipc_portal.dealers dlr
                        ON dlr.id = ph.dealer_id
                    LEFT JOIN ipc.ipc_vpc_claim_lines cl
                        ON cl.claim_header_id = ph.id
                WHERE ph.id = :claim_header_id
                GROUP BY
                        ph.id,
                        ph.creation_date,
                        usr.first_name, usr.last_name ,
                        ph.creation_date,
                        st.status,
                        ph.vehicle_type,
                        ct.name,
                        ph.dealer_id,
                        dlr.account_name,
                        ct.id,
                        ph.current_approval_hierarchy";
        $query = DB::select($sql, ['claim_header_id' => $claimHeaderId]);
        return !empty($query) ? $query[0] : $query;
    }

    public function updateStatus($params){
        $this
            ->where([
                [ 'id', '=' , $params['claim_header_id'] ],
            ])
            ->update([
                'status'             => $params['status'],
                'updated_by'         => $params['updated_by'],
                'update_user_source' => $params['update_user_source']
            ]);
    }

    public function getPendingApproval(){
        $sql = "SELECT usr.first_name || ' ' || usr.last_name approver_name,
                        nvl(va.email_address, usr.email_address) email_address,
                        apl.mail_sent_flag,
                        apl.date_mail_sent,
                        apl.id,
                        apl.module_reference_id,
                        apl.module_id,
                        mdl.module
                FROM ipc.ipc_vpc_claim_headers cp
                    INNER JOIN ipc.ipc_vpc_approval apl
                        ON apl.module_reference_id = cp.id
                        AND apl.module_id = 2
                    INNER JOIN ipc.ipc_vpc_approvers va
                        ON va.id = apl.approver_id
                        AND cp.current_approval_hierarchy = va.hierarchy
                    INNER JOIN ipc_vpc_users_v usr
                        ON usr.user_id = va.approver_user_id
                        AND usr.user_source_id = va.approver_source_id
                    INNER JOIN ipc.ipc_vpc_modules mdl
                        ON mdl.id = apl.module_id
                WHERE 1 = 1
                    AND apl.mail_sent_flag = 'N'";
        $query = DB::select($sql);
        return $query;
    }
    

    public function updateCurrentApprovalHierarchy($params){
        $this
            ->where([
                [ 'id', '=' , $params['claim_header_id'] ],
            ])
            ->update([
                'current_approval_hierarchy'             => $params['next_hierarchy']
            ]);
    }

    public function getApproved(){
        $sql = "SELECT usr.first_name || ' ' || usr.last_name approver_name,
                        usr.email_address,
                        cp.date_mail_sent,
                        cp.id claim_header_id
                FROM ipc.ipc_vpc_claim_headers cp
               
                    INNER JOIN ipc_vpc_users_v usr
                        ON usr.user_id = cp.created_by
                        AND usr.user_source_id = cp.create_user_source
            
                WHERE 1 = 1
                    AND cp.status = 2
                    AND cp.date_mail_sent IS NULL";
        $query = DB::select($sql);
        return $query;
    }
    


}
