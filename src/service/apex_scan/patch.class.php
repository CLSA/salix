<?php
/**
 * patch.class.php
 * 
 * @author Patrick Emond <emondpd@mcmaster.ca>
 */

namespace salix\service\apex_scan;
use cenozo\lib, cenozo\log, salix\util;

/**
 * Special service for handling the patch meta-resource
 */
class patch extends \cenozo\service\patch
{
  /**
   * Extend parent method
   */
  public function validate()
  {
    parent::validate();

    $service_class_name = lib::get_class_name( 'service\service' );
    $method = $this->get_method();

    if( 300 > $this->get_status()->get_code() )
    {
      if( 'PATCH' == $method )
      {
        $file = $this->get_file_as_array();
        $db_apex_scan = $this->get_leaf_record();
        if( array_key_exists( 'priority', $file ) && $file['priority'] && !$db_apex_scan->availability )
        {
          $this->set_data( 'This scan is not available so it cannot be made a priority.' );
          $this->get_status()->set_code( 306 );
        }
      }
    }
  }
}
