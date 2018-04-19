<?php
/**
 * status.class.php
 * 
 * @author Patrick Emond <emondpd@mcmaster.ca>
 */

namespace salix\business\report;
use cenozo\lib, cenozo\log, salix\util;

/**
 * Appointment report
 */
class status extends \cenozo\business\report\base_report
{
  /**
   * Build the report
   * @access protected
   */
  protected function build()
  {
    $apex_deployment_class_name = lib::get_class_name( 'database\apex_deployment' );

    // get whether restricting by qnaire or site
    $start_date = NULL;
    $end_date = NULL;
    $status = NULL;
    foreach( $this->get_restriction_list() as $restriction )
    {
      if( 'start_date' == $restriction['name'] )
      {
        $start_date = util::get_datetime_object( $restriction['value'] )->format( 'Y-m-d' );
        $start_date_operator = $restriction['operator'];
        $start_date_type = $restriction['restriction_type'];
      }
      else if( 'end_date' == $restriction['name'] )
      {
        $end_date = util::get_datetime_object( $restriction['value'] )->format( 'Y-m-d' );
        $end_date_operator = $restriction['operator'];
        $end_date_type = $restriction['restriction_type'];
      }
      else if( 'status' == $restriction['name'] )
      {
        $status = $restriction['value'];
        if( 'pending' == $status ) $datetime_column = 'import_datetime';
        if( 'completed' == $status ) $datetime_column = 'analysis_datetime';
        if( 'exported' == $status ) $datetime_column = 'export_datetime';
      }
    }

    $modifier = lib::create( 'database\modifier' );
    $select = lib::create( 'database\select' );

    $select->from( $this->db_report->get_report_type()->subject );
    $select->add_column( 'apex_host.name', 'Host', false );
    $select->add_column(
      sprintf( 'MIN( %s )', $this->get_datetime_column( $datetime_column, 'date' ) ),
      'Start Date',
      false
    );
    $select->add_column(
      sprintf( 'MAX( %s )', $this->get_datetime_column( $datetime_column, 'date' ) ),
      'End Date',
      false
    );
    $select->add_column( 'CONCAT( IF( side="none","",CONCAT( side, " " ) ), type )', 'Scan Type', false );
    $select->add_column( 'apex_exam.rank', 'Rank', false );
    $select->add_column( 'COUNT(*)', 'Total', false );

    $modifier->join( 'apex_scan', 'apex_scan.id', 'apex_deployment.apex_scan_id' );
    $modifier->join( 'apex_host', 'apex_host.id', 'apex_deployment.apex_host_id' );
    $modifier->join( 'scan_type', 'scan_type.id', 'apex_scan.scan_type_id' );
    $modifier->join( 'apex_exam', 'apex_exam.id', 'apex_scan.apex_exam_id' );
    $modifier->where( 'status', '=', $status );

    if( !is_null( $start_date ) )
    {
      $modifier->where(
        $this->get_datetime_column( $datetime_column, $start_date_type ),
        $start_date_operator,
        $start_date
      );
    }

    if( !is_null( $end_date ) )
    {
      $modifier->where(
        $this->get_datetime_column( $datetime_column, $end_date_type ),
        $end_date_operator,
        $end_date
      );
    }

    $modifier->group( 'apex_host.name' );
    $modifier->group( 'rank' );
    $modifier->group( 'type' );
    $modifier->group( 'side' );

    $this->apply_restrictions( $modifier );

    $this->add_table_from_select( NULL, $apex_deployment_class_name::select( $select, $modifier ) );
  }
}
