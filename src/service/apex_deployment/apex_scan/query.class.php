<?php
/**
 * query.class.php
 * 
 * @author Patrick Emond <emondpd@mcmaster.ca>
 */

namespace salix\service\apex_deployment\apex_scan;
use cenozo\lib, cenozo\log;

/**
 * Extends parent class
 */
class query extends \cenozo\service\query
{
  /**
   * Extends parent method
   */
  protected function prepare()
  {
    parent::prepare();

    // the status will be 404, reset it to 200
    $this->status->set_code( 200 );
  }

  /**
   * Extends parent method
   */
  protected function get_record_count()
  {
    $apex_scan_class_name = lib::create( 'database\apex_scan' );

    $db_apex_deployment = $this->get_parent_record();
    $modifier = clone $this->modifier;
    $modifier->join(
      'apex_exam', 'apex_exam.apex_baseline_id', 'current_apex_exam.apex_baseline_id', '', 'current_apex_exam' );
    $join_mod = lib::create( 'database\modifier' );
    $join_mod->where( 'current_apex_exam.id', '=', 'current_apex_scan.apex_exam_id', false );
    $join_mod->where( 'apex_scan.scan_type_id', '=', 'current_apex_scan.scan_type_id', false );
    $modifier->join_modifier( 'apex_scan', $join_mod, '', 'current_apex_scan' );
    $modifier->where( 'apex_scan.id', '!=', $db_apex_deployment->apex_scan_id );
    $modifier->where( 'current_apex_scan.id', '=', $db_apex_deployment->apex_scan_id );
    $this->select->apply_aliases_to_modifier( $modifier );

    return $apex_scan_class_name::count( $modifier );
  }

  /**
   * Extends parent method
   */
  protected function get_record_list()
  {
    $apex_scan_class_name = lib::create( 'database\apex_scan' );

    $db_apex_deployment = $this->get_parent_record();
    $select = clone $this->select;
    $modifier = clone $this->modifier;
    $modifier->join(
      'apex_exam', 'apex_exam.apex_baseline_id', 'current_apex_exam.apex_baseline_id', '', 'current_apex_exam' );
    $join_mod = lib::create( 'database\modifier' );
    $join_mod->where( 'current_apex_exam.id', '=', 'current_apex_scan.apex_exam_id', false );
    $join_mod->where( 'apex_scan.scan_type_id', '=', 'current_apex_scan.scan_type_id', false );
    $modifier->join_modifier( 'apex_scan', $join_mod, '', 'current_apex_scan' );
    $modifier->where( 'apex_scan.id', '!=', $db_apex_deployment->apex_scan_id );
    $modifier->where( 'current_apex_scan.id', '=', $db_apex_deployment->apex_scan_id );
    $this->select->apply_aliases_to_modifier( $modifier );

    return $apex_scan_class_name::select( $select, $modifier );
  }
}
