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

    $module = $this->get_module( 'apex_scan' );
    if( !is_null( $module ) )
    {
      $module->add_child( 'apex_deployment' );
      $module->add_child( 'code' );
    }

    $module = $this->get_module( 'apex_host' );
    if( !is_null( $module ) )
    {
      $module->add_child( 'apex_deployment' );
    }

    $module = $this->get_module( 'apex_deployment' );
    if( !is_null( $module ) )
    {
      $module->add_child( 'apex_scan' );
    }

    $module = $this->get_module( 'scan_type' );
    if( !is_null( $module ) )
    {
      $module->add_child( 'apex_scan' );
      $module->add_choose( 'code_type' );
    }

    $module = $this->get_module( 'code_type' );
    if( !is_null( $module ) )
    {
      $module->add_choose( 'scan_type' );
    }
  }

  /**
   * Extends the sparent method
   */
  protected function build_listitem_list()
  {
    parent::build_listitem_list();

    $db_role = lib::create( 'business\session' )->get_role();

    if( 3 <= $db_role->tier )
    {
      $this->add_listitem( 'Apex Baselines', 'apex_baseline' );
      $this->add_listitem( 'Apex Exams', 'apex_exam' );
      $this->add_listitem( 'Code Types', 'code_type' );
      $this->add_listitem( 'Scan Types', 'scan_type' );
      $this->add_listitem( 'Serial Numbers', 'serial_number' );
    }
    else
    {
      $this->remove_listitem( 'Users' );
    }

    $this->add_listitem( 'Apex Hosts', 'apex_host' );

    $this->remove_listitem( 'Availability Types' );
    $this->remove_listitem( 'Consent Types' );
    $this->remove_listitem( 'Event Types' );
    $this->remove_listitem( 'Languages' );
    $this->remove_listitem( 'Participants' );
    $this->remove_listitem( 'Settings' );
  }

  /**
   * Extend the parent method
   */
  protected function get_utility_items()
  {
    $list = parent::get_utility_items();

    $db_role = lib::create( 'business\session' )->get_role();

    if( array_key_exists( 'Participant Export', $list ) ) unset( $list['Participant Export'] );
    if( array_key_exists( 'Participant Multiedit', $list ) ) unset( $list['Participant Multiedit'] );
    if( array_key_exists( 'Participant Search', $list ) ) unset( $list['Participant Search'] );
    if( array_key_exists( 'Tracing', $list ) ) unset( $list['Tracing'] );
    if( array_key_exists( 'User Overview', $list ) ) if( 3 > $db_role->tier ) unset( $list['User Overview'] );

    return $list;
  }
}
