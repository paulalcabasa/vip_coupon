CREATE VIEW ipc_central.VPC_USERS AS SELECT 
  pt.employee_id,
  emt.employee_no, 
  pt.password,
  pit.first_name,
  pit.middle_name,
  pit.last_name,
  uat.user_type_id,
  utt.user_type
FROM ipc_central.employee_masterfile_tab emt 
  INNER JOIN ipc_central.password_tab pt 
    ON emt.id = pt.employee_id 
  INNER JOIN ipc_central.personal_information_tab pit 
    ON pit.employee_id = emt.id 
  INNER JOIN ipc_central.user_access_tab uat
		ON uat.employee_id = emt.id
	INNER JOIN ipc_central.user_type_tab utt
		ON utt.id = uat.user_type_id
WHERE 1 = 1 
	 AND uat.system_id = 64
   AND emt.status_id IN (1, 2, 3, 4) ;
