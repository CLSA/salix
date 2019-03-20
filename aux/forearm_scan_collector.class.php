<?php

require_once( 'scan_collector.class.php' );

class forearm_scan_collector extends scan_collector
{
  public function __construct($db, $db_prefix) 
  {
    parent::__construct($db, $db_prefix);

    $this->scan_type = 'forearm';
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
      '( '.
      '  select distinct e.id '.
      '  from apex_exam e '.
      '  join apex_scan s on e.id=s.apex_exam_id '.
      '  join scan_type t on t.id=s.scan_type_id '.
      '  where type = "forearm" '.
      '  and availability=1   '.
      '  and (invalid=0 or invalid=1) '.
      ') as t1  '.
      'left join '.
      '( '.
      '  select distinct e.id   '.
      '  from apex_exam e '.
      '  join apex_scan s on e.id=s.apex_exam_id '.
      '  join scan_type t on t.id=s.scan_type_id '.
      '  where type in ("hip","wbody","spine") '.
      '  and availability=1 '.
      '  and (invalid=0 or invalid is null) '.
      ') as t2 on t1.id=t2.id '.
      'join apex_exam e on e.id=t1.id '.
      'JOIN apex_scan s ON s.apex_exam_id=e.id  '.
      'JOIN scan_type t ON s.scan_type_id=t.id  '.
      'JOIN serial_number n ON e.serial_number_id=n.id  '.
      'JOIN apex_baseline b ON e.apex_baseline_id=b.id  '.
      'JOIN %scenozo.participant p ON b.participant_id=p.id  '.
      'LEFT JOIN apex_deployment d ON d.apex_scan_id=s.id  '.
      'where t2.id is null '.
      'and type="%s" '.
      'and availability=1 '.
      'order by uid, rank, side', $this->db_prefix, $this->scan_type);
  }
}
