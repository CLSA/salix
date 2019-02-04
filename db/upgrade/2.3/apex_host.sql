DROP PROCEDURE IF EXISTS patch_apex_host;
DELIMITER //
CREATE PROCEDURE patch_apex_host()
  BEGIN

    -- determine the @cenozo database name
    SET @cenozo = ( SELECT REPLACE( DATABASE(), "salix", "cenozo" ) );

    SELECT "Removing allocation column from apex_host table" AS "";

    SELECT COUNT(*) INTO @test
    FROM information_schema.COLUMNS
    WHERE TABLE_SCHEMA = DATABASE()
    AND TABLE_NAME = "apex_host"
    AND COLUMN_NAME = "allocation";
    IF @test = 1 THEN
      INSERT IGNORE INTO allocation( apex_host_id, scan_type_id, weight )
      SELECT apex_host.id, scan_type.id, 1
      FROM apex_host, scan_type
      WHERE apex_host.allocation > 0;

      ALTER TABLE apex_host DROP COLUMN allocation;
    END IF;

  END //
DELIMITER ;

CALL patch_apex_host();
DROP PROCEDURE IF EXISTS patch_apex_host;
