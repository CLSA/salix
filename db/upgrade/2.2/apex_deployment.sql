SELECT "Creating new apex_deployment table" AS "";

CREATE TABLE IF NOT EXISTS apex_deployment (
  id INT UNSIGNED NOT NULL AUTO_INCREMENT,
  update_timestamp TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  create_timestamp TIMESTAMP NOT NULL,
  apex_scan_id INT UNSIGNED NOT NULL,
  apex_host_id INT UNSIGNED NOT NULL,
  merged TINYINT(1) NOT NULL DEFAULT 0,
  priority TINYINT(1) NOT NULL DEFAULT 0,
  status ENUM('pending', 'completed', 'exported') NULL DEFAULT NULL,
  pass TINYINT(1) NULL DEFAULT NULL,
  comp_scanid VARCHAR(13) NULL DEFAULT NULL,
  analysis_datetime DATETIME NULL DEFAULT NULL,
  export_datetime DATETIME NULL DEFAULT NULL,
  import_datetime DATETIME NULL DEFAULT NULL,
  note TEXT NULL DEFAULT NULL,
  PRIMARY KEY (id),
  INDEX fk_apex_scan_id (apex_scan_id ASC),
  INDEX fk_apex_host_id (apex_host_id ASC),
  UNIQUE INDEX uq_apex_scan_id_apex_host_id (apex_scan_id ASC, apex_host_id ASC),
  CONSTRAINT fk_apex_deployment_apex_scan_id
    FOREIGN KEY (apex_scan_id)
    REFERENCES apex_scan (id)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT fk_apex_deployment_apex_host_id
    FOREIGN KEY (apex_host_id)
    REFERENCES apex_host (id)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;

DELIMITER $$

DROP TRIGGER IF EXISTS apex_deployment_BEFORE_INSERT $$
CREATE DEFINER = CURRENT_USER TRIGGER apex_deployment_BEFORE_INSERT BEFORE INSERT ON apex_deployment FOR EACH ROW
BEGIN
SET NEW.create_timestamp = NOW();
END$$

DELIMITER ;
