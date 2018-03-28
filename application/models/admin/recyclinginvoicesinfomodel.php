<?php
class RecyclingInvoicesInfoModel extends CI_Model {
	private $table = 'RecyclingInvoicesInfo';
	
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
	
	public function delete($id) {
		$id = (int)$id;
		
		$this->db->delete($this->table,
			array(
				'id' => $id
			)
		);
		
		return $this->db->affected_rows();
	}
	
	public function add($invoiceId, $data) {
		$invoiceId = (int)$invoiceId;
		
		$data = $this->cleanFields($data);
		$data['invoiceId'] = $invoiceId;
				
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
	
	public function getList($invoiceId) {
		$invoiceId = (int)$invoiceId;
		
		$this->db->select('*', false);
		$query = $this->db->get_where($this->table,
			array(
				'invoiceId' => $invoiceId
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