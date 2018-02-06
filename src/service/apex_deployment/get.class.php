<?php
/**
 * get.class.php
 * 
 * @author Patrick Emond <emondpd@mcmaster.ca>
 */

namespace salix\service\apex_deployment;
use cenozo\lib, cenozo\log, salix\util;

class get extends \cenozo\service\downloadable
{
  /**
   * Replace parent method
   * 
   * When the client calls for a file we return the apex deployment's report
   */
  protected function get_downloadable_mime_type_list()
  {
    return array( 'image/jpeg' );
  }

  /**
   * Replace parent method
   * 
   * When the client calls for a file we return the apex deployment's report
   */
  protected function get_downloadable_public_name()
  {
    return sprintf( '%s.jpg', $this->get_leaf_record()->id );
  }

  /**
   * Replace parent method
   * 
   * When the client calls for a file we return the apex deployment's report
   */
  protected function get_downloadable_file_path()
  {
    return sprintf( '%s/%s.jpg', DEPLOYMENT_REPORT_PATH, $this->get_leaf_record()->id );
  }

  /**
   * Extend parent method
   */
  public function execute()
  {
    if( $this->get_argument( 'report', false ) )
    {
      $db_apex_deployment = $this->get_leaf_record();
      $this->set_data( is_null( $db_apex_deployment ) ?
        NULL : file_exists( $this->get_downloadable_file_path() ) );
    }
    else
    {
      parent::execute();
    }
  }
}
