DROP PROCEDURE IF EXISTS patch_apex_baseline;
DELIMITER //
CREATE PROCEDURE patch_apex_baseline()
  BEGIN

    -- determine the @cenozo database name
    SET @cenozo = ( SELECT REPLACE( DATABASE(), "salix", "cenozo" ) );

    SELECT "Creating new apex_baseline table" AS "";

    SET @sql = CONCAT(
      "CREATE TABLE IF NOT EXISTS apex_baseline ( ",
        "id INT UNSIGNED NOT NULL AUTO_INCREMENT, ",
        "update_timestamp TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP, ",
        "create_timestamp TIMESTAMP NOT NULL, ",
        "participant_id INT UNSIGNED NOT NULL, ",
        "dob DATETIME NOT NULL, ",
        "ethnicity ENUM('W', 'B', 'H', 'O') NOT NULL DEFAULT 'W', ",
        "sex ENUM('M', 'F') NOT NULL DEFAULT 'M', ",
        "PRIMARY KEY (id), ",
        "INDEX fk_participant_id (participant_id ASC), ",
        "UNIQUE INDEX uq_participant_id (participant_id ASC), ",
        "CONSTRAINT fk_apex_baseline_participant_id ",
          "FOREIGN KEY (participant_id) ",
          "REFERENCES ", @cenozo, ".participant (id) ",
          "ON DELETE NO ACTION ",
          "ON UPDATE NO ACTION) ",
      "ENGINE = InnoDB" );
    PREPARE statement FROM @sql;
    EXECUTE statement;
    DEALLOCATE PREPARE statement;

  END //
DELIMITER ;

CALL patch_apex_baseline();
DROP PROCEDURE IF EXISTS patch_apex_baseline;


DELIMITER $$

DROP TRIGGER IF EXISTS apex_baseline_BEFORE_INSERT $$
CREATE DEFINER = CURRENT_USER TRIGGER apex_baseline_BEFORE_INSERT BEFORE INSERT ON apex_baseline FOR EACH ROW
BEGIN
SET NEW.create_timestamp = NOW();
END$$

DROP TRIGGER IF EXISTS apex_baseline_AFTER_INSERT $$
CREATE DEFINER = CURRENT_USER TRIGGER apex_baseline_AFTER_INSERT AFTER INSERT ON apex_baseline FOR EACH ROW
BEGIN
  CALL update_apex_baseline_first_apex_exam( NEW.id );
END$$

DELIMITER ;
