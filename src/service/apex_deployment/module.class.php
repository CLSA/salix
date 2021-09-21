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

    $service_class_name = lib::get_class_name( 'service\service' );
    $method = $this->get_method();

    if( 300 > $this->get_status()->get_code() )
    {
      if( $service_class_name::is_write_method( $method ) )
      {
        $status = $this->get_resource()->status;

        // don't delete deployments which have a status
        if( 'DELETE' == $method )
        {
          if( !is_null( $status ) ) $this->get_status()->set_code( 403 );
        }
        // do not allow editing if the deployment is exported or null
        else
        {
          if( is_null( $status ) || 'exported' == $status ) $this->get_status()->set_code( 403 );
        }
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
    $modifier->join( 'serial_number', 'apex_exam.serial_number_id', 'serial_number.id' );
    $modifier->join( 'site', 'serial_number.site_id', 'site.id' );
    $modifier->join( 'apex_baseline', 'apex_exam.apex_baseline_id', 'apex_baseline.id' );
    $modifier->join( 'participant', 'apex_baseline.participant_id', 'participant.id' );
    $modifier->join( 'scan_type', 'apex_scan.scan_type_id', 'scan_type.id' );
    $modifier->join( 'apex_host', 'apex_deployment.apex_host_id', 'apex_host.id' );
    $modifier->left_join( 'user', 'apex_deployment.user_id', 'user.id' );
    $modifier->join(
      'apex_deployment_code_summary', 'apex_deployment.id', 'apex_deployment_code_summary.apex_deployment_id' );

    if( $select->has_column( 'scan_type_side' ) )
      $select->add_column( 'CONCAT( IF( side="none","",CONCAT( side, " " ) ), type )', 'scan_type_side', false );

    if( !is_null( $this->get_resource() ) ) 
    {   
      // include the user first/last/name as supplemental data
      $select->add_column(
        'CONCAT( user.first_name, " ", user.last_name, " (", user.name, ")" )',
        'formatted_user_id',
        false );
    }   

    if( $select->has_table_columns( 'first_apex_exam' ) )
    {
      $modifier->join(
        'apex_baseline_first_apex_exam',
        'apex_baseline.id',
        'apex_baseline_first_apex_exam.apex_baseline_id'
      );
      $modifier->left_join(
        'apex_exam',
        'apex_baseline_first_apex_exam.apex_exam_id',
        'first_apex_exam.id',
        'first_apex_exam'
      );
    }
  }
}
