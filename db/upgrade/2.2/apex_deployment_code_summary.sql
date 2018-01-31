SELECT "Creating apex_deployment_code_summary table" AS "";

CREATE TABLE IF NOT EXISTS apex_deployment_code_summary (
  apex_deployment_id INT UNSIGNED NOT NULL,
  update_timestamp TIMESTAMP NOT NULL,
  create_timestamp TIMESTAMP NOT NULL,
  summary VARCHAR(255) NULL,
  PRIMARY KEY (apex_deployment_id),
  INDEX fk_apex_deployment_id (apex_deployment_id ASC),
  CONSTRAINT fk_apex_deployment_code_summary_apex_deployment_id
    FOREIGN KEY (apex_deployment_id)
    REFERENCES apex_deployment (id)
    ON DELETE CASCADE
    ON UPDATE CASCADE)
ENGINE = InnoDB;

INSERT IGNORE INTO apex_deployment_code_summary( apex_deployment_id, summary )
SELECT apex_deployment.id, GROUP_CONCAT( code_type.code ORDER BY code_type.code SEPARATOR ', ' )
FROM apex_deployment
LEFT JOIN code ON apex_deployment.id = code.apex_deployment_id
LEFT JOIN code_type ON code.code_type_id = code_type.id
GROUP BY apex_deployment.id;
