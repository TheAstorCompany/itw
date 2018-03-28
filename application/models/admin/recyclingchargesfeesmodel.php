<?php
class RecyclingChargesFeesModel extends CI_Model {
	private $table = 'RecyclingChargesFees';
	
	public function __construct() {
		parent::__construct();
		$this->load->database();	
	}
	
	public function getById($id) {
		$id = (int)$id;
		
		$query = $this->db->get_where($this->table,
			array(
				'id' => $id
			),
			1
		);
		
		return $query->row(); 
	}
	public function addUpdate($recyclingChargeId, $fees) {
		foreach($fees as $fee) {
			$fee = (array)$fee;
			$fee = $this->cleanFields($fee);
			$fee['recyclingChargeId'] = $recyclingChargeId;											
			if (!isset($fee['id'])) {
				//add
				$this->db->insert($this->table, $fee);
				$notForDelete[] = $this->db->insert_id();
			} else if ($feeId = (int)$fee['id']) {
				//update
				$notForDelete[] = $feeId;
				$this->db->where(
					array('id' => $feeId));		
					$this->db->update($this->table, $fee);										
			}						
		}
		if (count($notForDelete) > 0) {
			//remove old					
			$this->db->query("DELETE FROM {$this->table} WHERE id NOT IN (" . implode(',', $notForDelete) . ") AND recyclingChargeId = " . $recyclingChargeId);
		} else {
			//remove all
			$this->db->query("DELETE FROM {$this->table} WHERE recyclingChargeId = " . $recyclingChargeId);
		}
	}
	public function add($recyclingChargeId, $data) {
		$recyclingChargeId = (int)$recyclingChargeId;
		
		$data = $this->cleanFields($data);
		$data['recyclingChargeId'] = $recyclingChargeId;
				
		$this->db->insert($this->table, $data);
		
		return $this->db->insert_id();	
	}
	
	public function update($id, $data) {
		$id = (int)$id;
		
		$data = $this->cleanFields($data);
		
		$this->db->update($this->table,
			$data,
			array(
				'id' => $id,
			)
		);
	}
	
	public function getList($recyclingChargeId) {
		$recyclingChargeId = (int)$recyclingChargeId;
		
		$this->db->select('*', false);
		$query = $this->db->get_where($this->table,
			array(
				'recyclingChargeId' => $recyclingChargeId
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