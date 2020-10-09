SELECT  approver.rowid,
            approver.id approver_id,
            users.first_name,
            users.last_name,
            users.email_address,
            approver.hierarchy
FROM ipc.ipc_vpc_approvers approver
    left join ipc_vpc_users_v users
        on users.user_id = approver.approver_user_id
        and users.user_source_id = approver.approver_source_id
where approver.coupon_type_id = 1
    and approver.module_id = 2
    and approver.user_type_id = 51
    and approver.vehicle_type = 'CV'
order by approver.hierarchy;
 
SELECT GET_CUST_BUSINESS_STYLE(15107)
FROM DUAL;

INSERT INTO  ipc.ipc_vpc_approvers approver(
            approver_user_id, 
            approver_source_id,
            coupon_type_id,
            hierarchy,
            approver_type,
            email_address,
            module_id,
            user_type_id,
            vehicle_type) 

SELECT approver_user_id, 
            approver_source_id,
            coupon_type_id,
            hierarchy,
            approver_type,
            email_address,
            module_id,
            45,
            vehicle_type
from ipc.ipc_vpc_approvers approver
where approver.coupon_type_id = 1
    and approver.module_id = 1
    and approver.user_type_id = 51
    and approver.vehicle_type = 'CV';

delete from ipc.ipc_vpc_approvers approver
where approver.coupon_type_id = 1
    and approver.module_id = 1
    and approver.user_type_id = 45
    and approver.vehicle_type = 'CV' ;
