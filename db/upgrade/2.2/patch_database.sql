-- Patch to upgrade database to version 2.2

SET AUTOCOMMIT=0;

SOURCE application_type.sql
SOURCE application_type_has_role.sql
SOURCE application.sql
SOURCE application_has_cohort.sql
SOURCE application_has_site.sql

SOURCE access.sql
SOURCE service.sql
SOURCE role_has_service.sql
SOURCE setting.sql
SOURCE writelog.sql

SOURCE apex_host.sql
SOURCE apex_baseline.sql
SOURCE serial_number.sql
SOURCE scan_type.sql
SOURCE apex_exam.sql
SOURCE apex_scan.sql
SOURCE apex_deployment.sql
SOURCE code_type.sql
SOURCE scan_type_has_code_type.sql
SOURCE code.sql

SOURCE apex_scan_code_summary.sql
SOURCE update_apex_scan_code_summary.sql

SOURCE update_version_number.sql

COMMIT;
