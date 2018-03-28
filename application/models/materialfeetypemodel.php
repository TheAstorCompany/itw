<?php
class MaterialFeeTypeModel extends CI_Model {
    private $table = 'MaterialFeeType';
	
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
}
