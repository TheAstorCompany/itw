<?php
	function USToSQLDate($date) {
		//US date format mm/dd/YYYY
		//SQL format YYYY-mm-dd
		if (!empty($date)) {
			$temp = explode('/', $date);
			
			if (count($temp) != 3) {
				return $date;
			}
			
			$date = $temp[2] .'-'. $temp[0] . '-' . $temp[1];
		}
		
		return $date;
	}
	
	function SQLToUSDate($date) {
		//US date format mm/dd/YYYY
		//SQL format YYYY-mm-dd
		if (!empty($date)) {
			$temp = explode('-', $date);
			
			if (count($temp) != 3) {
				return $date;
			}
			
			$isZero = true;
			
			foreach ($temp as $num) {
				if ($num != 0) {
					$isZero = false;
					break;
				}	
			}
			
			if ($isZero) {
				return '';
			}
			
			$date = $temp[1] .'/'. $temp[2] . '/' . $temp[0];
		}
		
		return $date;
	}
	function getPeriods($periodType) {
		$result = Array();
		switch($periodType) {
			case 'CurrentMonth':
				$result['startMonth'] = date('m');
				$result['startYear'] = date('Y');
				$result['endMonth'] = $result['startMonth']; 
				$result['endYear'] = $result['startYear'];
				break;
			case 'PriorMonth':				
				$newdate = mktime(0, 0, 0, date("m")-1, 15, date("Y"));
				$result['startMonth'] = date('m', $newdate);
				$result['startYear'] = date('Y', $newdate);
				$result['endMonth'] = $result['startMonth']; 
				$result['endYear'] = $result['startYear'];				
				break;
			case 'PriorQuarter':				
				$newdate = mktime(0, 0, 0, date("m")-3, 15, date("Y"));				
				$result['startMonth'] = date('m', $newdate);
				$result['startYear'] = date('Y', $newdate);
				$result['endMonth'] = date('m'); 
				$result['endYear'] = date('Y');
				break;
			case 'SixMonths':
				$newdate = mktime(0, 0, 0, date("m")-6, 15, date("Y"));				
				$result['startMonth'] = date('m', $newdate);
				$result['startYear'] = date('Y', $newdate);
				$result['endMonth'] = date('m'); 
				$result['endYear'] = date('Y');				
				break;
			case 'LastYear':
				$newdate = mktime(0, 0, 0, date("m"), 15, date("Y")-1);
				$result['startMonth'] = 1;
				$result['startYear'] = date('Y', $newdate);
				$result['endMonth'] = 12; 
				$result['endYear'] = date('Y');				
				break;
			case '2MonthsBack':
				$newdate = mktime(0, 0, 0, date("m")-2, 15, date("Y"));
				$result['startMonth'] = date('m', $newdate);
				$result['startYear'] = date('Y', $newdate);
				$result['endMonth'] = $result['startMonth']; 
				$result['endYear'] = $result['startYear'];				
				break;				
                        case '3MonthsBack':
                                $newdate = mktime(0, 0, 0, date("m")-3, 15, date("Y"));
                                $result['startMonth'] = date('m', $newdate);
                                $result['startYear'] = date('Y', $newdate);
                                $result['endMonth'] = $result['startMonth'];
                                $result['endYear'] = $result['startYear'];
                                break;

			default:
				throw new Exception('getPeriods: Invalid period'  . $periodType);
		}
		
		return $result;
	}
?>
