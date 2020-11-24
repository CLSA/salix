<?php
/**
 * post.class.php
 * 
 * @author Patrick Emond <emondpd@mcmaster.ca>
 */

namespace salix\service\application\site;
use cenozo\lib, cenozo\log, salix\util;

/**
 * The base class of all post services.
 */
class post extends \cenozo\service\post
{
  /**
   * Extends parent method
   * 
   * Note, we need to replace cenozo's version of this file because we have to select a default apex host for new sites
   */
  protected function execute()
  {
    $setting_class_name = lib::get_class_name( 'database\setting' );
    $apex_host_class_name = lib::get_class_name( 'database\apex_host' );

    parent::execute();

    $post_object = $this->get_file_as_object();
    if( property_exists( $post_object, 'add' ) )
    {
      // create a setting record for the new site
      foreach( $post_object->add as $site_id )
      {
        // get the default apex_host id
        $select = lib::create( 'database\select' );
        $select->from( 'apex_host' );
        $select->add_column( 'id' );
        $modifier = lib::create( 'database\modifier' );
        $modifier->limit( 1 );
        $apex_host_id = $apex_host_class_name::db()->get_one( sprintf( '%s %s', $select->get_sql(), $modifier->get_sql() ) );

        $db_setting = $setting_class_name::get_unique_record( 'site_id', $site_id );
        if( is_null( $db_setting ) )
        {
          $db_setting = lib::create( 'database\setting' );
          $db_setting->site_id = $site_id;
          $db_setting->priority_apex_host_id = $apex_host_id;
          $db_setting->save();
        }
      }
    }
    else if( property_exists( $post_object, 'remove' ) )
    {
      // remove site settings
      foreach( $post_object->remove as $site_id )
      {
        $db_setting = $setting_class_name::get_unique_record( 'site_id', $site_id );
        if( !is_null( $db_setting ) ) $db_setting->delete();
      }
    }
  }
}
