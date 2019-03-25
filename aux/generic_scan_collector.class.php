<?php

require_once( 'scan_collector.class.php' );

class generic_scan_collector extends scan_collector
{
  public function __construct($db, $db_prefix, $type = '')
  {
    parent::__construct($db, $db_prefix);

    $this->scan_type =
      in_array($type, array('hip','spine','lateral','wbody')) ? $type : null;

    if('hip'==$type)
      $this->preferred_side = 'left';
  }

  protected function build_collection_query()
  {
    $this->query = sprintf(
      'select distinct '.
      'uid, type, side,  '.
      'rank, barcode, s.id as apex_scan_id,  '.
      'priority, n.id as serial_number, '.
      'IFNULL(d.apex_host_id, "NULL") AS apex_host_id, '.
      'availability, invalid, scan_type_id '.
      'from '.
      'apex_scan s '.
      'JOIN apex_exam e ON s.apex_exam_id=e.id  '.
      'JOIN scan_type t ON s.scan_type_id=t.id  '.
      'JOIN serial_number n ON e.serial_number_id=n.id  '.
      'JOIN apex_baseline b ON e.apex_baseline_id=b.id  '.
      'JOIN %scenozo.participant p ON b.participant_id=p.id  '.
      'LEFT JOIN apex_deployment d ON d.apex_scan_id=s.id  '.
      'where type="%s" '.
      'and availability=1 '.
      'and (invalid is null or invalid=0) '.
      'order by uid, rank', $this->db_prefix, $this->scan_type);
  }
}
