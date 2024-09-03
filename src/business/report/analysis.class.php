<?php
/**
 * analysis.class.php
 * 
 * @author Patrick Emond <emondpd@mcmaster.ca>
 */

namespace salix\business\report;
use cenozo\lib, cenozo\log, salix\util;

/**
 * Contact report
 */
class analysis extends \cenozo\business\report\base_report
{
  /**
   * Build the report
   * @access protected
   */
  protected function build()
  {
    $apex_deployment_class_name = lib::get_class_name( 'database\apex_deployment' );

    // determine which columns to show based on the restriction list
    $show_wave = true;
    foreach( $this->get_restriction_list( false ) as $restriction )
    {
      if( 'wave' == $restriction['name'] )
      {
        $show_wave = false;
        break;
      }
    }

    // restrict by scan type
    $scan_type_id = NULL;
    foreach( $this->get_restriction_list( true ) as $restriction )
    {
      if( 'scan_type' == $restriction['name'] ) $scan_type_id = $restriction['value'];
    }

    $select = lib::create( 'database\select' );
    $modifier = lib::create( 'database\modifier' );

    $select->from( 'apex_deployment' );
    $select->add_column( 'participant.uid', 'UID', false );
    if( $show_wave ) $select->add_column( 'apex_exam.rank', 'Wave', false );
    $select->add_column( 'apex_exam.barcode', 'Barcode', false );
    $select->add_column( 'apex_exam.technician', 'Technician', false );
    if( is_null( $scan_type_id ) )
    {
      $select->add_column(
        'CONCAT_WS( '.
          '" ", '.
          'scan_type.type, '.
          'IF("none" = scan_type.side, NULL, CONCAT( "(", scan_type.side, ")" ) ) '.
        ')',
        'Scan Type',
        false
      );
    }
    $select->add_column( 'apex_host.name', 'Host', false );
    $select->add_column( 'user.name', 'Typist', false );
    $select->add_column(
      $this->get_datetime_column( 'apex_deployment.analysis_datetime', 'date' ),
      'Analysis Date',
      false
    );
    $select->add_column( 'IF( pass IS NULL, "(empty)", IF( pass = 1, "Yes", "No" ) )', 'Pass', false );
    $select->add_column(
      'GROUP_CONCAT( code_type.code ORDER BY code_type.code SEPARATOR "; " )',
      'Codes',
      false
    );

    $modifier->join( 'apex_host', 'apex_deployment.apex_host_id', 'apex_host.id' );
    $modifier->join( 'user', 'apex_deployment.user_id', 'user.id' );
    $modifier->join( 'apex_scan', 'apex_deployment.apex_scan_id', 'apex_scan.id' );
    $modifier->join( 'scan_type', 'apex_scan.scan_type_id', 'scan_type.id' );
    $modifier->join( 'apex_exam', 'apex_scan.apex_exam_id', 'apex_exam.id' );
    $modifier->join( 'apex_baseline', 'apex_exam.apex_baseline_id', 'apex_baseline.id' );
    $modifier->join( 'participant', 'apex_baseline.participant_id', 'participant.id' );
    $modifier->left_join( 'code', 'apex_deployment.id', 'code.apex_deployment_id' );
    $modifier->left_join( 'code_type', 'code.code_type_id', 'code_type.id' );
    $modifier->where( 'apex_deployment.analysis_datetime', '!=', NULL );
    if( !is_null( $scan_type_id ) ) $modifier->where( 'scan_type.id', '=', $scan_type_id );
    $modifier->group( 'apex_deployment.id' );
    $modifier->order( 'uid' );
    $modifier->order( 'apex_exam.rank' );

    $this->apply_restrictions( $modifier );

    $this->add_table_from_select( NULL, $apex_deployment_class_name::select( $select, $modifier ) );
  }
}
