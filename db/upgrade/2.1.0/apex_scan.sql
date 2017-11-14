SELECT "Creating new apex_scan table" AS "";

CREATE TABLE IF NOT EXISTS apex_scan (
  id INT UNSIGNED NOT NULL AUTO_INCREMENT,
  update_timestamp TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  create_timestamp TIMESTAMP NOT NULL,
  apex_exam_id INT UNSIGNED NOT NULL,
  type ENUM('hip', 'lateral', 'forearm', 'spine', 'wbody', 'wbodycomposition') NOT NULL,
  side ENUM('left', 'right', 'none') NOT NULL DEFAULT 'none',
  availability INT UNSIGNED NOT NULL DEFAULT 0,
  scan_datetime DATETIME NULL DEFAULT NULL,
  scanid VARCHAR(13) NULL DEFAULT NULL,
  patient_key VARCHAR(24) NULL DEFAULT NULL,
  PRIMARY KEY (id),
  INDEX fk_apex_exam_id (apex_exam_id ASC),
  UNIQUE INDEX uq_apex_exam_id_type_side (apex_exam_id ASC, type ASC, side ASC),
  CONSTRAINT fk_apex_scan_apex_exam_id
    FOREIGN KEY (apex_exam_id)
    REFERENCES apex_exam (id)
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
