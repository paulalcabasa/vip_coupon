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
                WHERE ph.id = :claim_header_id";
        $query = DB::select($sql, ['claim_header_id' => $claimHeaderId]);
        return !empty($query) ? $query[0] : $query;
    }

    public function updateStatus($params){
        $this
            ->where([
                [ 'id', '=' , $params['payment_header_id'] ],
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


}
