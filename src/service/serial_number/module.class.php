<?php
/**
 * module.class.php
 * 
 * @author Patrick Emond <emondpd@mcmaster.ca>
 * @filesource
 */

namespace salix\service\serial_number;
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

    $modifier->join( 'site', 'serial_number.site_id', 'site.id' );
  }
}
