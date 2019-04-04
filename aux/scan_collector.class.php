<?php
require_once (dirname(__FILE__).'/../settings.ini.php');

// a lite mysqli wrapper
require_once( $settings['path']['PHP_UTIL'].'/database.class.php' );

// dexa scan helper class
require_once( 'dexa_scan.class.php' );

class scan_collector
{
  public function __construct($db, $db_prefix, $type)
  {
    $this->db = $db;
    $this->db_prefix = $db_prefix;
    $this->preferred_side = 'none';
    $this->reset_count_stats();
    $this->scan_type = $type;
    if('hip' == $type)
      $this->preferred_side = 'left';
  }

  protected function reset_count_stats()
  {
    $this->count_stats = array(
      'numCandidates' => 0,
      'numDeployments' => 0,
      'numHostCandidates' => array(),
      'numHostDeployments' => array(),
      'numHostPriorityCandidates' => array());
  }

  public function get_count_stats()
  {
    return $this->count_stats;
  }

  public function set_preferred_side($side)
  {
    if(in_array($side, array('none','left','right')))
    {
      $this->preferred_side = $side;
    }
  }

  public function set_rank_restrictions($ranks)
  {
    if(is_array($ranks))
      $this->rank_restrictions = $ranks;
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
      return false;
    }

    $this->reset_count_stats();

