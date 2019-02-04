<?php
/**
 * module.class.php
 * 
 * @author Patrick Emond <emondpd@mcmaster.ca>
 * @filesource
 */

namespace salix\service\allocation;
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

    $modifier->join( 'apex_host', 'allocation.apex_host_id', 'apex_host.id' );
    $modifier->join( 'scan_type', 'allocation.scan_type_id', 'scan_type.id' );

    if( $select->has_column( 'scan_type_side' ) )
      $select->add_column( 'CONCAT( IF( side="none","",CONCAT( side, " " ) ), type )', 'scan_type_side', false );
  }
}
