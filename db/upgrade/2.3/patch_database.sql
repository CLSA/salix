-- Patch to upgrade database to version 2.3

SET AUTOCOMMIT=0;

SOURCE apex_scan.sql
SOURCE allocation.sql
SOURCE apex_host.sql
SOURCE service.sql
SOURCE role_has_service.sql

SOURCE update_version_number.sql

COMMIT;
