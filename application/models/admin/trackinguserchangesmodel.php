<?php
include_once dirname(__FILE__).'/../basemodel.php';

class TrackingUserChangesModel extends BaseModel {
	
    public function __construct() {
        parent::__construct();
        $this->load->database();
    }
    
    public function getReportData($startDate, $endDate) {
        $this->db->select('Audit.*, CompanyUsers.username', false);
        $this->db->join('CompanyUsers', 'Audit.user_id = CompanyUsers.id');
        $this->db->order_by('Audit.dt', 'ASC');
        $query = $this->db->get_where('Audit',
            array(
                'DATE(`dt`) >=' => $startDate,
                'DATE(`dt`) <=' => $endDate
            )	
        );
      
        $rows = array();
        $unique_change = array();
        $changed_objects = array();
        $r = $query->result();
        foreach($r as $row) {
            if(!isset($rows[$row->user_id])) {
                $rows[$row->user_id] = array(
                    'username' => $row->username
                );
            }
            
            if(!isset($rows[$row->user_id][$row->object_type])) {
                $unique_change[$row->user_id][$row->object_type] = array();
                $rows[$row->user_id][$row->object_type] = array('add'=>0, 'change'=>0, 'unique_change'=>0, 'delete'=>0, 'close'=>0, 'errors'=>0);
            }
            $rows[$row->user_id][$row->object_type][$row->action] += 1;
            $chobj_key = $row->object_type.'**'.$row->object_id;
            if($row->action=='add') {
                $changed_objects[$chobj_key] = array('user_id'=>$row->user_id, 'is_changed'=>false);
            }
            if($row->action=='change') {
                if(!in_array($row->object_id, $unique_change[$row->user_id][$row->object_type])) {
                    $unique_change[$row->user_id][$row->object_type][] = $row->object_id;
                    $rows[$row->user_id][$row->object_type]['unique_change'] += 1;
                }
                if(isset($changed_objects[$chobj_key])) {
                    $changed_objects[$chobj_key]['is_changed'] = true;
                }
            }
        }

        foreach($changed_objects as $chobj_key=>$chobj) {
            if($chobj['is_changed']) {
                $user_id = $chobj['user_id'];
                list($object_type, $object_id) = explode('**', $chobj_key);
                if(isset($rows[$user_id][$object_type]['errors'])) {
                    $rows[$user_id][$object_type]['errors'] += 1;
                }
            }
        }

        return $rows;
    }
}
