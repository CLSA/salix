<?php
/**
 * ui.class.php
 * 
 * @author Patrick Emond <emondpd@mcmaster.ca>
 * @filesource
 */

namespace salix\ui;
use cenozo\lib, cenozo\log, salix\util;

/**
 * Application extension to ui class
 */
class ui extends \cenozo\ui\ui
{
  /** 
   * Extends the parent method
   */
  protected function build_module_list()
  {
    parent::build_module_list();

    $module = $this->get_module( 'apex_baseline' );
    if( !is_null( $module ) )
    {
      $module->add_child( 'apex_exam' );
    }

    $module = $this->get_module( 'apex_exam' );
    if( !is_null( $module ) )
    {
      $module->add_child( 'apex_scan' );
    }

    $module = $this->get_module( 'apex_host' );
    if( !is_null( $module ) )
    {
      $module->add_child( 'apex_scan' );
    }
  }

  /**
   * Extends the sparent method
   */
  protected function build_listitem_list()
  {
    parent::build_listitem_list();

    $this->add_listitem( 'Apex Baselines', 'apex_baseline' );
    $this->add_listitem( 'Apex Exams', 'apex_exam' );
    $this->add_listitem( 'Apex Hosts', 'apex_host' );
    $this->add_listitem( 'Apex Scans', 'apex_scan' );

    $this->remove_listitem( 'Availability Types' );
    $this->remove_listitem( 'Consent Types' );
    $this->remove_listitem( 'Event Types' );
    $this->remove_listitem( 'Languages' );
    $this->remove_listitem( 'Participants' );
    $this->remove_listitem( 'States' );
  }

  /**
   * Extend the parent method
   */
  protected function get_utility_items()
  {
    // remove export
    $list = parent::get_utility_items();
    if( array_key_exists( 'Participant Export', $list ) ) unset( $list['Participant Export'] );
    return $list;
  }
}
