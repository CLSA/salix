DROP PROCEDURE IF EXISTS patch_apex_deployment;
DELIMITER //
CREATE PROCEDURE patch_apex_deployment()
  BEGIN

    -- determine the cenozo database name
    SET @cenozo = (
      SELECT unique_constraint_schema
      FROM information_schema.referential_constraints
      WHERE constraint_schema = DATABASE()
      AND constraint_name = "fk_access_site_id"
    );

    SELECT "Adding new user_id column to apex_deployment table" AS "";

    SELECT COUNT(*) INTO @test
    FROM information_schema.COLUMNS
    WHERE table_schema = DATABASE()
    AND table_name = "apex_deployment"
    AND column_name = "user_id";

    IF @test = 0 THEN
      ALTER TABLE apex_deployment ADD COLUMN user_id INT(10) UNSIGNED NULL DEFAULT NULL AFTER apex_host_id;

      SET @sql = CONCAT(
        "ALTER TABLE apex_deployment ",
          "ADD INDEX fk_user_id( user_id ASC ), ",
          "ADD CONSTRAINT fk_apex_deployment_user_id ",
            "FOREIGN KEY (user_id) ",
            "REFERENCES ", @cenozo, ".user (id) ",
            "ON DELETE NO ACTION ",
            "ON UPDATE NO ACTION"
      );
      PREPARE statement FROM @sql;
      EXECUTE statement;
      DEALLOCATE PREPARE statement;

      SELECT "Filling in new apex_deployment.user_id column based on code table" AS "";

      SET @sql = CONCAT(
        "CREATE TEMPORARY TABLE apex_deployment_update ",
        "SELECT apex_deployment.id, IFNULL( code.user_id, user.id ) AS user_id ",
        "FROM ", @cenozo, ".user, apex_deployment ",
        "LEFT JOIN code ON apex_deployment.id = code.apex_deployment_id ",
        "WHERE user.name = 'gordonch' ",
        "AND apex_deployment.pass IS NOT NULL ",
        "GROUP BY apex_deployment.id"
      );
      PREPARE statement FROM @sql;
      EXECUTE statement;
      DEALLOCATE PREPARE statement;

      ALTER TABLE apex_deployment_update ADD PRIMARY KEY (id);
      UPDATE apex_deployment JOIN apex_deployment_update USING (id) SET apex_deployment.user_id = apex_deployment_update.user_id;

    END IF;

  END //
DELIMITER ;

CALL patch_apex_deployment();
DROP PROCEDURE IF EXISTS patch_apex_deployment;
