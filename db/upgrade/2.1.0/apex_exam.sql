SELECT "Creating new apex_exam table" AS "";

CREATE TABLE IF NOT EXISTS apex_exam (
  id INT UNSIGNED NOT NULL AUTO_INCREMENT,
  update_timestamp TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  create_timestamp TIMESTAMP NOT NULL,
  apex_baseline_id INT UNSIGNED NOT NULL,
  height FLOAT NULL DEFAULT NULL,
  weight FLOAT NULL DEFAULT NULL,
  age FLOAT NULL DEFAULT NULL,
  barcode VARCHAR(10) NOT NULL,
  site VARCHAR(45) NOT NULL,
  wave_rank INT UNSIGNED NOT NULL,
  technician VARCHAR(128) NULL DEFAULT NULL,
  PRIMARY KEY (id),
  INDEX fk_apex_baseline_id (apex_baseline_id ASC),
  UNIQUE INDEX uq_barcode_site_wave_rank (barcode ASC, site ASC, wave_rank ASC),
  CONSTRAINT fk_apex_exam_apex_baseline_id
    FOREIGN KEY (apex_baseline_id)
    REFERENCES apex_baseline (id)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


DELIMITER $$

DROP TRIGGER IF EXISTS apex_exam_BEFORE_INSERT $$
CREATE DEFINER = CURRENT_USER TRIGGER apex_exam_BEFORE_INSERT BEFORE INSERT ON apex_exam FOR EACH ROW
BEGIN
SET NEW.create_timestamp = NOW();
END$$

DELIMITER ;
