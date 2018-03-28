<?php

class DistributionCenterServices extends CI_Model 
{
	private $table = 'DistributionCenterServices';

	public function __construct() {
		parent::__construct();
		$this->load->database();
	}

	public function __destruct() {
		//		echo $this->db->last_query();
	}
	
	public function getById($id) {
		$id = (int)$id;
		
		$query = $this->db->get($this->table,
			1
		);
		
		return $query->row();
	}
	
	public function add($distributionCenterId, $data) {
		$distributionCenterId = (int)$distributionCenterId;
        
        $this->load->helper('dates');
        
		if (isset($data['days']) && is_array($data['days'])) {
			$data['days'] = array_sum($data['days']);
		} else {
			$data['days'] = 0;
		}
        
        
        if(isset($data['endDate']) && !empty($data['endDate'])){
            $data['endDate'] = USToSQLDate($data['endDate']);
        }else{
            unset($data['endDate']);
        }
        
        if(isset($data['startDate']) && !empty($data['startDate'])){
            $data['startDate'] = USToSQLDate($data['startDate']);
        }else{
            unset($data['startDate']);
        }
        
        
		$data = $this->cleanFields($data);
		$data['distributionCenterId'] = (int)$distributionCenterId;
		
        $this->db->insert($this->table, $data);
		
		return $this->db->insert_id();	
	}	
	
	public function delete($contactId) {
		$contactId = (int)$contactId;
		
		$this->db->delete($this->table,
			Array(
				'id' => $contactId
			)
		);		
	}

	public function getList($distributionCenterId) {
		$distributionCenterId = (int)$distributionCenterId;
		
		$this->db->select('*', false);
		$query = $this->db->get_where($this->table,
			array(
				'distributionCenterId' => $distributionCenterId
			)	
		);
		
		return $query->result();
	}

	private function cleanFields($data) {
		$tableFields = $this->db->list_fields($this->table);
		$result = array();

		foreach ($tableFields as $field) {
			if (array_key_exists($field, $data)) {
				$result[$field] = $data[$field];
			}
		}

		return $result;
	}
	
}