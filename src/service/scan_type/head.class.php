<?php
/**
 * head.class.php
 * 
 * @author Patrick Emond <emondpd@mcmaster.ca>
 */

namespace salix\service\scan_type;
use cenozo\lib, cenozo\log, salix\util;

/**
 * The base class of all head services
 */
class head extends \cenozo\service\head
{
  /**
   * Extends parent method
   */
  protected function setup()
  {
    parent::setup();

    $this->columns['name'] = array(
      'data_type' => 'varchar',
      'default' => null,
      'required' => '1'
    );
  }
}
