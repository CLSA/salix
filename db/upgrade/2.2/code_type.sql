SELECT "Creating new code_type table" AS "";

CREATE TABLE IF NOT EXISTS code_type (
  id INT UNSIGNED NOT NULL AUTO_INCREMENT,
  update_timestamp TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  create_timestamp TIMESTAMP NOT NULL,
  code VARCHAR(45) NOT NULL,
  description TEXT NULL DEFAULT NULL,
  PRIMARY KEY (id),
  UNIQUE INDEX uq_code (code ASC))
ENGINE = InnoDB;


DELIMITER $$

DROP TRIGGER IF EXISTS code_type_BEFORE_INSERT $$
CREATE DEFINER = CURRENT_USER TRIGGER code_type_BEFORE_INSERT BEFORE INSERT ON code_type FOR EACH ROW
BEGIN
SET NEW.create_timestamp = NOW();
END$$

DELIMITER ;

INSERT IGNORE INTO code_type ( code, description ) VALUES
( 'Ab', 'The femur is abducted.' ),
( 'Ad', 'The femur is adducted.' ),
( 'Erot', 'The femur is externally rotated.' ),
( 'Irot', 'The femur is internally rotated.' ),
( 'high', 'The ROI is placed too high.' ),
( 'low', 'The ROI is placed too low.' ),
( 'left', 'The anatomy is placed too far to the left in the ROI.' ),
( 'right', 'The anatomy is placed too far to the right in the ROI.' ),
( 'art(metal)', NULL ),
( 'art(blur)', NULL ),
( 'art(streak)', NULL ),
( 'art(unknown)', NULL ),
( 'art(button)', NULL );
