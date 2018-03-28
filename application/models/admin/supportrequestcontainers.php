<?php
class SupportRequestContainers extends CI_Model {
	private $table = 'SupportRequestContainers';
	
	public function __construct() {
		parent::__construct();
		$this->load->database();	
	}
	
	public function getListForSelect($companyId) {
		$companyId = (int)$companyId;
		
		$query = $this->db->get_where($this->table,
			array(
				'companyId' => $companyId
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