<?php

class States extends CI_Model {
	private $table = 'States';
	
	public function __construct() {
		parent::__construct();
		
		$this->load->database();
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

    public function getList() {

        $this->db->order_by("name", "asc");
        $query = $this->db->get($this->table);

        $data = $query->result();

        $result = array();

        foreach ($data as $item) {
            $result[$item->id] = $item;
        }

        return $result;
    }
















}