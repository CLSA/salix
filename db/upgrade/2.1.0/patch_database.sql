-- Patch to upgrade database to version 2.1.0

SET AUTOCOMMIT=0;

SOURCE event_type.sql
SOURCE application_type.sql
SOURCE application_type_has_role.sql
SOURCE application.sql
SOURCE application_has_cohort.sql
SOURCE application_has_site.sql
SOURCE site.sql

SOURCE access.sql
SOURCE service.sql
SOURCE role_has_service.sql
SOURCE setting.sql
SOURCE writelog.sql

SOURCE apex_host.sql
SOURCE apex_baseline.sql
SOURCE serial_number.sql
SOURCE apex_exam.sql
SOURCE apex_scan.sql
SOURCE apex_host_has_apex_scan.sql

SOURCE update_version_number.sql

COMMIT;
