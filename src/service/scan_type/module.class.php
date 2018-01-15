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

    // add the total number of apex_scans
    if( $select->has_column( 'apex_scan_count' ) )
    {
      // to accomplish this we need to create two sub-joins, so start by creating the inner join
      $inner_join_sel = lib::create( 'database\select' );
      $inner_join_sel->from( 'apex_scan' );
      $inner_join_sel->add_column( 'scan_type_id' );
      $inner_join_sel->add_column( 'COUNT(*)', 'apex_scan_count', false );

      $inner_join_mod = lib::create( 'database\modifier' );
      $inner_join_mod->group( 'scan_type_id' );
      $inner_join_mod->where( 'scan_type_id', '!=', NULL );

      // now create the outer join
      $apex_scan_outer_join_sel = lib::create( 'database\select' );
      $apex_scan_outer_join_sel->from( 'scan_type' );
      $apex_scan_outer_join_sel->add_column( 'id', 'scan_type_id' );
      $apex_scan_outer_join_sel->add_column(
        'IF( scan_type_id IS NOT NULL, apex_scan_count, 0 )', 'apex_scan_count', false );

      $apex_scan_outer_join_mod = lib::create( 'database\modifier' );
      $apex_scan_outer_join_mod->left_join(
        sprintf( '( %s %s ) AS inner_join', $inner_join_sel->get_sql(), $inner_join_mod->get_sql() ),
        'scan_type.id',
        'inner_join.scan_type_id' );

      // now join to our main modifier
      $modifier->left_join(
        sprintf(
          '( %s %s ) AS apex_scan_outer_join',
          $apex_scan_outer_join_sel->get_sql(),
          $apex_scan_outer_join_mod->get_sql()
        ),
        'scan_type.id',
        'apex_scan_outer_join.scan_type_id' );
      $select->add_column( 'apex_scan_count', 'apex_scan_count', false );
    }   

    // add the total number of participants
    if( $select->has_column( 'participant_count' ) )
    {
      // to accomplish this we need to create two sub-joins, so start by creating the inner join
      $inner_join_sel = lib::create( 'database\select' );
      $inner_join_sel->from( 'apex_baseline' );
      $inner_join_sel->add_table_column( 'apex_scan', 'scan_type_id' );
      $inner_join_sel->add_column( 'COUNT( DISTINCT apex_baseline.participant_id )', 'participant_count', false );

      $inner_join_mod = lib::create( 'database\modifier' );
      $inner_join_mod->join( 'apex_exam', 'apex_baseline.id', 'apex_exam.apex_baseline_id' );
      $inner_join_mod->join( 'apex_scan', 'apex_exam.id', 'apex_scan.apex_exam_id' );
      $inner_join_mod->group( 'scan_type_id' );
      $inner_join_mod->where( 'scan_type_id', '!=', NULL );

      // now create the outer join
      $participant_outer_join_sel = lib::create( 'database\select' );
      $participant_outer_join_sel->from( 'scan_type' );
      $participant_outer_join_sel->add_column( 'id', 'scan_type_id' );
      $participant_outer_join_sel->add_column(
        'IF( scan_type_id IS NOT NULL, participant_count, 0 )', 'participant_count', false );

      $participant_outer_join_mod = lib::create( 'database\modifier' );
      $participant_outer_join_mod->left_join(
        sprintf( '( %s %s ) AS inner_join', $inner_join_sel->get_sql(), $inner_join_mod->get_sql() ),
        'scan_type.id',
        'inner_join.scan_type_id' );

      // now join to our main modifier
      $modifier->left_join(
        sprintf(
          '( %s %s ) AS participant_outer_join',
          $participant_outer_join_sel->get_sql(),
          $participant_outer_join_mod->get_sql()
        ),
        'scan_type.id',
        'participant_outer_join.scan_type_id' );
      $select->add_column( 'participant_count', 'participant_count', false );
    }   
  }
}
