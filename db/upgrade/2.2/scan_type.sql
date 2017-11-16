SELECT "Creating new scan_type table" AS "";

CREATE TABLE IF NOT EXISTS scan_type (
  id INT UNSIGNED NOT NULL,
  update_timestamp TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  create_timestamp TIMESTAMP NOT NULL,
  type ENUM('hip', 'lateral', 'forearm', 'spine', 'wbody') NOT NULL,
  side ENUM('left', 'right', 'none') NOT NULL DEFAULT 'none',
  PRIMARY KEY (id),
  UNIQUE INDEX uq_type_side (type ASC, side ASC))
ENGINE = InnoDB;


DELIMITER $$

DROP TRIGGER IF EXISTS scan_type_BEFORE_INSERT $$
CREATE DEFINER = CURRENT_USER TRIGGER scan_type_BEFORE_INSERT BEFORE INSERT ON scan_type FOR EACH ROW
BEGIN
SET NEW.create_timestamp = NOW();
END$$

DELIMITER ;

INSERT IGNORE INTO scan_type ( id, type, side ) VALUES
( 1, 'spine', 'none' ),
( 2, 'hip', 'left' ),
( 3, 'hip', 'right' ),
( 5, 'wbody', 'none' ),
( 6, 'forearm', 'left' ),
( 7, 'forearm', 'right' ),
( 29, 'lateral', 'none' );
