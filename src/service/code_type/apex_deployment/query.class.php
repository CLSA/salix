<?php
/**
 * query.class.php
 * 
 * @author Patrick Emond <emondpd@mcmaster.ca>
 */

namespace salix\service\code_type\apex_deployment;
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
    $apex_deployment_class_name = lib::create( 'database\apex_deployment' );

    $db_code_type = $this->get_parent_record();
    $modifier = clone $this->modifier;
    $modifier->join( 'code', 'apex_deployment.id', 'code.apex_deployment_id' );
    $modifier->where( 'code.code_type_id', '=', $db_code_type->id );
    $this->select->apply_aliases_to_modifier( $modifier );

    return $apex_deployment_class_name::count( $modifier );
  }

  /**
   * Extends parent method
   */
  protected function get_record_list()
  {
    $apex_deployment_class_name = lib::create( 'database\apex_deployment' );

    $db_code_type = $this->get_parent_record();
    $select = clone $this->select;
    $modifier = clone $this->modifier;
    $modifier->join( 'code', 'apex_deployment.id', 'code.apex_deployment_id' );
    $modifier->where( 'code.code_type_id', '=', $db_code_type->id );
    $this->select->apply_aliases_to_modifier( $modifier );

    return $apex_deployment_class_name::select( $select, $modifier );
  }
}
