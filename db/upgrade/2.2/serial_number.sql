DROP PROCEDURE IF EXISTS patch_serial_number;
DELIMITER //
CREATE PROCEDURE patch_serial_number()
  BEGIN

    -- determine the @cenozo database name
    SET @cenozo = ( SELECT REPLACE( DATABASE(), "salix", "cenozo" ) );

    SELECT "Creating new serial_number table" AS "";

    SET @sql = CONCAT(
      "CREATE TABLE IF NOT EXISTS serial_number ( ",
        "id INT UNSIGNED NOT NULL, ",
        "update_timestamp TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP, ",
        "create_timestamp TIMESTAMP NOT NULL, ",
        "site_id INT UNSIGNED NOT NULL, ",
        "PRIMARY KEY (id), ",
        "INDEX fk_site_id (site_id ASC), ",
        "CONSTRAINT fk_serial_number_site_id ",
          "FOREIGN KEY (site_id) ",
          "REFERENCES ", @cenozo, ".site (id) ",
          "ON DELETE NO ACTION ",
          "ON UPDATE NO ACTION) ",
      "ENGINE = InnoDB" );

    PREPARE statement FROM @sql;
    EXECUTE statement;
    DEALLOCATE PREPARE statement;

  END //
DELIMITER ;

CALL patch_serial_number();
DROP PROCEDURE IF EXISTS patch_serial_number;


DELIMITER $$

DROP TRIGGER IF EXISTS serial_number_BEFORE_INSERT $$
CREATE DEFINER = CURRENT_USER TRIGGER serial_number_BEFORE_INSERT BEFORE INSERT ON serial_number FOR EACH ROW
BEGIN
SET NEW.create_timestamp = NOW();
END$$

DELIMITER ;
