<?php
/**
 * module.class.php
 * 
 * @author Patrick Emond <emondpd@mcmaster.ca>
 * @filesource
 */

namespace salix\service\code;
use cenozo\lib, cenozo\log, salix\util;

/**
 * Performs operations which effect how this module is used in a service
 */
class module extends \cenozo\service\module
{
  /**
   * Extend parent method
   */
  public function validate()
  {
    parent::validate();

    $service_class_name = lib::get_class_name( 'service\service' );
    $method = $this->get_method();

    if( $this->service->may_continue() )
    {
      // do not allow editing if the deployment is exported or null
      if( $service_class_name::is_write_method( $method ) )
      {
        $status = $this->get_resource()->get_apex_deployment()->status;
        if( is_null( $status ) || 'exported' == $status ) $this->get_status()->set_code( 403 );
      }
    }
  }

  /** 
   * Extend parent method
   */
  public function prepare_read( $select, $modifier )
  {
    parent::prepare_read( $select, $modifier );
  }
}
