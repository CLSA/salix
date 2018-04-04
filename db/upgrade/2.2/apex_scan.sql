SELECT "Creating new apex_scan table" AS "";

CREATE TABLE IF NOT EXISTS apex_scan (
  id INT UNSIGNED NOT NULL AUTO_INCREMENT,
  update_timestamp TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  create_timestamp TIMESTAMP NOT NULL,
  apex_exam_id INT UNSIGNED NOT NULL,
  scan_type_id INT UNSIGNED NOT NULL,
  availability TINYINT(1) UNSIGNED NOT NULL DEFAULT 0,
  priority TINYINT(1) UNSIGNED NOT NULL DEFAULT 0,
  invalid TINYINT(1) UNSIGNED NULL DEFAULT NULL,
  scan_datetime DATETIME NULL DEFAULT NULL,
  scanid VARCHAR(13) NULL DEFAULT NULL,
  patient_key VARCHAR(24) NULL DEFAULT NULL,
  PRIMARY KEY (id),
  INDEX fk_apex_exam_id (apex_exam_id ASC),
  UNIQUE INDEX uq_apex_exam_id_scan_type_id (apex_exam_id ASC, scan_type_id ASC),
  INDEX fk_scan_type_id (scan_type_id ASC),
  CONSTRAINT fk_apex_scan_apex_exam_id
    FOREIGN KEY (apex_exam_id)
    REFERENCES apex_exam (id)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT fk_apex_scan_scan_type_id
    FOREIGN KEY (scan_type_id)
    REFERENCES scan_type (id)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


DELIMITER $$

DROP TRIGGER IF EXISTS apex_scan_BEFORE_INSERT $$
CREATE DEFINER = CURRENT_USER TRIGGER apex_scan_BEFORE_INSERT BEFORE INSERT ON apex_scan FOR EACH ROW
BEGIN
SET NEW.create_timestamp = NOW();
END$$

DELIMITER ;
