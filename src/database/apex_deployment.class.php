<?php
/**
 * apex_deployment.class.php
 * 
 * @author Patrick Emond <emondpd@mcmaster.ca>
 * @filesource
 */

namespace salix\database;
use cenozo\lib, cenozo\log, salix\util;

/**
 * apex_deployment: record
 */
class apex_deployment extends \cenozo\database\record
{
  /**
   * Extend parent method
   */
  function save()
  {
    // affect status and analysis_datetime if pass column is being changed
    if( $this->has_column_changed( 'pass' ) )
    {
      if( '' === $this->pass ) // when changing to null pass will be an empty string
      {
        $this->status = 'pending';
        $this->analysis_datetime = NULL;
      }
      else
      {
        if( 'pending' == $this->status ) $this->status = 'completed';
        if( is_null( $this->analysis_datetime ) ) $this->analysis_datetime = util::get_datetime_object();
      }
    }

    parent::save();
  }

  /**
   * Removes all codes from the apex_deployment
   * @access public
   */
  function reset_codes()
  {
    $modifier = lib::create( 'database\modifier' );
    $modifier->where( 'apex_deployment_id', '=', $this->id );
    static::db()->execute( sprintf( 'DELETE FROM code%s', $modifier->get_sql() ) );
  }
}
