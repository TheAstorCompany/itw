<?php
class RecyclingChargeItemsModel extends CI_Model {
	private $table = 'RecyclingChargeItems';
	
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
	
	public function addUpdate($recyclingChargeId, $charges) {
		$this->load->helper('dates');
		foreach($charges as $item) {
			$item = (array)$item;
			$item = $this->cleanFields($item);
			$item['recyclingChargeId'] = $recyclingChargeId;
			$item['materialDate'] =  USToSQLDate($item['materialDate']);											
			if (!isset($item['id'])) {
				//add
				$this->db->insert($this->table, $item);
				$notForDelete[] = $this->db->insert_id();
			} else if ($chargeId = (int)$item['id']) {
				//update
				$notForDelete[] = $chargeId;
				$this->db->where(
					array('id' => $chargeId));		
					$this->db->update($this->table, $item);										
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
		$result = array();
		$recyclingChargeId = (int)$recyclingChargeId;
		
		$this->db->select('*', false);
		$query = $this->db->get_where($this->table,
			array(
				'recyclingChargeId' => $recyclingChargeId
			)	
		);
		$this->load->helper('dates');
		foreach($query->result() as $item) {
			if ($item->materialDate) {
				$item->materialDate = SQLToUSDate($item->materialDate);
			}
			$result[] = $item;
		}
		return $result;
	}
	
	public function delete($contactId) {
		$contactId = (int)$contactId;
		
		$this->db->delete($this->table,
			Array(
				'id' => $contactId
			)
		);		
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