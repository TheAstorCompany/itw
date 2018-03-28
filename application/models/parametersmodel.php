<?php
include_once 'basemodel.php';

class ParametersModel extends BaseModel {
	
	public function __construct() {
		parent::__construct();
		$this->load->database();
		$this->load->helper('dates');
	}

    function getByName($name) {
        $info = $this->db->query('SELECT * FROM `parameter` WHERE `name` = "'.$name.'"');
        $r = $info->result();
		if(isset($r[0])) {
			return $r[0]->value;
		}
		
		return null;
    }
	
	function setByName($name, $value) {
		$this->db->update('parameter', array('value' => $value), array('name' => $name));
	}
}


