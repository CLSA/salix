DROP PROCEDURE IF EXISTS initial_install;
DELIMITER //
CREATE PROCEDURE initial_install()
  BEGIN

    -- determine the @cenozo database name
    SET @cenozo = ( SELECT REPLACE( DATABASE(), "salix", "cenozo" ) );
    SET @mastodon = ( SELECT REPLACE( DATABASE(), "salix", "mastodon" ) );

    SELECT "Adding default site to new application" AS "";

    SET @sql = CONCAT(
      "INSERT IGNORE INTO ", @cenozo, ".site ",
      "SET name = 'McMaster APEX', ",
      "timezone = 'Canada/Eastern'" );
    PREPARE statement FROM @sql;
    EXECUTE statement;
    DEALLOCATE PREPARE statement;

    SELECT "Adding default administrator access based on Mastodon" AS "";

    SET @sql = CONCAT(
      "INSERT IGNORE INTO access ",
      "( user_id, role_id, site_id ) ",
      "SELECT access.user_id, access.role_id, site.id ",
      "FROM ", @cenozo, ".site, ", @mastodon, ".access ",
      "JOIN ", @cenozo, ".role ON access.role_id = role.id ",
      "JOIN ", @cenozo, ".site AS asite ON access.site_id = asite.id ",
      "WHERE site.name = 'McMaster APEX' ",
      "AND role.name = 'administrator' ",
      "AND asite.name = 'Sherbrooke CATI'" );
    PREPARE statement FROM @sql;
    EXECUTE statement;
    DEALLOCATE PREPARE statement;

  END //
DELIMITER ;

CALL initial_install();
DROP PROCEDURE IF EXISTS initial_install;
