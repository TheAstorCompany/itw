<?php //if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Import extends CI_Controller {

	/**
	 * Index Page for this controller.
	 *
	 * Maps to the following URL
	 * 		http://example.com/index.php/welcome
	 *	- or -  
	 * 		http://example.com/index.php/welcome/index
	 *	- or -
	 * Since this controller is set as the default controller in 
	 * config/routes.php, it's displayed at http://example.com/
	 *
	 * So any other public methods not prefixed with an underscore will
	 * map to /index.php/welcome/<method_name>
	 * @see http://codeigniter.com/user_guide/general/urls.html
	 */
	public function index()
	{		

		$importDir = "c:\\";

		$this->load->database();
		$states = array();
		$districts = array();
		$wasteRecycle = array();
		$containers = array();
		foreach($this->db->get('District')->Result() as $row) {
			if (!isset($districts[$row->name])) {
				$districts[$row->name] = $row->number;
			} 
		}
		foreach($this->db->get('States')->Result() as $row) {
			if (!isset($states[$row->code])) {
				$states[trim($row->code)] = $row;
			} 
		}
		
	
		//vendors			
		if ($fh = fopen($importDir. "vendors.csv", "r"))
		{
			$lines = 0;			
			while (($data = fgetcsv($fh, 1000, ',')) !== FALSE) {				
				
				if ($lines++ > 0) {					

				$sqlQuery = sprintf("INSERT IGNORE INTO Vendors (name, number, remitTo, addressLine1, addressLine2, city, stateId, 
					zip, phone,	fax, website, email, status, notes, lastUpdated, companyId)
	        		VALUES ('%s', '%s', '%s', '%s', '%s', '%s', %s,
	        		'%s', '%s', '%s', '%s', '%s', '%s', '%s', now(), 1)",
	        		addslashes($data[1]), addslashes($data[2]), addslashes($data[3]),
	        		addslashes($data[4]), addslashes($data[5]), addslashes($data[7]), ($states[$data[7]]?$states[$data[7]]->id:'null'),           		
	        		addslashes($data[8]), addslashes($data[9]), addslashes($data[10]), addslashes($data[11]), addslashes($data[12]), addslashes($data[13]), addslashes($data[13]));	        		
	        		
					
					$this->db->query($sqlQuery);										
				} else {
					// print_r($data);
				}			
			}
			fclose($fh);
		
		}
		
		
		
		//get containers		
		foreach($this->db->get('Containers')->Result() as $row) {
			if (!isset($containers[trim($row->name)])) {
				$containers[trim($row->name)] = $row->id;
			} 
		}

		$materials = array();
		foreach($this->db->get('Materials')->Result() as $row) {
			if (!isset($materials[trim($row->name)])) {
				$materials[trim($row->name)] = $row->id;
			} 
		}
		
		
		$shedules = Array(
		'On Call'=>1,
		'ONCALL' =>1, 
'Monthly'=>2,	
'Biweekly'=>3,	
'Weekly'=>4,	
'6x/week'=>5,	
'5x/week'=>6,	
'4x/week'=>7,	
'3x/Week'=>8,	
'2x/Week'=>9,
'Daily'=>10,	
'EOW'=>11,
'2x/Month'=>12);

		$wasteRecycle = Array(
		'Waste'=>0,
		'Recycle' =>1);
		
		$vendors = array();	
		foreach($this->db->get('Vendors')->Result() as $row) {
			if (!isset($vendors[trim($row->number)])) {
				$vendors[trim($row->number)] = $row->id;
			} 
		}
		
	
		//id [1] => StoreId [2] => Duration [3] => Purpose [4] => wasteRecycle [5] => material [6] => quantity (if no qty set to 1) [7] => container [8] => schedule [9] => days (if no AMo/PM set to AM) [10] => rate [11] => startDate [12] => endDate (if no date, set to 1 year after start date) [13] => Astor Key [14] => vendorID ) 
		//open file services			
		if ($fh = fopen($importDir. 'store_services.csv',"r")) {
			$lines = 0;			
			while (($data = fgetcsv($fh, 1000, ",")) !== FALSE) {				
				if ($lines++ > 0) {					

					$sqlQuery = sprintf("INSERT IGNORE INTO VendorServices (durationId, locationId, purposeId, category, quantity, 
					materialId, containerId, schedule, days, rate, startDate, endDate, vendorId, locationType
					) VALUES (1, %d, 1, '%s', %.2f, 
					%s, %s, %d, %d,	%.2f, '%s', '%s', %s, 'STORE')",
					(int)$data[0],
					@$wasteRecycle[$data[3]], 
					(int)$data[5],
	        		$materials[$data[4]]?$materials[$data[4]]:'null',
					isset($containers[$data[6]])?$containers[$data[6]]:'null',
	        		@$shedules[$data[7]], 
					$this->getDays($data[8]),
					str_replace(",",".",$data[9]), 
					$data[10], 
					date('Y-m-d', strtotime($data[11] . ' + 1 Year')), 
	        		isset($vendors[$data[13]])?$vendors[$data[13]]:'null');	        		
	        			
					$this->db->query($sqlQuery);	
										
				} else {					
					//print_r($data);
					//die();
				}			
			}
			fclose($fh);
		}
		
		print_r("Done.");
	}
	private function getDays($str) {
		$result = 0;
		$str = trim(strtoupper(str_replace(" ", "", $str)));
		$arrDays = array(			
        	'MO' => 2,
        	'TU' => 4,
        	'WE' => 8,
        	'TH' => 16,
        	'FR' => 32,
        	'SAT' => 64,
			'SA' => 64,
        	'SU' => 128,
            
        	'MOAM' => 2,
        	'TUAM' => 4,
        	'WEAM' => 8,
        	'THAM' => 16,
        	'FRAM' => 32,
        	'SATAM' => 64,
        	'SUAM' => 128,
		
			'MOPM' => 256,
        	'TUPM' => 512, 
        	'WEPM' => 1024,
        	'THPM' => 2048,
        	'FRPM' => 4096,
        	'SATPM' => 8192,
        	'SUPM' => 16384,
			'ONCALL' =>0,
			'DAILY' =>0
        );
		$daysMap = array("");
		$days = explode("/", $str);
		if (count($days) == 0) {
			$result = $arrDays[$str];
		} else {
			foreach($days as $day) {
				if (trim($day)) {
					$result += $arrDays[trim($day)];
				}
			}
		}
		return $result;
		echo $str; die();
	}
}
