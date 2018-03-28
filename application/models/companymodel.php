<?php
include_once 'basemodel.php';

class CompanyModel extends BaseModel {
	
	public function __construct() {
		parent::__construct();
		
		$this->load->database();
	}
	
	public function getCurrentMonthData() {
		$currentYear = date('Y');
		$currentMonth = date('m');
		
		return $this->getDataByInterval($currentMonth, $currentYear, $currentMonth, $currentYear);
	}
	
	public function getPriorMonthData() {
		$newdate = strtotime ( '-1 month');
		$startMonth = date('m', $newdate);
		$startYear = date('Y', $newdate);
		
		return $this->getDataByInterval($startMonth, $startYear, $startMonth, $startYear);
	}
	
	public function getPriorQuarterData() {
		$newdate = strtotime ( '-3 month');
		$startMonth = date('m', $newdate);
		$startYear = date('Y', $newdate);
		
		$endMonth = date('m');
		$endYear = date('Y');
		
		return $this->getDataByInterval($startMonth, $startYear, $endMonth, $endYear, 3);
	}
	
	public function getSixMonthsData() {
		$newdate = strtotime ( '-6 month');
		$startMonth = date('m', $newdate);
		$startYear = date('Y', $newdate);
		
		$endMonth = date('m');
		$endYear = date('Y');
		
		return $this->getDataByInterval($startMonth, $startYear, $endMonth, $endYear, 6);
	}
	
	public function getLastYearData() {
		$newdate = strtotime ( '-1 year');
		$startMonth = 1;
		$startYear = date('Y', $newdate);
		
		$endMonth = 12;
		$endYear = date('Y', $newdate);
		
		return $this->getDataByInterval($startMonth, $startYear, $endMonth, $endYear, 12);
	}
	
