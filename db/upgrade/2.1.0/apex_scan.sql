SELECT "Creating new apex_scan table" AS "";

CREATE TABLE IF NOT EXISTS apex_scan (
  id INT UNSIGNED NOT NULL AUTO_INCREMENT,
  update_timestamp TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  create_timestamp TIMESTAMP NOT NULL,
  apex_exam_id INT UNSIGNED NOT NULL,
  apex_host_id INT UNSIGNED NULL DEFAULT NULL,
  compare_apex_exam_id INT UNSIGNED NULL DEFAULT NULL,
  type ENUM('hip', 'iva', 'forearm', 'spine', 'wholebody') NOT NULL,
  side ENUM('left', 'right', 'none') NOT NULL DEFAULT 'none',
  availability INT UNSIGNED NOT NULL DEFAULT 0,
  code_list VARCHAR(128) NULL DEFAULT NULL,
  status ENUM('pending', 'completed', 'exported') NULL DEFAULT NULL,
  pass TINYINT(1) NULL DEFAULT NULL,
  merged TINYINT(1) NOT NULL DEFAULT 0,
  priority TINYINT(1) NOT NULL DEFAULT 0,
  analysis_datetime DATETIME NULL DEFAULT NULL,
  scan_datetime DATETIME NULL DEFAULT NULL,
  serial_number INT UNSIGNED NULL DEFAULT NULL,
  note TEXT NULL DEFAULT NULL,
  PRIMARY KEY (id),
  INDEX fk_apex_exam_id (apex_exam_id ASC),
  INDEX fk_compare_apex_exam_id (compare_apex_exam_id ASC),
  INDEX fk_apex_host_id (apex_host_id ASC),
  UNIQUE INDEX uq_type_side_apex_exam_id (type ASC, side ASC, apex_exam_id ASC),
  CONSTRAINT fk_apex_scan_apex_exam_id
    FOREIGN KEY (apex_exam_id)
    REFERENCES apex_exam (id)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT fk_apex_scan_compare_apex_exam_id
    FOREIGN KEY (compare_apex_exam_id)
    REFERENCES apex_exam (id)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT fk_apex_scan_apex_host_id
    FOREIGN KEY (apex_host_id)
    REFERENCES apex_host (id)
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
