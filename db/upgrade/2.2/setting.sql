DROP PROCEDURE IF EXISTS patch_setting;
DELIMITER //
CREATE PROCEDURE patch_setting()
  BEGIN

    -- determine the @cenozo database name
    SET @cenozo = ( SELECT REPLACE( DATABASE(), "salix", "cenozo" ) );

    SELECT "Creating new setting table" AS "";

    SET @sql = CONCAT(
      "CREATE TABLE IF NOT EXISTS setting ( ",
        "id INT UNSIGNED NOT NULL AUTO_INCREMENT, ",
        "update_timestamp TIMESTAMP NOT NULL, ",
        "create_timestamp TIMESTAMP NOT NULL, ",
        "site_id INT UNSIGNED NOT NULL, ",
        "priority_apex_host_id INT UNSIGNED NOT NULL, ",
        "PRIMARY KEY (id), ",
        "INDEX fk_site_id (site_id ASC), ",
        "UNIQUE INDEX uq_site_id (site_id ASC), ",
        "INDEX fk_priority_apex_host_id (priority_apex_host_id ASC), ",
        "CONSTRAINT fk_setting_site_id ",
          "FOREIGN KEY (site_id) ",
          "REFERENCES ", @cenozo, ".site (id) ",
          "ON DELETE CASCADE ",
          "ON UPDATE NO ACTION, "
        "CONSTRAINT fk_setting_priority_apex_host_id ",
          "FOREIGN KEY (priority_apex_host_id) ",
          "REFERENCES apex_host (id) ",
          "ON DELETE NO ACTION ",
          "ON UPDATE NO ACTION) ",
      "ENGINE = InnoDB" );
    PREPARE statement FROM @sql;
    EXECUTE statement;
    DEALLOCATE PREPARE statement;

    SET @sql = CONCAT(
      "INSERT IGNORE INTO setting( site_id, priority_apex_host_id ) ",
      "SELECT application_has_site.site_id, apex_host.id ",
      "FROM apex_host, ", @cenozo, ".application ",
      "JOIN ", @cenozo, ".application_has_site ON application.id = application_has_site.application_id ",
      "WHERE apex_host.name = 'skimmer' ",
      "AND application.name = 'salix'" );
    PREPARE statement FROM @sql;
    EXECUTE statement;
    DEALLOCATE PREPARE statement;
  END //
DELIMITER ;

CALL patch_setting();
DROP PROCEDURE IF EXISTS patch_setting;
