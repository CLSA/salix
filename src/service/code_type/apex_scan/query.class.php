<?php
/**
 * query.class.php
 * 
 * @author Patrick Emond <emondpd@mcmaster.ca>
 */

namespace salix\service\code_type\apex_scan;
use cenozo\lib, cenozo\log, salix\util;

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

    $db_code_type = $this->get_parent_record();
    $modifier = clone $this->modifier;
    $modifier->join( 'code', 'apex_scan.id', 'code.apex_scan_id' );
    $modifier->where( 'code.code_type_id', '=', $db_code_type->id );
    $this->select->apply_aliases_to_modifier( $modifier );

    return $apex_scan_class_name::count( $modifier, true ); // distinct
  }

  /**
   * Extends parent method
   */
  protected function get_record_list()
  {
    $apex_scan_class_name = lib::create( 'database\apex_scan' );

    $db_code_type = $this->get_parent_record();
    $select = clone $this->select;
    $select->set_distinct( true );
    $modifier = clone $this->modifier;
    $modifier->join( 'code', 'apex_scan.id', 'code.apex_scan_id' );
    $modifier->where( 'code.code_type_id', '=', $db_code_type->id );
    $this->select->apply_aliases_to_modifier( $modifier );

    return $apex_scan_class_name::select( $select, $modifier );
  }
}
