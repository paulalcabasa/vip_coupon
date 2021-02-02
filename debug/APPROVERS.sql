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
    and approver.user_type_id = 45
   and approver.vehicle_type = 'LCV'
order by approver.hierarchy;

SELECT *
FROM IPC.IPC_VPC_CLAIM_HEADERS
WHERE ID = 34;
SELECT A.ROWID,
        A.*
FROM IPC.IPC_VPC_APPROVAL A
WHERE MODULE_REFERENCE_ID = 34
AND MODULE_ID = 2;


select *
from ipc.ipc_vpc_approvers;

select *
from ipc.ipc_vpc_modules; 

select *
from ipc_portal.systems;

select *
from ipc_portal.system_user_types
where system_id = 12;

select *
from ipc_vpc_users_v;

select *
from ipc_dms.ipc_vehicle_models_v
where sales_model = 'D-MAX RZ4E 4X2 LS MT';
select *
from ipc.ipc_jo_job_types;

select attribute2 
from mtl_serial_numbers
where attribute9 = 'D-MAX RZ4E 4X2 LS MT';


SELECT *
FROM MTL_SERIAL_NUMBERS msn
    LEFT JOIN mtl_seria;

select *
from ipc_dms.fs_fpc_validity_request;

select * from ipc.ipc_vpc_modules;
where last_name = 'RAMOS';
 
select *
from ipc.ipc_vpc_coupon_types;
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
