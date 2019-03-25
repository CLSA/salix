#!/usr/bin/php
<?php
/**
 * receive_deployments.php
 *
 * A script for retrieving DEXA scans, verifying their content,
 * and creating a deployment record for subsequent transfer to
 * the APEX host workstations.  The script currently only allows
 * for grouped hip scan retrieval (left side preferred) and
 * priority whole body scans.
 *
 * @author Dean Inglis <inglisd@mcmaster.ca>
 */

chdir( dirname( __FILE__ ).'/../' );
require_once 'settings.ini.php';
require_once 'settings.local.ini.php';
require_once $SETTINGS['path']['CENOZO'].'/src/initial.class.php';
$initial = new \cenozo\initial();
$settings = $initial->get_settings();

define( 'OPAL_SERVER', $settings['opal']['server'] );
define( 'OPAL_PORT', $settings['opal']['port'] );
define( 'OPAL_USERNAME', $settings['opal']['username']  );
define( 'OPAL_PASSWORD', $settings['opal']['password'] );

define( 'DB_SERVER', $settings['db']['server'] );
define( 'DB_PREFIX', $settings['db']['database_prefix'] );
define( 'DB_USERNAME', $settings['db']['username'] );
define( 'DB_PASSWORD', $settings['db']['password'] );

define( 'IMAGE_PATH', $settings['path']['TEMPORARY_FILES'] );
define( 'USER', $settings['utility']['username'] );

// a lite mysqli wrapper
require_once( $settings['path']['PHP_UTIL'].'/database.class.php' );
// a lite curl wrapper
require_once( $settings['path']['PHP_UTIL'].'/opalcurl.class.php' );

require_once( $settings['path']['PHP_UTIL'].'/util.class.php' );

require_once( $settings['path']['APPLICATION'].'/aux/forearm_scan_collector.class.php' );

require_once( $settings['path']['APPLICATION'].'/aux/generic_scan_collector.class.php' );

$db_salix = null;
try
{
  $db_salix = new database(
    DB_SERVER, DB_USERNAME, DB_PASSWORD, DB_PREFIX . 'salix' );
}
catch( Exception $e )
{
  util::out( $e->getMessage() );
  return 0;
}

// verify that for each type that has more than one
// side and for each host that the weights are equal
//

$sql = 
 'select type, side, weight, h.id as apex_host_id '.
 'from allocation a '.
 'join scan_type t on t.id=a.scan_type_id '.
 'join apex_host h on h.id=a.apex_host_id';

$res = $db_salix->get_all($sql);
if(null===$res || !is_array($res))
{
  return 0;
}

$host_list = array();
foreach($res as $item)
{
  $id = $item['apex_host_id'];
  $type = $item['type'];
  $side = $item['side'];
  $weight = $item['weight'];
  $host_list[$id][$type][$side] = $weight;

  if(2==count(array_keys($host_list[$id][$type])) && 
     in_array($side,array('left','right')))
  {
    if($host_list[$id][$type]['left'] !=
       $host_list[$id][$type]['right'])
    {
      util::out('ERROR: asymetric weights');
      return 0;
    }
  }
}

$sql = 
  'select type, '.
  'if(sum(if(side="none",0,1))>0,1,0) as bilateral, '.
  'sum(weight) as total_weight '.
  'from allocation a '.
  'join scan_type t on t.id=a.scan_type_id '.
  'join apex_host h on h.id=a.apex_host_id '.
  'where side in ("left","none") '.
  'group by type order by type';

$res = $db_salix->get_all($sql);
if(null===$res || !is_array($res))
{
  return 0;
}

$collector_list = array();
foreach($res as $item)
{
  $type = $item['type'];
  $total_weight = round($item['total_weight'],3);
  foreach($host_list as $host_id=>$type_list)
  {
    if(array_key_exists($type,$type_list))
    {
      $side = $item['bilateral'] ? 'left' : 'none';
      $weight = $type_list[$type][$side];
      $percent = $weight / $total_weight;
      $host_list[$host_id][$type] = $percent;
      $collector_list[] = $type;
    }
  }
}

$collector_list = array_unique($collector_list);
// we need the total that can be sent by type
foreach($collector_list as $type)
{
  $collector = ('forearm'==$type) ? 
      (new forearm_scan_collector($db_salix,DB_PREFIX)) :
      (new generic_scan_collector($db_salix,DB_PREFIX, $type));
 util::out(sprintf('--------------------TYPE = %s --------',$type));
 $collector->collect_scans();
 $deployment_list = $collector->get_deployments();
 
 // 'any' key means each host gets their allocation percentage of 'any'
 // actual host key gets 100% of targed deployments for that host
 // 
 // t1 = host_percent x N('any') + N(host_id)
 //  
}
die();


util::out(sprintf('--------------------TYPE = forearm --------',$type));
$forearm = new forearm_scan_collector($db_salix, DB_PREFIX);
$forearm->collect_scans();
$forearm->get_deployments();

$generic =array('spine','wbody','lateral','hip');
foreach($generic as $type)
{
 util::out(sprintf('--------------------TYPE = %s --------',$type));
 $gtype = new generic_scan_collector($db_salix, DB_PREFIX, $type);

 //if('hip'==$type) 
 //  $gtype->set_preferred_side('left');
 //else continue;  

 $gtype->collect_scans();
 $gtype->get_deployments();

}


return 1;


