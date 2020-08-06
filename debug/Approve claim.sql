DECLARE 
    v_claim_header_id number := :v_claim_header_id;

BEGIN
       
    -- UPDATE STATUS OF APPROVAL
    UPDATE ipc.ipc_vpc_approval
    SET status = 2,
            mail_sent_flag = 'Y',
            date_mail_sent = SYSDATE
    WHERE module_reference_id = v_claim_header_id
        AND module_id = 2;
    
    
    -- UPDATE STATUS OF COUPON 
    UPDATE  ipc.ipc_vpc_claim_headers
    SET status = 2,
            current_approval_hierarchy = (
                SELECT max(hierarchy)
                FROM ipc.ipc_vpc_approval
                WHERE module_reference_id = v_claim_header_id
                    AND module_id = 2
            )
    WHERE id = v_claim_header_id;
    
    
    COMMIT;
END;

