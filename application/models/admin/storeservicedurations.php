<?php
class StoreServiceDurations extends CI_Model {
	private $table = 'StoreServiceDurations';

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
		
		foreach ($data as $item) {
			$result[$item->id] = $item->name;
		}
		
		return $result;
	}
}