DROP PROCEDURE IF EXISTS patch_event_type;
DELIMITER //
CREATE PROCEDURE patch_event_type()
  BEGIN

    -- determine the @cenozo database name
    SET @cenozo = ( SELECT REPLACE( DATABASE(), "salix", "cenozo" ) );

    SELECT "Adding release to salix as new event_type" AS "";

    SET @sql = CONCAT(
      "INSERT IGNORE INTO ", @cenozo, ".event_type ",
      "SET name = 'released to salix', ",
      "description = 'Released the participant to Salix'" );
    PREPARE statement FROM @sql;
    EXECUTE statement;
    DEALLOCATE PREPARE statement;

  END //
DELIMITER ;

CALL patch_event_type();
DROP PROCEDURE IF EXISTS patch_event_type;
