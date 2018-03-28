<?php

class Companies extends CI_Model {
	private $table = 'Companies';
	
	public function __construct() {
		parent::__construct();
		
		$this->load->database();
	}
	public function getList() {
		$query = $this->db->get($this->table);

		return $query->result();
	}
	
	public function getById($companyId) {
		$companyId = (int)$companyId;
		
		$query = $this->db->get_where($this->table,
			array(
				'id' => $companyId
			) 
		);
		
		return $query->row();
	}
	
	public function getCurrentMigrationVersion() {
		$q = $this->db->query("
			SELECT version FROM migrations LIMIT 1
		");
		
		return (int)$q->row()->version;
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