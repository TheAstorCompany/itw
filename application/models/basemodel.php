<?php

class MonthData {
	public $waste = 0;
	public $recycling = 0;
	public $diversion = 0;
	public $cost = 0;
	public $rebate = 0;
	public $costPerSqft = 0;
}

class CostsChart {
	public $dc = '0';
	public $West = '0';
	public $Southeast = '0';
	public $MidWest = '0';
	public $East = '0';
	
	public function hasData() {
		if (is_array($this->dc)) {
			return  !!count($this->dc);	
		}
		
		return ($this->dc + $this->West + $this->Southeast + $this->MidWest + $this->East) > 0 ? true:false;
	}
}

class WasteChart {
	public $dc = '0';
	public $West = '0';
	public $Southeast = '0';
	public $MidWest = '0';
	public $East = '0';
	
	public function hasData() {
		if (is_array($this->dc)) {
			return  !!count($this->dc);	
		}
		return ($this->dc + $this->West + $this->Southeast + $this->MidWest + $this->East) > 0 ? true:false;
	}	
}

class WasteTrends {
	public $dc = array();
	public $West = array();
	public $Southeast = array();
	public $MidWest = array();
	public $East = array();
	public $dates = array();
}

class RecyclingTrends {
	public $dc = array();
	public $West = array();
	public $Southeast = array();
	public $MidWest = array();
	public $East = array();
	public $dates = array();
}

class BaseModel extends CI_Model {

	function getFeeTypeOptions() {
		$feeTypeOptions = array(0 => '- Please select -');

		$this->db->select('*', false);
		$this->db->where(array('active' => 1));
		$this->db->order_by('name');
		$query = $this->db->get('FeeType');
		$rows = $query->result();
		
		foreach($rows as $row) {
			$feeTypeOptions[$row->id] = $row->name;
		}
		
		return $feeTypeOptions;
	}
	
	function getScheduleOptions() {
		return array(
			0 => '- please select -',
			1 => 'On Call',		 
				 'Monthly',	
				 '2x/Month',
				 'Biweekly',	
				 'Weekly',	
				 '6x/week',	
				 '5x/week',	
				 '4x/week',	
				 '3x/week',	
				 '2x/week',
				 'Daily',	
				 'EOW');
	}
        
    function addEvent($objet_type, $objet_id, $action, $userId=null) {
        if($userId==null) {
            $user = $this->session->userdata('USER');
            $userId = $user->id;
        }

        $data = array();

        $data['user_id'] = $userId;
        $data['object_type'] = $objet_type;
        $data['object_id'] = $objet_id;
        $data['action'] = $action;
        $data['dt'] = date('Y-m-d H:i:s');

        $this->db->insert('Audit', $data);
    }
}
