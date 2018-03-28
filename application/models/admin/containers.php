<?php
class Containers extends CI_Model {
	private $table = 'Containers';
	
	public function __construct() {
		parent::__construct();
		$this->load->database();	
	}
	
	public function getListForSelect($companyId) {
		$companyId = (int)$companyId;
		
		$query = $this->db->get_where($this->table,
			array(
				'companyId' => $companyId,
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

    public function getAll($companyId) {
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


    public function getOver8YardsList() {
        $query = $this->db->get_where($this->table,
            array(
                'companyId >' => 0
            )
        );

        $data = $query->result();

        $result = array();

        foreach ($data as $item) {
            $result[$item->id] = $item->over8Yards;
        }

        return $result;
    }
}