-- REVERT GENERATE SCRIPT

DECLARE
    v_coupon_id number := :p_coupon_id;
BEGIN

    -- cancel generated vouchers
    UPDATE ipc.ipc_vpc_vouchers 
    SET status = 4
    WHERE coupon_id = v_coupon_id;
    
    -- set coupon request to pending again
    UPDATE ipc.ipc_vpc_coupons
    SET status = 1,
           is_sent = 'N',
           date_sent = NULL
    WHERE id = v_coupon_id;
    

    
    -- set last approver to pending to approve and re run generate
    UPDATE ipc.ipc_vpc_approval 
    SET status = 1,
            mail_sent_flag = 'N',
           DATE_MAIL_SENT = NULL
    WHERE module_reference_id = v_coupon_id
        AND hierarchy = (
                                    select max(hierarchy)
                                    from ipc.ipc_vpc_approval
                                    where module_reference_id = v_coupon_id
                                            and module_id = 1
                                );
    
    -- SAVE THE UPDATE
    commit;
END;
