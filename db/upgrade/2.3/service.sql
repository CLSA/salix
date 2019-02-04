SELECT "Adding services for new allocation table" AS "";

INSERT IGNORE INTO service ( subject, method, resource, restricted ) VALUES
( 'allocation', 'DELETE', 1, 1 ),
( 'allocation', 'GET', 0, 1 ),
( 'allocation', 'GET', 1, 1 ),
( 'allocation', 'PATCH', 1, 1 ),
( 'allocation', 'POST', 0, 1 );
