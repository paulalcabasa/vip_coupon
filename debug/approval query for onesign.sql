SELECT *
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
WHERE approver_employee_number = :approver_employee_number ;
    

    
    select *
    from ipc.ipc_vpc_promos;
    
select *
from ipc.ipc_vpc_modules;

select *
from ipc_portal.dealers;

select *
from ipc.ipc_vpc_claim_headers;
select *
from ipc.ipc_vpc_coupons;
select A.ROWID,
        A.*
from ipc_portal.user_details A
where user_id =858;


select *
from ipc.ipc_vpc_coupon_types;
select *
from ipc_dms.ipc_portal_users_v;
SELECT *
FROM IPC_VPC_USERS_V;

select *
from  ipc.ipc_vpc_approvers ;
SELECT *
FROM ipc.ipc_vpc_approval;

SELECT *
from ipc.ipc_vpc_coupons;



SELECT *
FROM ipc.ipc_Vpc_status;