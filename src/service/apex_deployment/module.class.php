<?php
/**
 * module.class.php
 * 
 * @author Patrick Emond <emondpd@mcmaster.ca>
 * @filesource
 */

namespace salix\service\apex_deployment;
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

    $modifier->join( 'apex_scan', 'apex_deployment.apex_scan_id', 'apex_scan.id' );
    $modifier->join( 'apex_host', 'apex_deployment.apex_host_id', 'apex_host.id' );
    $modifier->join( 'site', 'apex_host.site_id', 'site.id' );
  }
}
