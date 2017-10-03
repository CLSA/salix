DROP PROCEDURE IF EXISTS patch_application_has_cohort;
DELIMITER //
CREATE PROCEDURE patch_application_has_cohort()
  BEGIN

    -- determine the @cenozo database name
    SET @cenozo = ( SELECT REPLACE( DATABASE(), "salix", "cenozo" ) );

    SET @sql = CONCAT(
      "SELECT COUNT(*) INTO @total ",
      "FROM ", @cenozo, ".application_has_cohort ",
      "JOIN ", @cenozo, ".application ON application_has_cohort.application_id = application.id ",
      "WHERE application.name = 'salix'"
    );
    PREPARE statement FROM @sql;
    EXECUTE statement;
    DEALLOCATE PREPARE statement;

    IF @total = 0 THEN
      SELECT "Adding all cohorts to the new salix application" AS "";

      SET @sql = CONCAT(
        "INSERT IGNORE INTO ", @cenozo, ".application_has_cohort( application_id, cohort_id ) ",
        "SELECT application.id, cohort.id ",
        "FROM ", @cenozo, ".application, ", @cenozo, ".cohort ",
        "WHERE application.name = 'salix'" );
      PREPARE statement FROM @sql;
      EXECUTE statement;
      DEALLOCATE PREPARE statement;
    END IF;

  END //
DELIMITER ;

CALL patch_application_has_cohort();
DROP PROCEDURE IF EXISTS patch_application_has_cohort;
