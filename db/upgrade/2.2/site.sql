DROP PROCEDURE IF EXISTS patch_site;
DELIMITER //
CREATE PROCEDURE patch_site()
  BEGIN

    -- determine the @cenozo database name
    SET @cenozo = ( SELECT REPLACE( DATABASE(), "salix", "cenozo" ) );

    SET @sql = CONCAT(
      "SELECT COUNT(*) INTO @total ",
      "FROM ", @cenozo, ".site ",
      "WHERE name = 'McMaster SCAN'"
    );
    PREPARE statement FROM @sql;
    EXECUTE statement;
    DEALLOCATE PREPARE statement;

    IF @total = 0 THEN
      SELECT "Adding default site to new application" AS "";

      SET @sql = CONCAT(
        "INSERT IGNORE INTO ", @cenozo, ".site ",
        "SET name = 'McMaster SCAN', ",
        "timezone = 'Canada/Eastern'" );
      PREPARE statement FROM @sql;
      EXECUTE statement;
      DEALLOCATE PREPARE statement;
    END IF;

  END //
DELIMITER ;

CALL patch_site();
DROP PROCEDURE IF EXISTS patch_site;
