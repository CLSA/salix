SELECT 'Adding new services' AS '';

INSERT IGNORE INTO service ( subject, method, resource, restricted ) VALUES
( 'notation', 'DELETE', 1, 1 ),
( 'notation', 'PATCH', 1, 1 ),
( 'notation', 'POST', 0, 1 );
