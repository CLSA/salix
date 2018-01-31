SELECT "Creating update_apex_deployment_code_summary procedure" AS "";

DROP procedure IF EXISTS update_apex_deployment_code_summary;

DELIMITER $$

CREATE PROCEDURE update_apex_deployment_code_summary (IN proc_apex_deployment_id INT(10) UNSIGNED)
BEGIN
  REPLACE INTO apex_deployment_code_summary( apex_deployment_id, summary )
  SELECT apex_deployment.id, GROUP_CONCAT( code_type.code ORDER BY code_type.code SEPARATOR ', ' )
  FROM apex_deployment
  LEFT JOIN code ON apex_deployment.id = code.apex_deployment_id
  LEFT JOIN code_type ON code.code_type_id = code_type.id
  WHERE apex_deployment.id = proc_apex_deployment_id;
END$$

DELIMITER ;
