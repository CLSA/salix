<?php
/**
 * query.class.php
 * 
 * @author Patrick Emond <emondpd@mcmaster.ca>
 */

namespace salix\service\apex_deployment;
use cenozo\lib, cenozo\log, salix\util;

/**
 * Extends parent class
 */
class query extends \cenozo\service\query
{
  /**
   * Extends parent method
   */
  protected function get_record_count()
  {
    $sibling_apex_deployment_id = $this->get_argument( 'sibling_apex_deployment_id', 0 );
    if( $sibling_apex_deployment_id )
    {
      $apex_deployment_class_name = lib::create( 'database\apex_deployment' );

      $db_apex_deployment = lib::create( 'database\apex_deployment', $sibling_apex_deployment_id );
      $modifier = clone $this->modifier;
      $modifier->join(
        'apex_exam', 'apex_exam.apex_baseline_id', 'current_apex_exam.apex_baseline_id', '', 'current_apex_exam' );
      $join_mod = lib::create( 'database\modifier' );
      $join_mod->where( 'current_apex_exam.id', '=', 'current_apex_scan.apex_exam_id', false );
      $join_mod->where( 'apex_scan.scan_type_id', '=', 'current_apex_scan.scan_type_id', false );
      $modifier->join_modifier( 'apex_scan', $join_mod, '', 'current_apex_scan' );
      $modifier->where( 'apex_scan.id', '!=', $db_apex_deployment->apex_scan_id );
      $modifier->join(
        'apex_deployment',
        'current_apex_scan.id',
        'current_apex_deployment.apex_scan_id',
        '',
        'current_apex_deployment'
      );
      $modifier->where( 'current_apex_deployment.id', '=', $db_apex_deployment->id );
      $modifier->where( 'apex_deployment.apex_host_id', '=', 'current_apex_deployment.apex_host_id', false );
      $this->select->apply_aliases_to_modifier( $modifier );
      return $apex_deployment_class_name::count( $modifier );
    }

    return parent::get_record_count();
  }

  /**
   * Extends parent method
   */
  protected function get_record_list()
  {
    $sibling_apex_deployment_id = $this->get_argument( 'sibling_apex_deployment_id', 0 );
    if( $sibling_apex_deployment_id )
    {
      $apex_deployment_class_name = lib::create( 'database\apex_deployment' );

      $db_apex_deployment = lib::create( 'database\apex_deployment', $sibling_apex_deployment_id );
      $select = clone $this->select;
      $modifier = clone $this->modifier;
      $modifier->join(
        'apex_exam', 'apex_exam.apex_baseline_id', 'current_apex_exam.apex_baseline_id', '', 'current_apex_exam' );
      $join_mod = lib::create( 'database\modifier' );
      $join_mod->where( 'current_apex_exam.id', '=', 'current_apex_scan.apex_exam_id', false );
      $join_mod->where( 'apex_scan.scan_type_id', '=', 'current_apex_scan.scan_type_id', false );
      $modifier->join_modifier( 'apex_scan', $join_mod, '', 'current_apex_scan' );
      $modifier->where( 'apex_scan.id', '!=', $db_apex_deployment->apex_scan_id );
      $modifier->join(
        'apex_deployment',
        'current_apex_scan.id',
        'current_apex_deployment.apex_scan_id',
        '',
        'current_apex_deployment'
      );
      $modifier->where( 'current_apex_deployment.id', '=', $db_apex_deployment->id );
      $modifier->where( 'apex_deployment.apex_host_id', '=', 'current_apex_deployment.apex_host_id', false );
      $this->select->apply_aliases_to_modifier( $modifier );

      return $apex_deployment_class_name::select( $select, $modifier );
    }
    
    return parent::get_record_list();
  }
}
