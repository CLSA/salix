DROP PROCEDURE IF EXISTS patch_report_restriction;
  DELIMITER //
  CREATE PROCEDURE patch_report_restriction()
  BEGIN

    -- determine the @cenozo database name
    SET @cenozo = (
      SELECT unique_constraint_schema
      FROM information_schema.referential_constraints
      WHERE constraint_schema = DATABASE()
      AND constraint_name = "fk_access_site_id" );

    SELECT "Adding records to report_restriction table" AS "";

    SET @sql = CONCAT(
      "INSERT IGNORE INTO ", @cenozo, ".report_restriction ( ",
        "report_type_id, rank, name, title, mandatory, null_allowed, restriction_type, custom, ",
        "subject, operator, enum_list, description ) ",
      "SELECT report_type.id, rank, restriction.name, restriction.title, mandatory, null_allowed, type, custom, ",
             "restriction.subject, operator, enum_list, restriction.description ",
      "FROM ", @cenozo, ".report_type, ( ",
        "SELECT ",
          "1 AS rank, ",
          "'status' AS name, ",
          "'Deployment Status' AS title, ",
          "1 AS mandatory, ",
          "0 AS null_allowed, ",
          "'enum' AS type, ",
          "1 AS custom, ",
          "'status' AS subject, ",
          "NULL AS operator, ",
          "'\"pending\",\"completed\",\"exported\"' AS enum_list, ",
          "'Determine the status type for the report.' AS description ",
        "UNION SELECT ",
          "2 AS rank, ",
          "'priority' AS name, ",
          "'Priority Scans' AS title, ",
          "0 AS mandatory, ",
          "0 AS null_allowed, ",
          "'boolean' AS type, ",
          "0 AS custom, ",
          "'apex_scan.priority' AS subject, ",
          "NULL AS operator, ",
          "NULL AS enum_list, ",
          "'Whether to restrict to priority or non-priority scans.' AS description ",
        "UNION SELECT ",
          "3 AS rank, ",
          "'start_date' AS name, ",
          "'Start Date' AS title, ",
          "0 AS mandatory, ",
          "0 AS null_allowed, ",
          "'date' AS type, ",
          "1 AS custom, ",
          "NULL AS subject, ",
          "'>=' AS operator, ",
          "NULL AS enum_list, ",
          "'Apex deployment status on or after the given date.' AS description ",
        "UNION SELECT ",
          "4 AS rank, ",
          "'end_date' AS name, ",
          "'End Date' AS title, ",
          "0 AS mandatory, ",
          "0 AS null_allowed, ",
          "'date' AS type, ",
          "1 AS custom, ",
          "NULL AS subject, ",
          "'<=' AS operator, ",
          "NULL AS enum_list, ",
          "'Apex deployment status on or before the given date.' AS description ",
      ") AS restriction ",
      "WHERE report_type.name = 'status'" );
    PREPARE statement FROM @sql;
    EXECUTE statement;
    DEALLOCATE PREPARE statement;
  END //
DELIMITER ;

-- now call the procedure and remove the procedure
CALL patch_report_restriction();
DROP PROCEDURE IF EXISTS patch_report_restriction;
