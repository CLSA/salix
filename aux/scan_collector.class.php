<?php
require_once (dirname(__FILE__).'/../settings.ini.php');

// a lite mysqli wrapper
require_once( $settings['path']['PHP_UTIL'].'/database.class.php' );

// dexa scan helper class
require_once( 'dexa_scan.class.php' );

abstract class scan_collector
{
  public function __construct($db, $db_prefix)
  {
    $this->db = $db;
    $this->db_prefix = $db_prefix;
    $this->preferred_side = 'none';
  }

  public function set_preferred_side($side)
  {
    if(in_array($side,array('none','left','right')))
    {
      $this->preferred_side = $side;
    }
  }

  protected function get_candidate_scans($uid)
  {
    return array_key_exists($uid, $this->candidate_scans) ? $this->candidate_scans[$uid] : null;
  }

  protected function get_deployed_scans($uid)
  {
    return array_key_exists($uid, $this->deployed_scans) ? $this->deployed_scans[$uid] : null;
  }

  public function collect_scans()
  {
    $this->build_collection_query();

    $res = $this->db->get_all($this->query);
    if(false === $res)
    {
      util::out(sprintf('SQL ERROR: %s',$this->query));
      die();
    }
    if(is_array($res) && 0 < count($res))
    {
      $numcan = 0;
      $numdep = 0;
      $partition = $this->preferred_side == 'none' ? false : true;
      foreach($res as $data)
      {
        $uid = $data['uid'];
        $side = $data['side'];
        $scan = new dexa_scan(
          $data['uid'], $data['type'], $data['side'],
          $data['rank'], $data['barcode'], $data['serial_number'],
          $data['apex_scan_id'], $data['scan_type_id'], $data['priority'], $data['apex_host_id'] );

        if($data['apex_host_id']=='NULL')
        {
          if($partition)
            $this->candidate_scans[$uid][$side][] = $scan;
          else
            $this->candidate_scans[$uid][] = $scan;
          $numcan++;
        }
        else
        {
          if($partition)
            $this->deployed_scans[$uid][$side][] = $scan;
          else
            $this->deployed_scans[$uid][] = $scan;

          $numdep++;
        }
      }
      util::out(sprintf('ok, found %d scans: candidates: %d, deployed: %d',
        count($res),$numcan,$numdep));
    }
  }

  abstract protected function build_collection_query();

