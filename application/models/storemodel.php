<?php
include_once 'basemodel.php';

class StoreModel extends BaseModel {
	private $table = 'Stores';
	public function __construct() {
		parent::__construct();
		$this->load->helper('dates');
		$this->load->database();
		$this->load->model('CompanyModel');
	}

	public function getCurrentMonthData() {		
		/*$currentYear = date('Y');
		$currentMonth = date('m');*/
		//return $this->getDataByInterval($currentMonth, $currentYear, $currentMonth, $currentYear);
		$dates = getPeriods('CurrentMonth');
		return $this->getDataByInterval($dates['startMonth'], $dates['startYear'], $dates['endMonth'], $dates['endYear']);		
	}

	public function getPriorMonthData($store_id = 0) {
		// $newdate = strtotime ( '-1 month');
		// $startMonth = 1;
		// $startYear = 2017;
		// $endMonth = 1;
		// $endYear = 2017;
		// return $this->getDataByInterval($startMonth, $startYear, $endMonth, $endYear,$store_id);
		$dates = getPeriods('PriorMonth');
		return $this->getDataByInterval($dates['startMonth'], $dates['startYear'], $dates['endMonth'], $dates['endYear'],$store_id);
	}

	public function getPriorQuarterData() {
		/*$newdate = strtotime ( '-3 month');
		$startMonth = date('m', $newdate);
		$startYear = date('Y', $newdate);

		$endMonth = date('m');
		$endYear = date('Y');

		return $this->getDataByInterval($startMonth, $startYear, $endMonth, $endYear, 3);*/
		$dates = getPeriods('PriorQuarter');
		return $this->getDataByInterval($dates['startMonth'], $dates['startYear'], $dates['endMonth'], $dates['endYear'], 3);
	}

	public function getSixMonthsData() {
		/*$newdate = strtotime ( '-6 month');
		$startMonth = date('m', $newdate);
		$startYear = date('Y', $newdate);

		$endMonth = date('m');
		$endYear = date('Y');

		return $this->getDataByInterval($startMonth, $startYear, $endMonth, $endYear, 6);*/
		$dates = getPeriods('SixMonths');
		return $this->getDataByInterval($dates['startMonth'], $dates['startYear'], $dates['endMonth'], $dates['endYear'], 6);		
	}


	public function getAllStores() {
		$retunData = array();
		$allDC = $this->db->query("SELECT * FROM `Stores`");
		foreach ($allDC->result("array") as $temp) {
			$retunData[$temp["id"]] = $temp;
		}
		return $retunData;
	}


	public function getLastYearData() {
		/*$newdate = strtotime ( '-1 year');
		$startMonth = 1;
		$startYear = date('Y', $newdate);

		$endMonth = 12;
		$endYear = date('Y', $newdate);

		return $this->getDataByInterval($startMonth, $startYear, $endMonth, $endYear, 12);*/
		$dates = getPeriods('LastYear');
		return $this->getDataByInterval($dates['startMonth'], $dates['startYear'], $dates['endMonth'], $dates['endYear'], 12);		
	}
	
	public function getPrior2MonthsBackData($store_id = 0) {
	    $dates = getPeriods('2MonthsBack');
	    return $this->getDataByInterval($dates['startMonth'], $dates['startYear'], $dates['endMonth'], $dates['endYear'],$store_id);	    
	}

