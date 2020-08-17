DECLARE 
    v_coupon_id number := :p_coupon_id;

BEGIN
       
    -- UPDATE STATUS OF APPROVAL
    UPDATE ipc.ipc_vpc_approval
    SET status = 2,
            mail_sent_flag = 'Y',
            date_mail_sent = SYSDATE
    WHERE module_reference_id = v_coupon_id
        AND module_id = 1;
    
    
    -- UPDATE STATUS OF COUPON 
        UPDATE  ipc.ipc_vpc_coupons
        SET status = 2,
                current_approval_hierarchy = (
                    SELECT max(hierarchy)
                    FROM ipc.ipc_vpc_approval
                    WHERE module_reference_id = v_coupon_id
                        AND module_id = 1
                )
        WHERE id = v_coupon_id;
    
    
    COMMIT;
END;

