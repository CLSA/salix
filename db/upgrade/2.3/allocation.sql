SELECT "Creating new allocation table" AS "";

CREATE TABLE IF NOT EXISTS allocation (
  id INT UNSIGNED NOT NULL AUTO_INCREMENT,
  update_timestamp TIMESTAMP NOT NULL,
  create_timestamp TIMESTAMP NOT NULL,
  apex_host_id INT UNSIGNED NOT NULL,
  scan_type_id INT UNSIGNED NOT NULL,
  weight FLOAT NOT NULL,
  PRIMARY KEY (id),
  INDEX fk_apex_host_id (apex_host_id ASC),
  INDEX fk_scan_type_id (scan_type_id ASC),
  UNIQUE INDEX uq_apex_host_id_scan_type_id (apex_host_id ASC, scan_type_id ASC),
  CONSTRAINT fk_allocation_apex_host_id
    FOREIGN KEY (apex_host_id)
    REFERENCES apex_host (id)
    ON DELETE CASCADE
    ON UPDATE CASCADE,
  CONSTRAINT fk_allocation_scan_type_id
    FOREIGN KEY (scan_type_id)
    REFERENCES scan_type (id)
    ON DELETE CASCADE
    ON UPDATE CASCADE)
ENGINE = InnoDB;