	private function getDataByInterval($startMonth, $startYear, $endMonth, $endYear,$storeId = 0) {
		$result = new MonthData();
		
		$between = '"'.$startYear.'-'.$startMonth.'-01" AND "'.$endYear.'-'.$endMonth.'-31"';
 
		//If drop down is seleted then location id will be added to the query
		if($storeId>0) {
            $storeIdQuery = ' AND wi.locationId = "'.$storeId.'"';
        } else
        	$storeIdQuery = ' ';

		/*
    	*  Commented By Palini Pulusu ON June 15th 2017
    	*/
		/*
		$costQuery = 'SELECT SUM(s) AS s FROM (
		    SELECT SUM(wis.quantity * wis.rate) AS s
		    FROM WasteInvoices AS wi LEFT OUTER JOIN WasteInvoiceServices AS wis ON wis.invoiceId = wi.id
		    WHERE
		      wi.locationType = "STORE"
		      AND (wi.invoiceDate BETWEEN '.$between.')
		    UNION
		    SELECT SUM(wif.feeAmount) AS s
		    FROM WasteInvoices wi LEFT JOIN WasteInvoiceFees wif ON wif.invoiceId = wi.id
		    WHERE
		      wi.locationType = "STORE"
		      AND (wi.invoiceDate BETWEEN '.$between.')
		      AND wif.feeType IN (SELECT id FROM `FeeType` WHERE `name` <> "Rebate")) ttt';

		      if($storeId>0) {
     	       	$costQuery .= ' AND wi.locationId = '.$storeId.' ';
     		 }
		*/
        $costQuery = "
            				SELECT SUM(wi.total) AS 'Cost'
            				FROM WasteInvoices wi
            				INNER JOIN WasteInvoiceServices wis
            				ON wi.id = wis.invoiceid
            				WHERE wi.invoiceDate 
            				BETWEEN '$startYear-$startMonth-01' 
            				AND '$endYear-$endMonth-31' 
            				$storeIdQuery
        				";

		$query = $this->db->query($costQuery);
		$result->cost = $query->row()->Cost;

		/*
    	*  Commented By Palini Pulusu ON June 15th 2017
    	*/
		/*
		$result->waste = $query->row()->Waste;


			$wasteRecyclingQuery = 'SELECT category, SUM(quantity * weightInLbs * IFNULL(pickupsPerMonth, 1))/'.TON_KOEFF.' AS s
		    FROM WasteInvoices as wi LEFT OUTER JOIN WasteInvoiceServices AS wis ON wis.invoiceId = wi.id 
		    LEFT OUTER JOIN Containers AS c ON c.id = wis.containerId
		    LEFT OUTER JOIN monthlyPickups m on m.id = wis.schedule  
		    WHERE wi.locationType = "STORE" AND (wi.invoiceDate BETWEEN '.$between.') 
		    GROUP BY category';
		*/
		$wasteRecyclingQuery = '
								SELECT category, SUM(quantity * weightInLbs * ifnull(pickupsPerMonth, 1)) AS s
		    					FROM WasteInvoices as wi 
		    					LEFT OUTER JOIN WasteInvoiceServices AS wis 
		    					ON wis.invoiceId = wi.id 
		    					LEFT OUTER JOIN Containers AS c 
		    					ON c.id = wis.containerId
		    					LEFT OUTER JOIN monthlyPickups m 
		    					on m.id = wis.schedule  
		    					WHERE wi.locationType = "STORE" '.$storeIdQuery.' AND (wi.invoiceDate BETWEEN '.$between.') 
		    					GROUP BY category';
		

		$query = $this->db->query($wasteRecyclingQuery, true);

		$result->waste = 0;
		$result->recycling = 0;
		foreach($query->result() as $row) {
		    if($row->category==0) {
				$result->waste = $row->s;
		    }
		    if($row->category==1) {
				$result->recycling = $row->s;
		    }			    
		}
		/*
    	*  Commented By Palini Pulusu ON June 15th 2017
    	*/
    	/*
		$query = $this->db->query('SELECT (SUM(quantity * weightInLbs * ifnull(pickupsPerMonth, 1)) / '.TON_KOEFF.') * FLOOR((DATEDIFF("'.$endYear.'-'.$endMonth.'-31", "'.$startYear.'-'.$startMonth.'-01") + 1) / 7) AS s
			FROM
			  VendorServices vs
			LEFT OUTER JOIN Containers c
			ON c.id = vs.containerId
			LEFT OUTER JOIN monthlyPickups mp
			ON mp.id = vs.schedule
			INNER JOIN (SELECT DISTINCT (id)
				    FROM
				      VendorServices vs
				    WHERE
				      (curdate() < vs.enddate
				      OR vs.enddate = "0000-00-00")
				      AND locationType = "Store"
				    GROUP BY
				      locationId
				    , vendorId
				    HAVING
				      sum(rate) = 0) vs2
			ON vs2.id = vs.id
			WHERE
			  category = 1');
				$result->recycling += $query->row()->s;

		$query = $this->db->query('
		SELECT SUM(wif.feeAmount) AS s FROM WasteInvoices wi
		LEFT JOIN WasteInvoiceFees wif ON wif.invoiceId = wi.id
		WHERE wi.locationType = "STORE" AND
		(wi.invoiceDate BETWEEN '.$between.')
		AND wif.feeType IN (SELECT id FROM `FeeType` WHERE `name`="Rebate")');


		$sql = "
              SELECT SUM(wif.feeAmount) AS s FROM WasteInvoices wi
            LEFT JOIN WasteInvoiceFees wif ON wif.invoiceId = wi.id
              WHERE wi.locationType = "STORE" 
              AND (wi.invoiceDate BETWEEN '.$between.')
              LEFT JOIN RecyclingInvoicesFees rif
				AND wif.feeType IN (SELECT id FROM `FeeType` WHERE `name`="Rebate" ";

		if($storeId > 0) {
        	$sql .= ' AND wi.locationId = "'.$storeId.'"';
    	}
		*/
		$sql = "
					SELECT sum(wif.`feeAmount`) AS rebate 
					FROM WasteInvoices wi,`WasteInvoiceFees` wif 
					INNER JOIN FeeType  
					ON wif.feeType = FeeType.id 
					WHERE FeeType.name = 'Rebate'
					AND wi.id = wif.invoiceId 
					AND (wi.invoiceDate BETWEEN $between) 
					$storeIdQuery
				";
    	$query = $this->db->query($sql);
		$result->rebate = $query->row()->rebate;

		$query = $this->db->query('
								SELECT SUM(s.squareFootage) as sqft 
								FROM Stores as s
								LEFT OUTER JOIN RecyclingInvoices as ri 
								ON ri.locationType = "STORE" 
								AND ri.locationId = s.id
								WHERE (ri.invoiceDate BETWEEN '.$between.')
								GROUP BY s.id');
		$storeFromRecyclingInvoices = $query->row();

		$query = $this->db->query('
	    						SELECT SUM(s.squareFootage) as sqft 
		    					FROM Stores as s 
		    					INNER JOIN  WasteInvoices as wi 
		    					ON wi.locationType = "STORE" 
		    					AND wi.locationId = s.id 
		    					WHERE wi.invoiceDate BETWEEN '.$between.'');
		$storeFromWasteInvoices = $query->row();
		$storesSqFt = (!empty($storeFromRecyclingInvoices) ? $storeFromRecyclingInvoices->sqft : 0) + (!empty($storeFromWasteInvoices) ? $storeFromWasteInvoices->sqft : 0);

		/*
    	*  Commented By Palini Pulusu ON June 15th 2017
    	*/
		/*
		if($average) {
		    $result->waste = $result->waste / $average;
		    $result->recycling = $result->recycling / $average;		    
		}
		*/

		$result->diversion = (($result->recycling + $result->waste)!=0 ? ($result->recycling / ($result->recycling + $result->waste)) * 100 : 0);
		$result->costPerSqft = ($storesSqFt != 0 ? (($result->cost - $result->rebate) / $storesSqFt) : 0);

		/*
    	*  Commented By Palini Pulusu ON June 15th 2017
    	*/
		/*
		$baselineQuery = 'SELECT SUM(CurrentMonthlyRate) AS s
		    FROM BaselineCosts bc INNER JOIN
		    (SELECT DISTINCT locationName FROM WasteInvoices wi
		    WHERE wi.locationType = "STORE" AND (wi.invoiceDate BETWEEN '.$between.')) t1 ON t1.locationName = bc.StoreId';
		sumOfAllTotals = 0;
		*/

		$sql = 'SELECT sum(wi.total) as Sum, MONTH(wi.invoiceDate) as Month 
				FROM WasteInvoices wi 
				WHERE (wi.invoiceDate BETWEEN "2017-01-01" 
				AND " '.$endYear.'-'.($endMonth - 1).'-31") '.$storeIdQuery.'
				GROUP BY YEAR(wi.invoiceDate), MONTH(wi.invoiceDate) ';
 
    	$query = $this->db->query($sql,true);
		$sql1 = $query->result();

		/*
		*
		*	Commented By Palini Pulusu on Aug 29th 2017 changes to the calculation of baseline,savings
		*
		*/
		/*
		// To calculate average by using count(months) ans sum of totals
		$count = count($sql1);
		$sum = 0;
		if($count == 0 ){
			$count = 1;
		}
		else {
			for($i =0 ; $i<$count ; $i++) {
        		$sum += $sql1[$i]->Sum;
    		}
    	}
		$result->savings = ($result->baseline) -($result->rebate)  - ($result->cost)  ;
    	$result->baseline = $sum/$count;
		*/
		

    	/*
   		 *  Commented By Palini Pulusu ON June 15th 2017
    	*/

		/*        
			$baselineQuery = '
				SELECT AVG(total) as s FROM WasteInvoices WHERE invoiceDate BETWEEN "2017-01-01" AND " '.$endYear.'-'.($endMonth - 1).'-31" ';


					// '"'.$startYear.'-'.$startMonth.'-01" AND "'.$endYear.'-'.$endMonth.'-31"';
			if($storeId > 0) {
            	$baselineQuery .= ' AND WasteInvoices.locationId = "'.$storeId.'"';
        	}	

			$query = $this->db->query($baselineQuery);
			$result->baseline = $query->row()->s;
		
			$savingsQuery = 'SELECT SUM(wis.rate) AS s 
		    	FROM WasteInvoiceServices wis LEFT JOIN WasteInvoices wi ON wis.invoiceId = wi.id 
		    	WHERE wis.schedule > 0 AND wi.locationType = "STORE" AND wi.invoiceDate BETWEEN '.$between.'';
			$query = $this->db->query($savingsQuery);
			$result->savings = $result->baseline - $query->row()->s;
		*/

		$minANDmaxDate = $this->db->query("SELECT MIN(invoiceDate) as oldestDate, MAX(invoiceDate) as earliestDate FROM WasteInvoices");
		$oldestDate = $minANDmaxDate->row()->oldestDate;
		$earliestDate = $minANDmaxDate->row()->earliestDate;

		$countOfMonths = $this->db->query( "SELECT TIMESTAMPDIFF(MONTH, '" .$oldestDate."', '".$earliestDate."') as monthCnt" );
		// $countOfMonths =  $this->db->query( "SELECT TIMESTAMPDIFF(DAY, '" .$oldestDate."', '".$earliestDate."') as monthCnt" );

		$countOfMonths = $countOfMonths->row()->monthCnt;
		// $countOfMonths = $countOfMonths->row()->monthCnt/30;
		// var_dump($countOfMonths);
		
		$annualize = $this->db->query("SELECT SUM(total) as annualize FROM WasteInvoices Where invoiceDate BETWEEN $oldestDate AND $earliestDate ");
		// $annualize = $this->db->query("SELECT SUM(total) as annualize FROM WasteInvoices Where invoiceDate BETWEEN '2016-11-01' AND '2017-05-31' ");
		$annualize = ($annualize->row()->annualize) * 12/($countOfMonths);
		
    	$result->baseline = $annualize/12;
		// $costToThisMonth = $this->db->query("SELECT SUM(total) as cost  FROM WasteInvoices Where invoiceDate BETWEEN 'y-m-01' AND 'y-m-31' ");
		$costToThisMonth = $this->db->query("SELECT SUM(total) as cost  FROM WasteInvoices Where invoiceDate BETWEEN $between '.$storeIdQuery.'");
		$result->costToThisMonth = ($costToThisMonth->row()->cost);
		$result->savings = $result->baseline - $result->costToThisMonth;
		return $result;		
	}
	
	public function getServiceRequestChartInfo() {

		$dates = getPeriods('2MonthsBack');

        $info = $this->db->query("
            SELECT
              srt.name AS Name,
              COUNT(srtt.id) AS value
            FROM SupportRequestTasks srtt
              LEFT JOIN SupportRequestServiceTypes srt
                ON srt.id = srtt.purposeId
              LEFT JOIN SupportRequests AS sr
                ON sr.id = srtt.supportRequestId
            WHERE locationType = 'STORE' AND (serviceDate BETWEEN '{$dates['startYear']}-{$dates['startMonth']}-01' AND '{$dates['endYear']}-{$dates['endMonth']}-31')
            GROUP BY (srt.name)
            ORDER BY srt.id");

		return $info->result();
	}

	public function getLastActivity() {
		$lastActivity = $this->db->query("SELECT *, date_format(lastUpdated, '%m/%d/%Y') AS lu,
        	(SELECT location FROM Stores WHERE id = locationId) AS ST_number,
        	(SELECT id FROM Stores WHERE id = locationId) AS ST_id
            FROM SupportRequests
            WHERE locationType = 'STORE' 
            ORDER BY id DESC
            LIMIT 3");
		return $lastActivity->result("array");
	}
		
	
	public function getRecyclingData($from, $to, $display, $for, $bystate) {
		$result =  Array ('rebates'=>'', 'reciclyng'=>'', 'trend'=>'');
		$from = USToSQLDate($from); $to = USToSQLDate($to);
		$groupBy = "";
		$whereStateRegion = "";
		$what = "m.name";
		$tableWhere = '';
		$tableOrder = '';
		switch ($display) {
			case 1: //By Regions
				$groupBy = "GROUP BY States.region";				
				$what = "States.region";
				$tableOrder = ' ORDER BY States.region';
				break;
			case 2: //'By District',
				$groupBy = "GROUP BY Stores.district";
				$tableOrder = ' ORDER BY Stores.district';
				$what = "Stores.district";
				break;
			case 3: //'By District (East Region)',
				$groupBy = "GROUP BY Stores.district";
				$tableWhere = $whereStateRegion = " AND States.region = 'East'";
				$tableOrder = ' ORDER BY Stores.district';
				$what = "Stores.district";
				break;
			case 4: //'By District (Southeast Region)',
				$groupBy = "GROUP BY Stores.district";
				$tableWhere = $whereStateRegion = " AND States.region = 'Southeast'";
				$tableOrder = ' ORDER BY Stores.district';
				$what = "Stores.district";
				break;
			case 5: //'By District (Midwest Region)',
				$groupBy = "GROUP BY Stores.district";
				$tableWhere = $whereStateRegion = " AND States.region = 'Midwest'";
				$tableOrder = ' ORDER BY Stores.district';
				$what = "Stores.district";
				break;
			case 6: //'By District (South Region)',
				$groupBy = "GROUP BY Stores.district";
				$tableWhere = $whereStateRegion = " AND States.region = 'South'";
				$tableOrder = ' ORDER BY Stores.district';
				$what = "Stores.district";
				break;
			case 7: //'By District (West Region)'
				$groupBy = "GROUP BY Stores.district";
				$tableWhere = $whereStateRegion = " AND States.region = 'West'";
				$tableOrder = ' ORDER BY Stores.district';
				$what = "Stores.district";
				break;
			case 8: //'By State'
				$groupBy = "GROUP BY States.id";
				if($bystate!=0)
				    $tableWhere = $whereStateRegion = " AND States.id = ".$bystate;
				$tableOrder = ' ORDER BY States.id';
				$what = "States.name";
				break;	
		}
		$whereByState="";
		if($bystate!=0)
		    $whereByState = "AND States.id = ".$bystate;
		
		//materials
		$value = "SUM(quantity*pricePerUnit)";
		$valueRecicling = "SUM(quantity)";
		$whereMaterialId = "";
		$groupByMaterials = $groupBy;
		$whatMaterials = $what;		
		switch ($for) {
			case 1: // Cardboard'
				$whereMaterialId = " AND m.id = 1";		
				break;
          	case 2: //'Film',
          		$whereMaterialId = " AND m.id = 8";
          		break;
          	case 3: //'Aluminum',
          		$whereMaterialId = " AND m.id = 3";
          		break;
          	case 4: //'Plastic',
          		$whereMaterialId = " AND m.id = 5";
          		break;
          	case 5: //'Rebates',
          		break;
          	case 6: //'Trees Saved',
          		$whereMaterialId = " AND m.id = 7";
          		break;
          	case 7: //=>'KWh Saved',
          		$whatMaterials = "m.name";
          		$value = "SUM(quantity*EnergySaves)";
          		$groupByMaterials = "GROUP BY m.name";
          		break;
          	case 8: //'CO2 Reduced',
          		$whatMaterials = "m.name";
          		$value = "SUM(quantity*CO2Saves)"; 
          		$groupByMaterials = "GROUP BY m.name";
          		break;          
          	case 9: //'Landfill Reduced'
          		break;
		}		
		$sqlQuery = "SELECT {$value} AS value, {$what} AS Name  FROM `RecyclingInvoices` ri	
			INNER JOIN Stores ON Stores.id = ri.locationId AND locationType = 'STORE'
			INNER JOIN RecyclingInvoicesMaterials rim ON rim.invoiceId = ri.id
			INNER JOIN Materials m ON m.id = rim.materialId
			INNER JOIN States ON States.id = Stores.stateId
			WHERE m.unit = 1 {$whereByState} {$whereStateRegion} {$whereMaterialId} AND ri.invoiceDate BETWEEN '{$from}' AND '{$to}' $groupBy ";		
			$res = $this->db->query($sqlQuery);		
		$result['rebates'] = $res->result();		
		$sqlQuery = "SELECT {$valueRecicling} AS value, {$whatMaterials} AS Name  FROM `RecyclingInvoices` ri	
			INNER JOIN Stores ON Stores.id = ri.locationId AND locationType = 'STORE'
			INNER JOIN RecyclingInvoicesMaterials rim ON rim.invoiceId = ri.id
			INNER JOIN Materials m ON m.id = rim.materialId
			INNER JOIN States ON States.id = Stores.stateId
			WHERE m.unit = 1 {$whereByState} {$whereStateRegion} {$whereMaterialId} AND ri.invoiceDate BETWEEN '{$from}' AND '{$to}' $groupByMaterials ";		
			$res = $this->db->query($sqlQuery);
		$result['recycling'] = $res->result();		
		$trends = Array();
		$months = Array();		
		for($i = 5; $i >= 0; $i--) {			
			$months[] = ($i ==0)?"Current": date("m/Y", strtotime("-$i month")); 
			$period = "BETWEEN '" . date("Y-m-01", strtotime("-$i month")) . "' AND '" . date("Y-m-31", strtotime("-$i month")) . "'";
			$sqlTrendsQuery = "SELECT {$valueRecicling} AS value, {$whatMaterials} AS Name  FROM Stores 	
			LEFT JOIN `RecyclingInvoices` ri ON Stores.id = ri.locationId AND invoiceDate {$period} AND locationType = 'STORE'
			LEFT JOIN RecyclingInvoicesMaterials rim ON rim.invoiceId = ri.id AND rim.unit = 1
			LEFT JOIN Materials m ON m.id = rim.materialId {$whereMaterialId}
			LEFT JOIN States ON States.id = Stores.stateId
			WHERE 1 {$whereByState} {$whereStateRegion} {$groupByMaterials}";
			$res = $this->db->query($sqlTrendsQuery);
			foreach($res->result() as $record) {
				$trends[$record->Name][] = $record->value;
			}
		}
		$result['trend'] = Array('months'=>json_encode($months, JSON_NUMERIC_CHECK),
			'countries'=>json_encode(array_keys($trends)), 
			'data'=>json_encode(array_values($trends), JSON_NUMERIC_CHECK)
		);
		//table data
		$sqlQuery = "SELECT Stores.id AS id, States.name AS state, States.region AS region, district, location, open24hours, squareFootage, ri.id AS invoiceId FROM Stores 	
			INNER JOIN `RecyclingInvoices` ri ON Stores.id = ri.locationId AND invoiceDate BETWEEN '{$from}' AND '{$to}' AND locationType = 'STORE'			
			LEFT JOIN States ON States.id = Stores.stateId
			WHERE 1 {$whereByState} {$tableWhere} {$tableOrder} ASC";
		$res = $this->db->query($sqlQuery);
		$res1 = $res->result();
				
		foreach($res1 as $k=>$i) {
			$sqlQueryInvoiceMaterials = "SELECT m.name AS materialName, quantity, (quantity*pricePerUnit) AS rebate, (quantity*CO2Saves) AS CO2Saves, (quantity*EnergySaves) AS EnergySaves, m.id AS materialId FROM RecyclingInvoicesMaterials rim 
			INNER JOIN Materials m ON m.id = rim.materialId 
			WHERE invoiceId = {$i->invoiceId} AND m.unit = 1
			ORDER BY m.id ASC LIMIT 10";			
			$invoice = $this->db->query($sqlQueryInvoiceMaterials);
			$res1[$k]->materials = $invoice->result(); 
			$g = Array();
			$res1[$k]->rebate = 0.0;
			$res1[$k]->CO2Saves = 0.0;
			$res1[$k]->EnergySaves = 0.0;
			foreach($res1[$k]->materials as $p=>$v) {
				$g[$v->materialId] = $v->quantity; 
				$res1[$k]->rebate += $v->rebate;
				$res1[$k]->CO2Saves += $v->CO2Saves;
				$res1[$k]->EnergySaves += $v->EnergySaves;
			}			
			$res1[$k]->materials = $g;
		}		
		$result['table'] = &$res1;
		return $result;		
	}
	/*
	 * CostSaving
	 */
	public function getCostSavingData($from, $to, $display, $bystate) {
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
		switch ($display) {
			case 1: //By Regions								
				$what = "States.region";
				$tableOrder = ' ORDER BY States.region';
				$groupBy = "GROUP BY States.region";
				break;
			case 2: //'By District',
				$groupBy = "GROUP BY Stores.district";
				$tableOrder = ' ORDER BY Stores.district';
				$what = "Stores.district";
				break;
			case 3: //'By District (East Region)',
				$groupBy = "GROUP BY Stores.district";
				$tableWhere = $whereStateRegion = " AND States.region = 'East'";
				$tableOrder = ' ORDER BY Stores.district';
				$what = "Stores.district";
				break;
			case 4: //'By District (Southeast Region)',
				$groupBy = "GROUP BY Stores.district";
				$tableWhere = $whereStateRegion = " AND States.region = 'Southeast'";
				$tableOrder = ' ORDER BY Stores.district';
				$what = "Stores.district";
				break;
			case 5: //'By District (Midwest Region)',
				$groupBy = "GROUP BY Stores.district";
				$tableWhere = $whereStateRegion = " AND States.region = 'Midwest'";
				$tableOrder = ' ORDER BY Stores.district';
				$what = "Stores.district";
				break;
			case 6: //'By District (South Region)',
				$groupBy = "GROUP BY Stores.district";
				$tableWhere = $whereStateRegion = " AND States.region = 'South'";
				$tableOrder = ' ORDER BY Stores.district';
				$what = "Stores.district";
				break;
			case 7: //'By District (West Region)'
				$groupBy = "GROUP BY Stores.district";
				$tableWhere = $whereStateRegion = " AND States.region = 'West'";
				$tableOrder = ' ORDER BY Stores.district';
				$what = "Stores.district";
				break;	
			case 8: //'By State'
				$groupBy = "GROUP BY States.id";
				if($bystate!=0)
				    $tableWhere = $whereStateRegion = " AND States.id = ".$bystate;
				$tableOrder = ' ORDER BY States.id';
				$what = "States.name";
				break;	
		}		
		/*$sqlQuery = "SELECT SUM(quantity) AS value, srst.name AS Name  FROM SupportRequestTasks srt
			INNER JOIN SupportRequestServiceTypes srst ON srst.id = srt.purposeId
			INNER JOIN SupportRequests sr ON sr.id = supportRequestId			 
			INNER JOIN Stores ON Stores.id = sr.locationId AND 	locationType = 'STORE'
			INNER JOIN States ON States.id = Stores.stateId
			WHERE 1 {$whereStateRegion} AND sr.lastUpdated BETWEEN '{$from}' AND '{$to}' GROUP BY srt.purposeId ";		
			$res = $this->db->query($sqlQuery);		 		
		$result['cost'] = $res->result();*/
		//$result['cost'] = $this->getCostOfServicestChartInfo($from, $to, $display);
		$result['cost'] = $this->CompanyModel->getCostOfServicesChartInfo($from, $to, $display);
		//echo "<pre>"; print_r($result['cost']);
		//Ако savings ?== rebat ??
		/*
		Savings for each location = (1)recycling rebates + (2)SAMS + (3)waived fees
		recycling rebates = total purchase order rebate total 
		SAMS= total prior scheduled services cost (01/06/2011 - 
> 20/06/2011 )
> - total current scheduled services cost (01/06/2012-20/06/2012)
		 */
		$whereByState="";
		if($bystate!=0)
		    $whereByState = "AND States.id = ".$bystate;		
		
		$sqlQuery = "SELECT {$what} AS Name, 
			(SELECT COALESCE(SUM(quantity*pricePerUnit),0) FROM `RecyclingInvoices` ri
			INNER JOIN RecyclingInvoicesMaterials rim ON rim.invoiceId = ri.id
			INNER JOIN Materials m ON m.id = rim.materialId			
			WHERE m.unit = 1 AND Stores.id = ri.locationId AND ri.locationType = 'STORE' AND ri.invoiceDate BETWEEN '{$from}' AND '{$to}'
			)  
			+
			-- SAMS 
			( 
			(SELECT COALESCE(SUM(wis.quantity * wis.rate),0) FROM `WasteInvoices` wi	
			INNER JOIN WasteInvoiceServices wis ON wis.invoiceId = wi.id
			WHERE unitId = 1 AND durationId = 1 AND Stores.id = wi.locationId AND wi.locationType = 'STORE' AND wi.invoiceDate BETWEEN '{$fromPY}' AND '{$toPY}'		 
			)
			-
			(SELECT COALESCE(SUM(wis.quantity * wis.rate),0) FROM `WasteInvoices` wi	
			INNER JOIN WasteInvoiceServices wis ON wis.invoiceId = wi.id
			WHERE unitId = 1 AND durationId = 1 AND Stores.id = wi.locationId AND wi.locationType = 'STORE' AND wi.invoiceDate BETWEEN '{$from}' AND '{$to}'
			)
			)
			-- END OF SAMS
			-- waived fees
			+
			(SELECT COALESCE(SUM(feeAmount),0) FROM `WasteInvoices` wi
				INNER JOIN WasteInvoiceFees wif ON wif.invoiceId = wi.id
				WHERE waived AND Stores.id = wi.locationId AND wi.locationType = 'STORE' AND wi.invoiceDate BETWEEN '{$from}' AND '{$to}'			
			)
			+
			(SELECT COALESCE(SUM(feeAmount),0) FROM `RecyclingInvoices` ri
				INNER JOIN RecyclingInvoicesFees rif ON rif.invoiceId = ri.id
				WHERE waived AND Stores.id = ri.locationId AND ri.locationType = 'STORE' AND ri.invoiceDate BETWEEN '{$from}' AND '{$to}'			
			)
			AS value			
			FROM Stores
			INNER JOIN States ON States.id = Stores.stateId
			WHERE 1 {$whereByState} {$whereStateRegion} $groupBy";	
		/*$sqlQuery = "SELECT SUM(quantity*pricePerUnit) AS value, {$what} AS Name  FROM `RecyclingInvoices` ri	
			INNER JOIN Stores ON Stores.id = ri.locationId AND locationType = 'STORE'
			INNER JOIN RecyclingInvoicesMaterials rim ON rim.invoiceId = ri.id
			INNER JOIN Materials m ON m.id = rim.materialId
			INNER JOIN States ON States.id = Stores.stateId
			WHERE unit = 1 {$whereStateRegion} AND ri.invoiceDate BETWEEN '{$from}' AND '{$to}' $groupBy ";		*/
			$res = $this->db->query($sqlQuery);		
		$result['savings'] =  $res->result();		
		$trends = Array();
		$months = Array();		
		for($i = 5; $i >= 0; $i--) {			
			$months[] = ($i ==0)?"Current": date("m/Y", strtotime("-$i month")); 
			$period = "BETWEEN '" . date("Y-m-01", strtotime("-$i month")) . "' AND '" . date("Y-m-31", strtotime("-$i month")) . "'";
						
			$sqlTrendsQuery = "SELECT SUM(quantity*pricePerUnit)
			-
			(SELECT COALESCE(SUM(`feeAmount`),0) FROM `WasteInvoiceFees` wf
				INNER JOIN WasteInvoices wi ON wi.id = `invoiceId`
				INNER JOIN Stores ON Stores.id = wi.locationId AND locationType = 'STORE'
				INNER JOIN States ON States.id = Stores.stateId			
				WHERE 1  {$whereByState} {$whereStateRegion} AND ri.invoiceDate {$period}
			)
			-  
			(SELECT COALESCE(SUM(rci.quantity * rci.pricePerTon),0) FROM RecyclingChargeItems as rci
				INNER JOIN RecyclingCharges rc ON rc.id = rci.recyclingChargeId
				INNER JOIN Stores ON Stores.id = rc.locationId AND rc.locationType = 'STORE'
				INNER JOIN States ON States.id = Stores.stateId			
				WHERE 1 {$whereByState} {$whereStateRegion} AND ri.invoiceDate {$period}
			)			
			AS value, {$what} AS Name  FROM States
			INNER JOIN Stores ON States.id = Stores.stateId
			LEFT JOIN `RecyclingInvoices` ri ON Stores.id = ri.locationId AND locationType = 'STORE' AND ri.invoiceDate {$period}
			LEFT JOIN RecyclingInvoicesMaterials rim ON rim.invoiceId = ri.id AND rim.unit = 1
			LEFT JOIN Materials m ON m.id = rim.materialId			
			WHERE 1 {$whereByState} {$whereStateRegion}  $groupBy ";
			$res = $this->db->query($sqlTrendsQuery);					
			foreach($res->result() as $record) {												
				$trends[$record->Name][] = (float)$record->value?(float)$record->value:0;
			}												
		}		
		$result['trend'] = Array('months'=>json_encode($months, JSON_NUMERIC_CHECK),
			'regions'=>json_encode(array_keys($trends)), 
			'data'=>json_encode(array_values($trends), JSON_NUMERIC_CHECK)
		);						
		//table data
		$sqlQuery = "SELECT Stores.id AS id, States.name as state, States.region AS region, district, location, open24hours, squareFootage,
			(SELECT SUM(feeAmount) FROM WasteInvoiceFees wif
			INNER JOIN WasteInvoices wi ON wif.invoiceId = wi.id 			
			WHERE Stores.id = wi.locationId AND wi.locationType = 'STORE' AND wi.invoiceDate BETWEEN '{$from}' AND '{$to}') AS WasteService,
			(SELECT SUM(feeAmount) FROM WasteInvoiceFees wif 
			INNER JOIN WasteInvoices wi ON wif.invoiceId = wi.id
			WHERE Stores.id = wi.locationId AND wi.locationType = 'STORE' AND wi.invoiceDate BETWEEN '{$from}' AND '{$to}' AND 6 = feeType) AS WasteEquipmentFee,
			(SELECT SUM(feeAmount) FROM WasteInvoiceFees wif 
			INNER JOIN WasteInvoices wi ON wif.invoiceId = wi.id
			WHERE Stores.id = wi.locationId AND wi.locationType = 'STORE' AND wi.invoiceDate BETWEEN '{$from}' AND '{$to}'AND 1 = feeType) AS WasteHaulFee,
			(SELECT SUM(feeAmount) FROM WasteInvoiceFees wif 
			INNER JOIN WasteInvoices wi ON wif.invoiceId = wi.id
			WHERE Stores.id = wi.locationId AND wi.locationType = 'STORE' AND wi.invoiceDate BETWEEN '{$from}' AND '{$to}'AND feeType NOT IN (1,2,3,4,5,6)) AS WasteDisposalFee,
			SUM(quantity*pricePerUnit) AS RecyclingRebate,
			(SELECT SUM(feeAmount) FROM WasteInvoiceFees WHERE WasteInvoiceFees.invoiceId = ri.id AND feeType NOT IN (1,6)) AS OtherFee,
			SUM(quantity*pricePerUnit)
			-
			(SELECT COALESCE(SUM(`feeAmount`),0) FROM `WasteInvoiceFees` wf
				INNER JOIN WasteInvoices wi ON wi.id = `invoiceId`
				INNER JOIN Stores ON Stores.id = wi.locationId AND locationType = 'STORE'
				INNER JOIN States ON States.id = Stores.stateId			
				WHERE 1 {$whereByState} {$whereStateRegion} AND ri.invoiceDate BETWEEN '{$from}' AND '{$to}' {$tableWhere}
			)
			-  
			(SELECT COALESCE(SUM(rci.quantity * rci.pricePerTon),0) FROM RecyclingChargeItems as rci
				INNER JOIN RecyclingCharges rc ON rc.id = rci.recyclingChargeId
				INNER JOIN Stores ON Stores.id = rc.locationId AND rc.locationType = 'STORE'
				INNER JOIN States ON States.id = Stores.stateId			
				WHERE 1 {$whereByState} {$whereStateRegion} AND ri.invoiceDate BETWEEN '{$from}' AND '{$to}' {$tableWhere}
			) AS net,
			SUM(quantity) AS TotalTonnage					
			FROM `RecyclingInvoices` ri	
			INNER JOIN Stores ON Stores.id = ri.locationId AND locationType = 'STORE'
			INNER JOIN RecyclingInvoicesMaterials rim ON rim.invoiceId = ri.id
			INNER JOIN Materials m ON m.id = rim.materialId
			INNER JOIN States ON States.id = Stores.stateId
			WHERE m.unit = 1 {$whereByState} AND (ri.invoiceDate BETWEEN '{$from}' AND '{$to}') {$tableWhere} GROUP BY Stores.id {$tableOrder} ASC";
		$res = $this->db->query($sqlQuery);
		$result['table'] = $res->result();
		//echo "<pre>"; print_r($result['table']); die();
		/*
		 INNER JOIN Stores ON Stores.id = ri.locationId AND locationType = 'STORE'
			INNER JOIN RecyclingInvoicesMaterials rim ON rim.invoiceId = ri.id
			INNER JOIN Materials m ON m.id = rim.materialId
			INNER JOIN States ON States.id = Stores.stateId
		  
		 */
				
		return $result;
	}
	/*
	 * Weaste
	 */
	public function getWasteData($from, $to, $display, $bystate) {
		$from = USToSQLDate($from); 
		$to = USToSQLDate($to);
		$groupBy = "";
		$whereStateRegion = "";
		$whereByState = "";
		$what = "m.name";
		$tableWhere = '';
		$tableOrder = '';

		switch ($display) {
			case 1: //By Regions								
				$what = "States.region";
				$tableOrder = ' ORDER BY States.region';
				$groupBy = "GROUP BY States.region";
				break;
			case 2: //'By District',
				$groupBy = "GROUP BY Stores.district";
				$tableOrder = ' ORDER BY Stores.district';
				$what = "Stores.district";
				break;
			case 3: //'By District (East Region)',
				$groupBy = "GROUP BY Stores.district";
				$tableWhere = $whereStateRegion = " AND States.region = 'East'";
				$tableOrder = ' ORDER BY Stores.district';
				$what = "Stores.district";
				break;
			case 4: //'By District (Southeast Region)',
				$groupBy = "GROUP BY Stores.district";
				$tableWhere = $whereStateRegion = " AND States.region = 'Southeast'";
				$tableOrder = ' ORDER BY Stores.district';
				$what = "Stores.district";
				break;
			case 5: //'By District (Midwest Region)',
				$groupBy = "GROUP BY Stores.district";
				$tableWhere = $whereStateRegion = " AND States.region = 'Midwest'";
				$tableOrder = ' ORDER BY Stores.district';
				$what = "Stores.district";
				break;
			case 6: //'By District (South Region)',
				$groupBy = "GROUP BY Stores.district";
				$tableWhere = $whereStateRegion = " AND States.region = 'South'";
				$tableOrder = ' ORDER BY Stores.district';
				$what = "Stores.district";
				break;
			case 7: //'By District (West Region)'
				$groupBy = "GROUP BY Stores.district";
				$tableWhere = $whereStateRegion = " AND States.region = 'West'";
				$tableOrder = ' ORDER BY Stores.district';
				$what = "Stores.district";
				break;	
			case 8: //'By State'
				$groupBy = "GROUP BY States.id";
				if($bystate!=0)
				    $tableWhere = $whereStateRegion = " AND States.id = ".$bystate;
				$tableOrder = ' ORDER BY States.id';
				$what = "States.name";
				break;	
		}
		if($bystate!=0)
		    $whereByState = "AND States.id = ".$bystate;		

		$sqlQuery = 'SELECT category, SUM(quantity*weightInLbs*IFNULL(pickupsPerMonth,1))/'.TON_KOEFF.' AS s
			FROM WasteInvoices wi  
			LEFT OUTER JOIN WasteInvoiceServices wis ON wis.invoiceId = wi.id
			LEFT OUTER JOIN Containers c ON c.id = wis.containerId
			LEFT OUTER JOIN monthlyPickups m on m.id = wis.schedule
			LEFT JOIN Stores ON Stores.id = wi.locationId
			LEFT JOIN States ON States.id = Stores.stateId
			WHERE locationType = "STORE" '.$whereStateRegion.' '.$whereByState.' AND invoiceDate BETWEEN "'.$from.'" AND "'.$to.'"
			GROUP BY category';
		$query = $this->db->query($sqlQuery);
		$rows = $query->result('array');
		$result = array();
		foreach($rows as $row) {
		    if($row['category']!='') {
		        $result[] = array('name'=>($row['category']==1 ? 'Recycling' : 'Waste'), 'value'=> $row['s']);
		    }
		}
	 
		return $result;	    
	}
	
	public function getWasteTrendData2($from, $to, $display, $bystate) {
		$from = USToSQLDate($from); 
		$to = USToSQLDate($to);
		$groupBy = "";
		$whereStateRegion = "";
		$whereByState = "";
		$what = "m.name";
		$tableWhere = '';
		$tableOrder = '';
		$trend = array();
		$months = array();

		switch ($display) {
			case 1: //By Regions								
				$what = "States.region";
				$tableOrder = ' ORDER BY States.region';
				$groupBy = "GROUP BY States.region";
				break;
			case 2: //'By District',
				$groupBy = "GROUP BY Stores.district";
				$tableOrder = ' ORDER BY Stores.district';
				$what = "Stores.district";
				break;
			case 3: //'By District (East Region)',
				$groupBy = "GROUP BY Stores.district";
				$tableWhere = $whereStateRegion = " AND States.region = 'East'";
				$tableOrder = ' ORDER BY Stores.district';
				$what = "Stores.district";
				break;
			case 4: //'By District (Southeast Region)',
				$groupBy = "GROUP BY Stores.district";
				$tableWhere = $whereStateRegion = " AND States.region = 'Southeast'";
				$tableOrder = ' ORDER BY Stores.district';
				$what = "Stores.district";
				break;
			case 5: //'By District (Midwest Region)',
				$groupBy = "GROUP BY Stores.district";
				$tableWhere = $whereStateRegion = " AND States.region = 'Midwest'";
				$tableOrder = ' ORDER BY Stores.district';
				$what = "Stores.district";
				break;
			case 6: //'By District (South Region)',
				$groupBy = "GROUP BY Stores.district";
				$tableWhere = $whereStateRegion = " AND States.region = 'South'";
				$tableOrder = ' ORDER BY Stores.district';
				$what = "Stores.district";
				break;
			case 7: //'By District (West Region)'
				$groupBy = "GROUP BY Stores.district";
				$tableWhere = $whereStateRegion = " AND States.region = 'West'";
				$tableOrder = ' ORDER BY Stores.district';
				$what = "Stores.district";
				break;	
			case 8: //'By State'
				$groupBy = "GROUP BY States.id";
				if($bystate!=0)
				    $tableWhere = $whereStateRegion = " AND States.id = ".$bystate;
				$tableOrder = ' ORDER BY States.id';
				$what = "States.name";
				break;	
		}
		if($bystate!=0)
		    $whereByState = "AND States.id = ".$bystate;		

		$where = ' 1 '.$whereByState.' '.$tableWhere;

		$mS =  mktime(0, 0, 0, date("m")-5, 15, date("Y"));
		$mE =  mktime(0, 0, 0, date("m")-0, 15, date("Y"));
		$period = '"' . date("Y-m-01", $mS) . '" AND "' . date("Y-m-31", $mE) . '"';

        $time_1 = microtime(true);

        $sqlQuery = '
		    SELECT
		    Stores.id AS locationId,
		    wi.id AS invoiceId,
		    DATE_FORMAT(wi.invoiceDate, "%Y/%m") AS monthyear,
		    0 AS WasteTons,
		    0 AS CommingleTons,
		    0 AS CardboardTons,
		    0 AS OtherTons
		    FROM Stores
		    LEFT OUTER JOIN WasteInvoices wi ON Stores.id = wi.locationId AND invoiceDate BETWEEN '.$period.'
		    LEFT JOIN States ON States.id = Stores.stateId
		    WHERE '.$where.' AND wi.invoiceDate IS NOT NULL';
        $res = $this->db->query($sqlQuery);
        $rows = $res->result();

        $sqlQuery = '
          SELECT
            invoiceId,
            category,
            materialId,
            SUM(quantity * weightInLbs * IFNULL(pickupsPerMonth, 1)) / '.TON_KOEFF.' AS VTons
          FROM WasteInvoiceServices AS wis
            LEFT OUTER JOIN Containers AS c
              ON c.id = wis.containerId
            LEFT OUTER JOIN monthlyPickups m
              ON m.id = wis.schedule
          GROUP BY category, materialId, invoiceId
          ORDER BY invoiceId';
        $res = $this->db->query($sqlQuery);
        $rowsTons1 = $res->result();

        $rowsTons1t = array();
        foreach($rowsTons1 as $k=>$rowTons1) {
            if(!isset($rowsTons1t[$rowTons1->invoiceId])) {
                $rowsTons1t[$rowTons1->invoiceId] = array();
            }
            array_push($rowsTons1t[$rowTons1->invoiceId], $rowTons1);
        }
        unset($rowsTons1);

        $sqlQuery = '
          SELECT
            locationId,
            category,
            materialId,
            SUM(quantity * weightInLbs * IFNULL(pickupsPerMonth, 1)) / '.TON_KOEFF.' AS VTons
          FROM VendorServices vs
            LEFT OUTER JOIN Containers c
              ON c.id = vs.containerId
            LEFT OUTER JOIN monthlyPickups mp
              ON mp.id = vs.schedule
            INNER JOIN (SELECT DISTINCT
              (
              id
              )
            FROM VendorServices vs
            WHERE (
            CURDATE() < vs.endDate
            OR vs.endDate = "0000-00-00"
            )
            AND locationType = "Store"
            GROUP BY locationId,
                     vendorId
            HAVING SUM(rate) = 0) vs2
              ON vs2.id = vs.id
          GROUP BY locationId
          ORDER BY locationId';
        $res = $this->db->query($sqlQuery);
        $rowsTons2 = $res->result();

        $rowsTons2t = array();
        foreach($rowsTons2 as $k=>$rowTons2) {
            if(!isset($rowsTons2t[$rowTons2->locationId])) {
                $rowsTons2t[$rowTons2->locationId] = array();
            }
            array_push($rowsTons2t[$rowTons2->locationId], $rowTons2);
        }
        unset($rowsTons2);

        $time_2 = microtime(true);

        foreach($rows as &$row) {
            if(isset($rowsTons1t[$row->invoiceId])) {
                foreach($rowsTons1t[$row->invoiceId] as $k=>$rowTons1) {
                    if($rowTons1->category==0) {
                        $row->WasteTons += $rowTons1->VTons;
                    }
                    if($rowTons1->category==1) {
                        if($rowTons1->materialId==9) {
                            $row->CommingleTons += $rowTons1->VTons;
                        }
                        if($rowTons1->materialId==1) {
                            $row->CardboardTons += $rowTons1->VTons;
                        }
                        if($rowTons1->materialId!=1 && $rowTons1->materialId!=9) {
                            $row->OtherTons += $rowTons1->VTons;
                        }
                    }
                }
            }

            if(isset($rowsTons2t[$row->locationId])) {
                foreach($rowsTons2t[$row->locationId] as $rowTons2) {
                    if($rowTons2->category==1) {
                        if($rowTons2->materialId==9) {
                            $row->CommingleTons += $rowTons2->VTons;
                        }
                        if($rowTons2->materialId==1) {
                            $row->CardboardTons += $rowTons2->VTons;
                        }
                        if($rowTons2->materialId!=1 && $rowTons2->materialId!=9) {
                            $row->OtherTons += $rowTons2->VTons;
                        }
                    }
                 }
            }

            if(!in_array($row->monthyear, $months)) {
                array_push($months, $row->monthyear);

                $trend[$row->monthyear] = array();
                $trend[$row->monthyear]['Waste'] = 0;
                $trend[$row->monthyear]['Cardboard'] = 0;
                $trend[$row->monthyear]['Commingle'] = 0;
                $trend[$row->monthyear]['Other'] = 0;
            }

            $trend[$row->monthyear]['Waste'] += floatval($row->WasteTons);
            $trend[$row->monthyear]['Cardboard'] += floatval($row->CardboardTons);
            $trend[$row->monthyear]['Commingle'] += floatval($row->CommingleTons);
            $trend[$row->monthyear]['Other'] += floatval($row->OtherTons);
        }

        $time_3 = microtime(true);
		
		return array('months'=>$months, 'trend'=>$trend);
	}

    public function getWasteTrendData($from, $to, $display, $bystate) {
        $from = USToSQLDate($from);
        $to = USToSQLDate($to);
        $groupBy = "";
        $whereStateRegion = "";
        $whereByState = "";
        $what = "m.name";
        $tableWhere = '';
        $tableOrder = '';
        $trend = array();
        $months = array();

        switch ($display) {
            case 1: //By Regions
                $what = "States.region";
                $tableOrder = ' ORDER BY States.region';
                $groupBy = "GROUP BY States.region";
                break;
            case 2: //'By District',
                $groupBy = "GROUP BY Stores.district";
                $tableOrder = ' ORDER BY Stores.district';
                $what = "Stores.district";
                break;
            case 3: //'By District (East Region)',
                $groupBy = "GROUP BY Stores.district";
                $tableWhere = $whereStateRegion = " AND States.region = 'East'";
                $tableOrder = ' ORDER BY Stores.district';
                $what = "Stores.district";
                break;
            case 4: //'By District (Southeast Region)',
                $groupBy = "GROUP BY Stores.district";
                $tableWhere = $whereStateRegion = " AND States.region = 'Southeast'";
                $tableOrder = ' ORDER BY Stores.district';
                $what = "Stores.district";
                break;
            case 5: //'By District (Midwest Region)',
                $groupBy = "GROUP BY Stores.district";
                $tableWhere = $whereStateRegion = " AND States.region = 'Midwest'";
                $tableOrder = ' ORDER BY Stores.district';
                $what = "Stores.district";
                break;
            case 6: //'By District (South Region)',
                $groupBy = "GROUP BY Stores.district";
                $tableWhere = $whereStateRegion = " AND States.region = 'South'";
                $tableOrder = ' ORDER BY Stores.district';
                $what = "Stores.district";
                break;
            case 7: //'By District (West Region)'
                $groupBy = "GROUP BY Stores.district";
                $tableWhere = $whereStateRegion = " AND States.region = 'West'";
                $tableOrder = ' ORDER BY Stores.district';
                $what = "Stores.district";
                break;
            case 8: //'By State'
                $groupBy = "GROUP BY States.id";
                if($bystate!=0)
                    $tableWhere = $whereStateRegion = " AND States.id = ".$bystate;
                $tableOrder = ' ORDER BY States.id';
                $what = "States.name";
                break;
        }
        if($bystate!=0)
            $whereByState = "AND States.id = ".$bystate;

        $where = ' 1 '.$whereByState.' '.$tableWhere;

        $mS =  mktime(0, 0, 0, date("m")-5, 15, date("Y"));
        $mE =  mktime(0, 0, 0, date("m")-0, 15, date("Y"));
        $period = '"' . date("Y-m-01", $mS) . '" AND "' . date("Y-m-31", $mE) . '"';

        $sqlQuery = '
			SELECT monthyear,
			SUM(WasteTons) AS WasteTons,
			SUM(CommingleTons) AS CommingleTons,
			SUM(CardboardTons) AS CardboardTons,
			SUM(OtherTons) AS OtherTons
			FROM (
			SELECT DATE_FORMAT(wi.invoiceDate, "%Y/%m") AS monthyear,
			WasteTons,
			IFNULL(CommTons,0) + IFNULL(CommTons2, 0) as CommingleTons,
			IFNULL(CardTons,0) + IFNULL(CardTons2, 0) as CardboardTons,
			IFNULL(OtherTons,0) + IFNULL(OtherTons2, 0) as OtherTons
			FROM Stores
			LEFT OUTER JOIN WasteInvoices wi ON Stores.id = wi.locationId AND invoiceDate BETWEEN '.$period.'
			LEFT JOIN States ON States.id = Stores.stateId
			LEFT OUTER JOIN (Select invoiceid, sum(quantity*weightInLbs*IFNULL(pickupsPerMonth,1))/'.TON_KOEFF.' as WasteTons FROM WasteInvoiceServices as wis LEFT OUTER JOIN Containers as c ON c.id=wis.containerId left outer join monthlyPickups m on m.id=wis.schedule WHERE category=0 group by invoiceID) as wt on wt.invoiceId = wi.id
			LEFT OUTER JOIN (Select invoiceid, sum(quantity*weightInLbs*IFNULL(pickupsPerMonth,1))/'.TON_KOEFF.' as CommTons FROM WasteInvoiceServices as wis LEFT OUTER JOIN Containers as c ON c.id=wis.containerId left outer join monthlyPickups m on m.id=wis.schedule WHERE category=1 and wis.materialid=9 group by invoiceID) as cmt on cmt.invoiceId = wi.id
			LEFT OUTER JOIN (Select invoiceid, sum(quantity*weightInLbs*IFNULL(pickupsPerMonth,1))/'.TON_KOEFF.' as CardTons FROM WasteInvoiceServices as wis LEFT OUTER JOIN Containers as c ON c.id=wis.containerId left outer join monthlyPickups m on m.id=wis.schedule WHERE category=1 and wis.materialid=1  group by invoiceID) as cdt on cdt.invoiceId = wi.id
			LEFT OUTER JOIN (Select invoiceid, sum(quantity*weightInLbs*IFNULL(pickupsPerMonth,1))/'.TON_KOEFF.' as OtherTons FROM WasteInvoiceServices as wis LEFT OUTER JOIN Containers as c ON c.id=wis.containerId left outer join monthlyPickups m on m.id=wis.schedule WHERE category=1 and wis.materialid!=9 and wis.materialid!=1 group by invoiceID) as ot on ot.invoiceId = wi.id
			LEFT OUTER JOIN (select locationId,SUM(quantity*weightInLbs*IFNULL(pickupsPerMonth,1)) /'.TON_KOEFF.' as commTons2 FROM VendorServices vs LEFT OUTER JOIN Containers c on c.id=vs.containerId LEFT OUTER JOIN monthlyPickups mp on mp.id=vs.schedule INNER JOIN (select distinct(id) from VendorServices vs where (CURDATE() < vs.enddate or vs.enddate="0000-00-00") and locationType="Store" group by locationId,vendorId having sum(rate)=0) vs2 on vs2.id=vs.id WHERE  category=1 and materialid=9 group by locationid) as vs on vs.locationId=Stores.location
			LEFT OUTER JOIN (select locationId,SUM(quantity*weightInLbs*IFNULL(pickupsPerMonth,1)) /'.TON_KOEFF.' as cardTons2 FROM VendorServices vs LEFT OUTER JOIN Containers c on c.id=vs.containerId LEFT OUTER JOIN monthlyPickups mp on mp.id=vs.schedule INNER JOIN (select distinct(id) from VendorServices vs where (CURDATE() < vs.enddate or vs.enddate="0000-00-00") and locationType="Store" group by locationId,vendorId having sum(rate)=0) vs2 on vs2.id=vs.id WHERE category=1 and materialid=1 group by locationid) as vs2 on vs2.locationId=Stores.location
			LEFT OUTER JOIN (select locationId,SUM(quantity*weightInLbs*IFNULL(pickupsPerMonth,1)) /'.TON_KOEFF.' as otherTons2 FROM VendorServices vs LEFT OUTER JOIN Containers c on c.id=vs.containerId LEFT OUTER JOIN monthlyPickups mp on mp.id=vs.schedule INNER JOIN (select distinct(id) from VendorServices vs where (CURDATE() < vs.enddate or vs.enddate="0000-00-00") and locationType="Store" group by locationId,vendorId having sum(rate)=0) vs2 on vs2.id=vs.id WHERE category=1 and materialid!=9 and materialid!=1 group by locationid) as vs3 on vs3.locationId=Stores.location
			WHERE '.$where.' AND wi.invoiceDate IS NOT NULL) ttt GROUP BY monthyear ORDER BY CAST(REPLACE(monthyear, "/", "") AS UNSIGNED)';
        //echo $sqlQuery; exit();
        $res = $this->db->query($sqlQuery);

        $result = $res->result();
        foreach($result as $k=>$item) {
            array_push($months, $item->monthyear);
            $trend[$item->monthyear] = array();
            $trend[$item->monthyear]['Waste'] = floatval($item->WasteTons);
            $trend[$item->monthyear]['Cardboard'] = floatval($item->CardboardTons);
            $trend[$item->monthyear]['Commingle'] = floatval($item->CommingleTons);
            $trend[$item->monthyear]['Other'] = floatval($item->OtherTons);
        }

        return array('months'=>$months, 'trend'=>$trend);
    }
	
	public function getWasteTableData($from, $to, $display, $bystate, $start, $length, $orderColumn = null, $orderDir = null) {
	    $from = USToSQLDate($from); $to = USToSQLDate($to);
	    $groupBy = "";
	    $whereStateRegion = "";
	    $whereByState = "";
	    $tableWhere = '';

	    switch ($display) {
		    case 1: //By Regions								
			    $groupBy = "GROUP BY States.region";
			    break;
		    case 2: //'By District',
			    $groupBy = "GROUP BY Stores.district";
			    break;
		    case 3: //'By District (East Region)',
			    $groupBy = "GROUP BY Stores.district";
			    $tableWhere = $whereStateRegion = " AND States.region = 'East'";
			    break;
		    case 4: //'By District (Southeast Region)',
			    $groupBy = "GROUP BY Stores.district";
			    $tableWhere = $whereStateRegion = " AND States.region = 'Southeast'";
			    break;
		    case 5: //'By District (Midwest Region)',
			    $groupBy = "GROUP BY Stores.district";
			    $tableWhere = $whereStateRegion = " AND States.region = 'Midwest'";
			    break;
		    case 6: //'By District (South Region)',
			    $groupBy = "GROUP BY Stores.district";
			    $tableWhere = $whereStateRegion = " AND States.region = 'South'";
			    break;
		    case 7: //'By District (West Region)'
			    $groupBy = "GROUP BY Stores.district";
			    $tableWhere = $whereStateRegion = " AND States.region = 'West'";
			    break;	
		    case 8: //'By State'
			    $groupBy = "GROUP BY States.id";
			    if($bystate!=0)
				$tableWhere = $whereStateRegion = " AND States.id = ".$bystate;
			    break;	
	    }
	    if($bystate!=0)
		    $whereByState = "AND States.id = ".$bystate;
	    
	    $where = ' 1 '.$whereByState.' '.$tableWhere. ' ORDER BY '.$orderColumn.' '.$orderDir.' LIMIT '.$start.', '.$length;
	    $period = '"'.$from.'" AND "'.$to.'"';
	    
	    $sqlQuery = '
		    SELECT SQL_CALC_FOUND_ROWS Stores.id AS id, States.region AS region, district, location, States.name as state, open24hours, squareFootage, wi.id AS invoiceId, DATE_FORMAT(wi.invoiceDate, "%m/%Y") AS monthyear, wi.total AS cost, 
		    WasteTons, 
		    IFNULL(CommTons,0) + IFNULL(CommTons2, 0) as CommingleTons, 
		    IFNULL(CardTons,0) + IFNULL(CardTons2, 0) as CardboardTons, 
		    IFNULL(OtherTons,0) + IFNULL(OtherTons2, 0) as OtherTons
		    FROM Stores
		    LEFT OUTER JOIN WasteInvoices wi ON Stores.id = wi.locationId AND invoiceDate BETWEEN '.$period.'
		    LEFT JOIN States ON States.id = Stores.stateId
		    LEFT OUTER JOIN (Select invoiceid, sum(quantity*weightInLbs*IFNULL(pickupsPerMonth,1))/'.TON_KOEFF.' as WasteTons FROM WasteInvoiceServices as wis LEFT OUTER JOIN Containers as c ON c.id=wis.containerId left outer join monthlyPickups m on m.id=wis.schedule WHERE category=0 group by invoiceID) as wt on wt.invoiceId = wi.id
		    LEFT OUTER JOIN (Select invoiceid, sum(quantity*weightInLbs*IFNULL(pickupsPerMonth,1))/'.TON_KOEFF.' as CommTons FROM WasteInvoiceServices as wis LEFT OUTER JOIN Containers as c ON c.id=wis.containerId left outer join monthlyPickups m on m.id=wis.schedule WHERE category=1 and wis.materialid=9 group by invoiceID) as cmt on cmt.invoiceId = wi.id
		    LEFT OUTER JOIN (Select invoiceid, sum(quantity*weightInLbs*IFNULL(pickupsPerMonth,1))/'.TON_KOEFF.' as CardTons FROM WasteInvoiceServices as wis LEFT OUTER JOIN Containers as c ON c.id=wis.containerId left outer join monthlyPickups m on m.id=wis.schedule WHERE category=1 and wis.materialid=1  group by invoiceID) as cdt on cdt.invoiceId = wi.id
		    LEFT OUTER JOIN (Select invoiceid, sum(quantity*weightInLbs*IFNULL(pickupsPerMonth,1))/'.TON_KOEFF.' as OtherTons FROM WasteInvoiceServices as wis LEFT OUTER JOIN Containers as c ON c.id=wis.containerId left outer join monthlyPickups m on m.id=wis.schedule WHERE category=1 and wis.materialid!=9 and wis.materialid!=1 group by invoiceID) as ot on ot.invoiceId = wi.id
		    LEFT OUTER JOIN (select locationId,SUM(quantity*weightInLbs*IFNULL(pickupsPerMonth,1)) /'.TON_KOEFF.' as commTons2 FROM VendorServices vs LEFT OUTER JOIN Containers c on c.id=vs.containerId LEFT OUTER JOIN monthlyPickups mp on mp.id=vs.schedule INNER JOIN (select distinct(id) from VendorServices vs where (CURDATE() < vs.enddate or vs.enddate="0000-00-00") and locationType="Store" group by locationId,vendorId having sum(rate)=0) vs2 on vs2.id=vs.id WHERE  category=1 and materialid=9 group by locationid) as vs on vs.locationId=Stores.location
		    LEFT OUTER JOIN (select locationId,SUM(quantity*weightInLbs*IFNULL(pickupsPerMonth,1)) /'.TON_KOEFF.' as cardTons2 FROM VendorServices vs LEFT OUTER JOIN Containers c on c.id=vs.containerId LEFT OUTER JOIN monthlyPickups mp on mp.id=vs.schedule INNER JOIN (select distinct(id) from VendorServices vs where (CURDATE() < vs.enddate or vs.enddate="0000-00-00") and locationType="Store" group by locationId,vendorId having sum(rate)=0) vs2 on vs2.id=vs.id WHERE category=1 and materialid=1 group by locationid) as vs2 on vs2.locationId=Stores.location
		    LEFT OUTER JOIN (select locationId,SUM(quantity*weightInLbs*IFNULL(pickupsPerMonth,1)) /'.TON_KOEFF.' as otherTons2 FROM VendorServices vs LEFT OUTER JOIN Containers c on c.id=vs.containerId LEFT OUTER JOIN monthlyPickups mp on mp.id=vs.schedule INNER JOIN (select distinct(id) from VendorServices vs where (CURDATE() < vs.enddate or vs.enddate="0000-00-00") and locationType="Store" group by locationId,vendorId having sum(rate)=0) vs2 on vs2.id=vs.id WHERE category=1 and materialid!=9 and materialid!=1 group by locationid) as vs3 on vs3.locationId=Stores.location
		    WHERE '.$where.' ';

	    $res = $this->db->query($sqlQuery);	
	    
	    $result = $res->result();
	    
	    $this->db->select('FOUND_ROWS() as i');
	    $query = $this->db->get();
	    $row = $query->row();

	    return array(
		    'records' => $row->i,
		    'data' => $result
	    );	    

	}

    public function getWasteTableData2($from, $to, $display, $bystate, $start, $length, $orderColumn = null, $orderDir = null) {
        $from = USToSQLDate($from); $to = USToSQLDate($to);
        $groupBy = "";
        $whereStateRegion = "";
        $whereByState = "";
        $tableWhere = '';

        switch ($display) {
            case 1: //By Regions
                $groupBy = "GROUP BY States.region";
                break;
            case 2: //'By District',
                $groupBy = "GROUP BY Stores.district";
                break;
            case 3: //'By District (East Region)',
                $groupBy = "GROUP BY Stores.district";
                $tableWhere = $whereStateRegion = " AND States.region = 'East'";
                break;
            case 4: //'By District (Southeast Region)',
                $groupBy = "GROUP BY Stores.district";
                $tableWhere = $whereStateRegion = " AND States.region = 'Southeast'";
                break;
            case 5: //'By District (Midwest Region)',
                $groupBy = "GROUP BY Stores.district";
                $tableWhere = $whereStateRegion = " AND States.region = 'Midwest'";
                break;
            case 6: //'By District (South Region)',
                $groupBy = "GROUP BY Stores.district";
                $tableWhere = $whereStateRegion = " AND States.region = 'South'";
                break;
            case 7: //'By District (West Region)'
                $groupBy = "GROUP BY Stores.district";
                $tableWhere = $whereStateRegion = " AND States.region = 'West'";
                break;
            case 8: //'By State'
                $groupBy = "GROUP BY States.id";
                if($bystate!=0)
                    $tableWhere = $whereStateRegion = " AND States.id = ".$bystate;
                break;
        }
        if($bystate!=0)
            $whereByState = "AND States.id = ".$bystate;

        $where = ' 1 '.$whereByState.' '.$tableWhere;//. ' ORDER BY '.$orderColumn.' '.$orderDir;//.' LIMIT '.$start.', '.$length;
        $period = '"'.$from.'" AND "'.$to.'"';

        $time_1 = microtime(true);
        $sqlQuery = '
		    SELECT SQL_CALC_FOUND_ROWS Stores.id, Stores.id AS locationId, States.region AS region, district, location, States.name as state, open24hours, squareFootage, wi.id AS invoiceId, DATE_FORMAT(wi.invoiceDate, "%m/%Y") AS monthyear, wi.total AS cost,
		    0 AS WasteTons,
		    0 AS CommingleTons,
		    0 AS CardboardTons,
		    0 AS OtherTons
		    FROM Stores
		    LEFT OUTER JOIN WasteInvoices wi ON Stores.id = wi.locationId AND invoiceDate BETWEEN '.$period.'
		    LEFT JOIN States ON States.id = Stores.stateId
		    WHERE '.$where.' ';
        $res = $this->db->query($sqlQuery);
        $rows = $res->result();

        $this->db->select('FOUND_ROWS() as i');
        $query = $this->db->get();
        $r_row = $query->row();

        $sqlQuery = '
          SELECT
            invoiceId,
            category,
            materialId,
            SUM(quantity * weightInLbs * IFNULL(pickupsPerMonth, 1)) / '.TON_KOEFF.' AS VTons
          FROM WasteInvoiceServices AS wis
            LEFT OUTER JOIN Containers AS c
              ON c.id = wis.containerId
            LEFT OUTER JOIN monthlyPickups m
              ON m.id = wis.schedule
          GROUP BY category, materialId, invoiceId
          ORDER BY invoiceId';
        $res = $this->db->query($sqlQuery);
        $rowsTons1 = $res->result();

        $sqlQuery = '
          SELECT
            locationId,
            category,
            materialId,
            SUM(quantity * weightInLbs * IFNULL(pickupsPerMonth, 1)) / '.TON_KOEFF.' AS VTons
          FROM VendorServices vs
            LEFT OUTER JOIN Containers c
              ON c.id = vs.containerId
            LEFT OUTER JOIN monthlyPickups mp
              ON mp.id = vs.schedule
            INNER JOIN (SELECT DISTINCT
              (
              id
              )
            FROM VendorServices vs
            WHERE (
            CURDATE() < vs.endDate
            OR vs.endDate = "0000-00-00"
            )
            AND locationType = "Store"
            GROUP BY locationId,
                     vendorId
            HAVING SUM(rate) = 0) vs2
              ON vs2.id = vs.id
          GROUP BY locationId
          ORDER BY locationId';
        $res = $this->db->query($sqlQuery);
        $rowsTons2 = $res->result();

        $time_2 = microtime(true);

        $rowsTons1t = array();
        foreach($rowsTons1 as $k=>$rowTons1) {
            if(!isset($rowsTons1t[$rowTons1->invoiceId])) {
                $rowsTons1t[$rowTons1->invoiceId] = array();
            }
            array_push($rowsTons1t[$rowTons1->invoiceId], $rowTons1);
        }
        unset($rowsTons1);

        $rowsTons2t = array();
        foreach($rowsTons2 as $k=>$rowTons2) {
            if(!isset($rowsTons2t[$rowTons2->locationId])) {
                $rowsTons2t[$rowTons2->locationId] = array();
            }
            array_push($rowsTons2t[$rowTons2->locationId], $rowTons2);
        }
        unset($rowsTons2);

        foreach($rows as &$row) {
            if(isset($rowsTons1t[$row->invoiceId])) {
                foreach($rowsTons1t[$row->invoiceId] as $k=>$rowTons1) {
                    if($rowTons1->category==0) {
                        $row->WasteTons += $rowTons1->VTons;
                    }
                    if($rowTons1->category==1) {
                        if($rowTons1->materialId==9) {
                            $row->CommingleTons += $rowTons1->VTons;
                        }
                        if($rowTons1->materialId==1) {
                            $row->CardboardTons += $rowTons1->VTons;
                        }
                        if($rowTons1->materialId!=1 && $rowTons1->materialId!=9) {
                            $row->OtherTons += $rowTons1->VTons;
                        }
                    }
                }
            }

            if(isset($rowsTons2t[$row->locationId])) {
                foreach($rowsTons2t[$row->locationId] as $rowTons2) {
                    if($rowTons2->category==1) {
                        if($rowTons2->materialId==9) {
                            $row->CommingleTons += $rowTons2->VTons;
                        }
                        if($rowTons2->materialId==1) {
                            $row->CardboardTons += $rowTons2->VTons;
                        }
                        if($rowTons2->materialId!=1 && $rowTons2->materialId!=9) {
                            $row->OtherTons += $rowTons2->VTons;
                        }
                    }
                }
            }
        }

        $time_3 = microtime(true);

        $orderColumnDatatype = 'int';
        if($orderColumn=='state' || $orderColumn=='open24hours') {
            $orderColumnDatatype = 'text';
        }
        $objOrderby = new OrderBy($orderColumn, $orderDir, $orderColumnDatatype);
        usort($rows, array(&$objOrderby, 'run'));

        $time_4 = microtime(true);

        return array(
            'records' => $r_row->i,
            'data' => array_slice($rows, $start, $length, true),
            'logs' => 'SQL: '.($time_2-$time_1).' PHP: '.($time_3-$time_2).' SORT: '.($time_4-$time_3)
        );

    }

	/*
	 * Services
	 */
	public function getServicesData($display, $bystate) {
		$result =  Array ('containers'=>'', 'duration'=>'', 'Frequency '=>'', 'table');
		$groupBy = "";
		$whereStateRegion = "";
		$whereByState = "";
		$what = "m.name";
		$tableWhere = '';
		$tableOrder = '';
		switch ($display) {
			case 1: //By Regions
				$groupBy = "GROUP BY States.region";				
				$what = "States.region";
				$tableOrder = ' ORDER BY States.region';
				break;
			case 2: //'By District',
				$groupBy = "GROUP BY Stores.district";
				$tableOrder = ' ORDER BY Stores.district';
				$what = "Stores.district";
				break;
			case 3: //'By District (East Region)',
				$groupBy = "GROUP BY Stores.district";
				$tableWhere = $whereStateRegion = " AND States.region = 'East'";
				$tableOrder = ' ORDER BY Stores.district';
				$what = "Stores.district";
				break;
			case 4: //'By District (Southeast Region)',
				$groupBy = "GROUP BY Stores.district";
				$tableWhere = $whereStateRegion = " AND States.region = 'Southeast'";
				$tableOrder = ' ORDER BY Stores.district';
				$what = "Stores.district";
				break;
			case 5: //'By District (Midwest Region)',
				$groupBy = "GROUP BY Stores.district";
				$tableWhere = $whereStateRegion = " AND States.region = 'Midwest'";
				$tableOrder = ' ORDER BY Stores.district';
				$what = "Stores.district";
				break;
			case 6: //'By District (South Region)',
				$groupBy = "GROUP BY Stores.district";
				$tableWhere = $whereStateRegion = " AND States.region = 'South'";
				$tableOrder = ' ORDER BY Stores.district';
				$what = "Stores.district";
				break;
			case 7: //'By District (West Region)'
				$groupBy = "GROUP BY Stores.district";
				$tableWhere = $whereStateRegion = " AND States.region = 'West'";
				$tableOrder = ' ORDER BY Stores.district';
				$what = "Stores.district";
				break;	
			case 8: //'By State'
				$groupBy = "GROUP BY States.id";
				if($bystate!=0)
				    $tableWhere = $whereStateRegion = " AND States.id = ".$bystate;
				$tableOrder = ' ORDER BY States.id';
				$what = "States.name";
				break;	
		}
		if($bystate!=0)
		    $whereByState = "AND States.id = ".$bystate;
		
		$sqlQuery = '
		    SELECT COUNT(containerType) AS value, containerType AS Name
		       FROM Stores AS s LEFT OUTER JOIN VendorServices vs ON s.location = vs.LocationID 
		       INNER JOIN States ON States.id = s.stateid 
		       INNER JOIN Containers c ON vs.containerId = c.id
		       WHERE
			 vs.locationType = "STORE"
			 AND s.status = "YES"
			 AND (curdate() < vs.enddate OR vs.enddate = "0000-00-00") '.$tableWhere.' '.$whereByState.'
		       GROUP BY containerType';
		$res = $this->db->query($sqlQuery);		
		$result['containers'] = $res->result();
		
		$sqlQuery = '
		    SELECT COUNT(category) AS value, "Waste" AS Name
		    FROM Stores AS s LEFT OUTER JOIN VendorServices vs ON s.location = vs.LocationID
		    INNER JOIN States ON States.id = s.stateid
		    WHERE
			vs.locationType = "STORE"
			AND s.status = "YES"
			AND (curdate() < vs.enddate OR vs.enddate = "0000-00-00") '.$tableWhere.' '.$whereByState.'
			AND category = 0
		    UNION
		    SELECT COUNT(category) AS value, "Cardboard" AS Name
		    FROM Stores AS s LEFT OUTER JOIN VendorServices vs ON s.location = vs.LocationID
		    INNER JOIN States ON States.id = s.stateid
		    WHERE
			 vs.locationType = "STORE"
			 AND s.status = "YES"
			 AND (curdate() < vs.enddate OR vs.enddate = "0000-00-00") '.$tableWhere.' '.$whereByState.'
			 AND category = 1
			 AND materialid = 1
		    UNION
		    SELECT COUNT(category) AS value, "Commingle" AS Name
		    FROM Stores AS s LEFT OUTER JOIN VendorServices vs ON s.location = vs.LocationID
		    INNER JOIN States ON States.id = s.stateid
		    WHERE
			 vs.locationType = "STORE"
			 AND s.status = "YES"
			 AND (curdate() < vs.enddate OR vs.enddate = "0000-00-00") '.$tableWhere.' '.$whereByState.'
			 AND category = 1
			 AND materialid = 9
		    UNION
		    SELECT COUNT(category) AS value, "Other" AS Name
		    FROM Stores AS s LEFT OUTER JOIN VendorServices vs ON s.location = vs.LocationID
		    INNER JOIN States ON States.id = s.stateid
		    WHERE
			 vs.locationType = "STORE"
			 AND s.status = "YES"
			 AND (curdate() < vs.enddate OR vs.enddate = "0000-00-00") '.$tableWhere.' '.$whereByState.'
			 AND category = 1
			 AND materialid != 1
			 AND materialid != 9';		
		$res = $this->db->query($sqlQuery);
		$result['services'] = $res->result();
		
		$sqlQuery = '
		    SELECT COUNT(mp.name) AS value, mp.name AS Name
		    FROM Stores AS s LEFT OUTER JOIN VendorServices vs ON s.location = vs.LocationID
		    INNER JOIN States ON States.id = s.stateid
		    INNER JOIN monthlyPickups mp ON vs.schedule = mp.id
		    WHERE
		      vs.locationType = "STORE"
		      AND s.status = "YES"
		      AND (curdate() < vs.enddate OR vs.enddate = "0000-00-00") '.$tableWhere.' '.$whereByState.'
		    GROUP BY mp.name';	
		$res = $this->db->query($sqlQuery);		
		$result['frequency'] = $res->result();

		$sqlQuery = '
		    SELECT Stores.id AS id
			, States.name AS state
			, States.region AS region
			, district
			, location
			, open24hours
			, squareFootage
			, c.name AS container
			, mp.name AS frequency
			, quantity * rate AS cost
		   FROM
		     Stores
		   LEFT JOIN States
		   ON States.id = Stores.stateId
		   LEFT OUTER JOIN VendorServices vs
		   ON Stores.location = vs.LocationID AND locationType = "STORE"
		   INNER JOIN Containers c
		   ON c.id = containerId
		   LEFT OUTER JOIN monthlyPickups mp
		   ON mp.id = vs.schedule
		   WHERE
		     Stores.status = "YES"
		     AND (curdate() < vs.enddate OR vs.enddate = "0000-00-00") '.$tableWhere.' '.$whereByState.'';
		$res = $this->db->query($sqlQuery);
		$result['table'] = $res->result();

		return $result;		
	}
	/*
	 * List
	 */
	public function getList($start, $length, $searchToken = null, $orderColumn = null, $orderDir = null, $bystate) {
    
		$sqlQuery = '
		    SELECT SQL_CALC_FOUND_ROWS
			Stores.id AS id
			, States.region AS region
			, district
			, location
			, addressLine1
			, city
			, States.name AS state
			, Stores.postCode
			, Stores.open24hours
			, Stores.officeLocation
			, Stores.squareFootage
			, (ry.recycling / (wt.Waste + ry.recycling)) * 100 AS diversion
			, (wt.wasteCost + ry.recyclingCost) / Stores.squareFootage AS cost
		    FROM
		      Stores
		    LEFT JOIN States
		    ON States.id = Stores.stateId
		    LEFT OUTER JOIN (SELECT sum(quantity * weightInLbs * ifnull(pickupsPerMonth, 1)) AS waste
					  , sum(quantity * rate) AS wasteCost
					  , locationId
				     FROM
				       VendorServices vs
				     LEFT OUTER JOIN Containers c
				     ON c.id = vs.containerId
				     LEFT OUTER JOIN monthlyPickups mp
				     ON mp.id = vs.schedule
				     WHERE
				       (curdate() < vs.enddate
				       OR vs.enddate = "0000-00-00")
				       AND category = 0
				     GROUP BY
				       locationId) AS wt
		    ON wt.locationId = Stores.location
		    LEFT OUTER JOIN (SELECT sum(quantity * weightInLbs * ifnull(pickupsPerMonth, 1)) AS recycling
					  , sum(quantity * rate) AS recyclingCost
					  , locationId
				     FROM
				       VendorServices vs
				     LEFT OUTER JOIN Containers c
				     ON c.id = vs.containerId
				     LEFT OUTER JOIN monthlyPickups mp
				     ON mp.id = vs.schedule
				     WHERE
				       (curdate() < vs.enddate
				       OR vs.enddate = "0000-00-00")
				       AND category = 1
				     GROUP BY
				       locationId) AS ry
		    ON ry.locationId = Stores.location
		    WHERE
		      Stores.status = "YES"';
		
		if($bystate>0) {
		    $sqlQuery .= ' AND States.id = '.$bystate.' ';
		}

		if (!empty($searchToken)) {
		    $sqlQuery .= ' AND (
			States.region LIKE "%'.$searchToken.'%" 
			    OR district LIKE "%'.$searchToken.'%" 
			    OR location LIKE "%'.$searchToken.'%" 
			    OR addressLine1 LIKE "%'.$searchToken.'%"
			    OR States.name LIKE "%'.$searchToken.'%"
			    OR postCode LIKE "%'.$searchToken.'%"
			    OR city LIKE "%'.$searchToken.'%")';
		}		

		if ($orderColumn) {
		    $sqlQuery .= ' ORDER BY '.$orderColumn.' '.$orderDir.' ';
		}
		$sqlQuery .= ' LIMIT '.$start.', '.$length.' ';
		//echo $sqlQuery;
		$result = $this->db->query($sqlQuery)->result();

		$this->db->select('FOUND_ROWS() as i');
		$query = $this->db->get();

		$row = $query->row();

		return array(
			'records' => $row->i,
			'data' => $result
		);
	}












































	/**
	 * @param integer id `Stores`.`id`
	 * @return array Store contacts
	 */
	public function getContacts($id)
	{
		$s = "
		SELECT
			*,
			IF(
				`email` != '',
				CONCAT('<a href=\"mailto:', `email`, '\">', `email`, '</a>'),
				'- - -'
			) AS email_lnk
		FROM
			`StoreContacts`
		WHERE 
			`storeId` = " . (int)$id;
		$r = $this->db->query($s);
		return $r->result('array');
	}


	/**
	 * @param integer id `Stores`.`id`
	 * @return array choosen Store data
	 */
	public function getStore($id)
	{
		$id = (int)$id;

		$s = "
		SELECT
			*,
			`location` AS name
		FROM
			`Stores`
		WHERE 
			`id` = " . $id . "
		LIMIT
			1";
		$r = $this->db->query($s);
		return $r->row_array();
	}


	/**
	 * @param string startDate date in format YYYY-MM-DD
	 * @param string endDate date in format YYYY-MM-DD
	 * @param integer id `Stores`.`id`
	 * @return array mixed
	 */
	public function getWasteTable($startDate, $endDate, $id)
	{
		$id = (int)$id;

		$s = "
		SELECT 
			`dc`.`location` AS DC_name,
			`dc`.`squareFootage` AS DC_squareFootage,
			DATE_FORMAT(`wi`.`invoiceDate`, '%Y') AS invoice_y,
			DATE_FORMAT(`wi`.`invoiceDate`, '%m') AS invoice_m,
			`ws`.`quantity`, 
			`m`.`name`,
			`wi`.`total` AS cost

		FROM 
			`WasteInvoices` AS wi

		RIGHT JOIN
			`Stores` AS dc ON (`wi`.`locationId` = `dc`.`id`)

		LEFT OUTER JOIN 
			`WasteInvoiceServices` AS ws ON (`wi`.`id` = `ws`.`invoiceId`)

		LEFT OUTER JOIN 
			`Materials` AS m ON (`m`.`id` = `materialId`)

		WHERE 
			`wi`.`locationType` = 'STORE' 
			AND `ws`.`unitID` = 1
			AND `wi`.`invoiceDate` >= '" . $startDate . "'
			AND `wi`.`invoiceDate` <= '" . $endDate . "'
			AND `wi`.`locationId` = " . $id . "
		ORDER BY
			`wi`.`invoiceDate` ASC";
		$r = $this->db->query($s);
		$data = $r->result("array");

		$tmp = array();
		while (list($k,$v) = each($data))
		{
			$d = $v['invoice_y'] . $v['invoice_m'];

			if(isset($tmp[$d]))
			{
				$tmp[$d]['waste']		+= $v['quantity'];
				$tmp[$d]['hazardous']	+= ($v['name'] == 'Hazardous') ? $v['quantity'] : 0;
				$tmp[$d]['cost']		+= $v['cost'];
			}
			else
			{
				$tmp[$d] = array(
					'dc_name'	=> $v['DC_name'],
					'sqft'		=> $v['DC_squareFootage'],
					'period'	=> $v['invoice_m'] . '/' . $v['invoice_y'],
					'waste'		=> $v['quantity'],
					'hazardous'	=> ($v['name'] == 'Hazardous') ? $v['quantity'] : 0,
					'cost'		=> $v['cost'],
				);
			}
		}

		$sum = array(
			'sqft'		=> 0,
			'waste'		=> 0,
			'hazardous'	=> 0,
			'cost'		=> 0,
			'other'		=> 0,
		);

		$rows = array();

		$chart_data = array(
			'dates'		=> array(),
			'waste'		=> array(),
			'hazardous'	=> array(),
			'other'		=> array(),
		);

		while (list($k,$v) = each($tmp))
		{
			$rows[$k] = $v;
			$rows[$k]['other'] = $v['waste'] - $v['hazardous'];

			$sum['sqft']		+= $v['sqft']; // why?
			$sum['waste']		+= $v['waste'];
			$sum['hazardous']	+= $v['hazardous'];
			$sum['other']		+= $rows[$k]['other'];
			$sum['cost']		+= $v['cost'];

			$chart_data['dates'][]		= $v['period'];
			$chart_data['waste'][]		= (int)$v['waste'];
			$chart_data['hazardous'][]	= (int)$v['hazardous'];
			$chart_data['other'][]		= (int)$rows[$k]['other'];
		}

		return array(
			'rows'			=> $rows, 
			'sum'			=> $sum,
			'chart_data'	=> $chart_data,
		);
	}


	/**
	 * @param string startDate date in format YYYY-MM-DD
	 * @param string endDate date in format YYYY-MM-DD
	 * @param integer id `Stores`.`id`
	 * @return array mixed
	 */
	public function getRecycleInvoices($startDate, $endDate, $id)
	{
		$id = (int)$id;

		$query = $this->db->query("
			SELECT * FROM Materials 
		");
		
		$tempMaterials = $query->result();
		$materials = array();
		
		foreach ($tempMaterials as $v) 
		{
			$materials[$v->id]['co2'] = $v->CO2Saves;
			$materials[$v->id]['kwh'] = $v->EnergySaves;
		}

		// Trees - from Dropbox\nikolay\cleanData\HowToComputeSustainabilityGains.xlsx
		// 1 ton CardBoard	= 17 Trees (35 foot)
		// 1 ton Newsprint	= 15 Trees (35 foot)
		// 1 ton Paper		= 17 Trees (35 foot)
		// ???

		// landfill - from Dropbox\nikolay\cleanData\HowToComputeSustainabilityGains.xlsx
		// CardBoard	= 9
		// Newsprint	= 4.6
		// Paper		= 3.3
		$s = "
		SELECT 
			`dc`.`location` AS DC_name,
			`dc`.`squareFootage` AS DC_squareFootage,
			`ri`.`id`,
			DATE_FORMAT(`ri`.`invoiceDate`, '%Y') AS invoice_y,
			DATE_FORMAT(`ri`.`invoiceDate`, '%m') AS invoice_m,
			SUM(IF(`rim`.`materialId` = 1, `rim`.`quantity`, 0)) AS cardboard,
			SUM(IF(`rim`.`materialId` = 3, `rim`.`quantity`, 0)) AS aluminum,
			SUM(IF(`rim`.`materialId` = 8, `rim`.`quantity`, 0)) AS film,
			SUM(IF(`rim`.`materialId` = 5, `rim`.`quantity`, 0)) AS plastic,
			SUM(IF(`rim`.`materialId` = 7, `rim`.`quantity`, 0)) AS trees,
			SUM(`rim`.`quantity` * `rim`.`pricePerUnit`) AS rebate,
			SUM(
				CASE `rim`.`materialId`
					WHEN 1 THEN 
						`rim`.`quantity` * 9
					ELSE
						0
				END
			) AS landfill

		FROM
			`RecyclingInvoices` AS ri

		LEFT OUTER JOIN `RecyclingInvoicesMaterials` AS rim 
				ON (`rim`.`invoiceId` = `ri`.`id` AND `rim`.`unit` = 1)

		LEFT JOIN `Stores` AS dc 
			ON (`ri`.`locationId` = `dc`.`id` AND `ri`.`locationType` = 'STORE')

		WHERE
			`ri`.`locationType` = 'STORE' 
			AND `ri`.`locationId` = " . $id . "
			AND (`ri`.`invoiceDate` BETWEEN '" . $startDate . "' AND ' " . $endDate . "')

		GROUP BY
			`ri`.`id`

		ORDER BY
			`ri`.`invoiceDate` ASC";
		$query = $this->db->query($s);
		$temp = $query->result('array');
		//echo '<pre>'; dump($temp);

		$invoices	= array();
		$invoices_sum	= array(
			'DC_count'	=> 0,
			'sqft'		=> 0,
			'cardboard'	=> 0,
			'aluminum'	=> 0,
			'film'		=> 0,
			'plastic'	=> 0,
			'trees'		=> 0,
			'landfill'	=> 0,
			'kwh'		=> 0,
			'co2'		=> 0,
			'rebate'	=> 0,
		);
		$by_months = array();

		$materialTons = 0;

		while (list($k,$v) = each($temp))
		{
			// Every invoice
			$invoices[$v['id']] = array(
				'DC_name'		=> $v['DC_name'],
				'sqft'			=> $v['DC_squareFootage'],
				'cardboard'		=> $v['cardboard'],
				'aluminum'		=> $v['aluminum'],
				'film'			=> $v['film'],
				'plastic'		=> $v['plastic'],
				'trees'			=> $v['trees'],
				'landfill'		=> $v['landfill'],
				'kwh'			=>	($v['cardboard']	* $materials[1]['kwh']
									+ $v['aluminum']	* $materials[3]['kwh']
									+ $v['film']		* $materials[8]['kwh']
									+ $v['plastic']		* $materials[5]['kwh']
									+ $v['trees']		* $materials[7]['kwh']),

				'co2'			=>	($v['cardboard']	* $materials[1]['co2']
									+ $v['aluminum']	* $materials[3]['co2']
									+ $v['film']		* $materials[8]['co2']
									+ $v['plastic']		* $materials[5]['co2']
									+ $v['trees']		* $materials[7]['co2']),

				'rebate'		=> (double)$v['rebate'],
			);


			// Invoices summary
			$invoices_sum['DC_count']++;
			$invoices_sum['sqft']		+= $invoices[$v['id']]['sqft'];
			$invoices_sum['cardboard']	+= $invoices[$v['id']]['cardboard'];
			$invoices_sum['aluminum']	+= $invoices[$v['id']]['aluminum'];
			$invoices_sum['film']		+= $invoices[$v['id']]['film'];
			$invoices_sum['plastic']	+= $invoices[$v['id']]['plastic'];
			$invoices_sum['trees']		+= $invoices[$v['id']]['trees'];
			$invoices_sum['landfill']	+= $invoices[$v['id']]['landfill'];
			$invoices_sum['kwh']		+= $invoices[$v['id']]['kwh'];
			$invoices_sum['co2']		+= $invoices[$v['id']]['co2'];
			$invoices_sum['rebate']		+= $invoices[$v['id']]['rebate'];


			// All recycled materials in tons
			$materialTons +=  $invoices[$v['id']]['cardboard']
							+ $invoices[$v['id']]['aluminum']
							+ $invoices[$v['id']]['film']
							+ $invoices[$v['id']]['plastic']
							+ $invoices[$v['id']]['trees'];


			// Tonage by months
			$d = $v['invoice_y'] . $v['invoice_m'];

			if(!isset($by_months[$d]))
			{
				$by_months[$d] = array(
					'period'	=> $v['invoice_m'] . '/' . $v['invoice_y'],
					'waste'		=> 0,
					'cardboard'	=> 0,
					'aluminum'	=> 0,
					'film'		=> 0,
					'plastic'	=> 0,
				);
			}

			$by_months[$d]['waste']		+=   $invoices[$v['id']]['cardboard']
										   + $invoices[$v['id']]['aluminum']
										   + $invoices[$v['id']]['film']
										   + $invoices[$v['id']]['plastic'];
			$by_months[$d]['cardboard']	+= $invoices[$v['id']]['cardboard'];
			$by_months[$d]['aluminum']	+= $invoices[$v['id']]['aluminum'];
			$by_months[$d]['film']		+= $invoices[$v['id']]['film'];
			$by_months[$d]['plastic']	+= $invoices[$v['id']]['plastic'];
		}


		// chart data
		$chart_data = array();
		foreach($by_months AS $v)
		{
			$chart_data['period'][]		= $v['period'];
			$chart_data['waste'][]		= $v['waste'];
			$chart_data['cardboard'][]	= $v['cardboard'];
			$chart_data['aluminum'][]	= $v['aluminum'];
			$chart_data['film'][]		= $v['film'];
			$chart_data['plastic'][]	= $v['plastic'];
		}


		return array(
			'rows'			=> $invoices, 
			'sum'			=> $invoices_sum,
			'by_months'		=> $by_months,
			'chart_data'	=> $chart_data,
			'materialTons'	=> $materialTons,
		);
	}


	/**
	 * @param double recycled recycled materials tons
	 * @param double waste waste materials tons
	 * @retrun double diversion rate
	 */
	public function getDiversionRate($locationId) {
	    $sqlQuery = '
		SELECT (ry.recycling/(wt.Waste + ry.recycling))*100 AS diversion
		FROM Stores
		LEFT OUTER JOIN (SELECT SUM(quantity*weightInLbs*IFNULL(pickupsPerMonth,1)) as waste, SUM(quantity * rate) as wasteCost,  locationId from VendorServices vs LEFT OUTER JOIN Containers c on c.id=vs.containerId LEFT OUTER JOIN monthlyPickups mp on mp.id=vs.schedule WHERE (CURDATE() < vs.enddate or vs.enddate="0000-00-00") and category = 0 group by locationId) as wt on wt.locationId=Stores.location
		LEFT OUTER JOIN (SELECT SUM(quantity*weightInLbs*IFNULL(pickupsPerMonth,1)) as recycling, SUM(quantity * rate) as recyclingCost, locationId from VendorServices vs LEFT OUTER JOIN Containers c on c.id=vs.containerId LEFT OUTER JOIN monthlyPickups mp on mp.id=vs.schedule WHERE (CURDATE() < vs.enddate or vs.enddate="0000-00-00") and category = 1 group by locationId) as ry on ry.locationId=Stores.location
		where
		Stores.status = "YES" and Stores.location="'.$locationId.'"';
	    $query = $this->db->query($sqlQuery);
	    $row = $query->row();
	    if(!empty($row)) {
		$diversion = $row->diversion;	    
		return floatval($diversion);
	    }
	    
	    return 0.0;
	}


	/**
	 * @param string startDate date in format YYYY-MM-DD
	 * @param string endDate date in format YYYY-MM-DD
	 * @param integer id `Stores`.`id`
	 * @return array mixed
	 */
	function getSingleDCCost($startDate, $endDate, $id) 
	{
		$id = (int)$id;
/*
		$fees = array(
			1 => 'Freight Charge',
			2 => 'Fuel Charge',
			3 => 'Stop Charge',
			4 => 'Tax',
			5 => 'Other',
			6 => 'Repair',
		);
*/
		/////////////////////////////////////////////////
		// Waste
		$s = "
		SELECT 
			`dc`.`location` AS DC_name,
			`dc`.`squareFootage` AS DC_squareFootage,
			`wi`.`id`,
			DATE_FORMAT(`wi`.`invoiceDate`, '%Y') AS invoice_y,
			DATE_FORMAT(`wi`.`invoiceDate`, '%m') AS invoice_m,
			`ws`.`quantity`, 
			`wf`.`feeType`, 
			`wf`.`feeAmount`,
			`wf`.`waived`,
			`ws`.`rate`,
			`ws`.`schedule`,
			`srst`.`name` AS sname

		FROM 
			`WasteInvoices` AS wi

		LEFT OUTER JOIN 
			`Stores` AS dc ON `dc`.`id` = `wi`.`locationId`

		LEFT OUTER JOIN 
			`WasteInvoiceServices` AS ws ON `wi`.`id` = `ws`.`invoiceId`

		LEFT OUTER JOIN 
			`SupportRequestServiceTypes` AS srst ON `ws`.`serviceId` = `srst`.`id`

		LEFT OUTER JOIN 
			`WasteInvoiceFees` AS wf ON `wi`.`id` = `wf`.`invoiceId`

		WHERE 
			`wi`.`locationType` = 'STORE' 
			AND `ws`.`unitID` = 1
			AND (`wi`.`invoiceDate` BETWEEN '" . $startDate . "' AND '" . $endDate . "')
			AND `wi`.`locationId` = " . $id . "

		ORDER BY
			`wi`.`invoiceDate` ASC";

		$q = $this->db->query($s);
		$waste_data = $q->result('array');

		$tmp = array();
		while (list($k,$v) = each($waste_data))
		{
			$d = $v['invoice_y'] . $v['invoice_m'];

			if(!isset($tmp[$d]))
			{
				$tmp[$d]['dc_name']					= $v['DC_name'];
				$tmp[$d]['sqft']					= $v['DC_squareFootage'];
				$tmp[$d]['period']					= $v['invoice_m'] . '/' . $v['invoice_y'];
				$tmp[$d]['total_tonage']			= $v['quantity'];
				$tmp[$d]['waste_service']			= 0;
				$tmp[$d]['waste_service_shedule']	= 0;
				$tmp[$d]['waste_equipment_fee']		= 0;
				$tmp[$d]['waste_haul_fee']			= 0;
				$tmp[$d]['waste_disposal_fee']		= 0;
				$tmp[$d]['recycling_rebate']		= 0;
				$tmp[$d]['other_fee']				= 0;
				$tmp[$d]['net']						= 0;
				$tmp[$d]['cost']					= 0;
				$tmp[$d]['waived_fee']				= 0;
			}

			$tmp[$d]['waste_service'] += $v['feeAmount'];

			if($v['schedule'] > 0)
			{
				$tmp[$d]['waste_service_shedule']	+= $v['feeAmount'];
			}

			if($v['feeType'] == 6)
			{
				$tmp[$d]['waste_equipment_fee'] += $v['feeAmount'];
			}
			elseif($v['feeType'] == 1)
			{
				$tmp[$d]['waste_haul_fee'] += $v['feeAmount'];
			}
			elseif($v['feeType'] == 3)
			{
				$tmp[$d]['waste_disposal_fee'] += $v['feeAmount'];
			}
			else
			{
				$tmp[$d]['other_fee'] += $v['feeAmount'];
			}

			$tmp[$d]['recycling_rebate'] += $v['quantity'] * $v['rate'];
			$tmp[$d]['cost'] += $v['quantity'] * $v['rate'];

			if($v['waived'] == 1)
			{
				$tmp[$d]['waived_fee'] += $v['feeAmount'];
			}
		}


		/////////////////////////////////////////////////
		// Recycling
		$s = "
		SELECT 
			`dc`.`location` AS DC_name,
			`dc`.`squareFootage` AS DC_squareFootage,
			`ri`.`id`,
			DATE_FORMAT(`ri`.`invoiceDate`, '%Y') AS invoice_y,
			DATE_FORMAT(`ri`.`invoiceDate`, '%m') AS invoice_m,
			`rim`.`quantity`,
			`rim`.`pricePerUnit`,
			ri.*,
			(
				SELECT 
					SUM(`feeAmount`)
				FROM 
					`RecyclingInvoicesFees` AS rif 
				WHERE 
					`ri`.`id` = `rif`.`invoiceId`
			) AS rif,

			(
				SELECT 
					SUM(`feeAmount`)
				FROM 
					`RecyclingInvoicesFees` AS rif 
				WHERE 
					`ri`.`id` = `rif`.`invoiceId`
					AND `rif`.`waived` = 1
			) AS waived_fee

		FROM 
			`RecyclingInvoices` AS ri

		LEFT OUTER JOIN 
			`RecyclingInvoicesMaterials` AS rim ON (`rim`.`invoiceId` = `ri`.`id`)

		LEFT OUTER JOIN 
			`Stores` AS dc ON (`dc`.`id` = `ri`.`locationId`)

		WHERE 
			`ri`.`locationType` = 'STORE' 
			AND `ri`.`locationId` = " . $id . "
			AND (`ri`.`invoiceDate` BETWEEN '" . $startDate . "' AND ' " . $endDate . "')

		ORDER BY
			`ri`.`invoiceDate` ASC";

		$q = $this->db->query($s);
		$recycle_data = $q->result('array');

		while (list($k,$v) = each($waste_data))
		{
			$d = $v['invoice_y'] . $v['invoice_m'];

			if(!isset($tmp[$d]))
			{
				$tmp[$d]['dc_name']				= $v['DC_name'];
				$tmp[$d]['sqft']				= $v['DC_squareFootage'];
				$tmp[$d]['period']				= $v['invoice_m'] . '/' . $v['invoice_y'];
				$tmp[$d]['total_tonage']		= $v['quantity'];
				$tmp[$d]['waste_service']		= 0;
				$tmp[$d]['waste_equipment_fee']	= 0;
				$tmp[$d]['waste_haul_fee']		= 0;
				$tmp[$d]['waste_disposal_fee']	= 0;
				$tmp[$d]['recycling_rebate']	= 0;
				$tmp[$d]['other_fee']			= 0;
				$tmp[$d]['net']					= 0;
				$tmp[$d]['cost']				= 0;
				$tmp[$d]['waived_fee']			= $v['waived_fee'];
			}

			$tmp[$d]['recycling_rebate']	+= $v['pricePerUnit'] * $v['quantity'];

			$net =	  $v['recycling_rebate'] 
					- $v['waste_service'] 
					- ($v['rif'] * $v['quantity']);
			$tmp[$d]['net'] = ($net > 0) ? $net : 0;


		}

		$sum = array(
			'sqft'					=> 0,
			'total_tonage'			=> 0,
			'waste_service'			=> 0,
			'waste_service_shedule'	=> 0,
			'waste_equipment_fee'	=> 0,
			'waste_haul_fee'		=> 0,
			'waste_disposal_fee'	=> 0,
			'recycling_rebate'		=> 0,
			'other_fee'				=> 0,
			'net'					=> 0,
			'waived_fee'			=> 0,
		);

		$chart_data = array(
			'months'	=> array(),
			'net'		=> array(),
			'savings'	=> array(),
			'cost'		=> array(),
		);

		foreach($tmp AS $v)
		{
			$sum['sqft']					+= $v['sqft'];
			$sum['total_tonage']			+= $v['total_tonage'];
			$sum['waste_service']			+= $v['waste_service'];
			$sum['waste_service_shedule']	+= $v['waste_service_shedule'];
			$sum['waste_equipment_fee']		+= $v['waste_equipment_fee'];
			$sum['waste_haul_fee']			+= $v['waste_haul_fee'];
			$sum['waste_disposal_fee']		+= $v['waste_disposal_fee'];
			$sum['recycling_rebate']		+= $v['recycling_rebate'];
			$sum['other_fee']				+= $v['other_fee'];
			$sum['net']						+= $v['net'];
			$sum['waived_fee']				+= $v['waived_fee'];

			$chart_data['months'][]			= $v['period'];
			$chart_data['net'][]			= $v['net'];
			$chart_data['savings'][]		= $v['recycling_rebate'];
			$chart_data['cost'][]			= $v['waste_service'];
		}

		return array(
			'rows'			=> $tmp, 
			'sum'			=> $sum,
			'chart_data'	=> $chart_data,
		);
	}


	/**
	 * @param integer id `Stores`.`id`
	 * @param integer iDisplayStart "x" in "LIMIT x,y"
	 * @param integer iDisplayLength "y" in "LIMIT x,y"
	 * @param string sortColumn "x" in "ORDER BY `x`"
	 * @param string sortDir "x" in "ORDER BY `colname` x"
	 * @return array mixed 
	 */
	public function getInvoices($id, $iDisplayStart=0, $iDisplayLength=10000, $sortColumn='invoice_date', $sortDir='DESC')
	{
		$s = "
		SELECT
			SQL_CALC_FOUND_ROWS
			`wi`.`id` AS id,
			`wi`.`invoiceNumber` AS invoice_num,
			DATE_FORMAT(`wi`.`invoiceDate`, '%m/%d/%Y') AS invoice_date,
			DATE_FORMAT(`wi`.`dateSent`, '%m/%d/%Y') AS sent_date,
			`dc`.`city` AS location,
			`v`.`name` AS vendor,
			`m`.`name` AS material,
			`wis`.`quantity`,
			(`wis`.`quantity` * `wis`.`rate`) AS total_rebate,
			`wi`.`total`,
			'waste' AS row_type

		FROM
			WasteInvoiceServices AS wis

		INNER JOIN `WasteInvoices` AS wi 
			ON (`wi`.`id` = `wis`.`invoiceId`)

		INNER JOIN `Materials` AS m
			ON (`m`.`id` = `wis`.`materialId`)

		INNER JOIN `Vendors` AS v 
			ON (`v`.`id` = `wi`.`vendorId`)

		INNER JOIN `Stores` AS dc 
			ON (`dc`.`id` = `wi`.`locationId`)

		WHERE 
			`dc`.`id` = " . $id . "
			AND `wi`.`locationType` = 'STORE'


		UNION


		SELECT
			`ri`.`id` AS id,
			`ri`.`poNumber` AS invoice_num,
			DATE_FORMAT(`ri`.`invoiceDate`, '%m/%d/%Y') AS invoice_date,
			DATE_FORMAT(`ri`.`dateSent`, '%m/%d/%Y') AS sent_date,
			`dc`.`city` AS location,
			`v`.`name` AS vendor,
			`m`.`name` AS material,
			`rim`.`quantity` AS quantity,
			(`rim`.`quantity` * `rim`.`pricePerUnit`) AS total_rebate,
			`ri`.`total`,
			'recycle' AS row_type

		FROM
			`RecyclingInvoicesMaterials` AS rim

		INNER JOIN `RecyclingInvoices` AS ri 
			ON (`ri`.`id` = `rim`.`invoiceId`)

		INNER JOIN `Materials` AS m
			ON (`m`.`id` = `rim`.`materialId`)

		INNER JOIN `Stores` AS dc 
			ON 
			(
				`dc`.`id` = `ri`.`locationId` 
				AND `ri`.`locationType` = 'STORE'
			)

		INNER JOIN `Vendors` AS v 
			ON (`v`.`id` = `ri`.`vendorId`)

		WHERE 
			`dc`.`id` = " . $id . "

		ORDER BY
			" . $sortColumn . " " . $sortDir . "

		LIMIT 
			" . $iDisplayStart . ", " . $iDisplayLength;







/*

		$s = "
		SELECT
			SQL_CALC_FOUND_ROWS
			`ri`.`id` AS id,
			`ri`.`poNumber` AS invoice_num,
			DATE_FORMAT(`ri`.`invoiceDate`, '%m/%d/%Y') AS invoice_date,
			DATE_FORMAT(`ri`.`dateSent`, '%m/%d/%Y') AS sent_date,
			`dc`.`location` AS location,
			`v`.`name` AS vendor,
			(
				SELECT m.name 
				FROM Materials as m
				LEFT OUTER JOIN RecyclingInvoicesMaterials as rim ON rim.materialId = m.id
				WHERE
				rim.id = (
					SELECT rimm.id FROM RecyclingInvoicesMaterials as rimm
					WHERE
						rimm.invoiceId = ri.id
					ORDER BY rimm.quantity DESC
					LIMIT 1 
				)
			) AS material,
			(
				SELECT rimm.quantity FROM RecyclingInvoicesMaterials as rimm
				WHERE
					rimm.invoiceId = ri.id
				ORDER BY rimm.quantity DESC
				LIMIT 1 
			) AS quantity,
			'-' AS total_rebate,
			`ri`.`total`

		FROM
			`RecyclingInvoices` AS ri

		INNER JOIN `Stores` AS dc 
			ON 
			(
				`dc`.`id` = `ri`.`locationId` 
				AND `ri`.`locationType` = 'STORE'
			)

		INNER JOIN `Vendors` AS v 
			ON (`v`.`id` = `ri`.`vendorId`)

		WHERE 
			`dc`.`id` = " . $id . "

		ORDER BY
			" . $sortColumn . " " . $sortDir . "

		LIMIT 
			" . $iDisplayStart . ", " . $iDisplayLength;
*/



/*
		$s = "
		UNION
		SELECT

			DATE_FORMAT(`wi`.`invoiceDate`, '%Y/%m/%d') AS invoice_date,

			`wi`.`invoiceNumber` AS invoice_num,

			DATE_FORMAT(`wi`.`dateSent`, '%Y/%m/%d') AS sent_date,

			`dc`.`location` AS location,

			`v`.`name` AS vendor,

			'-' AS material,

			'-' AS quantity,

			'-' AS total_rebate,

			`wi`.`total`

		FROM
			`WasteInvoices` AS wi


		RIGHT JOIN `DistributionCenters` AS dc 
			ON 
			(
				`dc`.`id` = `wi`.`locationId` 
				AND `wi`.`locationType` = 'DC'
			)

		RIGHT JOIN `Vendors` AS v 
			ON (`v`.`id` = `wi`.`vendorId`)

		WHERE 
			`dc`.`id` = " . $DC . "
		";
*/
		$q = $this->db->query($s);
		$rows = $q->result('array');

		$this->db->select('FOUND_ROWS() as i');
		$query = $this->db->get();
		$row = $query->row();

		return array(
			'rows_count'	=> $row->i,
			'rows'			=> $rows
		);
	}


	/**
	 * @param integer id `Stores`.`id`
	 * @param integer iDisplayStart "x" in "LIMIT x,y"
	 * @param integer iDisplayLength "y" in "LIMIT x,y"
	 * @param string sortColumn "x" in "ORDER BY `x`"
	 * @param string sortDir "x" in "ORDER BY `colname` x"
	 * @return array mixed 
	 */
	function getSupportRequests($id, $iDisplayStart=0, $iDisplayLength=10000, $sortColumn='`sr`.`timeStamp`', $sortDir='DESC')
	{
		$id = (int)$id;

		$s = "
		SELECT 
			SQL_CALC_FOUND_ROWS
			`dc`.`location` AS location,
			`sr`.`id` AS service_id,
			DATE_FORMAT(`sr`.`timeStamp`, '%m/%d/%Y') AS r_date,
			CONCAT(`sr`.`firstName`, ' ', `sr`.`lastName`) AS contact,
			`sr`.`phone`,
			`sr`.`notes` AS description,
			`sr`.`complete`,
			IF(`sr`.`complete` = 1, 'Y', 'N') AS complete_word

		FROM 
			`SupportRequests` AS sr

		INNER JOIN `Stores` AS dc 
			ON (
				`dc`.`id` = `sr`.`locationId` 
				AND `sr`.`locationType` = 'STORE'
			)

		WHERE
			`sr`.`locationId` = " . $id . "

		ORDER BY
			" . $sortColumn . " " . $sortDir . "

		LIMIT 
			" . $iDisplayStart . ", " . $iDisplayLength;

		$q = $this->db->query($s);
		$rows = $q->result('array');

		$this->db->select('FOUND_ROWS() as i');
		$query = $this->db->get();
		$row = $query->row();

		return array(
			'rows_count'	=> $row->i,
			'rows'			=> $rows
		);
	}



	/**
	 * @param integer id `Stores`.`id`
	 * @param integer iDisplayStart "x" in "LIMIT x,y"
	 * @param integer iDisplayLength "y" in "LIMIT x,y"
	 * @param string sortColumn "x" in "ORDER BY `x`"
	 * @param string sortDir "x" in "ORDER BY `colname` x"
	 * @return array mixed 
	 */
	function getVendorsByInvoices($id, $iDisplayStart=0, $iDisplayLength=10000, $sortColumn='last_updated', $sortDir='DESC')
	{
		$id = (int)$id;

		$s = "
		SELECT 
			SQL_CALC_FOUND_ROWS
			`dc`.`location` AS location,
			`dc`.`squareFootage` AS DC_squareFootage,
			`v`.`name` AS vendor_name,
			`vsp`.`name` AS service_type,
			`c`.`containerType` AS container_type,
			`c`.`name` AS container_size,
			`vsd`.`name` AS duration,
			(
				CASE `vs`.`schedule`
					WHEN 1 THEN 'Weekly'
					WHEN 2 THEN 'Biweekly'
					WHEN 3 THEN 'Monthly'
					ELSE
						'On Call'
				END
			) AS frequency,
			`wi`.`total` AS cost,
			DATE_FORMAT(`wi`.`lastUpdated`, '%m/%d/%Y') AS last_updated

		FROM 
			`WasteInvoices` AS wi

		INNER JOIN `Stores` AS dc 
			ON (`dc`.`id` = `wi`.`locationId`)

		INNER JOIN `Vendors` AS v 
			ON (`v`.`id` = `wi`.`vendorId`)

		INNER JOIN `VendorServices` AS vs 
			ON (`vs`.`id` = `wi`.`vendorId`)

		INNER JOIN `VendorServicePurposes` AS vsp 
			ON (`vsp`.`id` = `vs`.`purposeId`)

		INNER JOIN `Containers` AS c 
			ON (`c`.`id` = `vs`.`containerId`)

		LEFT JOIN `VendorServiceDurations` AS vsd 
			ON (`vsd`.`id` = `vs`.`durationId`)

		WHERE 
			`wi`.`locationType` = 'STORE'
			AND `wi`.`locationId` = " . $id . "


		UNION


		SELECT 
			`dc`.`location` AS location,
			`dc`.`squareFootage` AS DC_squareFootage,
			`v`.`name` AS vendor_name,
			`vsp`.`name` AS service_type,
			`c`.`containerType` AS container_type,
			`c`.`name` AS container_size,
			`vsd`.`name` AS duration,
			(
				CASE `vs`.`schedule`
					WHEN 1 THEN 'Weekly'
					WHEN 2 THEN 'Biweekly'
					WHEN 3 THEN 'Monthly'
					ELSE
						'On Call'
				END
			) AS frequency,
			`ri`.`total` AS cost,
			DATE_FORMAT(`ri`.`lastUpdated`, '%m/%d/%Y') AS last_updated

		FROM 
			`RecyclingInvoices` AS ri

		INNER JOIN `Stores` AS dc 
			ON (
				`dc`.`id` = `ri`.`locationId` 
				AND `ri`.`locationType` = 'STORE'
			)

		INNER JOIN `Vendors` AS v 
			ON (`v`.`id` = `ri`.`vendorId`)

		INNER JOIN `VendorServices` AS vs 
			ON (`vs`.`id` = `ri`.`vendorId`)

		INNER JOIN `VendorServicePurposes` AS vsp 
			ON (`vsp`.`id` = `vs`.`purposeId`)

		INNER JOIN `Containers` AS c 
			ON (`c`.`id` = `vs`.`containerId`)

		LEFT JOIN `VendorServiceDurations` AS vsd 
			ON (`vsd`.`id` = `vs`.`durationId`)

		WHERE 
			`ri`.`locationType` = 'STORE'
			AND `ri`.`locationId` = " . $id . "

		ORDER BY
			" . $sortColumn . " " . $sortDir . "

		LIMIT 
			" . $iDisplayStart . ", " . $iDisplayLength;

		$q = $this->db->query($s);
		$rows = $q->result('array');

		$this->db->select('FOUND_ROWS() as i');
		$query = $this->db->get();
		$row = $query->row();

		return array(
			'rows_count'	=> $row->i,
			'rows'			=> $rows
		);
	}


	public function getDiversionReportData($startDate, $endDate) {
		
	    $query = $this->db->query('SELECT * FROM Containers');
	    $rows = $query->result('array');
	    $containers = array();
	    foreach($rows as $row) {
		    $containers[$row['id']] = $row;			
	    }

	    $query = $this->db->query('SELECT * FROM Materials');
	    $rows = $query->result('array');
	    $materials = array();
	    foreach($rows as $row) {
		    $materials[$row['id']] = $row;			
	    }

	    $schedules = $this->getScheduleOptions();
	    $schedules[0] = 'On~Call';

        $sd = explode('-', $startDate);
        $ed = explode('-', $endDate);


        $data = array();
        $rows_all = array();

        $i = 0;
        while(true) {
            $d = mktime(0, 0, 0, intval($sd[1]) + $i, 1, intval($sd[0]));
            if($d>mktime(0, 0, 0, intval($ed[1]), 1, intval($ed[0]))) {
                break;
            }

            $startDate = date('Y-m-d', mktime(0, 0, 0, intval($sd[1]) + $i, 1, intval($sd[0])));
            $endDate = date('Y-m-d', mktime(0, 0, 0, intval($sd[1]) + $i + 1, 0, intval($sd[0])));

            $i++;

            $sql = '
            SELECT * FROM (
            SELECT DISTINCT wi.locationId
            , wi.locationName
            , wis1.containerId AS containerIdTrash
            , wis1.schedule AS scheduleIdTrash
            , wis1.quantity AS weightInTonsTrash
            , wis2.containerId AS containerIdCardboard
            , wis2.schedule AS scheduleIdCardboard
            , wis2.quantity AS weightInTonsCardboard
            , wis3.materialId AS materialIdRecycling
            , wis3.containerId AS containerIdRecycling
            , wis3.schedule AS scheduleIdRecycling
            , wis3.quantity AS weightInTonsRecycling
            , FLOOR((DATEDIFF("'.$endDate.'", "'.$startDate.'") + 1) / 7) AS cw

            FROM
            WasteInvoiceServices wis
            LEFT JOIN WasteInvoiceServices wis1
            ON wis.invoiceId = wis1.invoiceId AND wis1.materialId = 11 AND (wis1.schedule != 1 AND wis1.schedule != 0)
            LEFT JOIN WasteInvoiceServices wis2
            ON wis.invoiceId = wis2.invoiceId AND (wis2.materialId = 1 OR wis2.materialId = 10) AND (wis2.schedule != 1 AND wis2.schedule != 0)
            LEFT JOIN WasteInvoiceServices wis3
            ON wis.invoiceId = wis3.invoiceId AND (wis3.materialId != 11 AND wis3.materialId != 10 AND wis3.materialId != 1) AND (wis3.schedule != 1 AND wis3.schedule != 0)
            LEFT JOIN WasteInvoices wi
            ON wi.id = wis.invoiceId
            WHERE wi.invoiceDate BETWEEN "'.$startDate.'" AND "'.$endDate.'"

            UNION

            SELECT v.locationId
                 , v.locationId AS locationName
                 , vs1.containerId AS containerIdTrash
                 , vs1.schedule AS scheduleIdTrash
                 , vs1.quantity AS weightInTonsTrash
                 , vs2.containerId AS containerIdCardboard
                 , vs2.schedule AS scheduleIdCardboard
                 , vs2.quantity AS weightInTonsCardboard
                 , vs3.materialId AS materialIdRecycling
                 , vs3.containerId AS containerIdRecycling
                 , vs3.schedule AS scheduleIdRecycling
                 , vs3.quantity AS weightInTonsRecycling
                 , FLOOR((DATEDIFF("'.$endDate.'", "'.$startDate.'") + 1) / 7) AS cw

            FROM
              (SELECT vs0.locationId
                , vs0.vendorId
                , sum(vs0.rate) AS r
               FROM
                 VendorServices vs0
               WHERE
                 vs0.schedule > 0
               GROUP BY
                 vs0.locationId
               , vs0.vendorId) v
            LEFT JOIN VendorServices AS vs1
            ON v.locationId = vs1.locationId AND v.vendorId = vs1.vendorId AND vs1.materialId = 11 AND (vs1.schedule != 1 AND vs1.schedule != 0)
            LEFT JOIN VendorServices AS vs2
            ON v.locationId = vs2.locationId AND v.vendorId = vs2.vendorId AND (vs2.materialId = 1 OR vs2.materialId = 10) AND (vs2.schedule != 1 AND vs2.schedule != 0)
            LEFT JOIN VendorServices AS vs3
            ON v.locationId = vs3.locationId AND v.vendorId = vs3.vendorId AND (vs3.materialId != 11 AND vs3.materialId != 10 AND vs3.materialId != 1) AND (vs3.schedule != 1 AND vs3.schedule != 0)
            WHERE
              v.r = 0
            ) ttt
            WHERE ttt.scheduleIdTrash IS NOT NULL OR ttt.scheduleIdCardboard IS NOT NULL OR ttt.scheduleIdRecycling IS NOT NULL AND ttt.locationId=13
            ORDER BY ttt.locationId';

            //echo $sql;exit();
            $query = $this->db->query($sql);
            $rows = $query->result('array');

            foreach ($rows as &$row) {
                $row['materialNameRecycling'] = '';
                $row['containerNameTrash'] = '';
                $row['containerNameCardboard'] = '';
                $row['containerNameRecycling'] = '';
                $row['scheduleNameTrash'] = '';
                $row['scheduleNameCardboard'] = '';
                $row['scheduleNameRecycling'] = '';

                if($row['containerIdTrash']!=null && isset($containers[$row['containerIdTrash']])) {
                    $row['containerNameTrash'] = $containers[$row['containerIdTrash']]['name'];                
                }
                if($row['containerIdCardboard']!=null && isset($containers[$row['containerIdCardboard']])) {
                    $row['containerNameCardboard'] = $containers[$row['containerIdCardboard']]['name'];                   
                }
                if($row['containerIdRecycling']!=null && isset($containers[$row['containerIdRecycling']])) {
                    $row['containerNameRecycling'] = $containers[$row['containerIdRecycling']]['name'];                  
                }

                if($row['scheduleIdTrash']!=null && isset($schedules[$row['scheduleIdTrash']])) {
                    $row['scheduleNameTrash'] = $schedules[$row['scheduleIdTrash']];
                    if($row['scheduleIdTrash']>0) {
                        $f = 0;
                        switch($row['scheduleIdTrash']) {
                            case 4: $f = 2;
                                break;
                            case 5: $f = 1;
                                break;
                            case 6: $f = 6;
                                break;
                            case 7: $f = 5;
                                break;
                            case 8: $f = 4;
                                break;
                            case 9: $f = 3;
                                break;
                            case 10: $f = 2;
                                break;
                            case 12: $f = 0.5;
                                break;
                        }

                        if(!isset($containers[$row['containerIdTrash']]['weightInLbs'])) {
                             $row['weightInTonsTrash'] = 0.0;
                        } else {
                            $row['weightInTonsTrash'] = ($containers[$row['containerIdTrash']]['weightInLbs'] * $f * $row['cw']) / TON_KOEFF;
                        }
                    } else {
                        //$row['weightInTonsTrash'] = $row['weightInTonsTrash']* $row['cw'];
                    }
                }
                if($row['scheduleIdCardboard']!=null && isset($schedules[$row['scheduleIdCardboard']])) {
                    $row['scheduleNameCardboard'] = $schedules[$row['scheduleIdCardboard']];
                    if($row['scheduleIdCardboard']>0) {
                        $f = 0;
                        switch($row['scheduleIdCardboard']) {
                            case 4: $f = 2;
                                break;
                            case 5: $f = 1;
                                break;
                            case 6: $f = 6;
                                break;
                            case 7: $f = 5;
                                break;
                            case 8: $f = 4;
                                break;
                            case 9: $f = 3;
                                break;
                            case 10: $f = 2;
                                break;
                            case 12: $f = 0.5;
                                break;
                        }
                        if(!isset($containers[$row['containerIdCardboard']]['weightInLbs'])) {
                            $row['weightInTonsCardboard'] = 0.0;
                        } else {
                            $row['weightInTonsCardboard'] = ($containers[$row['containerIdCardboard']]['weightInLbs'] * $f * $row['cw']) / TON_KOEFF;
                        }
                    } else {
                        //$row['weightInTonsCardboard'] = $row['weightInTonsCardboard'] * $row['cw'];
                    }
                }
                if($row['scheduleIdRecycling']!=null && isset($schedules[$row['scheduleIdRecycling']])) {
                    $row['scheduleNameRecycling'] = $schedules[$row['scheduleIdRecycling']];
                    if($row['scheduleIdRecycling']>0) {
                        $f = 0;
                        switch($row['scheduleIdRecycling']) {
                            case 4: $f = 2;
                                break;
                            case 5: $f = 1;
                                break;
                            case 6: $f = 6;
                                break;
                            case 7: $f = 5;
                                break;
                            case 8: $f = 4;
                                break;
                            case 9: $f = 3;
                                break;
                            case 10: $f = 2;
                                break;
                            case 12: $f = 0.5;
                                break;
                        }
                        if(!isset($containers[$row['containerIdRecycling']]['weightInLbs'])) {
                            $row['weightInTonsRecycling'] = 0.0;
                        } else {
                            $row['weightInTonsRecycling'] = ($containers[$row['containerIdRecycling']]['weightInLbs'] * $f * $row['cw']) / TON_KOEFF;
                        }
                    } else {
                        //$row['weightInTonsRecycling'] = $row['weightInTonsRecycling'] * $row['cw'];
                    }
                }
                if (isset($materials[$row['materialIdRecycling']])) {
                    $row['materialNameRecycling'] = $materials[$row['materialIdRecycling']]['name'];
                }

                $rows_all[] = $row;
            }
        }

        foreach($rows_all as $row) {
            $fl_locationId = false;
            $fl_Trash = false;
            $fl_Cardboard = false;
            $fl_Recycling = false;
            foreach($data as &$item) {
                if($item['locationId']==$row['locationId']) {
                    if($item['containerIdTrash']==$row['containerIdTrash'] && $item['scheduleIdTrash']==$row['scheduleIdTrash']) {
                        $item['weightInTonsTrash'] = floatval($item['weightInTonsTrash']) + floatval($row['weightInTonsTrash']);
                        $fl_Trash = true;
                    }
                    if($item['containerIdCardboard']==$row['containerIdCardboard'] && $item['scheduleIdCardboard']==$row['scheduleIdCardboard']) {
                        $item['weightInTonsCardboard'] = floatval($item['weightInTonsCardboard']) + floatval($row['weightInTonsCardboard']);
                        $fl_Cardboard = true;
                    }
                    if($item['containerIdRecycling']==$row['containerIdRecycling'] && $item['scheduleIdRecycling']==$row['scheduleIdRecycling'] && $item['materialIdRecycling']==$row['materialIdRecycling']) {
                        $item['weightInTonsRecycling'] = floatval($item['weightInTonsRecycling']) + floatval($row['weightInTonsRecycling']);
                        $fl_Recycling = true;
                    }
                    $fl_locationId = true;
                }
            }
            if(!$fl_locationId) {
                $data[] = $row;
            } else {
                $row_tmp = array(
                    'locationId' => $row['locationId'],
                    'locationName' => $row['locationName'],
                    'containerIdTrash' => '',
                    'scheduleIdTrash' => '',
                    'weightInTonsTrash' => '',
                    'containerIdCardboard' => '',
                    'scheduleIdCardboard' => '',
                    'weightInTonsCardboard' => '',
                    'materialIdRecycling' => '',
                    'containerIdRecycling' => '',
                    'scheduleIdRecycling' => '',
                    'weightInTonsRecycling' => '',
                    'cw' => $row['cw'],
                    'materialNameRecycling' => '',
                    'containerNameTrash' => '',
                    'containerNameCardboard' => '',
                    'containerNameRecycling' => '',
                    'scheduleNameTrash' => '',
                    'scheduleNameCardboard' => '',
                    'scheduleNameRecycling' => '',
                );
                
                if(!$fl_Trash) {
                    $row_tmp['containerIdTrash'] = $row['containerIdTrash'];
                    $row_tmp['scheduleIdTrash'] = $row['scheduleIdTrash'];
                    $row_tmp['weightInTonsTrash'] = $row['weightInTonsTrash'];
                    $row_tmp['containerNameTrash'] = $row['containerNameTrash'];
                    $row_tmp['scheduleNameTrash'] = $row['scheduleNameTrash'];
                }
                if(!$fl_Cardboard) {
                    $row_tmp['containerIdCardboard'] = $row['containerIdCardboard'];
                    $row_tmp['scheduleIdCardboard'] = $row['scheduleIdCardboard'];
                    $row_tmp['weightInTonsCardboard'] = $row['weightInTonsCardboard'];
                    $row_tmp['containerNameCardboard'] = $row['containerNameCardboard'];
                    $row_tmp['scheduleNameCardboard'] = $row['scheduleNameCardboard'];
                }
                if(!$fl_Recycling) {
                    $row_tmp['materialIdRecycling'] = $row['materialIdRecycling'];
                    $row_tmp['materialNameRecycling'] = $row['materialNameRecycling'];
                    $row_tmp['containerIdRecycling'] = $row['containerIdRecycling'];
                    $row_tmp['scheduleIdRecycling'] = $row['scheduleIdRecycling'];
                    $row_tmp['weightInTonsRecycling'] = $row['weightInTonsRecycling'];
                    $row_tmp['containerNameRecycling'] = $row['containerNameRecycling'];
                    $row_tmp['scheduleNameRecycling'] = $row['scheduleNameRecycling'];
                }

                if(!$fl_Trash || !$fl_Cardboard || !$fl_Recycling) {
                    $data[] = $row_tmp;
                }
            }
        }

        function cmpzzxx($a, $b) {
            if ($a['locationId'] == $b['locationId']) {
                return 0;
            }
            return ($a['locationId'] < $b['locationId']) ? -1 : 1;
        }

        usort($data, 'cmpzzxx');

		return $data;
	}
}


class OrderBy {

    private $orderColumn;
    private $orderDir;
    private $orderColumnDatatype;

    public function __construct($orderColumn, $orderDir, $orderColumnDatatype) {
        $this->orderColumn = $orderColumn;
        $this->orderDir = $orderDir;
        $this->orderColumnDatatype = $orderColumnDatatype;
    }

    public function run($a, $b) {
        $orderColumn = $this->orderColumn;
        if($this->orderColumnDatatype=='int') {
            if ($a->$orderColumn == $b->$orderColumn) {
                return 0;
            }
            if($this->orderDir=='DESC') {
                return ($a->$orderColumn > $b->$orderColumn) ? -1 : 1;
            } else {
                return ($a->$orderColumn < $b->$orderColumn) ? -1 : 1;
            }
        } else {
            if($this->orderDir=='DESC') {
                return strcmp($b->$orderColumn, $a->$orderColumn);
            } else {
                return strcmp($a->$orderColumn, $b->$orderColumn);
            }
        }
    }
}