    if(is_array($res) && 0 < count($res))
    {
      $partition = 'none' == $this->preferred_side ? false : true;
      foreach($res as $data)
      {
        $uid = $data['uid'];
        $side = $data['side'];
        $host_id = $data['apex_host_id'];
        $scan = new dexa_scan(
          $data['uid'], $data['type'], $data['side'],
          $data['rank'], $data['barcode'], $data['serial_number'],
          $data['apex_scan_id'], $data['scan_type_id'], $data['priority'], $host_id );

        if('NULL' == $data['apex_host_id'])
        {
          if($partition)
            $this->candidate_scans[$uid][$side][] = $scan;
          else
            $this->candidate_scans[$uid][] = $scan;

          $this->count_stats['numCandidates']++;
        }
        else
        {
          if($partition)
            $this->deployed_scans[$uid][$side][] = $scan;
          else
            $this->deployed_scans[$uid][] = $scan;

          $this->count_stats['numDeployments']++;
          if(!array_key_exists($host_id,$this->count_stats['numHostDeployments']))
          {
            $this->count_stats['numHostDeployments'][$host_id] = 0;
          }
          $this->count_stats['numHostDeployments'][$host_id]++;
        }
      }
    }
    return true;
  }

  protected function build_collection_query()
  {
    if('forearm' == $this->scan_type)
    {
      $this->query = sprintf(
        'SELECT DISTINCT '.
        'uid, type, side,  '.
        'rank, barcode, s.id AS apex_scan_id,  '.
        'priority, n.id AS serial_number, '.
        'IFNULL(d.apex_host_id, "NULL") AS apex_host_id, '.
        'availability, invalid, scan_type_id '.
        'FROM '.
        '( '.
        '  SELECT DISTINCT e.id '.
        '  FROM apex_exam e '.
        '  JOIN apex_scan s ON e.id=s.apex_exam_id '.
        '  JOIN scan_type t ON t.id=s.scan_type_id '.
        '  WHERE type="forearm" '.
        '  AND availability=1   '.
        '  AND (invalid=0 OR invalid=1) '.
        ') AS t1  '.
        'LEFT JOIN '.
        '( '.
        '  SELECT DISTINCT e.id   '.
        '  FROM apex_exam e '.
        '  JOIN apex_scan s ON e.id=s.apex_exam_id '.
        '  JOIN scan_type t ON t.id=s.scan_type_id '.
        '  WHERE type IN ("hip","wbody","spine") '.
        '  AND availability=1 '.
        '  AND (invalid=0 OR invalid IS NULL) '.
        ') AS t2 ON t1.id=t2.id '.
        'JOIN apex_exam e ON e.id=t1.id '.
        'JOIN apex_scan s ON s.apex_exam_id=e.id  '.
        'JOIN scan_type t ON s.scan_type_id=t.id  '.
        'JOIN serial_number n ON e.serial_number_id=n.id  '.
        'JOIN apex_baseline b ON e.apex_baseline_id=b.id  '.
        'JOIN %scenozo.participant p ON b.participant_id=p.id  '.
        'LEFT JOIN apex_deployment d ON d.apex_scan_id=s.id  '.
        'WHERE t2.id IS NULL '.
        'AND type="forearm" '.
        (0 < count($this->rank_restrictions) ?
         sprintf('AND rank IN (%s) ',implode($this->rank_restrictions)) : '').
        'AND availability=1 '.
        'ORDER BY uid, rank, side', $this->db_prefix);
    }
    else
    {
      $this->query = sprintf(
        'select distinct '.
        'uid, type, side,  '.
        'rank, barcode, s.id AS apex_scan_id,  '.
        'priority, n.id AS serial_number, '.
        'IFNULL(d.apex_host_id, "NULL") AS apex_host_id, '.
        'availability, invalid, scan_type_id '.
        'FROM '.
        'apex_scan s '.
        'JOIN apex_exam e ON s.apex_exam_id=e.id  '.
        'JOIN scan_type t ON s.scan_type_id=t.id  '.
        'JOIN serial_number n ON e.serial_number_id=n.id  '.
        'JOIN apex_baseline b ON e.apex_baseline_id=b.id  '.
        'JOIN %scenozo.participant p ON b.participant_id=p.id  '.
        'LEFT JOIN apex_deployment d ON d.apex_scan_id=s.id  '.
        'WHERE type="%s" '.
        (0 < count($this->rank_restrictions) ?
         sprintf('AND rank IN (%s) ',implode($this->rank_restrictions)) : '').
        'AND availability=1 '.
        'AND (invalid IS NULL OR invalid=0) '.
        'ORDER BY uid, rank', $this->db_prefix, $this->scan_type);
    }
    var_dump($this->query);
  }

  // two chain types:
  // a chain that must be deployed to a host so that sibling relationships are maintained
  // a chain that can be deployed to any host
  //
  protected function get_scan_chain($uid)
  {
    $candidate_list = $this->get_candidate_scans($uid);
    $deployed_list = $this->get_deployed_scans($uid);

    if(null === $candidate_list) return null;

    $scan_chain = null;
    if('none' == $this->preferred_side)
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
        if(0 < count($host_id_list))
        {
          // an array of ids which will either be a single id or an array of ties
          //
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
      //
      $candidate_side_keys = array_keys($candidate_list);
      if(null === $deployed_list)
      {
        $scan_chain['host_id'] = null;
        if(in_array($this->preferred_side, $candidate_side_keys))
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
      //
      else
      {
        $deployed_side_keys = array_keys($deployed_list);

        // case 2.1:
        // preferred candidates
        //   - preferred deployed, find max host id or ties
        //   - discard non-preferred candidates
        //
        $alternate_side = 'left' == $this->preferred_side ? 'right' : 'left';
        $chain_side = null;
        if(in_array($this->preferred_side, $candidate_side_keys) &&
           in_array($this->preferred_side, $deployed_side_keys))
        {
          $chain_side = $this->preferred_side;
        }
        // case 2.2:
        // no preferred, only non-preferred candidates
        //  - non-deferred deployed, find max host id or ties
        //
        else if(in_array($alternate_side, $candidate_side_keys) &&
           in_array($alternate_side, $deployed_side_keys))
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

  protected function get_priority_sorting( $deployment_list )
  {
    $priority_chain = array();
    $non_priority_chain = array();
    foreach($deployment_list as $uid => $scan_chain)
    {
      $found = false;
      foreach($scan_chain as $item)
      {
        if(1 == $item->priority)
        {
          $found = true;
          break;
        }
      }
      if($found)
        $priority_chain[$uid] = $scan_chain;
      else
        $non_priority_chain[$uid] = $scan_chain;
    }
    return $priority_chain + $non_priority_chain;
  }

  public function get_deployments( $ordered_host_list )
  {
    // get all uid's that have candidates
    //
    $uid_list = array_keys($this->candidate_scans);
    $deployment_list = array();
    foreach($uid_list as $uid)
    {
      $scan_chain = $this->get_scan_chain($uid);
      if(null === $scan_chain) continue;

      $host_id = $scan_chain['host_id'];
      if(null === $host_id)
      {
        // deployable to any host since there are no deployed siblings
        //
        $deployment_list['any'][] = $scan_chain['scans'];
      }
      else
      {
        // candidates must be deployed to this host
        //
        if(1 == count($host_id))
        {
          $id = current($host_id);
          $deployment_list[$id][] = $scan_chain['scans'];
        }
        else // candidates have siblings on more than one host
        {
          // the id of the first host in the list of hosts ordered by allocation preference
          // that has sibling scans that would complete the chain.
          // If there is no host suitable at this time, then do not deploy the scans
          //
          $list = array_intersect($ordered_host_list,$host_id);
          if(0 < count($list))
          {
            $id = current($list);
            $deployment_list[$id][] = $scan_chain['scans'];
          }
        }
      }
    }

    //DEBUG - report how many candidate and deployed uid per host
    foreach($deployment_list as $key => $scan_chain_list)
    {
      $deployment_list[$key] = $this->get_priority_sorting($scan_chain_list);
      $num = 0;
      $num_priority = 0;
      foreach($scan_chain_list as $list)
      {
        $num += count($list);
        foreach($list as $item)
          $num_priority += $item->priority;
      }
      $this->count_stats['numHostCandidates'][$key] = $num;
      $this->count_stats['numHostPriorityCandidates'][$key] = $num_priority;
    }

    $total = array_sum(array_values($this->count_stats['numHostCandidates']));

    return $deployment_list;
  }

  protected $candidate_scans = array();

  protected $deployed_scans = array();

  protected $query = null;

  protected $db = null;

  protected $db_prefix = null;

  protected $scan_type = null;

  protected $preferred_side = 'none';

  protected $count_stats = array();

  protected $rank_restrictions = array();
}
