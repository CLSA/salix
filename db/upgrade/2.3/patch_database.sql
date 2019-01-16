-- Patch to upgrade database to version 2.3

SET AUTOCOMMIT=0;

SOURCE apex_scan.sql

SOURCE update_version_number.sql

COMMIT;
