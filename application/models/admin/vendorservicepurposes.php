<?php

class VendorServicePurposes extends CI_Model {
	private $table = 'VendorServicePurposes';

	public function __construct() {
		parent::__construct();
		$this->load->database();
	}

	public function __destruct() {
		//echo $this->db->last_query();
	}	
	public function getListForSelect($companyId) {
		$companyId = (int)$companyId;
		
		$query = $this->db->get_where($this->table,
			array(
				'companyId' => $companyId
			)
		);
		
		$data = $query->result();
		
		$result = array();
		
		foreach ($data as $item) {
			$result[$item->id] = $item->name;
		}
		
		return $result;
	}
	/*
	 * 
	 */
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