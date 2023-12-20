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
  protected function prepare()
  {
    $this->extract_parameter_list[] = 'reset_codes';

    parent::prepare();
  }

  /**
   * Override parent method
   */
  protected function execute()
  {
    parent::execute();

    // reset the deployment's codes, if requested
    $db_apex_deployment = $this->get_leaf_record();
    if( $this->get_argument( 'reset_codes', false ) && '' === $db_apex_deployment->pass )
    {
      $this->get_leaf_record()->reset_codes();
    }
  }
}
