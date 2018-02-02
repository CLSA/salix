SELECT "Creating new service table" AS "";

CREATE TABLE IF NOT EXISTS service(
  id INT UNSIGNED NOT NULL AUTO_INCREMENT,
  update_timestamp TIMESTAMP NOT NULL,
  create_timestamp TIMESTAMP NOT NULL,
  method ENUM('DELETE','GET','PATCH','POST','PUT') NOT NULL,
  subject VARCHAR(45) NOT NULL,
  resource TINYINT(1) NOT NULL DEFAULT 0,
  restricted TINYINT(1) NOT NULL DEFAULT 1,
  PRIMARY KEY (id),
  UNIQUE INDEX uq_method_subject_resource (method ASC, subject ASC, resource ASC))
ENGINE = InnoDB;

-- rebuild the service list
DELETE FROM service;
ALTER TABLE service AUTO_INCREMENT = 1;
INSERT INTO service ( subject, method, resource, restricted ) VALUES

-- framework services
( 'access', 'DELETE', 1, 1 ),
( 'access', 'GET', 0, 1 ),
( 'access', 'POST', 0, 1 ),
( 'activity', 'GET', 0, 1 ),
( 'application', 'GET', 0, 1 ),
( 'application', 'GET', 1, 0 ),
( 'application', 'PATCH', 1, 1 ),
( 'application_type', 'GET', 0, 0 ),
( 'application_type', 'GET', 1, 0 ),
( 'cohort', 'GET', 0, 0 ),
( 'failed_login', 'GET', 0, 1 ),
( 'language', 'GET', 0, 0 ),
( 'language', 'GET', 1, 0 ),
( 'overview', 'GET', 0, 0 ),
( 'overview', 'GET', 1, 0 ),
( 'region', 'GET', 0, 0 ),
( 'region', 'GET', 1, 0 ),
( 'report', 'DELETE', 1, 1 ),
( 'report', 'GET', 0, 1 ),
( 'report', 'GET', 1, 1 ),
( 'report', 'PATCH', 1, 1 ),
( 'report', 'POST', 0, 1 ),
( 'report_restriction', 'DELETE', 1, 1 ),
( 'report_restriction', 'GET', 0, 1 ),
( 'report_restriction', 'GET', 1, 1 ),
( 'report_restriction', 'PATCH', 1, 1 ),
( 'report_restriction', 'POST', 0, 1 ),
( 'report_schedule', 'DELETE', 1, 1 ),
( 'report_schedule', 'GET', 0, 1 ),
( 'report_schedule', 'GET', 1, 1 ),
( 'report_schedule', 'PATCH', 1, 1 ),
( 'report_schedule', 'POST', 0, 1 ),
( 'report_type', 'GET', 0, 1 ),
( 'report_type', 'GET', 1, 1 ),
( 'report_type', 'PATCH', 1, 1 ),
( 'role', 'GET', 0, 0 ),
( 'self', 'DELETE', 1, 0 ),
( 'self', 'GET', 1, 0 ),
( 'self', 'PATCH', 1, 0 ),
( 'self', 'POST', 1, 0 ),
( 'setting', 'GET', 0, 0 ),
( 'setting', 'GET', 1, 0 ),
( 'setting', 'PATCH', 1, 1 ),
( 'site', 'DELETE', 1, 1 ),
( 'site', 'GET', 0, 0 ),
( 'site', 'GET', 1, 1 ),
( 'site', 'PATCH', 1, 1 ),
( 'site', 'POST', 0, 1 ),
( 'system_message', 'DELETE', 1, 1 ),
( 'system_message', 'GET', 0, 0 ),
( 'system_message', 'GET', 1, 1 ),
( 'system_message', 'PATCH', 1, 1 ),
( 'system_message', 'POST', 0, 1 ),
( 'user', 'DELETE', 1, 1 ),
( 'user', 'GET', 0, 0 ),
( 'user', 'GET', 1, 0 ),
( 'user', 'PATCH', 1, 1 ),
( 'user', 'POST', 0, 1 ),

-- application services
( 'apex_baseline', 'GET', 0, 0 ),
( 'apex_baseline', 'GET', 1, 0 ),
( 'apex_deployment', 'DELETE', 1, 1 ),
( 'apex_deployment', 'GET', 0, 0 ),
( 'apex_deployment', 'GET', 1, 0 ),
( 'apex_deployment', 'PATCH', 1, 1 ),
( 'apex_deployment', 'POST', 0, 1 ),
( 'apex_exam', 'GET', 0, 0 ),
( 'apex_exam', 'GET', 1, 0 ),
( 'apex_host', 'GET', 0, 0 ),
( 'apex_host', 'GET', 1, 0 ),
( 'apex_host', 'PATCH', 1, 1 ),
( 'apex_scan', 'GET', 0, 0 ),
( 'apex_scan', 'GET', 1, 0 ),
( 'apex_scan', 'PATCH', 1, 1 ),
( 'code', 'DELETE', 1, 1 ),
( 'code', 'GET', 0, 0 ),
( 'code', 'GET', 1, 0 ),
( 'code', 'POST', 0, 1 ),
( 'code_type', 'DELETE', 1, 1 ),
( 'code_type', 'GET', 0, 0 ),
( 'code_type', 'GET', 1, 0 ),
( 'code_type', 'PATCH', 1, 1 ),
( 'code_type', 'POST', 0, 1 ),
( 'scan_type', 'DELETE', 1, 1 ),
( 'scan_type', 'GET', 0, 0 ),
( 'scan_type', 'GET', 1, 0 ),
( 'scan_type', 'PATCH', 1, 1 ),
( 'scan_type', 'POST', 0, 1 ),
( 'serial_number', 'GET', 0, 0 ),
( 'serial_number', 'GET', 1, 0 );
