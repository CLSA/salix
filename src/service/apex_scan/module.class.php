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
    $modifier->join( 'scan_type', 'apex_scan.scan_type_id', 'scan_type.id' );
    $modifier->join( 'apex_scan_code_summary', 'apex_scan.id', 'apex_scan_code_summary.apex_scan_id' );

    if( $select->has_column( 'scan_type_side' ) )
    {
      $select->add_column(
        'CONCAT( IF( scan_type.side="none","",CONCAT( scan_type.side, " " ) ), scan_type.type )',
        'scan_type_side',
        false
      );
    }
  }
}
