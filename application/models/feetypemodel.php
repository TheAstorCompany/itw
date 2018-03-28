<?php
class FeeTypeModel extends CI_Model {
    private $table = 'FeeType';
	
	public function __construct() {
		parent::__construct();
		$this->load->database();	
	}
	
	public function get_all() {
	    $this->db->select('*');
	    $this->db->from($this->table);
	    $query = $this->db->get();
	    $results = array();
	    foreach ( $query->result('array') as $row ) {
	        $results[$row['id']] = $row['name'];
	    }
	    return $results;
	}
	
	public function getListForSelect() {
		$query = $this->db->get_where($this->table,
			array(
				'active' => 1
			)
		);
		
		$data = $query->result();
		
		$result = array();
		$result[0] = "- Please select -";
		
		foreach ($data as $item) {
			$result[$item->id] = $item->name;
		}
		
		return $result;
	}
}
?>