SELECT 'Adding new services' AS '';

INSERT IGNORE INTO service ( subject, method, resource, restricted ) VALUES
( 'log_entry', 'GET', 0, 1 ),
( 'log_entry', 'GET', 1, 1 );
