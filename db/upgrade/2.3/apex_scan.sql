DROP PROCEDURE IF EXISTS patch_apex_scan;
DELIMITER //
CREATE PROCEDURE patch_apex_scan()
  BEGIN

    -- determine the @cenozo database name
    SET @cenozo = ( SELECT REPLACE( DATABASE(), "salix", "cenozo" ) );

    SELECT "Adding new forearm_length column to apex_scan table" AS "";

    SELECT COUNT(*) INTO @test
    FROM information_schema.COLUMNS
    WHERE TABLE_SCHEMA = DATABASE()
    AND TABLE_NAME = "apex_scan"
    AND COLUMN_NAME = "forearm_length";
    IF @test = 0 THEN
      ALTER TABLE apex_scan
      ADD COLUMN forearm_length FLOAT NULL DEFAULT NULL
      AFTER patient_key;
    END IF;

  END //
DELIMITER ;

CALL patch_apex_scan();
DROP PROCEDURE IF EXISTS patch_apex_scan;
