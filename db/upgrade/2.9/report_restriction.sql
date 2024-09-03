DROP PROCEDURE IF EXISTS patch_report_restriction;
  DELIMITER //
  CREATE PROCEDURE patch_report_restriction()
  BEGIN

    -- determine the @cenozo database name
    SET @cenozo = (
      SELECT unique_constraint_schema
      FROM information_schema.referential_constraints
      WHERE constraint_schema = DATABASE()
      AND constraint_name = "fk_access_site_id"
    );

    SELECT "Adding records to report_restriction table" AS "";

    SET @sql = CONCAT(
      "INSERT IGNORE INTO ", @cenozo, ".report_restriction ( ",
        "report_type_id, rank, name, title, mandatory, null_allowed, ",
        "restriction_type, custom, subject, enum_list, description ",
      ") ",
      "SELECT ",
        "report_type.id, 1, 'wave', 'Wave', 0, 0, ",
        "'enum', 0, 'apex_exam.rank', '\"1\",\"2\",\"3\",\"4\"', ",
        "'Restrict to a particular study wave.' ",
      "FROM ", @cenozo, ".report_type ",
      "WHERE report_type.name = 'analysis'" );
    PREPARE statement FROM @sql;
    EXECUTE statement;
    DEALLOCATE PREPARE statement;

    SET @sql = CONCAT(
      "INSERT IGNORE INTO ", @cenozo, ".report_restriction ( ",
        "report_type_id, rank, name, title, mandatory, null_allowed, ",
        "restriction_type, custom, subject, description ",
      ") ",
      "SELECT ",
        "report_type.id, 2, 'scan_type', 'Scan Type', 0, 0, ",
        "'table', 1, 'scan_type', ",
        "'Restrict to a particular scan type.' ",
      "FROM ", @cenozo, ".report_type ",
      "WHERE report_type.name = 'analysis'" );
    PREPARE statement FROM @sql;
    EXECUTE statement;
    DEALLOCATE PREPARE statement;

    SET @sql = CONCAT(
      "INSERT IGNORE INTO ", @cenozo, ".report_restriction ( ",
        "report_type_id, rank, name, title, mandatory, null_allowed, ",
        "restriction_type, custom, subject, operator, description ",
      ") ",
      "SELECT ",
        "report_type.id, 3, 'start_date', 'Start Date', 0, 0, ",
        "'date', 0, 'apex_deployment.analysis_datetime', '>=', ",
        "'Analysis completed on or after the given date.' ",
      "FROM ", @cenozo, ".report_type ",
      "WHERE report_type.name = 'analysis'" );
    PREPARE statement FROM @sql;
    EXECUTE statement;
    DEALLOCATE PREPARE statement;

    SET @sql = CONCAT(
      "INSERT IGNORE INTO ", @cenozo, ".report_restriction ( ",
        "report_type_id, rank, name, title, mandatory, null_allowed, ",
        "restriction_type, custom, subject, operator, description ",
      ") ",
      "SELECT ",
        "report_type.id, 4, 'end_date', 'End Date', 0, 0, ",
        "'date', 0, 'apex_deployment.analysis_datetime', '<=', ",
        "'Analysis completed on or before the given date.' ",
      "FROM ", @cenozo, ".report_type ",
      "WHERE report_type.name = 'analysis'" );
    PREPARE statement FROM @sql;
    EXECUTE statement;
    DEALLOCATE PREPARE statement;

  END //
DELIMITER ;

-- now call the procedure and remove the procedure
CALL patch_report_restriction();
DROP PROCEDURE IF EXISTS patch_report_restriction;
