SELECT "Creating update_apex_baseline_first_apex_exam procedure" AS "";

DROP procedure IF EXISTS update_apex_baseline_first_apex_exam;

DELIMITER $$

CREATE PROCEDURE update_apex_baseline_first_apex_exam(IN proc_apex_baseline_id INT(10) UNSIGNED)
BEGIN
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
  )
  WHERE apex_baseline.id = proc_apex_baseline_id;
END$$

DELIMITER ;
