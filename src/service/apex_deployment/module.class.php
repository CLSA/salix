<?php
/**
 * module.class.php
 * 
 * @author Patrick Emond <emondpd@mcmaster.ca>
 * @filesource
 */

namespace salix\service\apex_deployment;
use cenozo\lib, cenozo\log, salix\util;

/**
 * Performs operations which effect how this module is used in a service
 */
class module extends \cenozo\service\module
{
  /**
   * Extend parent method
   */
  public function validate()
  {
    parent::validate();

    if( 300 > $this->get_status()->get_code() )
    {
      // don't delete deployments which have a status
      if( 'DELETE' == $this->get_method() )
      {
        $db_apex_deployment = $this->get_resource();
        if( !is_null( $db_apex_deployment->status ) ) $this->get_status()->set_code( 403 );
      }
    }
  }

  /** 
   * Extend parent method
   */
  public function prepare_read( $select, $modifier )
  {
    parent::prepare_read( $select, $modifier );

    $modifier->join( 'apex_scan', 'apex_deployment.apex_scan_id', 'apex_scan.id' );
    $modifier->join( 'apex_exam', 'apex_scan.apex_exam_id', 'apex_exam.id' );
    $modifier->join( 'apex_baseline', 'apex_exam.apex_baseline_id', 'apex_baseline.id' );
    $modifier->join( 'participant', 'apex_baseline.participant_id', 'participant.id' );
    $modifier->join( 'scan_type', 'apex_scan.scan_type_id', 'scan_type.id' );
    $modifier->join( 'apex_host', 'apex_deployment.apex_host_id', 'apex_host.id' );

    if( $select->has_column( 'scan_type_side' ) )
      $select->add_column( 'CONCAT( IF( side="none","",CONCAT( side, " " ) ), type )', 'scan_type_side', false );
  }
}
