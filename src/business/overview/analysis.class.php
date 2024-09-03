<?php
/**
 * overview.class.php
 * 
 * @author Patrick Emond <emondpd@mcmaster.ca>
 */

namespace salix\business\overview;
use cenozo\lib, cenozo\log, salix\util;

/**
 * overview: analysis
 */
class analysis extends \cenozo\business\overview\base_overview
{
  /**
   * Implements abstract method
   */
  protected function build( $modifier = NULL )
  {
    $scan_type_class_name = lib::get_class_name( 'database\scan_type' );
    $apex_deployment_class_name = lib::get_class_name( 'database\apex_deployment' );

    // get a list of all code types by scan type
    $scan_type_sel = lib::create( 'database\select' );
    $scan_type_sel->add_column( 'id', 'scan_type_id' );
    $scan_type_sel->add_column(
      'CONCAT_WS( '.
        '" ", '.
        'scan_type.type, '.
        'IF("none" = scan_type.side, NULL, CONCAT( "(", scan_type.side, ")" ) ) '.
      ')',
      'name',
      false
    );
    $scan_type_sel->add_table_column( 'code_type', 'id', 'code_type_id' );
    $scan_type_sel->add_table_column( 'code_type', 'code' );
    $scan_type_sel->add_table_column( 'code_type', 'description' );

    $scan_type_mod = lib::create( 'database\modifier' );
    $scan_type_mod->join( 'scan_type_has_code_type', 'scan_type.id', 'scan_type_has_code_type.scan_type_id' );
    $scan_type_mod->join( 'code_type', 'scan_type_has_code_type.code_type_id', 'code_type.id' );
    $scan_type_mod->order( 'scan_type.type' );
    $scan_type_mod->order( 'scan_type.side' );
    $scan_type_mod->order( 'code_type.code' );

    $scan_type_list = [];
    foreach( $scan_type_class_name::select( $scan_type_sel, $scan_type_mod ) as $row )
    {
      $scan_type_id = $row['scan_type_id'];
      $name = $row['name'];
      $code_type_id = $row['code_type_id'];
      $code = $row['code'];
      $description = $row['description'];
      if( !array_key_exists( $scan_type_id, $scan_type_list ) )
        $scan_type_list[$scan_type_id] = ['name' => $name, 'code_list' => []];
      $scan_type_list[$scan_type_id]['code_list'][$code_type_id] = sprintf( '[%s]: %s', $code, $description );
    }

    $select = lib::create( 'database\select' );
    $select->add_table_column( 'apex_exam', 'rank' );
    $select->add_table_column( 'apex_scan', 'scan_type_id' );
    $select->add_column( 'COUNT(*)', 'total', false );

    $modifier = lib::create( 'database\modifier' );
    $modifier->join( 'apex_scan', 'apex_deployment.apex_scan_id', 'apex_scan.id' );
    $modifier->join( 'apex_exam', 'apex_scan.apex_exam_id', 'apex_exam.id' );
    $modifier->where( 'apex_deployment.analysis_datetime', '!=', NULL );
    $modifier->group( 'apex_exam.rank' );
    $modifier->group( 'apex_scan.scan_type_id' );

    // count all pass/fails
    $pass_sel = clone( $select );
    $pass_sel->add_table_column( 'apex_deployment', 'pass' );

    $pass_mod = clone( $modifier );
    $pass_mod->where( 'apex_deployment.pass', '!=', NULL );
    $pass_mod->group( 'apex_deployment.pass' );

    $data = ['All Waves' => []];
    foreach( $apex_deployment_class_name::select( $pass_sel, $pass_mod ) as $row )
    {
      $rank = sprintf( 'Wave %d', $row['rank'] );
      $scan_type = $scan_type_list[$row['scan_type_id']];
      $pass = $row['pass'];
      $total = $row['total'];

      if( !array_key_exists( $rank, $data ) ) $data[$rank] = [];

      if( !array_key_exists( $scan_type['name'], $data['All Waves'] ) )
      {
        $data['All Waves'][$scan_type['name']] = ['PASS' => 0, 'FAIL' => 0];
        foreach( $scan_type['code_list'] as $code ) $data['All Waves'][$scan_type['name']][$code] = 0;
      }
      $data['All Waves'][$scan_type['name']][$pass ? 'PASS' : 'FAIL'] += $total;

      if( !array_key_exists( $scan_type['name'], $data[$rank] ) )
      {
        $data[$rank][$scan_type['name']] = ['PASS' => 0, 'FAIL' => 0];
        foreach( $scan_type['code_list'] as $code ) $data[$rank][$scan_type['name']][$code] = 0;
      }
      $data[$rank][$scan_type['name']][$pass ? 'PASS' : 'FAIL'] += $total;
    }

    // count all code types
    $code_sel = clone( $select );
    $code_sel->add_table_column( 'code_type', 'id', 'code_type_id' );

    $code_mod = clone( $modifier );
    $code_mod->join( 'code', 'apex_deployment.id', 'code.apex_deployment_id' );
    $code_mod->join( 'code_type', 'code.code_type_id', 'code_type.id' );
    $code_mod->group( 'code_type.code' );

    foreach( $apex_deployment_class_name::select( $code_sel, $code_mod ) as $row )
    {
      $rank = sprintf( 'Wave %d', $row['rank'] );
      $scan_type = $scan_type_list[$row['scan_type_id']];
      $code_type = $scan_type_list[$row['scan_type_id']]['code_list'][$row['code_type_id']];
      $total = $row['total'];

      $data['All Waves'][$scan_type['name']][$code_type] += $total;
      $data[$rank][$scan_type['name']][$code_type] += $total;
    }
    
    foreach( $data as $wave => $wave_data )
    {
      $root_node = $this->add_root_item( $wave );

      foreach( $wave_data as $code_type => $code_type_totals )
      {
        $node = $this->add_item( $root_node, $code_type );
        foreach( $code_type_totals as $code_type => $total ) $this->add_item( $node, $code_type, $total );
      }
    }
  }
}
