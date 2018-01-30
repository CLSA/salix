SELECT "Creating new scan_type_has_code_type table" AS "";

CREATE TABLE IF NOT EXISTS scan_type_has_code_type (
  code_type_id INT UNSIGNED NOT NULL,
  scan_type_id INT UNSIGNED NOT NULL,
  update_timestamp TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  create_timestamp TIMESTAMP NOT NULL,
  PRIMARY KEY (code_type_id, scan_type_id),
  INDEX fk_scan_type_id (scan_type_id ASC),
  INDEX fk_code_type_id (code_type_id ASC),
  CONSTRAINT fk_scan_type_has_code_type_code_type_id
    FOREIGN KEY (code_type_id)
    REFERENCES code_type (id)
    ON DELETE CASCADE
    ON UPDATE CASCADE,
  CONSTRAINT fk_scan_type_has_code_type_scan_type_id
    FOREIGN KEY (scan_type_id)
    REFERENCES scan_type (id)
    ON DELETE CASCADE
    ON UPDATE CASCADE)
ENGINE = InnoDB;


DELIMITER $$

DROP TRIGGER IF EXISTS scan_type_has_code_type_BEFORE_INSERT $$
CREATE DEFINER = CURRENT_USER TRIGGER scan_type_has_code_type_BEFORE_INSERT BEFORE INSERT ON scan_type_has_code_type FOR EACH ROW
BEGIN
SET NEW.create_timestamp = NOW();
END$$

DELIMITER ;

SET @scan_type_id:=(SELECT id FROM scan_type WHERE type='hip' AND side='left');
INSERT IGNORE INTO scan_type_has_code_type (scan_type_id, code_type_id)
SELECT @scan_type_id, id
FROM
code_type;

SET @scan_type_id:=(SELECT id FROM scan_type WHERE type='hip' AND side='right');
INSERT IGNORE INTO scan_type_has_code_type (scan_type_id, code_type_id)
SELECT @scan_type_id, id
FROM
code_type;
