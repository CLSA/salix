SELECT "Modifying apex_deployment triggers" AS "";

DELIMITER $$

DROP TRIGGER IF EXISTS apex_deployment_AFTER_INSERT $$
CREATE DEFINER = CURRENT_USER TRIGGER apex_deployment_AFTER_INSERT AFTER INSERT ON apex_deployment FOR EACH ROW
BEGIN
  CALL update_apex_deployment_code_summary( NEW.id );

  IF 0 = IFNULL( NEW.pass, 1 ) THEN
    UPDATE apex_scan SET priority = false WHERE id = NEW.apex_scan_id;
 END IF;
END$$


DROP TRIGGER IF EXISTS apex_deployment_AFTER_UPDATE $$
CREATE DEFINER = CURRENT_USER TRIGGER apex_deployment_AFTER_UPDATE AFTER UPDATE ON apex_deployment FOR EACH ROW
BEGIN
  IF 0 = IFNULL( NEW.pass, 1 ) THEN
    UPDATE apex_scan SET priority = false WHERE id = NEW.apex_scan_id;
  END IF;
END$$

DELIMITER ;
