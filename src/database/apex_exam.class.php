<?php
/**
 * apex_exam.class.php
 * 
 * @author Patrick Emond <emondpd@mcmaster.ca>
 * @filesource
 */

namespace salix\database;
use cenozo\lib, cenozo\log, salix\util;

/**
 * apex_exam: record
 */
class apex_exam extends \cenozo\database\has_rank
{
  protected static $rank_parent = 'apex_baseline';
}
