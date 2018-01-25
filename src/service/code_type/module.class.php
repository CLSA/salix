<?php
/**
 * module.class.php
 * 
 * @author Patrick Emond <emondpd@mcmaster.ca>
 * @filesource
 */

namespace salix\service\code_type;
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

    // add the total number of apex_scans
    if( $select->has_column( 'apex_scan_count' ) )
    {
      // to accomplish this we need to create two sub-joins, so start by creating the inner join
      $inner_join_sel = lib::create( 'database\select' );
      $inner_join_sel->from( 'code' );
      $inner_join_sel->add_column( 'code_type_id' );
      $inner_join_sel->add_column( 'COUNT( DISTINCT apex_scan_id )', 'apex_scan_count', false );

      $inner_join_mod = lib::create( 'database\modifier' );
      $inner_join_mod->group( 'code_type_id' );

      // now create the outer join
      $apex_scan_outer_join_sel = lib::create( 'database\select' );
      $apex_scan_outer_join_sel->from( 'code_type' );
      $apex_scan_outer_join_sel->add_column( 'id', 'code_type_id' );
      $apex_scan_outer_join_sel->add_column(
        'IF( code_type_id IS NOT NULL, apex_scan_count, 0 )', 'apex_scan_count', false );

      $apex_scan_outer_join_mod = lib::create( 'database\modifier' );
      $apex_scan_outer_join_mod->left_join(
        sprintf( '( %s %s ) AS inner_join', $inner_join_sel->get_sql(), $inner_join_mod->get_sql() ),
        'code_type.id',
        'inner_join.code_type_id' );

      // now join to our main modifier
      $modifier->left_join(
        sprintf(
          '( %s %s ) AS apex_scan_outer_join',
          $apex_scan_outer_join_sel->get_sql(),
          $apex_scan_outer_join_mod->get_sql()
        ),
        'code_type.id',
        'apex_scan_outer_join.code_type_id' );
      $select->add_column( 'apex_scan_count', 'apex_scan_count', false );
    }   

    // add the total number of scan_types
    if( $select->has_column( 'scan_type_count' ) )
    {
      // to accomplish this we need to create two sub-joins, so start by creating the inner join
      $inner_join_sel = lib::create( 'database\select' );
      $inner_join_sel->from( 'scan_type_has_code_type' );
      $inner_join_sel->add_column( 'code_type_id' );
      $inner_join_sel->add_column( 'COUNT(*)', 'scan_type_count', false );

      $inner_join_mod = lib::create( 'database\modifier' );
      $inner_join_mod->group( 'code_type_id' );

      // now create the outer join
      $scan_type_outer_join_sel = lib::create( 'database\select' );
      $scan_type_outer_join_sel->from( 'code_type' );
      $scan_type_outer_join_sel->add_column( 'id', 'code_type_id' );
      $scan_type_outer_join_sel->add_column(
        'IF( code_type_id IS NOT NULL, scan_type_count, 0 )', 'scan_type_count', false );

      $scan_type_outer_join_mod = lib::create( 'database\modifier' );
      $scan_type_outer_join_mod->left_join(
        sprintf( '( %s %s ) AS inner_join', $inner_join_sel->get_sql(), $inner_join_mod->get_sql() ),
        'code_type.id',
        'inner_join.code_type_id' );

      // now join to our main modifier
      $modifier->left_join(
        sprintf(
          '( %s %s ) AS scan_type_outer_join',
          $scan_type_outer_join_sel->get_sql(),
          $scan_type_outer_join_mod->get_sql()
        ),
        'code_type.id',
        'scan_type_outer_join.code_type_id' );
      $select->add_column( 'scan_type_count', 'scan_type_count', false );
    }   
  }
}
