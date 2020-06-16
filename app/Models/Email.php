<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use DB;

class Email extends Model
{
    public function getCouponApprovalMail(){
        $sql = "SELECT usr.first_name || ' ' || usr.last_name approver_name,
                        va.email_address,
                        apl.mail_sent_flag,
                        apl.date_mail_sent,
                        apl.id,
                        apl.module_reference_id,
                        apl.module_id,
                        mdl.module
                FROM ipc.ipc_vpc_coupons cp
                    INNER JOIN ipc.ipc_vpc_approval apl
                        ON apl.module_reference_id = cp.id
                        AND apl.module_id = 1
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

    public function getMailCredentials(){
        $sql = "SELECT nse.id,
                            ne.email,
                            ne.email_password,
                            st.system
                FROM ipc_central.notification_system_email nse
                    LEFT JOIN ipc_central.system_tab st 
                        ON nse.system_id = st.id
                    LEFT JOIN ipc_central.notification_emails ne
                        ON ne.id = nse.notif_email_id
                WHERE st.system = 'VIP Coupon'";
        $query = DB::connection('ipc_central')->select($sql);
        return !empty($query)  ? $query[0] : $query;
    }
}