	private function getDataByInterval($startMonth, $startYear, $endMonth, $endYear, $average = false) {
		$average = false;
		//current month waste in tons
		$query = $this->db->query("
			SELECT SUM(wis.quantity) as waste, SUM(wis.quantity * wis.rate) as sum FROM WasteInvoices as wi
				LEFT OUTER JOIN WasteInvoiceServices as wis ON wis.invoiceId = wi.id
			WHERE
				(wi.invoiceDate BETWEEN '$startYear-$startMonth-01' AND '$endYear-$endMonth-31')
				AND wis.unitId = 1 
		");
		$waste = $query->row();
		//end of current month waste in tons
		
		//current month recycling in tons
		$query = $this->db->query("
			SELECT SUM(rim.quantity) as recycling, SUM(rim.quantity * rim.pricePerUnit) as sum FROM RecyclingInvoices as ri
				LEFT OUTER JOIN RecyclingInvoicesMaterials as rim ON rim.invoiceId = ri.id
			WHERE
				(ri.invoiceDate BETWEEN '$startYear-$startMonth-01' AND '$endYear-$endMonth-31')
				AND rim.unit = 1 
		");
		$recycling = $query->row();		
		//end of current month recycling in tons		
		
		//get sum of all Stores/DCs sq footage
		$storesSqFt = 0;
		$dcSqFt = 0;
		
		$query = $this->db->query("
			SELECT SUM(s.squareFootage) as sqft FROM Stores as s
				LEFT OUTER JOIN RecyclingInvoices as ri ON ri.locationType = 'STORE' AND ri.locationId = s.id
			WHERE
				(ri.invoiceDate BETWEEN '$startYear-$startMonth-01' AND '$endYear-$endMonth-31')
			GROUP BY s.id 
		");

		$storeFromRecyclingInvoices = $query->row();
		
		$query = $this->db->query("
			SELECT SUM(s.squareFootage) as sqft FROM Stores as s
				LEFT OUTER JOIN WasteInvoices as wi ON wi.locationType = 'STORE' AND wi.locationId = s.id
			WHERE
				(wi.invoiceDate BETWEEN '$startYear-$startMonth-01' AND '$endYear-$endMonth-31')
			GROUP BY s.id 
		");
		$storeFromWasteInvoices = $query->row();
		$storesSqFt = (!empty($storeFromRecyclingInvoices) ? $storeFromRecyclingInvoices->sqft : 0) + (!empty($storeFromWasteInvoices) ? $storeFromWasteInvoices->sqft : 0);
		
		$query = $this->db->query("
			SELECT SUM(dc.squareFootage) as sqft FROM DistributionCenters as dc
				LEFT OUTER JOIN RecyclingInvoices as ri ON ri.locationType = 'DC' AND ri.locationId = dc.id
			WHERE
				(ri.invoiceDate BETWEEN '$startYear-$startMonth-01' AND '$endYear-$endMonth-31')
			GROUP BY dc.id 
		");

		$dcFromRecyclingInvoices = $query->row();
		
		$query = $this->db->query("
			SELECT SUM(dc.squareFootage) as sqft FROM DistributionCenters as dc
				LEFT OUTER JOIN WasteInvoices as wi ON wi.locationType = 'DC' AND wi.locationId = dc.id
			WHERE
				(wi.invoiceDate BETWEEN '$startYear-$startMonth-01' AND '$endYear-$endMonth-31')
			GROUP BY dc.id 
		");
		$dcFromWasteInvoices = $query->row();
		
		$dcSqFt = (!empty($dcFromRecyclingInvoices) ? $dcFromRecyclingInvoices->sqft : 0) + (!empty($dcFromWasteInvoices) ? $dcFromWasteInvoices->sqft : 0);

		//end of get sum of all Stores/DCs sq footage

		//$query = $this->db->get($this->table);
		$result = new MonthData();
		if ($waste->waste > 0) {
			$result->waste = number_format(($average ? (double)$waste->waste / $average : (double)$waste->waste), 3);
		}
		
		if ($recycling->recycling > 0) {
			$result->recycling = number_format(($average ? $recycling->recycling / $average:$recycling->recycling), 3);
		}
		
		$recyclingSum = $recycling->sum;
		if ($recyclingSum > 0) {
			if ($average) {
				$recyclingSum = $recyclingSum / $average;
			}
		}
		
		$wasteSum = $waste->sum;
		if ($wasteSum > 0) {
			if ($average) {
				$wasteSum = $wasteSum / $average;
			}
		}
		
		//$result->cost = number_format($wasteSum + $recyclingSum, 3);
		//cost = feeAmount на рецикираните + боклуци
		//cost = Waste Invoice Services + Waste Invoice fees + 
		//		 Recycling Invoice fees + Recycling charges + 
		//		 Recycling charges fees
		// # Waste Invoice Services + Waste Invoice fees = WasteInvoice.total
		// # Recycling Invoice fees = RecyclingInvoices.totalFees
		$costQuery = "
			SELECT (
			(
				SELECT COALESCE(SUM(wi.total), 0) FROM WasteInvoices as wi
				WHERE
					wi.invoiceDate BETWEEN '$startYear-$startMonth-01' AND '$endYear-$endMonth-31'
			)
			+
			(
				SELECT COALESCE(SUM(ri.totalFees), 0) FROM RecyclingInvoices as ri
				WHERE ri.invoiceDate BETWEEN '$startYear-$startMonth-01' AND '$endYear-$endMonth-31'
					
			)
			+
			(
				SELECT COALESCE(SUM(rci.pricePerTon * rci.quantity), 0) FROM RecyclingCharges as rc
					INNER JOIN RecyclingChargeItems as rci ON rci.recyclingChargeId = rc.id AND rci.unitId = 1
					INNER JOIN WasteInvoices as wi ON wi.id = rc.invoiceNumber
				WHERE
					wi.invoiceDate BETWEEN '$startYear-$startMonth-01' AND '$endYear-$endMonth-31'
			)
			+
			(
				SELECT COALESCE(SUM(rcf.fee), 0) FROM RecyclingCharges as rc
					INNER JOIN RecyclingChargesFees as rcf ON rcf.recyclingChargeId = rc.id AND rcf.waived = 0
					INNER JOIN WasteInvoices as wi ON wi.id = rc.invoiceNumber
				WHERE
					wi.invoiceDate BETWEEN '$startYear-$startMonth-01' AND '$endYear-$endMonth-31'
			)
			) as c
		";

		$query = $this->db->query($costQuery, true);             
		                  
		$result->cost = number_format($query->row()->c, 3);
		
		// Savings = rebates + SAMS + waived fees
		$sqlQuery = "SELECT 
			(SELECT COALESCE(SUM(quantity*pricePerUnit),0) FROM `RecyclingInvoices` ri
			INNER JOIN RecyclingInvoicesMaterials rim ON rim.invoiceId = ri.id
			INNER JOIN Materials m ON m.id = rim.materialId			
			WHERE rim.unit = 1 AND ri.invoiceDate BETWEEN '$startYear-$startMonth-01' AND '$endYear-$endMonth-31'
			)  
			+
			-- SAMS 
			( 
			(SELECT COALESCE(SUM(wis.quantity * wis.rate),0) FROM `WasteInvoices` wi	
			INNER JOIN WasteInvoiceServices wis ON wis.invoiceId = wi.id
			WHERE unitId = 1 AND durationId = 1 AND  wi.invoiceDate BETWEEN '".($startYear - 1)."-$startMonth-01' AND '".($endYear - 1)."-$endMonth-31'		 
			)
			-
			(SELECT COALESCE(SUM(wis.quantity * wis.rate),0) FROM `WasteInvoices` wi	
			INNER JOIN WasteInvoiceServices wis ON wis.invoiceId = wi.id
			WHERE unitId = 1 AND durationId = 1 AND wi.invoiceDate BETWEEN '$startYear-$startMonth-01' AND '$endYear-$endMonth-31'
			)
			)
			-- END OF SAMS
			-- waived fees
			+
			(SELECT COALESCE(SUM(feeAmount),0) FROM `WasteInvoices` wi
				INNER JOIN WasteInvoiceFees wif ON wif.invoiceId = wi.id
				WHERE waived AND wi.invoiceDate BETWEEN '$startYear-$startMonth-01' AND '$endYear-$endMonth-31'			
			)
			+
			(SELECT COALESCE(SUM(feeAmount),0) FROM `RecyclingInvoices` ri
				INNER JOIN RecyclingInvoicesFees rif ON rif.invoiceId = ri.id
				WHERE waived AND ri.invoiceDate BETWEEN '$startYear-$startMonth-01' AND '$endYear-$endMonth-31'			
			)
			AS value			
		";			
		
		$res = $this->db->query($sqlQuery);		
		$result->savings =  $res->row()->value;
		
		
		if (($result->cost > 0) && (($storesSqFt + $dcSqFt) > 0)) {
			$result->costPerSqft = number_format($result->cost / ($storesSqFt + $dcSqFt), 3);
		} 
		//prevent division by zero error
		if ($result->recycling > 0) {
			$result->diversion = number_format(($result->recycling / ($result->recycling + $result->waste)) * 100, 2);
		}	
		
		$result->rebate = number_format($recycling->sum, 3);             

		return $result;		
	}
	
	public function wasteTrendsChart() {
		$months = 6;
		$dates = array();
		$result = new WasteTrends();
		
		for ($i=$months -1;$i >= 0;$i--) {
			$temp = strtotime ( '-'.$i.' month');
			
			$dates[] = array(
				date('Y', $temp) . '-' . date('m', $temp) . '-01',
				date('Y', $temp) . '-' . date('m', $temp) . '-31',
			);
			
			$result->dates[] = date('m', $temp) . '/' . date('Y', $temp); 
		}

		foreach ($dates as $k=>$date) {
			$query = $this->db->query("
				SELECT SUM(wis.quantity) as waste FROM WasteInvoices as wi
					LEFT OUTER JOIN WasteInvoiceServices as wis ON wis.invoiceId = wi.id
				WHERE
					wi.locationType = 'DC'
					AND wi.invoiceDate BETWEEN '{$date[0]}' AND '{$date[1]}'
			");
			
			$waste = $query->row()->waste;
			if ($waste <= 0) {
				$waste = '0';
			}
			
			$result->dc[] = $waste;
			
			$query = $this->db->query("
				SELECT SUM(wis.quantity) as waste, st.region  FROM WasteInvoices
					INNER JOIN Stores as s ON s.id = locationId
					INNER JOIN States as st ON st.id = s.stateId
					LEFT OUTER JOIN WasteInvoiceServices as wis ON wis.invoiceId = WasteInvoices.id AND wis.unitId = 1  
				WHERE
					(invoiceDate BETWEEN '{$date[0]}' AND '{$date[1]}')
				AND locationType = 'STORE'
				GROUP BY st.region
			");
			
			$stores = $query->result();
			
			if (!isset($result->East[$k])) {
				$result->East[$k] = '0';
			}
			
			if (!isset($result->MidWest[$k])) {
				$result->MidWest[$k] = '0';
			}
			
			if (!isset($result->Southeast[$k])) {
				$result->Southeast[$k] = '0';
			}
			
			if (!isset($result->West[$k])) {
				$result->West[$k] = '0';
			}
			
			foreach ($stores as $store) {
				if(!empty($store->waste) && !empty($store->region)) {
					$result->{$store->region}[$k] = $store->waste;
				}
			}
		}
		
		return $result;		
	}
	
	public function recyclingTrendsChart() {
		
		$moths = 6;
		$dates = array();
		$result = new RecyclingTrends();
		
		for ($i=$moths -1;$i >= 0;$i--) {
			$temp = strtotime ( '-'.$i.' month');
			
			$dates[] = array(
				date('Y', $temp) . '-' . date('m', $temp) . '-01',
				date('Y', $temp) . '-' . date('m', $temp) . '-31',
			);
			
			$result->dates[] = date('m', $temp) . '/' . date('Y', $temp); 
		}

		foreach ($dates as $k=>$date) {
			$query = $this->db->query("
				SELECT SUM(rim.quantity) as recycling FROM RecyclingInvoices as ri
					LEFT OUTER JOIN RecyclingInvoicesMaterials as rim ON rim.invoiceId = ri.id
				WHERE
					ri.locationType = 'DC'
					AND ri.invoiceDate BETWEEN '{$date[0]}' AND '{$date[1]}'
			");
			
			$recycling = $query->row()->recycling;
			if ($recycling <= 0) {
				$recycling = '0';
			}
			
			$result->dc[] = $recycling;
			
			$query = $this->db->query("
				SELECT SUM(rim.quantity) as recycling, st.region  FROM RecyclingInvoices as ri
					INNER JOIN Stores as s ON s.id = locationId
					INNER JOIN States as st ON st.id = s.stateId
					LEFT OUTER JOIN RecyclingInvoicesMaterials as rim ON rim.invoiceId = ri.id AND rim.unit = 1  
				WHERE
					(ri.invoiceDate BETWEEN '{$date[0]}' AND '{$date[1]}')
				AND locationType = 'STORE'
				GROUP BY st.region
			");
			
			$stores = $query->result();
			
			if (!isset($result->East[$k])) {
				$result->East[$k] = '0';
			}
			
			if (!isset($result->MidWest[$k])) {
				$result->MidWest[$k] = '0';
			}
			
			if (!isset($result->Southeast[$k])) {
				$result->Southeast[$k] = '0';
			}
			
			if (!isset($result->West[$k])) {
				$result->West[$k] = '0';
			}
			
			foreach ($stores as $store) {
				$result->{$store->region}[$k] = $store->recycling;
			}
		}

		return $result;		
	}
	
	public function recyclingReportByDateRange($startDate, $endDate, $for) {
		$for = (int)$for;
		
		if (($for > 3) || ($for < 1)) {
			$for = 1;
		}
		
		$sqlStartDate =	USToSQLDate($startDate);
		$sqlEndDate   = USToSQLDate($endDate);
		
		$dcList = array();
		$storesList = array();
		$rebatesChart = array();
		$recyclingChart = array();
		
		// chart "Recycling"
		$where = '';
		
		if ($for == 2) {
			$where = 'ri.locationType = \'DC\' AND';	
		} elseif ($for == 3) {
			$where = 'ri.locationType = \'STORE\' AND';
		}
		
		$query = $this->db->query("
			SELECT m.name, SUM(rim.quantity) as materialPrice FROM RecyclingInvoices as ri
				LEFT OUTER JOIN RecyclingInvoicesMaterials as rim ON rim.invoiceId = ri.id
				INNER JOIN Materials as m ON m.id = rim.materialId
			WHERE
				 $where rim.unit = 1
				AND (ri.invoiceDate BETWEEN '$sqlStartDate' AND '$sqlEndDate')
			GROUP BY rim.materialId
			ORDER BY m.name ASC
		");
		
		$recyclingChart = $query->result();
		// end of chart "Recycling"
		
		if (($for == 1) || ($for == 2)) {
			//DCs - list
			
			$query = $this->db->query("
				SELECT 'DC' as type, dc.id, dc.name, dc.squareFootage as sqft FROM DistributionCenters as dc
					LEFT OUTER JOIN RecyclingInvoices as ri ON ri.locationType = 'DC' AND ri.locationId = dc.id AND (ri.invoiceDate BETWEEN '$sqlStartDate' AND '$sqlEndDate')
				GROUP BY dc.id
			");
			
			$tempDc = $query->result();
			
			$query = $this->db->query("
				SELECT * FROM Materials 
			");
			
			$tempMaterials = $query->result();
			$materials = array();
			
			foreach ($tempMaterials as $v) {
				$materials[$v->id]['co2'] = $v->CO2Saves;
				$materials[$v->id]['kwh'] = $v->EnergySaves;
			}
				
			foreach ($tempDc as $k=>&$dc) {
				$query = $this->db->query("
					SELECT SUM(IF(rim.materialId = 1, rim.quantity, 0)) as cardboard,
						   SUM(IF(rim.materialId = 3, rim.quantity, 0)) as aluminum,
						   SUM(IF(rim.materialId = 8, rim.quantity, 0)) as film,
						   SUM(IF(rim.materialId = 5, rim.quantity, 0)) as plastic,
						   SUM(IF(rim.materialId = 7, rim.quantity, 0)) as trees,
						   SUM(rim.quantity * rim.pricePerUnit) as rebate
					FROM RecyclingInvoices as ri
					LEFT OUTER JOIN RecyclingInvoicesMaterials as rim ON rim.invoiceId = ri.id AND rim.unit = 1
					
					WHERE
						ri.locationType = 'DC' AND ri.locationId = {$dc->id}
						AND (ri.invoiceDate BETWEEN '$sqlStartDate' AND '$sqlEndDate')
				");
				
				$temp = $query->row();
				
				$dc->cardboard = empty($temp->cardboard) ? '0':$temp->cardboard;
				$dc->aluminum = empty($temp->aluminum) ? '0':$temp->aluminum;
				$dc->film = empty($temp->film) ? '0':$temp->film;
				$dc->plastic = empty($temp->plastic) ? '0':$temp->plastic;
				$dc->trees = empty($temp->trees) ? '0':$temp->trees;
				
				$dc->rebate = number_format($temp->rebate, 2);
				
				$dc->co2 = $dc->cardboard * $materials[1]['co2'] + 
						   $dc->aluminum * $materials[3]['co2'] + 
						   $dc->film * $materials[8]['co2'] +
						   $dc->plastic * $materials[5]['co2'] +
						   $dc->trees *  $materials[7]['co2'];
						   
				$dc->kwh = $dc->cardboard * $materials[1]['kwh'] + 
						   $dc->aluminum * $materials[3]['kwh'] + 
						   $dc->film * $materials[8]['kwh'] +
						   $dc->plastic * $materials[5]['kwh'] +
						   $dc->trees *  $materials[7]['kwh'];
						   
				$dc->cardboard = number_format($dc->cardboard, 2);
				$dc->aluminum = number_format($dc->aluminum ,2);
				$dc->film = number_format($dc->film ,2);
				$dc->plastic = number_format($dc->plastic ,2);
				$dc->trees = number_format($dc->trees ,2);
				
				$dc->kwh = number_format($dc->kwh ,2);
				$dc->co2 = number_format($dc->co2 ,2);
			}
			

			
			$dcList = $tempDc;

			if ($for == 1) {
				$totalRebateSum = 0;
				
				foreach ($dcList as $item) {
					$sum = str_replace(',', '', $item->rebate);
					$totalRebateSum += doubleval($sum);
				}
				
				$rebatesChart['DC'] = $totalRebateSum;
			} else {
				foreach ($dcList as $item) {
					$rebatesChart[$item->name] = $item->rebate;
				}
			}
		}
		
		if (($for == 1) || ($for == 3)) {
			//regions - list
			
			$query = $this->db->query("
				SELECT id, region FROM States
			");
			
			$tempStates = $query->result();
			$states = array();
			
			foreach ($tempStates as $item) {
				$states[$item->region][] = $item->id;	
			}
			
			$query = $this->db->query("
				SELECT * FROM Materials 
			");
			
			$tempMaterials = $query->result();
			$materials = array();
			
			foreach ($tempMaterials as $v) {
				$materials[$v->id]['co2'] = $v->CO2Saves;
				$materials[$v->id]['kwh'] = $v->EnergySaves;
			}
			
			
			$query = $this->db->query("
				SELECT SUM(s.squareFootage) as sqft, st.region, st.id as state 
				FROM Stores as s
				INNER JOIN States as st ON st.id = s.stateId
				GROUP BY st.region
			");
			
			
			$temp = $query->result();
			
			foreach ($temp as $k=>&$v) {
				$query = $this->db->query("
					SELECT SUM(rim.quantity * rim.pricePerUnit) as rebate FROM RecyclingInvoicesMaterials as rim
						INNER JOIN RecyclingInvoices as ri ON ri.id = rim.invoiceId
						INNER JOIN Stores as s ON ri.locationType = 'STORE' AND s.id = ri.locationId
						INNER JOIN States as st ON st.id = s.stateId
					WHERE
						st.region = '{$v->region}' AND rim.unit = 1
						AND (ri.invoiceDate BETWEEN '$sqlStartDate' AND '$sqlEndDate')
				");
				
				$v->rebate = $query->row()->rebate;
				
				if (!$v->rebate) {
					$v->rebate = '0';
				}

				
				$query = $this->db->query("
					SELECT rim.materialId, SUM(rim.quantity) as q FROM RecyclingInvoicesMaterials as rim
						INNER JOIN RecyclingInvoices as ri ON ri.id = rim.invoiceId
						INNER JOIN Stores as s ON ri.locationType = 'STORE' AND s.id = ri.locationId
						INNER JOIN States as st ON st.id = s.stateId
					WHERE
						st.region = '{$v->region}' AND rim.unit = 1
						AND (ri.invoiceDate BETWEEN '$sqlStartDate' AND '$sqlEndDate')
					GROUP BY 
						rim.materialId
				");	

				$materialsData = $query->result();
				
				$v->cardboard = '0';
				$v->aluminum = '0';
				$v->film = '0';
				$v->plastic = '0';
				$v->trees = '0';
				

				foreach ($materialsData as $mk=>$material) {
					switch ($material->materialId) {
						case 1:
							$v->cardboard += $material->q;
							break;
						case 3:
							$v->aluminum += $material->q;
							break;
						case 8:
							$v->film += $material->q;
							break;
						case 5:
							$v->plastic += $material->q;
							break;
						case 7:
							$v->trees += $material->q;
							break;
					}
				}
				
				$v->co2 = $v->cardboard * $materials[1]['co2'] + 
						   $v->aluminum * $materials[3]['co2'] + 
						   $v->film * $materials[8]['co2'] +
						   $v->plastic * $materials[5]['co2'] +
						   $v->trees *  $materials[7]['co2'];
						   
				$v->kwh = $v->cardboard * $materials[1]['kwh'] + 
						   $v->aluminum * $materials[3]['kwh'] + 
						   $v->film * $materials[8]['kwh'] +
						   $v->plastic * $materials[5]['kwh'] +
						   $v->trees *  $materials[7]['kwh'];
						   
				
				$v->cardboard = number_format($v->cardboard, 2);
				$v->aluminum = number_format($v->aluminum ,2);
				$v->film = number_format($v->film ,2);
				$v->plastic = number_format($v->plastic ,2);
				$v->trees = number_format($v->trees ,2);
				
				$v->kwh = number_format($v->kwh ,2);
				$v->co2 = number_format($v->co2 ,2);
				
				$v->type = 'Region';
				$v->name = $v->region;
			}

			
			$storesList = $temp;
			
			foreach ($storesList as $item) {
				$rebatesChart['Stores ('.$item->name.')'] = $item->rebate;
			}
		}

		return array(
			'rebatesChart' => $rebatesChart,
			'recyclingChart' => $recyclingChart,
			'list' => array_merge($dcList, $storesList),
			'savings' => array()
		);
	}
	
	public function wasteReportByDateRange($startDate, $endDate, $for, $bystate) {
		$for = (int)$for;
		
		if (($for > 4) || ($for < 1)) {
			$for = 1;
		}
		
		$sqlStartDate =	USToSQLDate($startDate);
		$sqlEndDate   = USToSQLDate($endDate);
		
		$costChart = new CostsChart();
		$wasteChart = new WasteChart();
		
		$dcList = array();
		$storesList = array();
		
		$whereByState = "";
		if($bystate!=0)
		    $whereByState = " AND st.id = ".$bystate;
		if($for==4){
		    $query = $this->db->query("
			    SELECT 'DC' as type,
				    dc.id as id, 
				    dc.name as name, 
				    st.name as state, 
				    dc.squareFootage as squareFootage,
				    (
					    SELECT SUM(ri.total) FROM RecyclingInvoices as ri
					    WHERE
						    ri.locationType = 'DC' AND ri.locationId = dc.id 
				    ) as recyclingCost,
				    (
					    SELECT SUM(wi.total) FROM WasteInvoices as wi
					    WHERE
						    wi.locationType = 'DC' AND wi.locationId = dc.id
				    ) as wasteCost, (
				    SELECT SUM(rim.quantity) FROM RecyclingInvoices as ri
					    LEFT OUTER JOIN RecyclingInvoicesMaterials as rim ON rim.invoiceId = ri.id
				    WHERE
					    ri.locationType = 'DC' AND ri.locationId = dc.id
			    ) as recycling, (
				    SELECT SUM(wis.quantity) FROM WasteInvoices as wi
					    LEFT OUTER JOIN WasteInvoiceServices as wis ON wis.invoiceId = wi.id
				    WHERE
					    wi.locationType = 'DC' AND wi.locationId = dc.id
			    ) as waste
			     FROM DistributionCenters as dc
			     INNER JOIN States AS st ON st.id = dc.stateId
			     WHERE 1 ".$whereByState." GROUP BY dc.stateId
				 UNION ALL
			    SELECT 'Region' as type, 
				    '' as id,
				    st.region as name, 
				    st.name as state, 
				    SUM(s.squareFootage) as squareFootage, 
				    SUM(wi.total) as wasteCost,
				    SUM(ri.total) as recyclingCost, 
				    SUM(rim.quantity) as recycling,
				    SUM(wis.quantity) as waste
			     FROM Stores as s
				    LEFT JOIN WasteInvoices as wi ON wi.locationId = s.id AND wi.locationType = 'STORE' AND (wi.invoiceDate BETWEEN '$sqlStartDate' AND '$sqlEndDate')
				    LEFT JOIN WasteInvoiceServices as wis ON wis.invoiceId = wi.id AND wis.unitId = 1
				    LEFT JOIN RecyclingInvoices as ri ON ri.locationId = s.id AND ri.locationType = 'STORE' AND (ri.invoiceDate BETWEEN '$sqlStartDate' AND '$sqlEndDate')
				    LEFT JOIN RecyclingInvoicesMaterials as rim ON rim.invoiceId = ri.id AND rim.unit = 1
				    INNER JOIN States as st ON st.id = s.stateId 
			    WHERE 1 ".$whereByState." GROUP BY s.stateId
			");

		    $dcList = $query->result();
		    
		    foreach ($dcList as $region) {
			    $region->cost = ($region->wasteCost + $region->recyclingCost) + '0';
		    }
		    		        
		    $query = $this->db->query("
			    SELECT SUM(total) as cost, SUM(wis.quantity) as waste, st.name as Name FROM WasteInvoices
				    INNER JOIN Stores as s ON s.id = locationId
				    INNER JOIN States as st ON st.id = s.stateId
				    LEFT OUTER JOIN WasteInvoiceServices as wis ON wis.invoiceId = WasteInvoices.id AND wis.unitId = 1  
			    WHERE
				    (invoiceDate BETWEEN '$sqlStartDate' AND '$sqlEndDate')
				AND locationType = 'STORE' ".$whereByState." 
			    GROUP BY s.stateId
		    ");

		    $result = $query->result(); 
		    
		    foreach ($result as $region) {
			if(!empty($region->Name)) {
			    $costChart->stateCost[] = array(
					($region->cost > 0) ? $region->cost : '0',
					$region->Name
			    );
			    $wasteChart->stateWaste[] = array(
					($region->waste > 0) ? $region->waste : '0',
					$region->Name
			    );
			}
		    }
		}	    
		else{
			if (($for == 1) || ($for == 2)) {			
				$query = $this->db->query("
					SELECT 'DC' as type,dc.id, dc.name, st.name as state, dc.squareFootage,(
						(
							SELECT SUM(ri.total) FROM RecyclingInvoices as ri
							WHERE
								ri.locationType = 'DC' AND ri.locationId = dc.id 
						) 
						+
						(
							SELECT SUM(wi.total) FROM WasteInvoices as wi
							WHERE
								wi.locationType = 'DC' AND wi.locationId = dc.id
						)
					) as cost, (
						SELECT SUM(rim.quantity) FROM RecyclingInvoices as ri
							LEFT OUTER JOIN RecyclingInvoicesMaterials as rim ON rim.invoiceId = ri.id
						WHERE
							ri.locationType = 'DC' AND ri.locationId = dc.id
					) as recycling, (
						SELECT SUM(wis.quantity) FROM WasteInvoices as wi
							LEFT OUTER JOIN WasteInvoiceServices as wis ON wis.invoiceId = wi.id
						WHERE
							wi.locationType = 'DC' AND wi.locationId = dc.id
					) as waste
					 FROM DistributionCenters as dc
					 INNER JOIN States AS st ON st.id = dc.stateId 
					 WHERE 1 ".$whereByState);

				$dcList = $query->result();

				$query = $this->db->query("
					SELECT dc.name, SUM(wi.total) as cost, SUM(wis.quantity) as waste FROM DistributionCenters as dc
						LEFT JOIN WasteInvoices as wi ON wi.locationId = dc.id AND wi.locationType = 'DC'
						LEFT JOIN WasteInvoiceServices as wis ON wis.invoiceId = wi.id AND wis.unitId = 1
					WHERE
						(wi.invoiceDate BETWEEN '$sqlStartDate' AND '$sqlEndDate')
				");

				$result = $query->result();
				$costChart->dc = array();
				$wasteChart->dc = array();

				foreach ($result as $dc) {
					$costChart->dc[] = array(
						($dc->cost > 0) ? $dc->cost : '0',
						$dc->name
					);
					$wasteChart->dc[] = array(
						($dc->waste > 0) ? $dc->waste : '0',
						$dc->name
					);
				}
			}
			//All DCs and Stores
			if (($for == 1) || ($for == 3)) {
				if ($for == 1) {
					$query = $this->db->query("
						SELECT SUM(total) as cost, SUM(wis.quantity) as waste FROM WasteInvoices
							LEFT OUTER JOIN WasteInvoiceServices as wis ON wis.invoiceId = WasteInvoices.id AND wis.unitId = 1
						WHERE
							(invoiceDate BETWEEN '$sqlStartDate' AND '$sqlEndDate')
						AND locationType = 'DC'
					");


					$costChart->dc = ($query->row()->cost > 0) ? $query->row()->cost : '0';
					$wasteChart->dc = ($query->row()->waste > 0) ? $query->row()->waste : '0';
				}

				$query = $this->db->query("
					SELECT SUM(total) as cost, SUM(wis.quantity) as waste, st.region  FROM WasteInvoices
						INNER JOIN Stores as s ON s.id = locationId
						INNER JOIN States as st ON st.id = s.stateId
						LEFT OUTER JOIN WasteInvoiceServices as wis ON wis.invoiceId = WasteInvoices.id AND wis.unitId = 1  
					WHERE
						(invoiceDate BETWEEN '$sqlStartDate' AND '$sqlEndDate')
					AND locationType = 'STORE'
					GROUP BY st.region
				");

				$storesByRegionCosts = $query->result(); 

				foreach ($storesByRegionCosts as $region) {
					if(!empty($region->region)) {
						$costChart->{$region->region} = $region->cost;
						$wasteChart->{$region->region} = (empty($region->waste)) ? '0' : $region->waste;
					}
				}

				$query = $this->db->query("
					SELECT SUM(wis.quantity) as waste, SUM(wis.quantity * wis.rate) as sum FROM WasteInvoices as wi
						LEFT OUTER JOIN WasteInvoiceServices as wis ON wis.invoiceId = wi.id
					WHERE
						(wi.invoiceDate BETWEEN '$sqlStartDate' AND '$sqlEndDate')
						AND wis.unitId = 1 
				");

				$waste = $query->row();			

				$query = $this->db->query("
					SELECT 'Region' as type, st.region as name, st.name as state, SUM(s.squareFootage) as squareFootage, SUM(wi.total) as wasteCost, SUM(wis.quantity) as waste,
						  SUM(ri.total) as recyclingCost, SUM(rim.quantity) as recycling
					 FROM Stores as s
						LEFT JOIN WasteInvoices as wi ON wi.locationId = s.id AND wi.locationType = 'STORE' AND (wi.invoiceDate BETWEEN '$sqlStartDate' AND '$sqlEndDate')
						LEFT JOIN WasteInvoiceServices as wis ON wis.invoiceId = wi.id AND wis.unitId = 1
						LEFT JOIN RecyclingInvoices as ri ON ri.locationId = s.id AND ri.locationType = 'STORE' AND (ri.invoiceDate BETWEEN '$sqlStartDate' AND '$sqlEndDate')
						LEFT JOIN RecyclingInvoicesMaterials as rim ON rim.invoiceId = ri.id AND rim.unit = 1
						INNER JOIN States as st ON st.id = s.stateId 
					WHERE 1 ".$whereByState." GROUP BY st.region");

				$storesList = $query->result();

				foreach ($storesList as &$region) {
					$region->cost = ($region->wasteCost + $region->recyclingCost) + '0';
				}
			}
			//end of All DCs and Stores
		}
		
		return array(
			'costChart' => $costChart,
			'wasteChart' => $wasteChart,
			'list' => array_merge($dcList, $storesList)
		);
	}
	
	
	public function getServiceRequests($startDate, $endDate) {
		$query = $this->db->query("
			SELECT srts.name, count(purposeId) as c FROM SupportRequestTasks as srt
				INNER JOIN SupportRequests as sr ON sr.id = srt.supportRequestId
				LEFT OUTER JOIN SupportRequestServiceTypes as srts ON srts.id = srt.purposeId
			WHERE 
				sr.companyId = 1 AND sr.complete = 1
			GROUP BY  srt.purposeId
		");

		return $query->result();
	}
	
	public function getCostOfServicesChartInfo($from='', $to='', $display=1) {
		$result = Array();
		
		$aditional = '';
		$where = '';
		
		if (!empty($from) && !(empty($to))) {
			$where = "
				WHERE
					(ri.invoiceDate BETWEEN '$from' AND '$to') 
			";
		}
		
		switch ($display) {
			case 1:
				$aditional = $where;
				break;
			case 2:
				$aditional = "
					INNER JOIN Stores as st ON st.id = ri.locationId AND ri.locationType = 'STORE'
					$where
				";
				break;
			case 3:
				$aditional = "
					INNER JOIN DistributionCenters as dc ON dc.id = ri.locationId AND ri.locationType = 'DC'
					$where
				";
				break;
		}
		
		$cost = $this->db->query("
		SELECT SUM( `feeAmount` ) AS feeAmount, `feeType` FROM `WasteInvoiceFees` wf
			INNER JOIN WasteInvoices wi ON wi.id = `invoiceId`
		GROUP BY feeType	
		UNION ( SELECT SUM( `feeAmount` ) , `feeType` FROM `RecyclingInvoicesFees` wf
			INNER JOIN RecyclingInvoices ri ON ri.id = `invoiceId` 
			$aditional
			GROUP BY feeType
		)
		");
		
		$res = $cost->Result();

		$this->load->model('FeeTypeModel');		
		$feeOptions = $this->FeeTypeModel->get_all();
		
		foreach($res as $fee) {
			$res1[$fee->feeType][] = $fee->feeAmount;
		}

		if(isset($res1))
			foreach($res1 as $feeType=>$feeCost) {
				if(array_sum($res1[$feeType])!= null && array_sum($res1[$feeType])> 0) {
					$item = new stdClass();
					$item->Name = isset($feeOptions[$feeType]) ? $feeOptions[$feeType] : '';
					$item->value = array_sum($res1[$feeType]);
					$result[] = $item;
				}
			}

		$cost = $this->db->query("
		SELECT SUM(`rate`) AS rateAmount, ws.`materialId` FROM `WasteInvoiceServices` ws
			INNER JOIN WasteInvoices wi ON wi.id = ws.`invoiceId`
		GROUP BY ws.`materialId`");
		
		$res = $cost->Result();
		
		$this->load->model('MaterialsModel');
		$materialOptions = 	$this->MaterialsModel->getList(1);	 		
		
		foreach($res as $material) {
			$res2[$material->materialId][] = $material->rateAmount;
		}

		if(isset($res2))
			foreach($res2 as $materialId=>$rateAmount) {
				if(array_sum($res2[$materialId])!= null && array_sum($res2[$materialId])> 0) {
					$item = new stdClass();
					$item->Name = isset($materialOptions[$materialId]) ? $materialOptions[$materialId] : '';
					$item->value = array_sum($res2[$materialId]);
					$result[] = $item;
				}
			}
		
		return $result;		
	}
	
	public function wasteRecyclingTrendsChart() {
		$months = 6;
		$dates = array();
		$result = new stdClass();
		
		for ($i=$months -1;$i >= 0;$i--) {
			$temp = strtotime ( '-'.$i.' month');
			
			$dates[] = array(
				date('Y', $temp) . '-' . date('m', $temp) . '-01',
				date('Y', $temp) . '-' . date('m', $temp) . '-31',
			);
			
			$result->dates[] = date('m', $temp) . '/' . date('Y', $temp); 
		}

		$query = $this->db->query("
			SELECT id, name FROM Materials WHERE categoryId = 1
		");
		
		$materials = $query->result();
		
		$result->materials[] = 'Waste';
		
		foreach ($materials as $material) {
			$result->materials[] = $material->name;
		}

		foreach ($dates as $k=>$date) {
			$query = $this->db->query("
				SELECT SUM(wis.quantity) as waste FROM WasteInvoices as wi
					LEFT OUTER JOIN WasteInvoiceServices as wis ON wis.invoiceId = wi.id
				WHERE wis.unitId = 1 AND
					wi.invoiceDate BETWEEN '{$date[0]}' AND '{$date[1]}'
			");
			
			$waste = $query->row()->waste;
			
			if ($waste <= 0) {
				$waste = '0';
			}
			
			$result->data[0][] = $waste;
			
			foreach ($materials as $i=>$material) {
				$query = $this->db->query("
					SELECT SUM(rim.quantity) as q FROM RecyclingInvoices as ri
						LEFT OUTER JOIN RecyclingInvoicesMaterials as rim ON rim.invoiceId = ri.id
					WHERE
						(ri.invoiceDate BETWEEN '{$date[0]}' AND '{$date[1]}')
						AND rim.materialId = {$material->id}
						
				");
				
				$q = $query->row()->q;
				if ($q <= 0) {
					$q = '0';
				}
				
				$result->data[$i+1][] = $q;
			}
		}
		
		return $result;		
	}
	
	public function wasteDiversion() {
		$result = new stdClass();
		
		$query = $this->db->query("
			SELECT dc.id, dc.name, SUM(wis.quantity * wis.rate) as i FROM DistributionCenters as dc
				LEFT OUTER JOIN WasteInvoices as wi ON wi.locationId = dc.id
				LEFT OUTER JOIN WasteInvoiceServices as wis ON wis.invoiceId = wi.id
			WHERE
				wi.locationType = 'DC'
				AND wis.unitId = 1
			GROUP BY dc.id
			ORDER BY i DESC
			LIMIT 3
		");
		
		$result->DCHigh = $query->result();
		
		$query = $this->db->query("
			SELECT dc.id, dc.name, SUM(wis.quantity * wis.rate) as i FROM DistributionCenters as dc
				LEFT OUTER JOIN WasteInvoices as wi ON wi.locationId = dc.id
				LEFT OUTER JOIN WasteInvoiceServices as wis ON wis.invoiceId = wi.id
			WHERE
				wi.locationType = 'DC'
				AND wis.unitId = 1
			GROUP BY dc.id
			ORDER BY i ASC
			LIMIT 3
		");
		
		$result->DCLow = $query->result();
		
		$query = $this->db->query("
			SELECT st.id, st.location as name, SUM(wis.quantity * wis.rate) as i FROM Stores as st
				LEFT OUTER JOIN WasteInvoices as wi ON wi.locationId = st.id
				LEFT OUTER JOIN WasteInvoiceServices as wis ON wis.invoiceId = wi.id
			WHERE
				wi.locationType = 'STORE'
				AND wis.unitId = 1
			GROUP BY st.id
			ORDER BY i DESC
			LIMIT 3
		");
		
		$result->StoreHigh = $query->result();
		
		$query = $this->db->query("
			SELECT st.id, st.location as name, SUM(wis.quantity * wis.rate) as i FROM Stores as st
				LEFT OUTER JOIN WasteInvoices as wi ON wi.locationId = st.id
				LEFT OUTER JOIN WasteInvoiceServices as wis ON wis.invoiceId = wi.id
			WHERE
				wi.locationType = 'STORE'
				AND wis.unitId = 1
			GROUP BY st.id
			ORDER BY i ASC
			LIMIT 3
		");
		
		$result->StoreLow = $query->result();

		return $result;
	}
	
	public function recyclingRebates() {
		$result = new stdClass();
		
		$query = $this->db->query("
			SELECT dc.id, dc.name, SUM(ri.total - ri.totalFees - ri.totalWaivedFees) as i FROM DistributionCenters as dc
				LEFT OUTER JOIN RecyclingInvoices as ri ON ri.locationId = dc.id
			WHERE
				ri.locationType = 'DC'
			GROUP BY dc.id
			ORDER BY i DESC
			LIMIT 3
		");
		
		$result->DCHigh = $query->result();
		
		$query = $this->db->query("
			SELECT dc.id, dc.name, SUM(ri.total - ri.totalFees - ri.totalWaivedFees) as i FROM DistributionCenters as dc
				LEFT OUTER JOIN RecyclingInvoices as ri ON ri.locationId = dc.id
			WHERE
				ri.locationType = 'DC'
			GROUP BY dc.id
			ORDER BY i ASC
			LIMIT 3
		");
		
		$result->DCLow = $query->result();
		
		$query = $this->db->query("
			SELECT st.id, st.location as name, SUM(ri.total - ri.totalFees - ri.totalWaivedFees) as i FROM Stores as st
				LEFT OUTER JOIN RecyclingInvoices as ri ON ri.locationId = st.id
			WHERE
				ri.locationType = 'STORE'
			GROUP BY st.id
			ORDER BY i DESC
			LIMIT 3
		");
		
		$result->StoreHigh = $query->result();
		
		$query = $this->db->query("
			SELECT st.id, st.location as name, SUM(ri.total - ri.totalFees - ri.totalWaivedFees) as i FROM Stores as st
				LEFT OUTER JOIN RecyclingInvoices as ri ON ri.locationId = st.id
			WHERE
				ri.locationType = 'STORE'
			GROUP BY st.id
			ORDER BY i ASC
			LIMIT 3
		");
		
		$result->StoreLow = $query->result();

		return $result;
	}
	
	
	public function netCost() {
		$result = new stdClass();
		
		$query = $this->db->query("
			SELECT dc.id, dc.name, SUM(ri.total - ri.totalFees - ri.totalWaivedFees - (
				SELECT SUM(wif.feeAmount) FROM WasteInvoiceFees as wif
					JOIN WasteInvoices as wi ON wi.id = wif.invoiceId
				WHERE
					wi.locationType = 'DC' AND wi.locationId = dc.id
			) - (
				SELECT SUM(rci.quantity * rci.pricePerTon) FROM RecyclingChargeItems as rci
					JOIN RecyclingCharges as rc ON rc.id = rci.recyclingChargeId
				WHERE
					rci.unitId = 1 AND
					rc.locationType = 'DC' AND rc.locationId = dc.id
			)) as i FROM DistributionCenters as dc
				LEFT OUTER JOIN RecyclingInvoices as ri ON ri.locationId = dc.id
			WHERE
				ri.locationType = 'DC'
			GROUP BY dc.id
			ORDER BY i DESC
			LIMIT 3
		");
		
		$result->DCHigh = $query->result();

		
		$query = $this->db->query("
			SELECT dc.id, dc.name, SUM(ri.total - ri.totalFees - ri.totalWaivedFees - (
				SELECT SUM(wif.feeAmount) FROM WasteInvoiceFees as wif
					JOIN WasteInvoices as wi ON wi.id = wif.invoiceId
				WHERE
					wi.locationType = 'DC' AND wi.locationId = dc.id
			) - (
				SELECT SUM(rci.quantity * rci.pricePerTon) FROM RecyclingChargeItems as rci
					JOIN RecyclingCharges as rc ON rc.id = rci.recyclingChargeId
				WHERE
					rci.unitId = 1 AND
					rc.locationType = 'DC' AND rc.locationId = dc.id
			)) as i FROM DistributionCenters as dc
				LEFT OUTER JOIN RecyclingInvoices as ri ON ri.locationId = dc.id
			WHERE
				ri.locationType = 'DC'
			GROUP BY dc.id
			ORDER BY i ASC
			LIMIT 3
		");
		
		$result->DCLow = $query->result();
		
		$query = $this->db->query("
			SELECT st.id, st.location as name, SUM(ri.total - ri.totalFees - ri.totalWaivedFees - (
				SELECT SUM(wif.feeAmount) FROM WasteInvoiceFees as wif
					JOIN WasteInvoices as wi ON wi.id = wif.invoiceId
				WHERE
					wi.locationType = 'STORE' AND wi.locationId = st.id
			) - (
				SELECT SUM(rci.quantity * rci.pricePerTon) FROM RecyclingChargeItems as rci
					JOIN RecyclingCharges as rc ON rc.id = rci.recyclingChargeId
				WHERE
					rci.unitId = 1 AND
					rc.locationType = 'STORE' AND rc.locationId = st.id
			)) as i FROM Stores as st
				LEFT OUTER JOIN RecyclingInvoices as ri ON ri.locationId = st.id
			WHERE
				ri.locationType = 'STORE'
			GROUP BY st.id
			ORDER BY i DESC
			LIMIT 3
		");
		
		$result->StoreHigh = $query->result();
		
		$query = $this->db->query("
			SELECT st.id, st.location as name, SUM(ri.total - ri.totalFees - ri.totalWaivedFees - (
				SELECT SUM(wif.feeAmount) FROM WasteInvoiceFees as wif
					JOIN WasteInvoices as wi ON wi.id = wif.invoiceId
				WHERE
					wi.locationType = 'STORE' AND wi.locationId = st.id
			) - (
				SELECT SUM(rci.quantity * rci.pricePerTon) FROM RecyclingChargeItems as rci
					JOIN RecyclingCharges as rc ON rc.id = rci.recyclingChargeId
				WHERE
					rci.unitId = 1 AND
					rc.locationType = 'STORE' AND rc.locationId = st.id
			)) as i FROM Stores as st
				LEFT OUTER JOIN RecyclingInvoices as ri ON ri.locationId = st.id
			WHERE
				ri.locationType = 'STORE'
			GROUP BY st.id
			ORDER BY i ASC
			LIMIT 3
		");
		
		$result->StoreLow = $query->result();

		return $result;
	} 
	
	public function services() {
		$result = new stdClass();
		
		$query = $this->db->query("
			SELECT dc.id, dc.name, count(vs.id) as i FROM DistributionCenters as dc
				LEFT OUTER JOIN VendorServices as vs ON vs.locationId = dc.id
			WHERE
				vs.locationType = 'DC'
			GROUP BY dc.id
			ORDER BY i DESC
			LIMIT 3
		");
		
		$result->DCHigh = $query->result();
		
		$query = $this->db->query("
			SELECT dc.id, dc.name, count(vs.id) as i FROM DistributionCenters as dc
				LEFT OUTER JOIN VendorServices as vs ON vs.locationId = dc.id
			WHERE
				vs.locationType = 'DC'
			GROUP BY dc.id
			ORDER BY i ASC
			LIMIT 3
		");
		
		$result->DCLow = $query->result();
		
		$query = $this->db->query("
			SELECT st.id, st.location as name, count(vs.id) as i FROM Stores as st
				LEFT OUTER JOIN VendorServices as vs ON vs.locationId = st.id
			WHERE
				vs.locationType = 'STORE'
			GROUP BY st.id
			ORDER BY i DESC
			LIMIT 3
		");
		
		$result->StoreHigh = $query->result();
		
		$query = $this->db->query("
			SELECT st.id, st.location as name, count(vs.id) as i FROM Stores as st
				LEFT OUTER JOIN VendorServices as vs ON vs.locationId = st.id
			WHERE
				vs.locationType = 'STORE'
			GROUP BY st.id
			ORDER BY i ASC
			LIMIT 3
		");
		
		$result->StoreLow = $query->result();

		return $result;
	}
	
	public function getServicesData($from, $to, $display) {
		$result =  Array ('containers'=>'', 'duration'=>'', 'Frequency '=>'', 'table');
		$from = USToSQLDate($from); 
		$to = USToSQLDate($to);		
		$groupBy = "";
		$whereStateRegion = "";
		$what = "m.name";
		$tableWhere = '';
		$tableOrder = '';
		$count = " BIT_COUNT(((days & 0xFF00) >> 7) | (days & 0xff)) ";
		
		$containersJoins = '';
		
		switch ($display) {
			case 1:
				$sqlQuery = "
				SELECT locationType, States.region AS name, c.name AS container,
					IF(serviceTypeId = 1, 'Normal',
						IF(serviceTypeId = 2, 'Temporary',
							IF(serviceTypeId = 3, 'Extra',
							'')
						)
					) as duration, 
					IF (schedule = 1 AND days > 0, CONCAT($count, 'x/Week'),
					IF (schedule = 2, 'Biweekly',
					IF (schedule = 3, 'Monthly',
					IF (schedule = 4, 'On Call', '')))) AS frequency,
					quantity*rate as cost,
					DATE(Stores.lastUpdated) as lastUpdated
					FROM Stores			 	
					LEFT JOIN States ON States.id = Stores.stateId
					INNER JOIN WasteInvoices wi ON Stores.id = wi.locationId AND locationType = 'STORE'
					INNER JOIN WasteInvoiceServices wis ON wi.id = wis.invoiceId
					INNER JOIN Containers c ON c.id = containerId	
					WHERE (wi.invoiceDate BETWEEN '{$from}' AND '{$to}') {$tableWhere} ORDER BY States.region ASC
				";
				$res = $this->db->query($sqlQuery);
				$result['table'] = $res->result();
				
				$sqlQuery = "SELECT locationType,dc.id,  dc.name, c.name AS container,
					IF(serviceTypeId = 1, 'Normal',
					IF(serviceTypeId = 2, 'Temporary',
					IF(serviceTypeId = 3, 'Extra',
					''))) as duration, 
					IF (schedule = 1 AND days > 0, CONCAT($count, 'x/Week'),
					IF (schedule = 2, 'Biweekly',
					IF (schedule = 3, 'Monthly',
					IF (schedule = 4, 'On Call', '')))) AS frequency,
					quantity*rate as cost,
					DATE(dc.lastUpdated) as lastUpdated
					FROM DistributionCenters as dc			 	
					LEFT JOIN States ON States.id = dc.stateId
					INNER JOIN WasteInvoices wi ON dc.id = wi.locationId AND locationType = 'DC'
					INNER JOIN WasteInvoiceServices wis ON wi.id = wis.invoiceId
					INNER JOIN Containers c ON c.id = containerId	
					WHERE (wi.invoiceDate BETWEEN '{$from}' AND '{$to}') {$tableWhere} ORDER BY dc.name ASC";
				$res = $this->db->query($sqlQuery);
				$result['table'] = array_merge($res->result(), $result['table']);
				break;
			case 2:
				$containersJoins = "
					INNER JOIN DistributionCenters as dc ON dc.id = wi.locationId AND locationType = 'DC'	
				";
				
				$sqlQuery = "SELECT locationType,dc.id,  dc.name, c.name AS container,
					IF(serviceTypeId = 1, 'Normal',
					IF(serviceTypeId = 2, 'Temporary',
					IF(serviceTypeId = 3, 'Extra',
					''))) as duration, 
					IF (schedule = 1 AND days > 0, CONCAT($count, 'x/Week'),
					IF (schedule = 2, 'Biweekly',
					IF (schedule = 3, 'Monthly',
					IF (schedule = 4, 'On Call', '')))) AS frequency,
					quantity*rate as cost,
					DATE(dc.lastUpdated) as lastUpdated
					FROM DistributionCenters as dc			 	
					LEFT JOIN States ON States.id = dc.stateId
					INNER JOIN WasteInvoices wi ON dc.id = wi.locationId AND locationType = 'DC'
					INNER JOIN WasteInvoiceServices wis ON wi.id = wis.invoiceId
					INNER JOIN Containers c ON c.id = containerId	
					WHERE (wi.invoiceDate BETWEEN '{$from}' AND '{$to}') {$tableWhere} ORDER BY dc.name ASC";
				$res = $this->db->query($sqlQuery);
				$result['table'] = $res->result();
				
				break;
			case 3:
				$containersJoins = "
					INNER JOIN Stores ON Stores.id = wi.locationId AND locationType = 'STORE'	
					INNER JOIN States ON States.id = Stores.stateId
				";
				
				$sqlQuery = "
				SELECT locationType, States.region AS name, c.name AS container,
					IF(serviceTypeId = 1, 'Normal',
						IF(serviceTypeId = 2, 'Temporary',
							IF(serviceTypeId = 3, 'Extra',
							'')
						)
					) as duration, 
					IF (schedule = 1 AND days > 0, CONCAT($count, 'x/Week'),
					IF (schedule = 2, 'Biweekly',
					IF (schedule = 3, 'Monthly',
					IF (schedule = 4, 'On Call', '')))) AS frequency,
					quantity*rate as cost,
					DATE(Stores.lastUpdated) as lastUpdated
					FROM Stores			 	
					LEFT JOIN States ON States.id = Stores.stateId
					INNER JOIN WasteInvoices wi ON Stores.id = wi.locationId AND locationType = 'STORE'
					INNER JOIN WasteInvoiceServices wis ON wi.id = wis.invoiceId
					INNER JOIN Containers c ON c.id = containerId	
					WHERE (wi.invoiceDate BETWEEN '{$from}' AND '{$to}') {$tableWhere} ORDER BY States.region ASC
				";
				$res = $this->db->query($sqlQuery);
				$result['table'] = $res->result();
		
				break;
		}
		
		$sqlQuery = "SELECT COUNT(containerType) AS value, containerType AS Name  FROM Containers cc
			WHERE cc.id IN (SELECT containerId FROM WasteInvoiceServices wis 
				INNER JOIN `WasteInvoices` wi ON wi.id = wis.invoiceId
				$containersJoins
				WHERE (wi.invoiceDate BETWEEN '{$from}' AND '{$to}')
			)  GROUP BY containerType ";
		$res = $this->db->query($sqlQuery);		
		$result['containers'] = $res->result();
		
		
		$sqlQuery = "SELECT COUNT(serviceTypeId) AS value, 
			IF(serviceTypeId = 1, 'Normal',
			IF(serviceTypeId = 2, 'Temporary',
			IF(serviceTypeId = 3, 'Extra',
			'-'))) AS Name FROM WasteInvoiceServices wis 
				INNER JOIN `WasteInvoices` wi ON wi.id = wis.invoiceId
				$containersJoins
				WHERE (wi.invoiceDate BETWEEN '{$from}' AND '{$to}')
				GROUP BY serviceTypeId";		
			$res = $this->db->query($sqlQuery);
		$result['services'] = $res->result();
		
		
		$sqlQuery = "SELECT COUNT($count) AS value, 
			IF($count BETWEEN 1 AND 6, CONCAT($count, 'x/Week'),
			IF ($count = 7, 'Daily/Week', '-')) AS Name 
				FROM WasteInvoiceServices wis
				INNER JOIN WasteInvoices wi ON wi.id = wis.invoiceId 
				$containersJoins
				WHERE days > 0 AND schedule = 1 {$whereStateRegion}
				AND (wi.invoiceDate BETWEEN '{$from}' AND '{$to}')
				GROUP BY $count";		
				
		$res = $this->db->query($sqlQuery);		
		$result['frequency'] = $res->result();
		
		return $result;
	}
	
	public function getCostSavingData($from, $to, $display) {
		$result =  Array ('cost'=>'', 'savings'=>'', 'trend'=>'', 'table'=>'');
		$from = USToSQLDate($from); $to = USToSQLDate($to);
		$fromPY = date("Y-m-d", strtotime($from . ' - 1 Year')); 
		$toPY = date("Y-m-d", strtotime($to . ' - 1 Year'));
		
		$groupBy = "";
		$whereStateRegion = "";
		$what = "m.name";
		$tableWhere = '';
		$tableOrder = '';
		$value = "SUM(wi.total)";
		
		$locationSql = '';
				$what = "States.region";
				$tableOrder = ' ORDER BY States.region';
				$groupBy = "GROUP BY States.region";
				
				$locationSql = "INNER JOIN Stores ON Stores.id = ri.locationId AND locationType = 'STORE'";

		$result['cost'] = $this->getCostOfServicesChartInfo($from, $to, $display);
		///// Savings
		switch ($display) {
			case 1: 
			$sqlQuery = "SELECT 'REGION' as type,  {$what} AS Name, 
				(SELECT COALESCE(SUM(quantity*pricePerUnit),0) FROM `RecyclingInvoices` ri
				INNER JOIN RecyclingInvoicesMaterials rim ON rim.invoiceId = ri.id
				INNER JOIN Materials m ON m.id = rim.materialId			
				WHERE  ri.status = 'YES' AND rim.unit = 1 AND Stores.id = ri.locationId AND ri.locationType = 'STORE' AND ri.invoiceDate BETWEEN '{$from}' AND '{$to}'
				)  
				+
				-- SAMS 
				( 
				(SELECT COALESCE(SUM(wis.quantity * wis.rate),0) FROM `WasteInvoices` wi	
				INNER JOIN WasteInvoiceServices wis ON wis.invoiceId = wi.id
				WHERE wi.status = 'YES' AND unitId = 1 AND durationId = 1 AND Stores.id = wi.locationId AND wi.locationType = 'STORE' AND wi.invoiceDate BETWEEN '{$fromPY}' AND '{$toPY}'		 
				)
				-
				(SELECT COALESCE(SUM(wis.quantity * wis.rate),0) FROM `WasteInvoices` wi	
				INNER JOIN WasteInvoiceServices wis ON wis.invoiceId = wi.id
				WHERE wi.status = 'YES' AND unitId = 1 AND durationId = 1 AND Stores.id = wi.locationId AND wi.locationType = 'STORE' AND wi.invoiceDate BETWEEN '{$from}' AND '{$to}'
				)
				)
				-- END OF SAMS
				-- waived fees
				+
				(SELECT COALESCE(SUM(feeAmount),0) FROM `WasteInvoices` wi
					INNER JOIN WasteInvoiceFees wif ON wif.invoiceId = wi.id
					WHERE wi.status = 'YES' AND waived AND Stores.id = wi.locationId AND wi.locationType = 'STORE' AND wi.invoiceDate BETWEEN '{$from}' AND '{$to}'			
				)
				+
				(SELECT COALESCE(SUM(feeAmount),0) FROM `RecyclingInvoices` ri
					INNER JOIN RecyclingInvoicesFees rif ON rif.invoiceId = ri.id
					WHERE ri.status = 'YES' AND waived AND Stores.id = ri.locationId AND ri.locationType = 'STORE' AND ri.invoiceDate BETWEEN '{$from}' AND '{$to}'			
				)
				AS value			
				FROM Stores
				INNER JOIN States ON States.id = Stores.stateId
				WHERE 1 GROUP BY States.region";
				
			$query = $this->db->query($sqlQuery);
			$resultPart1 = $query->result();
			
			$sqlQuery = "SELECT 'DC' as type, dc.name AS Name, 
				(SELECT COALESCE(SUM(quantity*pricePerUnit),0) FROM `RecyclingInvoices` ri
				INNER JOIN RecyclingInvoicesMaterials rim ON rim.invoiceId = ri.id
				INNER JOIN Materials m ON m.id = rim.materialId			
				WHERE  ri.status = 'YES' AND rim.unit = 1 AND dc.id = ri.locationId AND ri.locationType = 'DC' AND ri.invoiceDate BETWEEN '{$from}' AND '{$to}'
				)  
				+
				-- SAMS 
				( 
				(SELECT COALESCE(SUM(wis.quantity * wis.rate),0) FROM `WasteInvoices` wi	
				INNER JOIN WasteInvoiceServices wis ON wis.invoiceId = wi.id
				WHERE  wi.status = 'YES' AND unitId = 1 AND durationId = 1 AND dc.id = wi.locationId AND wi.locationType = 'DC' AND wi.invoiceDate BETWEEN '{$fromPY}' AND '{$toPY}'		 
				)
				-
				(SELECT COALESCE(SUM(wis.quantity * wis.rate),0) FROM `WasteInvoices` wi	
				INNER JOIN WasteInvoiceServices wis ON wis.invoiceId = wi.id
				WHERE  wi.status = 'YES' AND unitId = 1 AND durationId = 1 AND dc.id = wi.locationId AND wi.locationType = 'DC' AND wi.invoiceDate BETWEEN '{$from}' AND '{$to}'
				)
				)
				-- END OF SAMS
				-- waived fees
				+
				(SELECT COALESCE(SUM(feeAmount),0) FROM `WasteInvoices` wi
					INNER JOIN WasteInvoiceFees wif ON wif.invoiceId = wi.id
					WHERE  wi.status = 'YES' AND waived AND dc.id = wi.locationId AND wi.locationType = 'DC' AND wi.invoiceDate BETWEEN '{$from}' AND '{$to}'			
				)
				+
				(SELECT COALESCE(SUM(feeAmount),0) FROM `RecyclingInvoices` ri
					INNER JOIN RecyclingInvoicesFees rif ON rif.invoiceId = ri.id
					WHERE  ri.status = 'YES' AND waived AND dc.id = ri.locationId AND ri.locationType = 'STORE' AND ri.invoiceDate BETWEEN '{$from}' AND '{$to}'			
				)
				AS value			
				FROM DistributionCenters as dc
				WHERE 1 GROUP BY dc.id";
			
				$query = $this->db->query($sqlQuery);
				$resultPart2 = $query->result();
				
				
				
				$result['savings'] = array_merge($resultPart1, $resultPart2);
				
				$trends = Array();
				$months = Array();		
				for($i = 5; $i >= 0; $i--) {			
					$months[] = ($i ==0)?"Current": date("m/Y", strtotime("-$i month")); 
					$period = "BETWEEN '" . date("Y-m-01", strtotime("-$i month")) . "' AND '" . date("Y-m-31", strtotime("-$i month")) . "'";
								
					$sqlTrendsQuery = "SELECT SUM(ri.total - ri.totalFees - ri.totalWaivedFees)
					-
					(SELECT COALESCE(SUM(`feeAmount`),0) FROM `WasteInvoiceFees` wf
						INNER JOIN WasteInvoices wi ON wi.id = `invoiceId` AND wi.status = 'YES' 
						INNER JOIN Stores ON Stores.id = wi.locationId AND locationType = 'STORE'
						INNER JOIN States ON States.id = Stores.stateId			
						WHERE 1  AND wi.invoiceDate {$period}
					)
					-  
					(SELECT COALESCE(SUM(rci.quantity * rci.pricePerTon),0) FROM RecyclingChargeItems as rci
						INNER JOIN RecyclingCharges rc ON rc.id = rci.recyclingChargeId AND  rc.status = 'YES'
						INNER JOIN WasteInvoices as wi ON wi.id = rc.invoiceNumber AND  wi.status = 'YES'
						INNER JOIN Stores ON Stores.id = rc.locationId AND rc.locationType = 'STORE'
						INNER JOIN States ON States.id = Stores.stateId			
						WHERE 1  AND wi.invoiceDate {$period}
					)			
					AS value, {$what} AS Name  FROM States
					INNER JOIN Stores ON States.id = Stores.stateId
					LEFT JOIN `RecyclingInvoices` ri ON Stores.id = ri.locationId AND locationType = 'STORE' AND ri.invoiceDate {$period}		
					WHERE 1  $groupBy ";
					$res = $this->db->query($sqlTrendsQuery);					
					foreach($res->result() as $record) {												
						$trends[$record->Name][] = (float)$record->value?(float)$record->value:0;
					}												
				}
				
				$trends = Array();
				$months = Array();		
				for($i = 5; $i >= 0; $i--) {			
					$months[] = ($i ==0)?"Current": date("m/Y", strtotime("-$i month")); 
					$period = "BETWEEN '" . date("Y-m-01", strtotime("-$i month")) . "' AND '" . date("Y-m-31", strtotime("-$i month")) . "'";
								
					$sqlTrendsQuery = "SELECT SUM(ri.total - ri.totalFees - ri.totalWaivedFees)
					-
					(SELECT COALESCE(SUM(`feeAmount`),0) FROM `WasteInvoiceFees` wf
						INNER JOIN WasteInvoices wi ON wi.id = `invoiceId` AND  wi.status = 'YES' 
						INNER JOIN DistributionCenters as dcc ON dcc.id = wi.locationId AND locationType = 'DC'			
						WHERE 1 AND wi.invoiceDate {$period}
					)
					-  
					(SELECT COALESCE(SUM(rci.quantity * rci.pricePerTon),0) FROM RecyclingChargeItems as rci
						INNER JOIN RecyclingCharges rc ON rc.id = rci.recyclingChargeId AND  rc.status = 'YES'
						INNER JOIN WasteInvoices as wi ON wi.id = rc.invoiceNumber AND  wi.status = 'YES'
						INNER JOIN DistributionCenters as dcc ON dcc.id = rc.locationId AND rc.locationType = 'DC'
						WHERE 1 AND wi.invoiceDate {$period}
					)			
					AS value, dc.name AS Name  FROM DistributionCenters  as dc
					LEFT JOIN `RecyclingInvoices` ri ON  ri.status = 'YES' AND dc.id = ri.locationId AND locationType = 'DC' AND ri.invoiceDate {$period}			
					WHERE 1 GROUP BY dc.id ";
					$res = $this->db->query($sqlTrendsQuery);					
					foreach($res->result() as $record) {												
						$trends[$record->Name][] = (float)$record->value?(float)$record->value:0;
					}												
				}		

				$result['trend'] = Array('months'=>json_encode($months, JSON_NUMERIC_CHECK),
					'regions'=>json_encode(array_keys($trends)), 
					'data'=>json_encode(array_values($trends), JSON_NUMERIC_CHECK)
				);
				
				$sqlQuery = "SELECT 'REGION' as type, Stores.id AS id, States.region AS region, district, location, open24hours, squareFootage,
					(SELECT SUM(feeAmount) FROM WasteInvoiceFees wif
					INNER JOIN WasteInvoices wi ON wif.invoiceId = wi.id AND wi.status = 'YES' 			
					WHERE Stores.id = wi.locationId AND wi.locationType = 'STORE' AND wi.invoiceDate BETWEEN '{$from}' AND '{$to}') AS WasteService,
					(SELECT SUM(feeAmount) FROM WasteInvoiceFees wif 
					INNER JOIN WasteInvoices wi ON wif.invoiceId = wi.id  AND wi.status = 'YES'
					WHERE Stores.id = wi.locationId AND wi.locationType = 'STORE' AND wi.invoiceDate BETWEEN '{$from}' AND '{$to}' AND 6 = feeType) AS WasteEquipmentFee,
					(SELECT SUM(feeAmount) FROM WasteInvoiceFees wif 
					INNER JOIN WasteInvoices wi ON wif.invoiceId = wi.id AND wi.status = 'YES'
					WHERE Stores.id = wi.locationId AND wi.locationType = 'STORE' AND wi.invoiceDate BETWEEN '{$from}' AND '{$to}'AND 1 = feeType) AS WasteHaulFee,
					(SELECT SUM(feeAmount) FROM WasteInvoiceFees wif 
					INNER JOIN WasteInvoices wi ON wif.invoiceId = wi.id AND wi.status = 'YES'
					WHERE Stores.id = wi.locationId AND wi.locationType = 'STORE' AND wi.invoiceDate BETWEEN '{$from}' AND '{$to}'AND feeType NOT IN (1,2,3,4,5,6)) AS WasteDisposalFee,
					SUM(quantity*pricePerUnit) AS RecyclingRebate,
					(SELECT SUM(feeAmount) FROM WasteInvoiceFees WHERE WasteInvoiceFees.invoiceId = ri.id AND feeType NOT IN (1,6)) AS OtherFee,
					SUM(quantity*pricePerUnit)
					-
					(SELECT COALESCE(SUM(`feeAmount`),0) FROM `WasteInvoiceFees` wf
						INNER JOIN WasteInvoices wi ON wi.id = `invoiceId` AND wi.status = 'YES'
						INNER JOIN Stores ON Stores.id = wi.locationId AND locationType = 'STORE'
						INNER JOIN States ON States.id = Stores.stateId			
						WHERE 1 {$whereStateRegion} AND ri.invoiceDate BETWEEN '{$from}' AND '{$to}' {$tableWhere}
					)
					-  
					(SELECT COALESCE(SUM(rci.quantity * rci.pricePerTon),0) FROM RecyclingChargeItems as rci
						INNER JOIN RecyclingCharges rc ON rc.id = rci.recyclingChargeId AND rc.status = 'YES'
						INNER JOIN Stores ON Stores.id = rc.locationId AND rc.locationType = 'STORE'
						INNER JOIN States ON States.id = Stores.stateId			
						WHERE 1 {$whereStateRegion} AND ri.invoiceDate BETWEEN '{$from}' AND '{$to}' {$tableWhere}
					) AS net,
					SUM(quantity) AS TotalTonnage					
					FROM `RecyclingInvoices` ri	
					INNER JOIN Stores ON Stores.id = ri.locationId AND locationType = 'STORE'
					INNER JOIN RecyclingInvoicesMaterials rim ON rim.invoiceId = ri.id
					INNER JOIN States ON States.id = Stores.stateId
					WHERE ri.status = 'YES' AND rim.unit = 1 AND (ri.invoiceDate BETWEEN '{$from}' AND '{$to}') {$tableWhere} GROUP BY Stores.id {$tableOrder} ASC";
				$res = $this->db->query($sqlQuery)->result();

				$sqlQuery = "SELECT 'DC' as type, dc.id AS id, dc.name as location, dc.name as region, squareFootage,
					(
						SELECT SUM(feeAmount) FROM WasteInvoiceFees wif
						INNER JOIN WasteInvoices wi ON wif.invoiceId = wi.id AND wi.status = 'YES' 			
						WHERE dc.id = wi.locationId AND wi.locationType = 'DC' AND wi.invoiceDate BETWEEN '{$from}' AND '{$to}'
					) AS WasteService,
					(
						SELECT SUM(feeAmount) FROM WasteInvoiceFees wif 
						INNER JOIN WasteInvoices wi ON wif.invoiceId = wi.id  AND wi.status = 'YES'
						WHERE dc.id = wi.locationId AND wi.locationType = 'DC' AND wi.invoiceDate BETWEEN '{$from}' AND '{$to}' AND 6 = feeType
					) AS WasteEquipmentFee,
					(
						SELECT SUM(feeAmount) FROM WasteInvoiceFees wif 
						INNER JOIN WasteInvoices wi ON wif.invoiceId = wi.id AND wi.status = 'YES'
						WHERE dc.id = wi.locationId AND wi.locationType = 'DC' AND wi.invoiceDate BETWEEN '{$from}' AND '{$to}'AND 1 = feeType
					) AS WasteHaulFee,
					(
						SELECT SUM(feeAmount) FROM WasteInvoiceFees wif 
						INNER JOIN WasteInvoices wi ON wif.invoiceId = wi.id AND wi.status = 'YES'
						WHERE dc.id = wi.locationId AND wi.locationType = 'DC' AND wi.invoiceDate BETWEEN '{$from}' AND '{$to}'AND feeType NOT IN (1,2,3,4,5,6)
					) AS WasteDisposalFee,
					SUM(quantity*pricePerUnit) AS RecyclingRebate,
					(
						SELECT SUM(feeAmount) FROM WasteInvoiceFees WHERE WasteInvoiceFees.invoiceId = ri.id AND feeType NOT IN (1,6)
					) AS OtherFee,
					
					SUM(quantity*pricePerUnit)
					-
					(
						SELECT COALESCE(SUM(`feeAmount`),0) FROM `WasteInvoiceFees` wf
						INNER JOIN WasteInvoices wi ON wi.id = `invoiceId` AND wi.status = 'YES'
						INNER JOIN DistributionCenters as dc ON dc.id = wi.locationId AND locationType = 'DC'			
						WHERE 1  AND ri.invoiceDate BETWEEN '{$from}' AND '{$to}' {$tableWhere}
					)
					-  
					(
						SELECT COALESCE(SUM(rci.quantity * rci.pricePerTon),0) FROM RecyclingChargeItems as rci
						INNER JOIN RecyclingCharges rc ON rc.id = rci.recyclingChargeId AND rc.status = 'YES'
						INNER JOIN DistributionCenters as dc ON dc.id = rc.locationId AND rc.locationType = 'DC'
						WHERE 1 AND ri.invoiceDate BETWEEN '{$from}' AND '{$to}' {$tableWhere}
					) AS net,
					
					SUM(quantity) AS TotalTonnage	
					FROM DistributionCenters as dc
					-- FROM `RecyclingInvoices` ri	
					INNER JOIN RecyclingInvoices as ri ON dc.id = ri.locationId AND locationType = 'DC'
					INNER JOIN RecyclingInvoicesMaterials rim ON rim.invoiceId = ri.id
					WHERE ri.status = 'YES' AND rim.unit = 1 AND (ri.invoiceDate BETWEEN '{$from}' AND '{$to}') {$tableWhere} GROUP BY dc.id ORDER BY dc.name ASC";
				$res2 = $this->db->query($sqlQuery)->result();
				
				$result['table'] = array_merge($res, $res2);
				
				break;
			case 2:
				$sqlQuery = "SELECT 'DC' as type, dc.name AS Name, 
					(SELECT COALESCE(SUM(quantity*pricePerUnit),0) FROM `RecyclingInvoices` ri
					INNER JOIN RecyclingInvoicesMaterials rim ON rim.invoiceId = ri.id
					INNER JOIN Materials m ON m.id = rim.materialId			
					WHERE  ri.status = 'YES' AND rim.unit = 1 AND dc.id = ri.locationId AND ri.locationType = 'DC' AND ri.invoiceDate BETWEEN '{$from}' AND '{$to}'
					)  
					+
					-- SAMS 
					( 
					(SELECT COALESCE(SUM(wis.quantity * wis.rate),0) FROM `WasteInvoices` wi	
					INNER JOIN WasteInvoiceServices wis ON wis.invoiceId = wi.id
					WHERE  wi.status = 'YES' AND unitId = 1 AND durationId = 1 AND dc.id = wi.locationId AND wi.locationType = 'DC' AND wi.invoiceDate BETWEEN '{$fromPY}' AND '{$toPY}'		 
					)
					-
					(SELECT COALESCE(SUM(wis.quantity * wis.rate),0) FROM `WasteInvoices` wi	
					INNER JOIN WasteInvoiceServices wis ON wis.invoiceId = wi.id
					WHERE  wi.status = 'YES' AND unitId = 1 AND durationId = 1 AND dc.id = wi.locationId AND wi.locationType = 'DC' AND wi.invoiceDate BETWEEN '{$from}' AND '{$to}'
					)
					)
					-- END OF SAMS
					-- waived fees
					+
					(SELECT COALESCE(SUM(feeAmount),0) FROM `WasteInvoices` wi
						INNER JOIN WasteInvoiceFees wif ON wif.invoiceId = wi.id
						WHERE  wi.status = 'YES' AND waived AND dc.id = wi.locationId AND wi.locationType = 'DC' AND wi.invoiceDate BETWEEN '{$from}' AND '{$to}'			
					)
					+
					(SELECT COALESCE(SUM(feeAmount),0) FROM `RecyclingInvoices` ri
						INNER JOIN RecyclingInvoicesFees rif ON rif.invoiceId = ri.id
						WHERE  ri.status = 'YES' AND waived AND dc.id = ri.locationId AND ri.locationType = 'STORE' AND ri.invoiceDate BETWEEN '{$from}' AND '{$to}'			
					)
					AS value			
					FROM DistributionCenters as dc
					WHERE 1 GROUP BY dc.id";
				
				$query = $this->db->query($sqlQuery);
				$result['savings'] = $query->result();
				
				$trends = Array();
				$months = Array();		
				for($i = 5; $i >= 0; $i--) {			
					$months[] = ($i ==0)?"Current": date("m/Y", strtotime("-$i month")); 
					$period = "BETWEEN '" . date("Y-m-01", strtotime("-$i month")) . "' AND '" . date("Y-m-31", strtotime("-$i month")) . "'";
								
					$sqlTrendsQuery = "SELECT SUM(ri.total - ri.totalFees - ri.totalWaivedFees)
					-
					(SELECT COALESCE(SUM(`feeAmount`),0) FROM `WasteInvoiceFees` wf
						INNER JOIN WasteInvoices wi ON wi.id = `invoiceId` AND  wi.status = 'YES' 
						INNER JOIN DistributionCenters as dcc ON dcc.id = wi.locationId AND locationType = 'DC'			
						WHERE 1 AND wi.invoiceDate {$period}
					)
					-  
					(SELECT COALESCE(SUM(rci.quantity * rci.pricePerTon),0) FROM RecyclingChargeItems as rci
						INNER JOIN RecyclingCharges rc ON rc.id = rci.recyclingChargeId AND  rc.status = 'YES'
						INNER JOIN WasteInvoices as wi ON wi.id = rc.invoiceNumber AND  wi.status = 'YES'
						INNER JOIN DistributionCenters as dcc ON dcc.id = rc.locationId AND rc.locationType = 'DC'
						WHERE 1 AND wi.invoiceDate {$period}
					)			
					AS value, dc.name AS Name  FROM DistributionCenters  as dc
					LEFT JOIN `RecyclingInvoices` ri ON  ri.status = 'YES' AND dc.id = ri.locationId AND locationType = 'DC' AND ri.invoiceDate {$period}			
					WHERE 1 GROUP BY dc.id ";
					$res = $this->db->query($sqlTrendsQuery);					
					foreach($res->result() as $record) {												
						$trends[$record->Name][] = (float)$record->value?(float)$record->value:0;
					}												
				}		

				$result['trend'] = Array('months'=>json_encode($months, JSON_NUMERIC_CHECK),
					'regions'=>json_encode(array_keys($trends)), 
					'data'=>json_encode(array_values($trends), JSON_NUMERIC_CHECK)
				);
				
				$sqlQuery = "SELECT 'DC' as type, dc.id AS id, dc.name as location, dc.name as region, squareFootage,
					(
						SELECT SUM(feeAmount) FROM WasteInvoiceFees wif
						INNER JOIN WasteInvoices wi ON wif.invoiceId = wi.id AND wi.status = 'YES' 			
						WHERE dc.id = wi.locationId AND wi.locationType = 'DC' AND wi.invoiceDate BETWEEN '{$from}' AND '{$to}'
					) AS WasteService,
					(
						SELECT SUM(feeAmount) FROM WasteInvoiceFees wif 
						INNER JOIN WasteInvoices wi ON wif.invoiceId = wi.id  AND wi.status = 'YES'
						WHERE dc.id = wi.locationId AND wi.locationType = 'DC' AND wi.invoiceDate BETWEEN '{$from}' AND '{$to}' AND 6 = feeType
					) AS WasteEquipmentFee,
					(
						SELECT SUM(feeAmount) FROM WasteInvoiceFees wif 
						INNER JOIN WasteInvoices wi ON wif.invoiceId = wi.id AND wi.status = 'YES'
						WHERE dc.id = wi.locationId AND wi.locationType = 'DC' AND wi.invoiceDate BETWEEN '{$from}' AND '{$to}'AND 1 = feeType
					) AS WasteHaulFee,
					(
						SELECT SUM(feeAmount) FROM WasteInvoiceFees wif 
						INNER JOIN WasteInvoices wi ON wif.invoiceId = wi.id AND wi.status = 'YES'
						WHERE dc.id = wi.locationId AND wi.locationType = 'DC' AND wi.invoiceDate BETWEEN '{$from}' AND '{$to}'AND feeType NOT IN (1,2,3,4,5,6)
					) AS WasteDisposalFee,
					SUM(quantity*pricePerUnit) AS RecyclingRebate,
					(
						SELECT SUM(feeAmount) FROM WasteInvoiceFees WHERE WasteInvoiceFees.invoiceId = ri.id AND feeType NOT IN (1,6)
					) AS OtherFee,
					
					SUM(quantity*pricePerUnit)
					-
					(
						SELECT COALESCE(SUM(`feeAmount`),0) FROM `WasteInvoiceFees` wf
						INNER JOIN WasteInvoices wi ON wi.id = `invoiceId` AND wi.status = 'YES'
						INNER JOIN DistributionCenters as dc ON dc.id = wi.locationId AND locationType = 'DC'			
						WHERE 1  AND ri.invoiceDate BETWEEN '{$from}' AND '{$to}' {$tableWhere}
					)
					-  
					(
						SELECT COALESCE(SUM(rci.quantity * rci.pricePerTon),0) FROM RecyclingChargeItems as rci
						INNER JOIN RecyclingCharges rc ON rc.id = rci.recyclingChargeId AND rc.status = 'YES'
						INNER JOIN DistributionCenters as dc ON dc.id = rc.locationId AND rc.locationType = 'DC'
						WHERE 1 AND ri.invoiceDate BETWEEN '{$from}' AND '{$to}' {$tableWhere}
					) AS net,
					
					SUM(quantity) AS TotalTonnage	
					FROM DistributionCenters as dc
					-- FROM `RecyclingInvoices` ri	
					INNER JOIN RecyclingInvoices as ri ON dc.id = ri.locationId AND locationType = 'DC'
					INNER JOIN RecyclingInvoicesMaterials rim ON rim.invoiceId = ri.id
					WHERE ri.status = 'YES' AND rim.unit = 1 AND (ri.invoiceDate BETWEEN '{$from}' AND '{$to}') {$tableWhere} GROUP BY dc.id ORDER BY dc.name ASC";
				$res2 = $this->db->query($sqlQuery)->result();
				
				$result['table'] = $res2;
				
				break;
			case 3:
				$sqlQuery = "SELECT 'REGION' as type,  {$what} AS Name, 
					(SELECT COALESCE(SUM(quantity*pricePerUnit),0) FROM `RecyclingInvoices` ri
					INNER JOIN RecyclingInvoicesMaterials rim ON rim.invoiceId = ri.id
					INNER JOIN Materials m ON m.id = rim.materialId			
					WHERE  ri.status = 'YES' AND unit = 1 AND Stores.id = ri.locationId AND ri.locationType = 'STORE' AND ri.invoiceDate BETWEEN '{$from}' AND '{$to}'
					)  
					+
					-- SAMS 
					( 
					(SELECT COALESCE(SUM(wis.quantity * wis.rate),0) FROM `WasteInvoices` wi	
					INNER JOIN WasteInvoiceServices wis ON wis.invoiceId = wi.id
					WHERE wi.status = 'YES' AND unitId = 1 AND durationId = 1 AND Stores.id = wi.locationId AND wi.locationType = 'STORE' AND wi.invoiceDate BETWEEN '{$fromPY}' AND '{$toPY}'		 
					)
					-
					(SELECT COALESCE(SUM(wis.quantity * wis.rate),0) FROM `WasteInvoices` wi	
					INNER JOIN WasteInvoiceServices wis ON wis.invoiceId = wi.id
					WHERE wi.status = 'YES' AND unitId = 1 AND durationId = 1 AND Stores.id = wi.locationId AND wi.locationType = 'STORE' AND wi.invoiceDate BETWEEN '{$from}' AND '{$to}'
					)
					)
					-- END OF SAMS
					-- waived fees
					+
					(SELECT COALESCE(SUM(feeAmount),0) FROM `WasteInvoices` wi
						INNER JOIN WasteInvoiceFees wif ON wif.invoiceId = wi.id
						WHERE wi.status = 'YES' AND waived AND Stores.id = wi.locationId AND wi.locationType = 'STORE' AND wi.invoiceDate BETWEEN '{$from}' AND '{$to}'			
					)
					+
					(SELECT COALESCE(SUM(feeAmount),0) FROM `RecyclingInvoices` ri
						INNER JOIN RecyclingInvoicesFees rif ON rif.invoiceId = ri.id
						WHERE ri.status = 'YES' AND waived AND Stores.id = ri.locationId AND ri.locationType = 'STORE' AND ri.invoiceDate BETWEEN '{$from}' AND '{$to}'			
					)
					AS value			
					FROM Stores
					INNER JOIN States ON States.id = Stores.stateId
					WHERE 1 GROUP BY States.region";
					
				$query = $this->db->query($sqlQuery);
				$result['savings'] = $query->result();
				
				$trends = Array();
				$months = Array();		
				for($i = 5; $i >= 0; $i--) {			
					$months[] = ($i ==0)?"Current": date("m/Y", strtotime("-$i month")); 
					$period = "BETWEEN '" . date("Y-m-01", strtotime("-$i month")) . "' AND '" . date("Y-m-31", strtotime("-$i month")) . "'";
								
					$sqlTrendsQuery = "SELECT SUM(ri.total - ri.totalFees - ri.totalWaivedFees)
					-
					(SELECT COALESCE(SUM(`feeAmount`),0) FROM `WasteInvoiceFees` wf
						INNER JOIN WasteInvoices wi ON wi.id = `invoiceId` AND wi.status = 'YES' 
						INNER JOIN Stores ON Stores.id = wi.locationId AND locationType = 'STORE'
						INNER JOIN States ON States.id = Stores.stateId			
						WHERE 1  AND wi.invoiceDate {$period}
					)
					-  
					(SELECT COALESCE(SUM(rci.quantity * rci.pricePerTon),0) FROM RecyclingChargeItems as rci
						INNER JOIN RecyclingCharges rc ON rc.id = rci.recyclingChargeId AND  rc.status = 'YES'
						INNER JOIN WasteInvoices as wi ON wi.id = rc.invoiceNumber AND  wi.status = 'YES'
						INNER JOIN Stores ON Stores.id = rc.locationId AND rc.locationType = 'STORE'
						INNER JOIN States ON States.id = Stores.stateId			
						WHERE 1  AND wi.invoiceDate {$period}
					)			
					AS value, {$what} AS Name  FROM States
					INNER JOIN Stores ON States.id = Stores.stateId
					LEFT JOIN `RecyclingInvoices` ri ON Stores.id = ri.locationId AND locationType = 'STORE' AND ri.invoiceDate {$period}		
					WHERE 1  $groupBy ";
					$res = $this->db->query($sqlTrendsQuery);					
					foreach($res->result() as $record) {												
						$trends[$record->Name][] = (float)$record->value?(float)$record->value:0;
					}												
				}
				
				$sqlQuery = "SELECT 'REGION' as type, Stores.id AS id, States.region AS region, district, location, open24hours, squareFootage,
					(SELECT SUM(feeAmount) FROM WasteInvoiceFees wif
					INNER JOIN WasteInvoices wi ON wif.invoiceId = wi.id AND wi.status = 'YES' 			
					WHERE Stores.id = wi.locationId AND wi.locationType = 'STORE' AND wi.invoiceDate BETWEEN '{$from}' AND '{$to}') AS WasteService,
					(SELECT SUM(feeAmount) FROM WasteInvoiceFees wif 
					INNER JOIN WasteInvoices wi ON wif.invoiceId = wi.id  AND wi.status = 'YES'
					WHERE Stores.id = wi.locationId AND wi.locationType = 'STORE' AND wi.invoiceDate BETWEEN '{$from}' AND '{$to}' AND 6 = feeType) AS WasteEquipmentFee,
					(SELECT SUM(feeAmount) FROM WasteInvoiceFees wif 
					INNER JOIN WasteInvoices wi ON wif.invoiceId = wi.id AND wi.status = 'YES'
					WHERE Stores.id = wi.locationId AND wi.locationType = 'STORE' AND wi.invoiceDate BETWEEN '{$from}' AND '{$to}'AND 1 = feeType) AS WasteHaulFee,
					(SELECT SUM(feeAmount) FROM WasteInvoiceFees wif 
					INNER JOIN WasteInvoices wi ON wif.invoiceId = wi.id AND wi.status = 'YES'
					WHERE Stores.id = wi.locationId AND wi.locationType = 'STORE' AND wi.invoiceDate BETWEEN '{$from}' AND '{$to}'AND feeType NOT IN (1,2,3,4,5,6)) AS WasteDisposalFee,
					SUM(quantity*pricePerUnit) AS RecyclingRebate,
					(SELECT SUM(feeAmount) FROM WasteInvoiceFees WHERE WasteInvoiceFees.invoiceId = ri.id AND feeType NOT IN (1,6)) AS OtherFee,
					SUM(quantity*pricePerUnit)
					-
					(SELECT COALESCE(SUM(`feeAmount`),0) FROM `WasteInvoiceFees` wf
						INNER JOIN WasteInvoices wi ON wi.id = `invoiceId` AND wi.status = 'YES'
						INNER JOIN Stores ON Stores.id = wi.locationId AND locationType = 'STORE'
						INNER JOIN States ON States.id = Stores.stateId			
						WHERE 1  AND ri.invoiceDate BETWEEN '{$from}' AND '{$to}' {$tableWhere}
					)
					-  
					(SELECT COALESCE(SUM(rci.quantity * rci.pricePerTon),0) FROM RecyclingChargeItems as rci
						INNER JOIN RecyclingCharges rc ON rc.id = rci.recyclingChargeId AND rc.status = 'YES'
						INNER JOIN Stores ON Stores.id = rc.locationId AND rc.locationType = 'STORE'
						INNER JOIN States ON States.id = Stores.stateId			
						WHERE 1  AND ri.invoiceDate BETWEEN '{$from}' AND '{$to}' {$tableWhere}
					) AS net,
					SUM(quantity) AS TotalTonnage					
					FROM `RecyclingInvoices` ri	
					INNER JOIN Stores ON Stores.id = ri.locationId AND locationType = 'STORE'
					INNER JOIN RecyclingInvoicesMaterials rim ON rim.invoiceId = ri.id
					INNER JOIN States ON States.id = Stores.stateId
					WHERE ri.status = 'YES' AND rim.unit = 1 AND (ri.invoiceDate BETWEEN '{$from}' AND '{$to}') {$tableWhere} GROUP BY Stores.id {$tableOrder} ASC";
				$res = $this->db->query($sqlQuery)->result();
				$result['table'] = $res;
				break;
		}
		

		return $result;
	}
	
	
	function loadCostFromDB($startDate, $endDate, $locationId, $locationType = 'DC') {
		$conditions = "";
		$conditionsRi = "";
		
		if($startDate) {
			$conditions .= " AND wi.invoiceDate >= '" . $startDate . "'";
			$conditionsRi .= " AND ri.invoiceDate >= '" . $startDate . "'";
		}
		
		if($endDate) {
			$conditions .= " AND wi.invoiceDate <= '" . $endDate . "'";
			$conditionsRi .= " AND ri.invoiceDate <= '" . $endDate . "'";
		}
		
		if($locationId) {
			$conditions .= " AND locationId = " . (int) $locationId . " ";
		}
		
		$allSum = array("sqft" => 0, "t" => 0, "ws" => 0, "we" => 0, "wh" => 0, "wd" => 0, "rr" => 0, "o" => 0, "n" => 0);
		$costGraph = array();
		$returnArray = array();
		
		
		$wsData = $this->db->query("SELECT wi.*, ws.quantity, ws.rate as trashFee,  srst.name AS sname,
				wf.feeType, wf.feeAmount, squareFootage AS sqft,
				
				 (SELECT SUM(quantity*rate) 
                    FROM WasteInvoiceServices 
                            WHERE wi.id = WasteInvoiceServices.invoiceID AND 
                            date_format(serviceDate, '%Y') = date_format(now(), '%Y')) AS WTY,

    
                  (SELECT SUM(quantity*rate) 
                    FROM WasteInvoiceServices 
                            WHERE wi.id = WasteInvoiceServices.invoiceID AND 
                            date_format(serviceDate, '%Y') = date_format(date_sub(now(), INTERVAL 1 YEAR), '%Y')) AS WLY				
				
				FROM WasteInvoices as wi
				LEFT OUTER JOIN DistributionCenters AS dc ON dc.id = wi.locationId
				LEFT OUTER JOIN WasteInvoiceServices as ws ON wi.id = ws.invoiceId
				LEFT OUTER JOIN SupportRequestServiceTypes AS srst ON ws.serviceId = srst.id
				LEFT OUTER JOIN WasteInvoiceFees AS wf ON wi.id = wf.invoiceId
				WHERE wi.locationType = '$locationType' AND unitID = 1 " . $conditions);
		
		
		//Стари данни

		$rcData = $this->db->query("SELECT ri.*,
				dc.name AS locationName, dc.number AS dnumber, dc.id AS id, squareFootage as sqft,
				quantity, pricePerUnit,
				(SELECT SUM(feeAmount) FROM RecyclingInvoicesFees AS rif WHERE ri.id = rif.invoiceId) AS rif
				FROM RecyclingInvoices as ri
				LEFT OUTER JOIN RecyclingInvoicesMaterials as rim ON rim.invoiceId = ri.id
				LEFT OUTER JOIN DistributionCenters AS dc ON dc.id = ri.locationId
				WHERE ri.locationType = '$locationType' AND rim.unit = 1 " . $conditionsRi);		
		
		return array($wsData->result("array"), $rcData->result("array"));
		
		//return array(array(), array());
	}
	
}