  // two chain types:
  // a chain that must be deployed to a host so that sibling relationships are maintained
  // a chain that can be deployed to any host
  //
  protected function get_scan_chain($uid)
  {
    $candidate_list = $this->get_candidate_scans($uid);
    $deployed_list = $this->get_deployed_scans($uid);

    if(null===$candidate_list) return null;

    $scan_chain = null;
    if($this->preferred_side == 'none')
    {
      $scan_chain['scans'] = $candidate_list;
      $scan_chain['host_id'] = null;
      if(null !== $deployed_list)
      {
        $host_id_list = array();
        foreach($deployed_list as $item)
        {
          if(!array_key_exists($item->apex_host_id, $host_id_list))
          {
            $host_id_list[$item->apex_host_id] = 0;
          }
          $host_id_list[$item->apex_host_id]++;
        }
        if(0<count($host_id_list))
        {
          // an array of ids which will either be a single id or an array of ties
          $scan_chain['host_id'] = array_keys($host_id_list, max($host_id_list));
        }
      }
    }
    else
    {
      // handle left and rights, preferred is usually left

      // case 1: no prior deployments
      // - if there are no preferred side candidates send the other side
      // - if there are only preferred side candidates send them
      $candidate_side_keys = array_keys($candidate_list);
      if(count($candidate_side_keys)>2)
      {
        util::out('ERROR: more than 2 candidate side keys');
        die();
      }
      if(null===$deployed_list)
      {
        $scan_chain['host_id'] = null;
        if(in_array($this->preferred_side,$candidate_side_keys))
        {
          $scan_chain['scans'] = $candidate_list[$this->preferred_side];
        }
        else
        {
          $scan_chain['scans'] = $candidate_list[reset($candidate_side_keys)];
        }
      }
      // case 2: prior deployments
      // - preferred candidates
      // - non-preferred candidates
      else
      {
        $deployed_side_keys = array_keys($deployed_list);
        if(count($deployed_side_keys)>2)
        {
          util::out('ERROR: more than 2 deployed side keys');
          die();
        }
        // case 2.1:
        // preferred candidates
        //   - preferred deployed, find max host id or ties
        //   - discard non-preferred candidates
        $alternate_side = $this->preferred_side == 'left' ? 'right' : 'left';
        $chain_side = null;
        if(in_array($this->preferred_side,$candidate_side_keys) &&
           in_array($this->preferred_side,$deployed_side_keys))
        {
          $chain_side = $this->preferred_side;
        }
        // case 2.1:
        // no preferred, only non-preferred candidates
        //  - non-deferred deployed, find max host id or ties
        else if(in_array($alternate_side,$candidate_side_keys) &&
           in_array($alternate_side,$deployed_side_keys))
        {
          $chain_side = $alternate_side;
        }

        if(null !== $chain_side)
        {
          $scan_chain['scans'] = $candidate_list[$chain_side];
          $host_id_list = array();
          foreach($deployed_list[$chain_side] as $item)
          {
            if(!array_key_exists($item->apex_host_id, $host_id_list))
            {
              $host_id_list[$item->apex_host_id] = 0;
            }
            $host_id_list[$item->apex_host_id]++;
          }
          $scan_chain['host_id'] = array_keys($host_id_list, max($host_id_list));
        }
      }
    }
    return $scan_chain;
  }

  public function get_deployments()
  {
    // get all the uid's that have candidates
    $uid_list = array_keys($this->candidate_scans);
    $deployment_list = array('any'=>array(),'undecided'=array());
    foreach($uid_list as $uid)
    {
      $scan_chain = $this->get_scan_chain($uid);
      if(null === $scan_chain) continue;
      $host_id = $scan_chain['host_id'];
      if(null === $host_id)
      {
        // each array element in the final deployment lists must
        // be deployed to the same host (ie., keep scans for a uid together)
        //
        $deployment_list['any'][] = $scan_chain['scans'];
      }
      else
      {
        // we dont have a choice, these scans must be deployed to this host
        if(1 == count($host_id))
        {
          $id = $host_id[0];
          $deployment_list[$id][] = $scan_chain['scans'];
        }
        else  // in the list of hosts that have weights
        {
          //util::out(sprintf('tie situation among %d hosts',count($host_id)));
          $deployment_list['undecided'][] = $scan_chain['scans'];
        }
      }
    }

    //DEBUG - report how many uid per host
    $total_deploy = 0;
    $total_uid = 0;
    foreach($deployment_list as $key => $scan_chain_list)
    {
      $num = 0;
      $num_multi = 0;
      $num_single = 0;
      foreach($scan_chain_list as $list)
      { 
        $total_uid++;
        $n = count($list);
        $num += $n;
        if(1 == $n) 
          $num_single++;
        else if(1 < $n)
          $num_multi++;
      }
      $total_deploy += $num;

      $deployment_list[$key]['uid_count'] = count($scan_chain_list);  
      $deployment_list[$key]['total_count'] = $num;
      $deployment_list[$key]['single_count'] = $num_single;
      $deployment_list[$key]['multi_count'] = $num_multi;
      
    }
    $deployment_list['total_uid'] = $total_uid;
    $deployment_list['total_deploy'] = $total_deploy;
    return $deployment_list; 
  }


  protected $candidate_scans = array();

  protected $deployed_scans = array();

  protected $query = null;

  protected $db = null;

  protected $db_prefix = null;

  protected $scan_type = null;

  protected $preferred_side = 'none';
}
