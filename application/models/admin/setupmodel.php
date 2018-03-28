<?php

class SetupModel extends CI_Model {

	public function __construct() {
		parent::__construct();
		$this->load->database();
	}

	public function __destruct() {
		//echo $this->db->last_query();
	}	
		
	public function getContainerTypeOptions() {
		$query = $this->db->query('SHOW COLUMNS FROM Containers LIKE "containerType"');
		$row = $query->row();
		
		preg_match('/^enum\((.+)\)$/', $row->Type, $matches, PREG_OFFSET_CAPTURE);
		$items = explode(',', $matches[1][0]);
		$items_r = array();
		foreach($items as $k=>$item) {
			$item = substr($item, 1);
			$item = substr($item, 0, (strlen($item) - 1));
			$items_r[$item] = $item;
		}

		return $items_r;
	}

	public function getContainerList() {
		$this->db->select('*', false);
		$this->db->where(array('active' => 1));
		$this->db->order_by('name');
		$query = $this->db->get('Containers');
		return $query->result();
	}

	public function getMaterialList() {
		$this->db->select('*', false);
		$this->db->where(array('active' => 1));
		$this->db->order_by('name');
		$query = $this->db->get('Materials');
		return $query->result();
	}

	public function getPurposeList() {
		$this->db->select('*', false);
		$this->db->where(array('active' => 1));
		$this->db->order_by('name');
		$query = $this->db->get('SupportRequestServiceTypes');
		return $query->result();
	}

	public function getFeetypeList() {
		$this->db->select('*', false);
		$this->db->where(array('active' => 1));
		$this->db->order_by('name');
		$query = $this->db->get('FeeType');
		return $query->result();
	}
	
	public function getMarketRatesList($iDisplayStart, $iDisplayLength, $startDate, $distributionCenterId) {
		//$iDisplayStart, $iDisplayLength
		$this->db->select('SQL_CALC_FOUND_ROWS *', false);
		if(!empty($startDate)) {
		    $this->db->where(array('startDate >=' => $startDate));
		}
		if(!empty($distributionCenterId)) {
		    $this->db->where(array('distributionCenterId =' => $distributionCenterId));
		}
		$this->db->limit($iDisplayLength, $iDisplayStart);
		$query = $this->db->get('MarketRate');

		$result = $query->result();

		$this->db->select('FOUND_ROWS() as i');
		$query = $this->db->get();

		$row = $query->row();

		return array(
		    'records' => $row->i,
		    'data' => $result
		);
	}
	
	public function addContainer($data) {
		$data = $this->cleanFields('Containers', $data);
		$data['companyId'] = 1;
		
		$this->db->insert('Containers', $data); 
	}
	
	public function addMaterial($data) {
		$data = $this->cleanFields('Materials', $data);
		$data['companyId'] = 1;
		
		$this->db->insert('Materials', $data); 
	}
	
	public function addPurpose($data) {
		$data = $this->cleanFields('SupportRequestServiceTypes', $data);
		$data['companyId'] = 1;
		
		$this->db->insert('SupportRequestServiceTypes', $data); 
	}
		
	public function addFeetype($data) {
		$data = $this->cleanFields('FeeType', $data);
		
		$this->db->insert('FeeType', $data); 
	}
	
	public function addMarketRates($data) {
		$data = $this->cleanFields('MarketRate', $data);
		
		$this->db->insert('MarketRate', $data); 
	}
	
	public function updateContainer($data) {
		$containerId = intval($data['containerId']);
		$data = $this->cleanFields('Containers', $data);
		$data['companyId'] = 1;
		
		$this->db->update('Containers', $data, array('id'=>$containerId)); 
	}
	
	public function updateMaterial($data) {
		$materialId = intval($data['materialId']);
		$data = $this->cleanFields('Materials', $data);
		$data['companyId'] = 1;
		
		$this->db->update('Materials', $data, array('id'=>$materialId)); 
	}
	
	public function updatePurpose($data) {
		$purposeId = intval($data['purposeId']);
		$data = $this->cleanFields('SupportRequestServiceTypes', $data);
		$data['companyId'] = 1;
		
		$this->db->update('SupportRequestServiceTypes', $data, array('id'=>$purposeId)); 
	}
	
	public function updateFeetype($data) {
		$feetypeId = intval($data['feetypeId']);
		$data = $this->cleanFields('FeeType', $data);
		
		$this->db->update('FeeType', $data, array('id'=>$feetypeId)); 
	}	
	
	public function updateMarketRates($data) {
		$marketratesId = intval($data['marketratesId']);
		$data = $this->cleanFields('MarketRate', $data);
		
		$this->db->update('MarketRate', $data, array('id'=>$marketratesId)); 
	}	
	
	public function deleteContainer($containerId) {
		$this->db->update('Containers', array('active' => 0), array('id'=>$containerId)); 
	}
	
	public function deleteMaterial($materialId) {
		$this->db->update('Materials', array('active' => 0), array('id'=>$materialId)); 
	}
	
	public function deletePurpose($purposeId) {
		$this->db->update('SupportRequestServiceTypes', array('active' => 0), array('id'=>$purposeId)); 
	}
	public function deleteFeetype($feetypeId) {
		$this->db->update('FeeType', array('active' => 0), array('id'=>$feetypeId)); 
	}	
	public function deleteMarketRates($marketratesId) {
		$this->db->delete('MarketRate', array('id' => $marketratesId)); 
	}
	
	private function cleanFields($table, $data) {
		$tableFields = $this->db->list_fields($table);
		$result = array();

		foreach ($tableFields as $field) {
			if (array_key_exists($field, $data)) {
				$result[$field] = $data[$field];
			}
		}

		return $result;
	}	
}