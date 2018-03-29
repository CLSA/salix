DROP PROCEDURE IF EXISTS patch_apex_scan;
DELIMITER //
CREATE PROCEDURE patch_apex_scan()
  BEGIN

    SELECT "Adding apex_scan.valid column" AS "";

    SET @test = (
      SELECT COUNT(*)
      FROM information_schema.COLUMNS
      WHERE TABLE_SCHEMA = DATABASE()
      AND TABLE_NAME = "apex_scan"
      AND COLUMN_NAME = "valid" );
    IF @test = 0 THEN
      ALTER TABLE apex_scan ADD COLUMN valid TINYINT(1) UNSIGNED NULL DEFAULT NULL;
      UPDATE apex_scan
      JOIN apex_deployment ON apex_scan.id=apex_deployment.apex_scan_id
      SET valid=1;
    END IF;

  END //
DELIMITER ;

CALL patch_apex_scan();
DROP PROCEDURE IF EXISTS patch_apex_scan;
