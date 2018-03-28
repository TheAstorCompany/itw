<?php

class MaterialsModel extends CI_Model {
	private $table = 'Materials';
	
	public function __construct() {
		parent::__construct();
		
		$this->load->database();
	}
	public function getList($companyId, $categoryId = 1) {
		$companyId = (int)$companyId;
		
		$query = $this->db->get_where($this->table,
			array(
				'companyId' => $companyId,
				'active' => 1
				/*
				 'categoryId' => $categoryId
				*/
			)
		);
		
		$data = $query->result();
		
		$result = array();
		$result[0] = '- Please select -';
		
		foreach ($data as $item) {
			$result[$item->id] = $item->name;
		}
		
		return $result;
	}
	
	public function get_id_by_name($company_id, $material_name) {
	    $data = $this->get_by_name($company_id, $material_name);
	    if ( $data ) {
	        return $data[0]['id'];
	    }
	}
	
	public function get_by_name($company_id, $material_name) {
	    $query = $this->db->get_where($this->table,
	        array(
	            'companyId' => $company_id,
	            'name' => $material_name
	        )
	    );
	    return $query->result('array');
	}
	
	public function getListForSelect() {
	    $this->db->order_by("name", "asc"); 
	    $query = $this->db->get($this->table);

	    $data = $query->result();

	    $result = array();

	    foreach ($data as $item) {
		    $result[$item->id] = $item->name;
	    }

	    return $result;
	}
}