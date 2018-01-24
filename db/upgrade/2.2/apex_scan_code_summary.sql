SELECT "Creating apex_scan_code_summary table" AS "";

CREATE TABLE IF NOT EXISTS apex_scan_code_summary (
  apex_scan_id INT UNSIGNED NOT NULL,
  update_timestamp TIMESTAMP NOT NULL,
  create_timestamp TIMESTAMP NOT NULL,
  summary VARCHAR(255) NULL,
  PRIMARY KEY (apex_scan_id),
  INDEX fk_apex_scan_id (apex_scan_id ASC),
  CONSTRAINT fk_apex_scan_code_summary_apex_scan_id
    FOREIGN KEY (apex_scan_id)
    REFERENCES apex_scan (id)
    ON DELETE CASCADE
    ON UPDATE CASCADE)
ENGINE = InnoDB;

INSERT IGNORE INTO apex_scan_code_summary( apex_scan_id, summary )
SELECT apex_scan.id, GROUP_CONCAT( code_type.code ORDER BY code_type.code SEPARATOR ', ' )
FROM apex_scan
LEFT JOIN code ON apex_scan.id = code.apex_scan_id
LEFT JOIN code_type ON code.code_type_id = code_type.id
GROUP BY apex_scan.id;
