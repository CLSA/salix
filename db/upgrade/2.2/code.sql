DROP PROCEDURE IF EXISTS patch_code;
DELIMITER //
CREATE PROCEDURE patch_code()
  BEGIN

    -- determine the @cenozo database name
    SET @cenozo = ( SELECT REPLACE( DATABASE(), "salix", "cenozo" ) );

    SELECT "Creating new code table" AS "";

    SET @sql = CONCAT(
      "CREATE TABLE IF NOT EXISTS code ( ",
        "id INT UNSIGNED NOT NULL AUTO_INCREMENT, ",
        "update_timestamp TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP, ",
        "create_timestamp TIMESTAMP NOT NULL, ",
        "apex_scan_id INT UNSIGNED NOT NULL, ",
        "code_type_id INT UNSIGNED NOT NULL, ",
        "user_id INT UNSIGNED NOT NULL, ",
        "PRIMARY KEY (id), ",
        "INDEX fk_apex_scan_id (apex_scan_id ASC), ",
        "INDEX fk_code_type_id (code_type_id ASC), ",
        "INDEX fk_user_id (user_id ASC), ",
        "CONSTRAINT fk_code_code_type_id ",
          "FOREIGN KEY (code_type_id) ",
          "REFERENCES code_type (id) ",
          "ON DELETE NO ACTION ",
          "ON UPDATE NO ACTION, ",
        "CONSTRAINT fk_code_apex_scan_id ",
          "FOREIGN KEY (apex_scan_id) ",
          "REFERENCES apex_scan (id) ",
          "ON DELETE NO ACTION ",
          "ON UPDATE NO ACTION, ",
        "CONSTRAINT fk_code_user_id ",
          "FOREIGN KEY (user_id) ",
          "REFERENCES ", @cenozo, ".user (id) ",
          "ON DELETE NO ACTION ",
          "ON UPDATE NO ACTION) ",
      "ENGINE = InnoDB" );
    PREPARE statement FROM @sql;
    EXECUTE statement;
    DEALLOCATE PREPARE statement;

  END //
DELIMITER ;

CALL patch_code();
DROP PROCEDURE IF EXISTS patch_code;


DELIMITER $$

DROP TRIGGER IF EXISTS code_BEFORE_INSERT $$
CREATE DEFINER = CURRENT_USER TRIGGER code_BEFORE_INSERT BEFORE INSERT ON code FOR EACH ROW
BEGIN
SET NEW.create_timestamp = NOW();
END$$


DROP TRIGGER IF EXISTS code_AFTER_INSERT $$
CREATE DEFINER = CURRENT_USER TRIGGER code_AFTER_INSERT AFTER INSERT ON code FOR EACH ROW
BEGIN
  CALL update_apex_scan_code_summary( NEW.apex_scan_id );
END$$


DROP TRIGGER IF EXISTS code_AFTER_UPDATE $$
CREATE DEFINER = CURRENT_USER TRIGGER code_AFTER_UPDATE AFTER UPDATE ON code FOR EACH ROW
BEGIN
  CALL update_apex_scan_code_summary( NEW.apex_scan_id );
END$$

DELIMITER ;
