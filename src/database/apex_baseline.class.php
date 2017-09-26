<?php
/**
 * apex_baseline.class.php
 * 
 * @author Patrick Emond <emondpd@mcmaster.ca>
 * @filesource
 */

namespace salix\database;
use cenozo\lib, cenozo\log, salix\util;

/**
 * apex_baseline: record
 */
class apex_baseline extends \cenozo\database\record
{
  /**
   * Override parent method to allow uid as a unique column
   */
  public static function get_unique_record( $column, $value )
  {
    // convert column and value to arrays
    $column_as_array = is_array( $column ) ? $column : array( $column );
    $value_as_array = is_array( $value ) ? $value : array( $value );

    if( 1 == count( $column_as_array ) && 'uid' == $column_as_array[0] )
    {
      $select = lib::create( 'database\select' );
      $select->from( 'participant' );
      $select->add_column( 'id' );
      $modifier = lib::create( 'database\modifier' );
      $modifier->where( 'uid', '=', $value_as_array[0] );
      $participant_id = static::db()->get_one( sprintf( '%s %s', $select->get_sql(), $modifier->get_sql() ) );

      return is_null( $participant_id ) ?
        NULL : parent::get_unique_record( 'participant_id', $participant_id );
    }

    return parent::get_unique_record( $column, $value );
  }
}
