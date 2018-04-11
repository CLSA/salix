DROP PROCEDURE IF EXISTS patch_apex_baseline_first_apex_exam;
DELIMITER //
CREATE PROCEDURE patch_apex_baseline_first_apex_exam()
  BEGIN

    SELECT "Creating new apex_baseline_first_apex_exam table" AS "";

    SET @test = (
      SELECT COUNT(*)
      FROM information_schema.TABLES
      WHERE TABLE_SCHEMA = DATABASE()
      AND TABLE_NAME = "apex_baseline_first_apex_exam" );
    IF @test = 0 THEN

      CREATE TABLE IF NOT EXISTS apex_baseline_first_apex_exam (
        apex_baseline_id INT UNSIGNED NOT NULL,
        apex_exam_id INT UNSIGNED NULL DEFAULT NULL,
        update_timestamp TIMESTAMP NOT NULL,
        create_timestamp TIMESTAMP NOT NULL,
        PRIMARY KEY (apex_baseline_id),
        INDEX fk_apex_exam_id (apex_exam_id ASC),
        CONSTRAINT fk_apex_baseline_first_apex_exam_apex_baseline_id
          FOREIGN KEY (apex_baseline_id)
          REFERENCES apex_baseline (id)
          ON DELETE CASCADE
          ON UPDATE CASCADE,
        CONSTRAINT fk_apex_baseline_first_apex_exam_apex_exam_id
          FOREIGN KEY (apex_exam_id)
          REFERENCES apex_exam (id)
          ON DELETE SET NULL
          ON UPDATE CASCADE)
      ENGINE = InnoDB;

      REPLACE INTO apex_baseline_first_apex_exam( apex_baseline_id, apex_exam_id )
      SELECT apex_baseline.id, apex_exam.id
      FROM apex_baseline
      LEFT JOIN apex_exam ON apex_baseline.id = apex_exam.apex_baseline_id
      AND apex_exam.barcode <=> (
        SELECT MIN( barcode )
        FROM apex_exam
        WHERE apex_baseline.id = apex_exam.apex_baseline_id
        GROUP BY apex_exam.apex_baseline_id
        LIMIT 1
      );

    END IF;

  END //
DELIMITER ;

CALL patch_apex_baseline_first_apex_exam();
DROP PROCEDURE IF EXISTS patch_apex_baseline_first_apex_exam;
