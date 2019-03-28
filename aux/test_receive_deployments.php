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

$deployment_limit = 300;

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

$opal_curl = new opalcurl( OPAL_SERVER, OPAL_PORT, OPAL_USERNAME, OPAL_PASSWORD );
$opal_curl->set_datasource( 'salix' );

// order of preference for tie breakers
$ordered_host_list = array('dancer'=>1,'bluet'=>2,'skimmer'=>3);
$sql = 'select name, id from apex_host';
$res = $db_salix->get_all($sql);
if(null===$res || !is_array($res))
{
  return 0;
}
foreach($res as $item)
{
  $name = $item['name'];
  $ordered_host_list[$name] = $item['id'];
}
$ordered_host_list = array_values($ordered_host_list);

// verify that for each type that has more than one
// side and for each host that the weights are equal
//

$sql =
 'select type, side, weight, h.name as host, h.id as apex_host_id '.
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
  $host = $item['host'];
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

$deploy_scan_list = array();
$invalid_scan_list = array();

$collector_list = array_unique($collector_list);
foreach($collector_list as $type)
{
  $collector = ('forearm'==$type) ?
      (new forearm_scan_collector($db_salix,DB_PREFIX)) :
      (new generic_scan_collector($db_salix,DB_PREFIX, $type));
  util::out(sprintf('--------------------TYPE = %s --------',$type));

  $collector->collect_scans();
  $deployment_list = $collector->get_deployments( $ordered_host_list );

  $stats = $collector->get_count_stats();

  $all_total_candidate =
    array_sum($stats['numHostCandidates']);
  $total_residual_candidate =
    array_key_exists('any', $stats['numHostCandidates']) ? $stats['numHostCandidates']['any'] : 0;
  $total_targeted_candidate = $all_total_candidate - $total_residual_candidate;
  $all_total_deployment = $stats['numDeployments'];

  $residual_list = 0 < $total_residual_candidate ? $deployment_list['any'] : array();

  foreach($host_list as $host_id=>$type_list)
  {
    if(!array_key_exists($type,$type_list)) continue;
    if(!array_key_exists($host_id,$stats['numHostCandidates'])) continue;

    $percent = $type_list[$type];

    $num_priority =
      array_key_exists($host_id,$stats['numHostPriorityCandidates']) ? $stats['numHostPriorityCandidates'][$host_id] : 0;
    $num_deployments =
      array_key_exists($host_id,$stats['numHostDeployments']) ? $stats['numHostDeployments'][$host_id] : 0;
    $num_targeted =
      array_key_exists($host_id,$stats['numHostCandidates']) ? $stats['numHostCandidates'][$host_id] : 0;
    $num_residual = max( array(
      intval(round($percent*($all_total_deployment + $all_total_candidate) - $num_deployments - $num_targeted,0)), 0 ) );

    util::out(sprintf('host %d must deploy %d candidates (%d priority) + %d additional untargeted (%d available)',
      $host_id,$num_targeted,$num_priority,$num_residual,$total_residual_candidate));

    // the quota for this host
    $quota = intval(round($percent*$deployment_limit,0));

    // take from the targeted scans for this host first
    $list = $deployment_list[$host_id];
    util::out(sprintf('host %d: initial number of candidate chains %d',$host_id,count($list)));

    $num_p = 0;
    if(0 < $num_targeted)
    {
      $chain_key_list = array();
      $num_used = 0; 
      foreach($list as $chain_key=>$scan_chain)
      {
        foreach($scan_chain as $item_key=>$item)
        {
          /*
          // validate undeployed scans
          $filename = $item->get_scan_file( $opal_curl, IMAGE_PATH );
          if( NULL !== $filename )
          {
            if( false === $item->validate( $filename ) )
            {
              $deploy_scan_list[] = $item;
              $quota--;
              $num_used++;
            }  
            else
            {
              $invalid_scan_list[] = $item->apex_scan_id;
            }
            unlink( $filename );
          }
          */
              $deploy_scan_list[] = $item;
              $num_p += $item->priority;
              $quota--;
              $num_used++;
        }// end current scan chain
        
        $chain_key_list[] = $chain_key;  

        // break only on completion of a chain to keep scans grouped to a uid 
        if(0 >= $quota) break;

      }// end chain list

      foreach($chain_key_list as $key) unset($list[$key]);
      
      util::out(sprintf('host %d: used up %d targeted', $host_id, $num_used));

    }//end if there are targets for this host

    if(0 < $num_residual && 0 < $quota && 0 < count($residual_list))
    {
      if($num_residual < $quota) $quota = $num_residual;
      $num_used = 0; 

      $chain_key_list = array();
      foreach($residual_list as $chain_key=>$scan_chain)
      {
        foreach($scan_chain as $item_key=>$item)
        {
          /*
          // validate undeployed scans
          $filename = $item->get_scan_file( $opal_curl, IMAGE_PATH );
          if( NULL !== $filename )
          {
            if( false === $item->validate( $filename ) )
            {
              $deploy_scan_list[] = $item;
              $quota--;
              $num_used++;
            }  
            else
            {
              $invalid_scan_list[] = $item->apex_scan_id;
            }
            unlink( $filename );
          }
          */
              $deploy_scan_list[] = $item;
              $num_p += $item->priority;
              $quota--;
              $num_used++;

        }// end current scan chain
        
        $chain_key_list[] = $chain_key;  

        // break only on completion of a chain to keep scans grouped to a uid 
        if(0 >= $quota) break;

      }// end chain list

      foreach($chain_key_list as $key) unset($residual_list[$key]);
      
      util::out(sprintf('host %d: used up %d residuals', $host_id, $num_used));
    }
    
    util::out(sprintf('host %d: priority deployments: %s,  candidate chains remaining %d',$host_id,$num_p, count($list)));

  }// end loop on host ids
}// end loop on collector types

die();


if( 0 < count( $invalid_scan_list ) )
{
  $sql =  sprintf(
    'UPDATE apex_scan '.
    'SET invalid=1 '.
    'WHERE id IN ( %s )', implode( ',', $invalid_scan_list ) );
  /*
  if( false === $db_salix->execute( $sql ) )
    write_log( sprintf(
      'WARNING: failed to set invalid state to 1 in %d apex_scan records',
      count( $invalid_scan_list ) ) );
  */
}


return 1;


