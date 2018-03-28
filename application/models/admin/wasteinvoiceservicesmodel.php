<?php

class WasteInvoiceServicesModel extends CI_Model {
	private $table = 'WasteInvoiceServices';

	public function __construct() {
		parent::__construct();
		$this->load->database();
	}

	public function __destruct() {
		//		echo $this->db->last_query();
	}
	
	public function getByInvoiceId($id) {
		$id = (int)$id;
		
		$query = $this->db->get_where($this->table,
			array(
				'invoiceId' => $id	
			)
		);
		
		return $query->result();
	}
	
	public function getByInvoiceIdArray($invoice_ids) {
	    if ( is_array($invoice_ids) ) {
	        $this->db->select(array('WasteInvoiceServices.*', 'Materials.name as material_name'));
    	    $this->db->from($this->table);
    	    if ( is_array( $invoice_ids ) && count($invoice_ids) > 0 ) {
    	        $this->db->where_in('invoiceId', $invoice_ids);
    	    }
    	    $this->db->join('Materials', 'Materials.id=WasteInvoiceServices.materialId');
    	    $query = $this->db->get();
    	    $results = array();
    	    foreach ( $query->result('array') as $row ) {
    	        $results[$row['invoiceId']][] = $row;
    	    }
    	    return $results;
	    }
	    return false;
	}
	
	public function getById($id) {
		$id = (int)$id;
		
		$query = $this->db->get($this->table,
			1
		);
		
		return $query->row();
	}
	
	public function add($invoiceId, $data) {
		$invoiceId = (int)$invoiceId;
		
		$data = $this->cleanFields($data);
		$data['invoiceId'] = (int)$invoiceId;
		
		if (!isset($data['durationId'])) {
			$data['durationId'] = NULL;
		}
		
		$this->db->insert($this->table, $data);
		
		return $this->db->insert_id();	
	}	
	
	public function delete($id) {
		$id = (int)$id;
		
		$this->db->delete($this->table,
			Array(
				'id' => $id
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
	
	public function updateMulti($invoiceId, $data) {
		$exists = array(0);
		$forInsert = array();

		foreach ($data as $k=>$item) {
			$item = $this->cleanFields((array)$item);

			if (isset($item['id']) && array_key_exists('CBRENumber', $item)) {
				$exists[] = $item['id'];
			} else {
				unset($item['id']);
				$forInsert[] = $item;
			}
		}
		
		
		if (!empty($exists)) {
			$this->db->query("
				DELETE FROM {$this->table} WHERE
					invoiceId = $invoiceId AND id NOT in (".implode(',', $exists).")
			");
		}
		
		foreach ($forInsert as $item) {
			$temp = (array)$item;
			unset($temp['id']);
			
			$this->add($invoiceId, $temp);
		}
	}
	
	public function addMulti($invoiceId, $data) {
		foreach ($data as $k=>$item) {
			$temp = (array)$item;
			unset($temp['id']);
			
			$this->add($invoiceId, $temp);
		}
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