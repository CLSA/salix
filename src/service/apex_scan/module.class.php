<?php
/**
 * module.class.php
 * 
 * @author Patrick Emond <emondpd@mcmaster.ca>
 * @filesource
 */

namespace salix\service\apex_scan;
use cenozo\lib, cenozo\log, salix\util;

/**
 * Performs operations which effect how this module is used in a service
 */
class module extends \cenozo\service\module
{
  /** 
   * Extend parent method
   */
  public function prepare_read( $select, $modifier )
  {
    parent::prepare_read( $select, $modifier );

    $modifier->join( 'apex_exam', 'apex_scan.apex_exam_id', 'apex_exam.id' );
    $modifier->join( 'apex_baseline', 'apex_exam.apex_baseline_id', 'apex_baseline.id' );
    $modifier->join( 'participant', 'apex_baseline.participant_id', 'participant.id' );
  }
}
