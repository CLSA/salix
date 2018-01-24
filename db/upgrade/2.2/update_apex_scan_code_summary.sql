SELECT "Creating update_apex_scan_code_summary procedure" AS "";

DROP procedure IF EXISTS update_apex_scan_code_summary;

DELIMITER $$

CREATE PROCEDURE update_apex_scan_code_summary (IN proc_apex_scan_id INT(10) UNSIGNED)
BEGIN
  REPLACE INTO apex_scan_code_summary( apex_scan_id, summary )
  SELECT apex_scan.id, GROUP_CONCAT( code_type.code ORDER BY code_type.code SEPARATOR ', ' )
  FROM apex_scan
  LEFT JOIN code ON apex_scan.id = code.apex_scan_id
  LEFT JOIN code_type ON code.code_type_id = code_type.id
  WHERE apex_scan.id = proc_apex_scan_id;
END$$

DELIMITER ;
