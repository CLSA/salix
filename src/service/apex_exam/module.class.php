<?php
/**
 * module.class.php
 * 
 * @author Patrick Emond <emondpd@mcmaster.ca>
 * @filesource
 */

namespace salix\service\apex_exam;
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

    $modifier->join( 'apex_baseline', 'apex_exam.apex_baseline_id', 'apex_baseline.id' );
    $modifier->join( 'participant', 'apex_baseline.participant_id', 'participant.id' );
    $modifier->join( 'serial_number', 'apex_exam.serial_number_id', 'serial_number.id' );
    $modifier->join( 'site', 'serial_number.site_id', 'site.id' );
  }
}
