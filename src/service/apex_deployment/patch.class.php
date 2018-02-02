<?php
/**
 * patch.class.php
 * 
 * @author Patrick Emond <emondpd@mcmaster.ca>
 */

namespace salix\service\apex_deployment;
use cenozo\lib, cenozo\log, salix\util;

/**
 * Special service for handling the patch meta-resource
 */
class patch extends \cenozo\service\patch
{
  /**
   * Override parent method
   */
  public function get_file_as_array()
  {
    // remove reset_codes from the patch array
    $patch_array = parent::get_file_as_array();
    if( array_key_exists( 'reset_codes', $patch_array ) )
    {
      $this->reset_codes = $patch_array['reset_codes'];
      unset( $patch_array['reset_codes'] );
    }

    return $patch_array;
  }

  /**
   * Override parent method
   */
  protected function execute()
  {
    parent::execute();

    // reset the deployment's codes, if requested
    $db_apex_deployment = $this->get_leaf_record();
    if( $this->reset_codes && '' === $db_apex_deployment->pass ) // when set to null pass will be an empty string
      $this->get_leaf_record()->reset_codes();
  }

  /**
   * Used to define the reason for this participant's last trace
   * @var object
   * @access protected
   */
  protected $reset_codes = false;
}
