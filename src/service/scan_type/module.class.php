<?php
/**
 * module.class.php
 * 
 * @author Patrick Emond <emondpd@mcmaster.ca>
 * @filesource
 */

namespace salix\service\scan_type;
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

    // add the total number of code_types
    if( $select->has_column( 'code_type_count' ) )
    {
      // to accomplish this we need to create two sub-joins, so start by creating the inner join
      $inner_join_sel = lib::create( 'database\select' );
      $inner_join_sel->from( 'scan_type_has_code_type' );
      $inner_join_sel->add_column( 'scan_type_id' );
      $inner_join_sel->add_column( 'COUNT(*)', 'code_type_count', false );

      $inner_join_mod = lib::create( 'database\modifier' );
      $inner_join_mod->group( 'scan_type_id' );

      // now create the outer join
      $code_type_outer_join_sel = lib::create( 'database\select' );
      $code_type_outer_join_sel->from( 'scan_type' );
      $code_type_outer_join_sel->add_column( 'id', 'scan_type_id' );
      $code_type_outer_join_sel->add_column(
        'IF( scan_type_id IS NOT NULL, code_type_count, 0 )', 'code_type_count', false );

      $code_type_outer_join_mod = lib::create( 'database\modifier' );
      $code_type_outer_join_mod->left_join(
        sprintf( '( %s %s ) AS inner_join', $inner_join_sel->get_sql(), $inner_join_mod->get_sql() ),
        'scan_type.id',
        'inner_join.scan_type_id' );

      // now join to our main modifier
      $modifier->left_join(
        sprintf(
          '( %s %s ) AS code_type_outer_join',
          $code_type_outer_join_sel->get_sql(),
          $code_type_outer_join_mod->get_sql()
        ),
        'scan_type.id',
        'code_type_outer_join.scan_type_id' );
      $select->add_column( 'code_type_count', 'code_type_count', false );
    }   
  }
}
