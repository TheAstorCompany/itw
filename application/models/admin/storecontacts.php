<?php

class StoreContacts extends CI_Model {
	private $table = 'StoreContacts';

	public function __construct() {
		parent::__construct();
		$this->load->database();
	}

	public function __destruct() {
		//		echo $this->db->last_query();
	}	
	
	public function add($distributionCenterId, $data) {
		$distributionCenterId = (int)$distributionCenterId;
		
		$data = $this->cleanFields($data);
		$data['storeId'] = (int)$distributionCenterId;
				
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
				'storeId' => $distributionCenterId
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