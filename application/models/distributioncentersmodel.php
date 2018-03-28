<?php
include_once 'basemodel.php';

class DistributioncentersModel extends BaseModel {
	
	public function __construct() {
		parent::__construct();
		$this->load->database();
		$this->load->helper('dates');
	}
	
	
	public function getLastActivity() {
		$lastActivity = $this->db->query("SELECT *, date_format(lastUpdated, '%m/%d/%Y') AS lu,
												(SELECT number FROM DistributionCenters WHERE id = locationId) AS DC_number
											FROM SupportRequests
											WHERE locationType = 'DC' 
											ORDER BY id DESC
											LIMIT 3");
		return $lastActivity->result("array");
	}
	
	//dashboard
	
	public function getServiceRequestChartInfo($period, $distributionCenterId = 0) {

        $dates = getPeriods($period);
        $info = $this->db->query("SELECT
              srt.name AS Name,
              COUNT(srtt.id) AS value
            FROM SupportRequestTasks srtt
              LEFT JOIN SupportRequestServiceTypes srt
                ON srt.id = srtt.purposeId
              LEFT JOIN SupportRequests AS sr
                ON sr.id = srtt.supportRequestId
            WHERE locationType = 'DC' ".($distributionCenterId>0 ? 'AND sr.locationId = '.$distributionCenterId : '')." AND (serviceDate BETWEEN '{$dates['startYear']}-{$dates['startMonth']}-01' AND '{$dates['endYear']}-{$dates['endMonth']}-31')
            GROUP BY (srt.name)
            ORDER BY srt.id");

		return $info->result();
	}	
		
	//Recycling
	
	function getAllDC() {
		$retunData = array();
		$allDC = $this->db->query("SELECT * FROM `DistributionCenters` ORDER BY `name`");
		foreach ($allDC->result("array") as $temp) {
			$retunData[$temp["id"]] = $temp;
		}
		return $retunData;
	}
	
	function getAllRecyclingMaterials() {
		$returnData = array();
		
		$allRecyclingMaterials = $this->db->query("SELECT * FROM Materials WHERE categoryId = 1");
		
		foreach($allRecyclingMaterials->result("array") as $temp) {
			$returnData[$temp["id"]] = $temp;
			$returnData[$temp["id"]]["quantity"] = 0;
		}
		return $returnData;
	}
	
	private function getDCRecyclingQuery($startDate = null, $endDate = null, $DC = null, $material = null) {
		$conditions = "";
		
		if($startDate) {
			$conditions .= " AND `ri`.`invoiceDate` >= '" . $startDate . "'";
		}
		
		if($endDate) {
			$conditions .= " AND `ri`.`invoiceDate` <= '" . $endDate . "'";
		}
		
		if($DC) {
			$conditions .= " AND dc.id = " . (int) $DC . " ";
		}
		
		if($material) {
			$conditions .= " AND rim.materialId = " . (int) $material . " ";
		}
		
		
		//Стари заявки
		
		$dcRecyclingData = $this->db->query("SELECT ri.*,
				dc.name AS dname, dc.number AS dnumber, dc.id AS id, squareFootage,
				m.id AS mid, m.name AS mname, EnergySaves, CO2Saves, quantity
		
				FROM RecyclingInvoices as ri
				LEFT OUTER JOIN RecyclingInvoicesMaterials as rim ON rim.invoiceId = ri.id
				LEFT OUTER JOIN DistributionCenters AS dc ON dc.id = ri.locationId
				LEFT OUTER JOIN Materials AS m ON m.id = rim.materialId
				WHERE ri.locationType = 'DC' AND m.unit = 1 " . $conditions);

		return $dcRecyclingData->result("array");
		
		//return array();
	}
	
	
	function getDCRecycling($startDate = null, $endDate = null, $DC = null) {
		$returnData = array();

		foreach($this->getDCRecyclingQuery($startDate, $endDate, $DC) as $temp) {
			if(isset($returnData[$temp["id"]])) {
				if(isset($returnData[$temp["id"]]["quantity_" . $temp["mid"]])) {
					$returnData[$temp["id"]]["quantity_" . $temp["mid"]] += $temp["quantity"]; 
				} else {
					$returnData[$temp["id"]]["quantity_" . $temp["mid"]] = $temp["quantity"];
					$returnData[$temp["id"]]["material_" . $temp["mid"]] = $temp["mname"];
				}
				$returnData[$temp["id"]]["energy"] += ($temp["EnergySaves"] * $temp["quantity"]);
				$returnData[$temp["id"]]["co"] += ($temp["CO2Saves"] * $temp["quantity"]);				
				$returnData[$temp["id"]]["total"] += $temp["total"];
			} else {
				$returnData[$temp["id"]] = array(	"id" => $temp["id"],
													"name" => $temp["dname"],
													"sqft" => $temp["squareFootage"],
													"material_" . $temp["mid"] => $temp["mname"],
													"quantity_" . $temp["mid"] => $temp["quantity"],
													"energy" => $temp["EnergySaves"] * $temp["quantity"],
													"co" => $temp["CO2Saves"] * $temp["quantity"],
													"total" => $temp["total"]);
			}
		}
		
		return $returnData;
	}
	
	
	private function sumRecyclingTrends($startDate, $endDate, $month,  &$data, $material = null) {

		foreach ($this->getDCRecyclingQuery($startDate, $endDate, null, $material) as $temp) {
			if (isset($data[$temp["id"]]["trend"][$month])) {
				$data[$temp["id"]]["trend"][$month] += $temp["quantity"];
			} else {
				$data[$temp["id"]]["trend"][$month] = $temp["quantity"];
			}
		}
	}
	
	
	public function RecyclingTrends($allDC, $material = null) {
		
		if(count($allDC) < 1) {
			return;
		}
		
		$orderedDC = $allDC;
		$monthArray = array();
		
		$newdate = strtotime ( '-5 month');
		$this->sumRecyclingTrends(date('Y-m-01', $newdate), date('Y-m-t', $newdate), date("m/Y", $newdate), $orderedDC, $material);		
		$monthArray[] = date("m/Y", $newdate);
		
		$newdate = strtotime ( '-4 month');
		$this->sumRecyclingTrends(date('Y-m-01', $newdate), date('Y-m-t', $newdate), date("m/Y", $newdate), $orderedDC, $material);
		$monthArray[] = date("m/Y", $newdate);
		
		$newdate = strtotime ( '-3 month');
		$this->sumRecyclingTrends(date('Y-m-01', $newdate), date('Y-m-t', $newdate), date("m/Y", $newdate), $orderedDC, $material);
		$monthArray[] = date("m/Y", $newdate);

		$newdate = strtotime ( '-2 month');
		$this->sumRecyclingTrends(date('Y-m-01', $newdate), date('Y-m-t', $newdate), date("m/Y", $newdate), $orderedDC, $material);
		$monthArray[] = date("m/Y", $newdate);
		
		$newdate = strtotime ( '-1 month');
		$this->sumRecyclingTrends(date('Y-m-01', $newdate), date('Y-m-t', $newdate), date("m/Y", $newdate), $orderedDC, $material);
		$monthArray[] = date("m/Y", $newdate);
		
		$this->sumRecyclingTrends(date('Y-m-01'), date('Y-m-t'), "Current",  $orderedDC);
		$monthArray[] = "Current";
		
		$trend = array();
		$dcNames = array();
		$i = 0;
		

		
			foreach ($orderedDC as $tmp) {
				foreach ($monthArray as $temp) {
					if(isset($tmp["trend"][$temp])) {
						$trend[$i][] = (int)$tmp["trend"][$temp];
					} else {
						$trend[$i][] = 0;
					}		
			}
			$i++;
		}
		foreach ($orderedDC as $temp) {
			$dcNames[] = array_key_exists('name', $temp) ? $temp["name"] : '';
		}

		return array($orderedDC, $monthArray, $trend, $dcNames);
	}
	
	
	
	
	
	
	
	//End Recycling
	
	
	
	public function getCurrentMonthData($distributionCenterId = 0) {
        $dates = getPeriods('CurrentMonth');
        return $this->getDataByInterval($dates['startMonth'], $dates['startYear'], $dates['endMonth'], $dates['endYear'], $distributionCenterId);
	}
	
	public function getPriorMonthData($distributionCenterId = 0) {
        $dates = getPeriods('PriorMonth');
		return $this->getDataByInterval($dates['startMonth'], $dates['startYear'], $dates['endMonth'], $dates['endYear'], $distributionCenterId);
	}
	
	public function getPriorQuarterData($distributionCenterId = 0) {
        $dates = getPeriods('PriorQuarter');
        return $this->getDataByInterval($dates['startMonth'], $dates['startYear'], $dates['endMonth'], $dates['endYear'], $distributionCenterId);
	}
	
	public function getSixMonthsData($distributionCenterId = 0) {
        $dates = getPeriods('SixMonths');
        return $this->getDataByInterval($dates['startMonth'], $dates['startYear'], $dates['endMonth'], $dates['endYear'], $distributionCenterId);
	}
	
	public function getLastYearData($distributionCenterId = 0) {
        $dates = getPeriods('LastYear');
        return $this->getDataByInterval($dates['startMonth'], $dates['startYear'], $dates['endMonth'], $dates['endYear'], $distributionCenterId);
	}

    public function getPrior2MonthsBackData($distributionCenterId = 0) {
        $dates = getPeriods('2MonthsBack');
        return $this->getDataByInterval($dates['startMonth'], $dates['startYear'], $dates['endMonth'], $dates['endYear'], $distributionCenterId);
    }
	

    public function getPrior3MonthsBackData($distributionCenterId = 0) {
        $dates = getPeriods('3MonthsBack');
        return $this->getDataByInterval($dates['startMonth'], $dates['startYear'], $dates['endMonth'], $dates['endYear'], $distributionCenterId);
    }

	
	private function getDataByInterval($startMonth, $startYear, $endMonth, $endYear, $distributionCenterId = 0) {
        $result = new MonthData();

		$sql = "
            SELECT
              SUM(amount) AS 'Cost',
              SUM(tons) AS 'WasteTons'
            FROM DistributionCenterInvoices dci
              LEFT JOIN DistributionCenterInvoicesFees dcif
                ON dci.id = dcif.invoiceId
            WHERE invoiceDate BETWEEN '$startYear-$startMonth-01' AND '$endYear-$endMonth-31'";
        if($distributionCenterId>0) {
            $sql .= ' AND dci.distributionCenterId = "'.$distributionCenterId.'"';
        }
        $query = $this->db->query($sql);
		$row = $query->row();
        $result->waste = $row->WasteTons;
        $result->cost = $row->Cost;

        $sql = "
            SELECT
              SUM(quantity) AS 'RecycleTons',
              SUM(quantity * pricePOUnit) - SUM(feeAmount) AS 'Rebate'
            FROM RecyclingInvoices ri
              LEFT JOIN RecyclingInvoicesMaterials rim
                ON ri.id = rim.invoiceId
              LEFT JOIN RecyclingInvoicesFees rif
                ON ri.id = rif.invoiceId
            WHERE ri.invoiceDate BETWEEN '$startYear-$startMonth-01' AND '$endYear-$endMonth-31'";
        if($distributionCenterId>0) {
            $sql .= ' AND ri.locationId = "'.$distributionCenterId.'"';
        }
        $query = $this->db->query($sql);
        $row = $query->row();
        $result->recycling = $row->RecycleTons;
        $result->rebate = $row->Rebate;

        $result->diversion = 0;
        if($result->recycling!=0){
            $result->diversion = (($result->recycling - $result->waste)/$result->recycling) * 100;
        }

        $sql = "SELECT
              SUM(squareFootage) totalSqFt
            FROM DistributionCenters dc
              INNER JOIN ((SELECT DISTINCT
                locationId AS id
              FROM RecyclingInvoices
              WHERE invoiceDate BETWEEN '$startYear-$startMonth-01' AND '$endYear-$endMonth-31')
              UNION (SELECT DISTINCT
                distributionCenterId AS id
              FROM DistributionCenterInvoices
              WHERE invoiceDate BETWEEN '$startYear-$startMonth-01' AND '$endYear-$endMonth-31')) AS t1
                ON t1.id = dc.id";
        if($distributionCenterId>0) {
            $sql .= ' AND dc.id = "'.$distributionCenterId.'"';
        }
        $query = $this->db->query($sql);
        $row = $query->row();
        //echo $this->db->last_query();
	    $totalSqFt = $row->totalSqFt;
        $result->wasteCostSqFt = 0;
        $result->recyclingCostSqFt = 0;
        if($totalSqFt!=0) {
            $result->wasteCostSqFt = $result->cost / $totalSqFt;
            $result->recyclingCostSqFt = ($result->cost - $result->recycling) / $totalSqFt;
        }

	    return $result;
	}

    public function getCostOfServicesChartInfo($period, $distributionCenterId = 0) {
        $result =array();

        $dates = getPeriods($period);
        $query = $this->db->query("
            SELECT
              ft.name AS feeType,
              SUM(amount) AS feeAmount
            FROM DistributionCenterInvoices dci
              LEFT JOIN DistributionCenterInvoicesFees dcif
                ON dci.id = dcif.invoiceId
              LEFT JOIN MaterialFeeType ft ON dcif.feeType = ft.id
            WHERE invoiceDate BETWEEN '$dates[startYear]-$dates[startMonth]-01' AND '$dates[endYear]-$dates[endMonth]-31' ".($distributionCenterId>0 ? 'AND dci.distributionCenterId = '.$distributionCenterId : '')."
            GROUP BY dcif.feeType");

        $rows =  $query->result();

        foreach($rows as $row) {
            $item = new stdClass();
            $item->Name = $row->feeType;
            $item->value = $row->feeAmount;
            $result[] = $item;
        }

        return $result;
    }
	
	//Waste
	
	private function getWasteFromDB($startDate = null, $endDate = null, $DC = null) {
		
		$conditions = "";
		
		if($startDate) {
			$conditions .= " AND invoiceDate >= '" . $startDate . "'";
		}
		
		if($endDate) {
			$conditions .= " AND invoiceDate <= '" . $endDate . "'";
		}
		
		if($DC) {
			$conditions .= " AND locationId = " . (int) $DC . " ";
		}
		
		
		$wasteData = $this->db->query("SELECT wi.*, ws.quantity, ws.rate, m.name,
				(SELECT squareFootage FROM DistributionCenters WHERE DistributionCenters.id = locationId) AS sqft
				FROM WasteInvoices as wi
				LEFT OUTER JOIN WasteInvoiceServices as ws ON wi.id = ws.invoiceId
				LEFT OUTER JOIN Materials AS m ON m.id = materialId
				WHERE wi.locationType = 'DC' AND unitID = 1 " . $conditions);
		
		return $wasteData->result("array");
	}
	
	
	
	function getWasteData($startDate, $endDate, $DC) {
		
		$returnData = array();
		$sumData = array("sqft" => 0, "waste" => 0, "hazardous" => 0, "cost" => 0);


		foreach ($this->getWasteFromDB($startDate, $endDate, $DC) as $temp) {
			if(isset($returnData[$temp["locationId"]])) {
				$returnData[$temp["locationId"]]["waste"] += $temp["quantity"];
				if($temp["name"] == "Hazardous") {
					$returnData[$temp["locationId"]]["hazardous"] += $temp["quantity"];
				}
				$returnData[$temp["locationId"]]["cost"] += $temp["total"];				
			} else {
				$returnData[$temp["locationId"]] = $temp;
				$returnData[$temp["locationId"]]["waste"] = $temp["quantity"];
				if($temp["name"] == "Hazardous") {
					$returnData[$temp["locationId"]]["hazardous"] = $temp["quantity"];
				} else {
					$returnData[$temp["locationId"]]["hazardous"] = 0;
				}
				$returnData[$temp["locationId"]]["cost"] = $temp["total"];
			}
		}

		foreach ($returnData as $temp) {
			$sumData["sqft"] += $temp["sqft"];
			$sumData["waste"] += $temp["waste"];
			$sumData["hazardous"] += $temp["hazardous"];
			$sumData["cost"] += $temp["cost"];
		}

		return array($returnData, $sumData);
	}

    function getCostChartInfo($startDate, $endDate, $distributioncenter_id) {
        $query = $this->db->query("
            SELECT
              mft.name AS feeType,
              SUM(amount) AS Cost
            FROM DistributionCenterInvoices dci
              LEFT JOIN DistributionCenterInvoicesFees dcif
                ON dci.id = dcif.invoiceId
              LEFT JOIN MaterialFeeType mft
                ON dcif.feeType = mft.id
            WHERE invoiceDate BETWEEN '$startDate' AND '$endDate' ".($distributioncenter_id!=0 ? 'AND dci.distributionCenterId = '.$distributioncenter_id : '')."
            GROUP BY dcif.feeType");

        return $query->result();
    }


    function getDatagridInfo($startDate, $endDate, $distributioncenter_id) {
        $query = $this->db->query("
            SELECT
              dc.id,
              dc.name,
              dc.squareFootage,
              SUM(CASE feeType
                WHEN 1 THEN tons
                WHEN 4 THEN tons ELSE 0
              END) AS ScheduledTons,
              SUM(CASE feeType
                WHEN 2 THEN tons
                WHEN 3 THEN tons
                WHEN 5 THEN tons
                WHEN 6 THEN tons ELSE 0
              END) AS OnCallTons,
              SUM(CASE feeType
                WHEN 1 THEN 0
                WHEN 4 THEN 0
                WHEN 2 THEN 0
                WHEN 3 THEN 0
                WHEN 5 THEN 0
                WHEN 6 THEN 0 ELSE tons
			  END) AS OtherTons,
              SUM(tons) AS TotalTons,
              SUM(CASE feeType
                WHEN 1 THEN amount
                WHEN 4 THEN amount ELSE 0
              END) AS ScheduledCost,
              SUM(CASE feeType
                WHEN 2 THEN amount
                WHEN 3 THEN amount
                WHEN 5 THEN amount
                WHEN 6 THEN amount ELSE 0
              END) AS OnCallCost,
              SUM(CASE feeType
                WHEN 1 THEN 0
                WHEN 4 THEN 0
                WHEN 2 THEN 0
                WHEN 3 THEN 0
                WHEN 5 THEN 0
                WHEN 6 THEN 0 ELSE amount
			  END) AS OtherCost,
              SUM(amount) AS TotalCost
            FROM DistributionCenters dc
              LEFT JOIN DistributionCenterInvoices dci
                ON dci.distributionCenterId = dc.id
              LEFT JOIN DistributionCenterInvoicesFees dcif
                ON dci.id = dcif.invoiceId
            WHERE invoiceDate BETWEEN '$startDate' AND '$endDate' ".($distributioncenter_id!=0 ? 'AND dci.distributionCenterId = '.$distributioncenter_id : '')."
            GROUP BY dc.id,
                     dc.name,
                     dc.squareFootage");

        return $query->result();
    }

    function getServicesDatagridInfo($startDate, $endDate, $distributioncenter_id) {
        $query = $this->db->query("
            SELECT
              dc.id AS id,
              dc.name AS dcName,
              squareFootage,
              v.name,
              CASE category
                  WHEN 0 THEN 'Trash' ELSE 'Recycling'
                END AS 'ServiceType',
              c.name AS container,
              mp.name AS Frequency,
              quantity * rate AS cost
            FROM DistributionCenters dc
              LEFT OUTER JOIN VendorServices vs
                ON vs.locationId = dc.id AND locationType = 'DC'
              LEFT OUTER JOIN Vendors v
                ON v.id = vs.vendorId
              LEFT OUTER JOIN Containers c
                ON c.id = vs.containerId
              LEFT OUTER JOIN monthlyPickups mp
                ON mp.id = vs.schedule
            WHERE dc.status = 'YES' AND vs.startDate <= '$startDate' AND (vs.endDate >= '$endDate' OR vs.endDate = '0000-00-00') ".($distributioncenter_id!=0 ? 'AND dc.id = '.$distributioncenter_id : '')." ");

        return $query->result();
    }

	private function sumWasteTrends($startDate, $endDate, $month,  &$data) {
	
		foreach ($this->getWasteFromDB($startDate, $endDate, null) as $temp) {
			if (isset($data[$temp["locationId"]]["trend"][$month])) {
				$data[$temp["locationId"]]["trend"][$month] += $temp["quantity"];
			} else {
				$data[$temp["locationId"]]["trend"][$month] = $temp["quantity"];
			}
		}
	}
	
	
	public function WasteTrends($allDC) {

	
		if(count($allDC) < 1) {
			return;
		}
	
		$orderedDC = $allDC;
		$monthArray = array();
	
		$newdate = strtotime ( '-5 month');
		$this->sumWasteTrends(date('Y-m-01', $newdate), date('Y-m-t', $newdate), date("m/Y", $newdate), $orderedDC);
		$monthArray[] = date("m/Y", $newdate);
	
		$newdate = strtotime ( '-4 month');
		$this->sumWasteTrends(date('Y-m-01', $newdate), date('Y-m-t', $newdate), date("m/Y", $newdate), $orderedDC);
		$monthArray[] = date("m/Y", $newdate);
	
		$newdate = strtotime ( '-3 month');
		$this->sumWasteTrends(date('Y-m-01', $newdate), date('Y-m-t', $newdate), date("m/Y", $newdate), $orderedDC);
		$monthArray[] = date("m/Y", $newdate);
	
		$newdate = strtotime ( '-2 month');
		$this->sumWasteTrends(date('Y-m-01', $newdate), date('Y-m-t', $newdate), date("m/Y", $newdate), $orderedDC);
		$monthArray[] = date("m/Y", $newdate);
	
		$newdate = strtotime ( '-1 month');
		$this->sumWasteTrends(date('Y-m-01', $newdate), date('Y-m-t', $newdate), date("m/Y", $newdate), $orderedDC);
		$monthArray[] = date("m/Y", $newdate);
	
		$this->sumWasteTrends(date('Y-m-01'), date('Y-m-t'), "Current",  $orderedDC);
		$monthArray[] = "Current";
	
		$trend = array();
		$dcNames = array();
		$i = 0;
	
		
		
		foreach ($orderedDC as $tmp) {
			foreach ($monthArray as $temp) {
				if(isset($tmp["trend"][$temp])) {
					$trend[$i][] = (int)$tmp["trend"][$temp];
				} else {
					$trend[$i][] = 0;
				}
			}
			$i++;
		}
		
		
		foreach ($orderedDC as $temp) {
			if(isset($temp["name"])) {
				$dcNames[] = $temp["name"];
			}
		}
	
		return array($orderedDC, $monthArray, $trend, $dcNames);
	}

    function getWasteTrends($distributioncenter_id) {
        $startDate = date('Y-m-d', mktime(0, 0, 0, (date('n')-5), 1, date('Y')));
        $endDate = date('Y-m-d', mktime(0, 0, 0, (date('n')+1), 0, date('Y')));
        $query = $this->db->query("
            SELECT
              DATE_FORMAT(invoiceDate, '%m/%Y') AS my,
              SUM(tons) AS wasteTons
            FROM DistributionCenterInvoices dci
              LEFT JOIN DistributionCenterInvoicesFees dcif
                ON dci.id = dcif.invoiceId
            WHERE invoiceDate BETWEEN '$startDate' AND '$endDate' ".($distributioncenter_id!=0 ? 'AND dci.distributionCenterId = '.$distributioncenter_id : '')."
            GROUP BY DATE_FORMAT(invoiceDate, '%m/%Y') ORDER BY invoiceDate");

        return $query->result();
    }

    function getTypeOfContainers($startDate, $endDate, $distributioncenter_id) {
        $query = $this->db->query("
            SELECT
              SUM(quantity) AS value,
              containerType AS Name
            FROM DistributionCenters AS dc
              LEFT OUTER JOIN VendorServices vs
                ON dc.id = vs.locationId
              INNER JOIN Containers c
                ON vs.containerId = c.id
            WHERE vs.locationType = 'DC' AND dc.status = 'YES' AND vs.startDate <= '$startDate' AND (vs.endDate >= '$endDate' OR vs.endDate = '0000-00-00')".($distributioncenter_id!=0 ? 'AND dc.id = '.$distributioncenter_id : '')."
            GROUP BY containerType");

        return $query->result();
    }

    function getFrequencyOfService($startDate, $endDate, $distributioncenter_id) {
        $query = $this->db->query("
            SELECT
              COUNT(mp.name) AS value,
              mp.name AS FrequencyType
            FROM DistributionCenters dc
              LEFT OUTER JOIN VendorServices vs
                ON dc.id = vs.locationId
              INNER JOIN monthlyPickups mp
                ON vs.schedule = mp.id
            WHERE vs.locationType = 'DC' AND dc.status = 'YES' AND vs.startDate <= '$startDate' AND (vs.endDate >= '$endDate' OR vs.endDate = '0000-00-00')".($distributioncenter_id!=0 ? 'AND dc.id = '.$distributioncenter_id : '')."
            GROUP BY mp.name");

        return $query->result();
    }

	function loadCostFromDB($startDate, $endDate, $DC) {
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
		
		if($DC) {
			$conditions .= " AND locationId = " . (int) $DC . " ";
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
				WHERE wi.locationType = 'DC' AND unitID = 1 " . $conditions);
		
		
		//Стари данни

		$rcData = $this->db->query("SELECT ri.*,
				dc.name AS locationName, dc.number AS dnumber, dc.id AS id, squareFootage as sqft,
				quantity, pricePerUnit,
				(SELECT SUM(feeAmount) FROM RecyclingInvoicesFees AS rif WHERE ri.id = rif.invoiceId) AS rif
				FROM RecyclingInvoices as ri
				LEFT OUTER JOIN RecyclingInvoicesMaterials as rim ON rim.invoiceId = ri.id
				LEFT OUTER JOIN DistributionCenters AS dc ON dc.id = ri.locationId
				WHERE ri.locationType = 'DC' AND unit = 1 " . $conditionsRi);		
		
		return array($wsData->result("array"), $rcData->result("array"));
		
		//return array(array(), array());
	}
	
	function getRecyclingCharges($startDate, $endDate, $DC) {
		$conditions = "";
		
		if($startDate) {
			$conditions .= " AND date >= '" . $startDate . "'";
		}
		
		if($endDate) {
			$conditions .= " AND date <= '" . $endDate . "'";
		}
		
		/*
		if($DC) {
			$conditions .= " AND vendorId = " . (int) $DC . " ";
		}
		*/
				
		$data = $this->db->query("SELECT
								SUM(
									(SELECT SUM(quantity*pricePerTon) FROM RecyclingChargeItems WHERE RecyclingCharges.id = recyclingChargeId)+
									(SELECT SUM(fee) FROM RecyclingChargesFees WHERE RecyclingCharges.id = recyclingChargeId)) AS allsum
							FROM RecyclingCharges
							WHERE 1=1 " . $conditions);
		$allSum = $data->result("array");
		return $allSum[0]["allsum"];		
		
		
	}
	
	function getCost($startDate, $endDate, $DC) {
		$allData = $this->loadCostFromDB($startDate, $endDate, $DC);	
		
		$allSum = array("sqft" => 0, "t" => 0, "ws" => 0, "we" => 0, "wh" => 0, "wd" => 0, "rr" => 0, "o" => 0, "n" => 0);
		$costGraph = array("wi" => 0, "rp" => 0, "rc" => $this->getRecyclingCharges($startDate, $endDate, $DC));
		$returnArray = array();
	
		foreach ($allData[0] as $temp) {
			$costGraph["wi"] += $temp["trashFee"] + $temp["feeAmount"];
			
			

			if(isset($returnArray[$temp["locationId"]])) {
				
				$allSum["t"] += $temp["quantity"];
				$returnArray[$temp["locationId"]]["t"] += $temp["quantity"];
				
				$allSum["ws"] += $temp["feeAmount"];
				$returnArray[$temp["locationId"]]["ws"] += $temp["feeAmount"];
				
				if($temp["feeType"] == 6) {
					$allSum["we"] += $temp["feeAmount"];
					$returnArray[$temp["locationId"]]["we"] += $temp["feeAmount"];
				}
				
				if($temp["feeType"] == 1) {
					$returnArray[$temp["locationId"]]["wh"] += $temp["feeAmount"];
					$allSum["wh"] += $temp["feeAmount"];
				}
				
				if($temp["feeType"] == 3) {
					$returnArray[$temp["locationId"]]["wd"] += $temp["feeAmount"];
					$allSum["wh"] += $temp["feeAmount"];
				}
				
				if($temp["feeType"] != 6 && $temp["feeType"] != 1 && $temp["feeType"] != 3) {
					$returnArray[$temp["locationId"]]["o"] += $temp["feeAmount"];
					$allSum["o"] += $temp["feeAmount"];
				}				
				
			} else {
				$returnArray[$temp["locationId"]] = $temp;
				$returnArray[$temp["locationId"]]["rr"] = 0;
				$returnArray[$temp["locationId"]]["rrf"] = 0;
				$allSum["sqft"] += $temp["sqft"];
				$allSum["t"] += $temp["quantity"];
				$returnArray[$temp["locationId"]]["t"] = $temp["quantity"];
				
				$allSum["ws"] += $temp["feeAmount"];
				$returnArray[$temp["locationId"]]["ws"] = $temp["feeAmount"];
				
				
				
				if($temp["feeType"] == 6) {
					$allSum["we"] += $temp["feeAmount"];
					$returnArray[$temp["locationId"]]["we"] = $temp["feeAmount"];
				} else {
					$returnArray[$temp["locationId"]]["we"] = 0;
				}

				if($temp["feeType"] == 1) {
					$returnArray[$temp["locationId"]]["wh"] = $temp["feeAmount"];
					$allSum["wh"] += $temp["feeAmount"];
				} else {
					$returnArray[$temp["locationId"]]["wh"] = 0;
				}
				
				if($temp["feeType"] == 3) {
					$returnArray[$temp["locationId"]]["wd"] = $temp["feeAmount"];
					$allSum["wd"] += $temp["feeAmount"];
				} else {
					$returnArray[$temp["locationId"]]["wd"] = 0;
				}

				if($temp["feeType"] != 6 && $temp["feeType"] != 1 && $temp["feeType"] != 3) {
					$returnArray[$temp["locationId"]]["o"] = $temp["feeAmount"];
					$allSum["o"] += $temp["feeAmount"];
				} else {
					$returnArray[$temp["locationId"]]["o"] = 0;
				}
			}
		}
			
			foreach ($allData[1] as $temp) {
				$costGraph["rp"] += $temp["rif"];
				if(isset($returnArray[$temp["locationId"]])) {
					
					$allSum["t"] += $temp["quantity"];
					$returnArray[$temp["locationId"]]["t"] += $temp["quantity"];
					
					
					if(isset($returnArray[$temp["locationId"]]["rq"])) {
					
						$returnArray[$temp["locationId"]]["rf"] += $temp["rif"];
						$returnArray[$temp["locationId"]]["rq"] += $temp["quantity"];
						
						$returnArray[$temp["locationId"]]["ra"] += $temp["pricePerUnit"];
						$returnArray[$temp["locationId"]]["rr"] += ($temp["pricePerUnit"]*$temp["quantity"]);
						$returnArray[$temp["locationId"]]["rrf"] += ($temp["rif"]*$temp["quantity"]);
					} else {
						
						$returnArray[$temp["locationId"]]["rf"] = $temp["rif"];
						$returnArray[$temp["locationId"]]["rq"] = $temp["quantity"];
						
						$returnArray[$temp["locationId"]]["ra"] = $temp["pricePerUnit"];
						$returnArray[$temp["locationId"]]["rr"] = ($temp["pricePerUnit"]*$temp["quantity"]);
						$returnArray[$temp["locationId"]]["rrf"] = ($temp["rif"]*$temp["quantity"]);						
						
					}
					
				} else {

					$returnArray[$temp["locationId"]] = $temp;
					$returnArray[$temp["locationId"]]["ws"] = 0;
					$returnArray[$temp["locationId"]]["WTY"] = 0;
					$returnArray[$temp["locationId"]]["WLY"] = 0;
					$allSum["sqft"] += $temp["sqft"];
						
					$allSum["t"] += $temp["quantity"];
					$returnArray[$temp["locationId"]]["t"] = $temp["quantity"];
						
					$returnArray[$temp["locationId"]]["ws"] = 0;
					$returnArray[$temp["locationId"]]["we"] = 0;;
					$returnArray[$temp["locationId"]]["wh"] = 0;
					$returnArray[$temp["locationId"]]["wd"] = 0;
					$returnArray[$temp["locationId"]]["o"] = 0;
					$returnArray[$temp["locationId"]]["rf"] = $temp["rif"];
					$returnArray[$temp["locationId"]]["rq"] = $temp["quantity"];
						
					$returnArray[$temp["locationId"]]["ra"] = $temp["pricePerUnit"];
					$returnArray[$temp["locationId"]]["rr"] = ($temp["pricePerUnit"]*$temp["quantity"]);
					$returnArray[$temp["locationId"]]["rrf"] = ($temp["rif"]*$temp["quantity"]);					
					
				}
			}

		return array($returnArray, $allSum, $costGraph);
	}
	
	
	private function sumCostTrends($startDate, $endDate, $month,  &$data) {
	
		$loadData = $this->loadCostFromDB($startDate, $endDate, "");
		
		foreach ($loadData[0] as $temp) {
			if (isset($data[$temp["id"]]["trend"][$month])) {
				$data[$temp["id"]]["trend"][$month] += $temp["quantity"];
			} else {
				$data[$temp["id"]]["trend"][$month] = $temp["quantity"];
			}			
		}
		foreach ($loadData[1] as $temp) {
			if (isset($data[$temp["id"]]["trend"][$month])) {
				$data[$temp["id"]]["trend"][$month] += $temp["quantity"];
			} else {
				$data[$temp["id"]]["trend"][$month] = $temp["quantity"];
			}
		}		

	}	
	
	
	public function CostTrends($allDC) {
	
		if(count($allDC) < 1) {
			return;
		}
	
		$orderedDC = $allDC;
		$monthArray = array();
	
		$newdate = strtotime ( '-5 month');
		$this->sumCostTrends(date('Y-m-01', $newdate), date('Y-m-t', $newdate), date("m/Y", $newdate), $orderedDC);
		$monthArray[] = date("m/Y", $newdate);
	
		$newdate = strtotime ( '-4 month');
		$this->sumCostTrends(date('Y-m-01', $newdate), date('Y-m-t', $newdate), date("m/Y", $newdate), $orderedDC);
		$monthArray[] = date("m/Y", $newdate);
	
		$newdate = strtotime ( '-3 month');
		$this->sumCostTrends(date('Y-m-01', $newdate), date('Y-m-t', $newdate), date("m/Y", $newdate), $orderedDC);
		$monthArray[] = date("m/Y", $newdate);
	
		$newdate = strtotime ( '-2 month');
		$this->sumCostTrends(date('Y-m-01', $newdate), date('Y-m-t', $newdate), date("m/Y", $newdate), $orderedDC);
		$monthArray[] = date("m/Y", $newdate);
	
		$newdate = strtotime ( '-1 month');
		$this->sumCostTrends(date('Y-m-01', $newdate), date('Y-m-t', $newdate), date("m/Y", $newdate), $orderedDC);
		$monthArray[] = date("m/Y", $newdate);
	
		$this->sumCostTrends(date('Y-m-01'), date('Y-m-t'), "Current",  $orderedDC);
		$monthArray[] = "Current";
	
		$trend = array();
		$dcNames = array();
		$i = 0;
	
		foreach ($orderedDC as $tmp) {
			foreach ($monthArray as $temp) {
				if(isset($tmp["trend"][$temp])) {
					$trend[$i][] = (int)$tmp["trend"][$temp];
				} else {
					$trend[$i][] = 0;
				}
			}
			$i++;
		}
		
		
		foreach ($orderedDC as $temp) {
			if(isset($temp["name"]))
				$dcNames[] = $temp["name"];
		}
	
		return array($orderedDC, $monthArray, $trend, $dcNames);
	}	
	
	
	
	
	//Services
	
	private function day2name($days) {
		$result = array();
		$arrDays = array(2 => 'Su',
				4 => 'Mo',
				8 => 'Tu',
				16 => 'We',
				32 => 'Th',
				64 => 'Fri',
				128 => 'Sat');

		foreach($arrDays as $mask=>$day) {
			if (($days & $mask) > 1) {
				$result[] = $day;
			}
		}
		return count($result);
	}	
	
	function getServices($startDate = null, $endDate = null, $DC = null) {
		
		$conditions = "";
		
		if($startDate) {
			$conditions .= " AND invoiceDate >= '" . $startDate . "'";
		}
		
		if($endDate) {
			$conditions .= " AND invoiceDate <= '" . $endDate . "'";
		}
		
		if($DC) {
			$conditions .= " AND wi.locationId = " . (int) $DC . " ";
		}		
		
		$returnData = array();
		$allSum = array("vendors"=>array(), "ct"=>array(), "duration"=>array(), "fr"=>array());
		$allSum["sqft"] = 0;
		$allSum["cost"] = 0;
		$allSum["frequency"] = array();
		$allSum["fr"] = 0;
		
		//Стари данни
		
		$allServices = $this->db->query("SELECT 
								dc.id, dc.name, dc.squareFootage AS sqft, vendorName, src.name AS containerType, vsd.name AS duration,
								vs.schedule, vs.days, vs.rate, total, date_format(dc.lastUpdated, '%m/%d/%Y')  AS lu, vs.containerId
							FROM WasteInvoices AS wi
								LEFT OUTER JOIN WasteInvoiceServices AS wis ON wi.id = wis.invoiceId
								LEFT OUTER JOIN VendorServices AS vs ON wis.serviceId = vs.id
								LEFT OUTER JOIN VendorServiceDurations AS vsd ON vs.durationId = vsd.id
								LEFT OUTER JOIN DistributionCenters AS dc ON wi.locationId = dc.id
								LEFT OUTER JOIN Containers AS src ON vs.containerId = src.id
							WHERE wi.locationType = 'DC' " . $conditions);
		
		foreach ($allServices->result("array") as $temp) {
			$fkey = 0;
			if($temp["days"]) {
				if($temp["schedule"] == 1) {
					$sch = "Weekly";
				} else if ($temp["schedule"] == 2) {
					$sch = "Biweekly";
				} else if ($temp["schedule"] == 3) {
					$sch = "Monthly";
				} else {
					$sch = "On Call";
				}
			
				$days = $this->day2name($temp["days"]);
				$fkey = $days . " / " . $sch;

				if(isset($allSum["frequency"][$fkey])) {
					$allSum["frequency"][$fkey] += 1;
				} else {
					$allSum["frequency"][$fkey] = 1;
				}
				$allSum["fr"] += $days;
			}
			
 			if(isset($returnData[$temp["id"]])) {
				$returnData[$temp["id"]]["total"] += $temp["total"];
			} else {
				$returnData[$temp["id"]] = $temp;
				$returnData[$temp["id"]]["days"] = $fkey;
			}
			$allSum["sqft"] += $temp["sqft"];
			$allSum["cost"] += $temp["total"];
			$allSum["vendors"][$temp["vendorName"]] = 1;
			
			if(isset($allSum["ct"][$temp["containerType"]])) {
				$allSum["ct"][$temp["containerType"]] = 1;
			} else {
				$allSum["ct"][$temp["containerType"]] = 1;
			}
			if(isset($allSum["duration"][$temp["duration"]])) {
				$allSum["duration"][$temp["duration"]] += 1;
			} else {
				$allSum["duration"][$temp["duration"]] = 1;
			}
			
			$allSum["ft"][$temp["schedule"]] = 1;
		}
		
		return array($returnData, $allSum);
		
		//return array(array(), array());
	}
	
	
	
	//List
	
	function getAllList() {
		
		$rData = $this->db->query("SELECT dc.*, States.name AS sname, date_format(lastUpdated, '%m/%d/%Y')  AS lu,
								(SELECT SUM(total) FROM WasteInvoices AS ws WHERE ws.locationId = dc.id) AS WIT,
								(SELECT SUM(total) FROM RecyclingInvoices AS rs WHERE rs.locationId = dc.id) AS RIT
							FROM DistributionCenters AS dc
						    LEFT OUTER JOIN States ON stateId = States.id");

		return $rData->result('array');
	}
        
	public function getListForSelect() {
	    $this->db->order_by("name", "asc"); 
	    $query = $this->db->get('DistributionCenters');

	    $data = $query->result();

	    $result = array();

	    foreach ($data as $item) {
		    $result[$item->id] = $item->name;
	    }

	    return $result;
	}	
	

	/**
	 * @param integer id `distributioncenters`.`id`
	 * @return array DC contacts
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
			`DistributionCenterContacts`
		WHERE 
			`distributionCenterId` = " . (int)$id;
		$r = $this->db->query($s);
		return $r->result('array');
	}


	/**
	 * @param integer id `distributioncenters`.`id`
	 * @return array choosen DC data
	 */
	public function getDC($id)
	{
		$id = (int)$id;

		$s = "
		SELECT
			*
		FROM
			`DistributionCenters`
		WHERE 
			`id` = " . $id . "
		LIMIT
			1";
		$r = $this->db->query($s);
		return $r->row_array();
	}
	
	public function get_by_ids($id_list) {
	    if ( is_array($id_list) ) {
	        $this->db->select('*');
    	    $this->db->from('DistributionCenters');
    	    $this->db->where_in('id', $id_list);
    	    $query = $this->db->get();
    	    return $query->result('array');
	    }
	    return false;
	}


	/**
	 * @param string startDate date in format YYYY-MM-DD
	 * @param string endDate date in format YYYY-MM-DD
	 * @param integer DC `distributioncenters`.`id`
	 * @return array mixed
	 */
	public function getWasteTable($startDate, $endDate, $DC)
	{
		$DC = (int)$DC;

		$s = "
		SELECT 
			`dc`.`name` AS DC_name,
			`dc`.`squareFootage` AS DC_squareFootage,
			DATE_FORMAT(`wi`.`invoiceDate`, '%Y') AS invoice_y,
			DATE_FORMAT(`wi`.`invoiceDate`, '%m') AS invoice_m,
			`ws`.`quantity`, 
			`m`.`name`,
			`wi`.`total` AS cost

		FROM 
			`WasteInvoices` AS wi

		RIGHT JOIN
			`DistributionCenters` AS dc ON (`wi`.`locationId` = `dc`.`id`)

		LEFT OUTER JOIN 
			`WasteInvoiceServices` AS ws ON (`wi`.`id` = `ws`.`invoiceId`)

		LEFT OUTER JOIN 
			`Materials` AS m ON (`m`.`id` = `materialId`)

		WHERE 
			`wi`.`locationType` = 'DC' 
			AND `ws`.`unitID` = 1
			AND `wi`.`invoiceDate` >= '" . $startDate . "'
			AND `wi`.`invoiceDate` <= '" . $endDate . "'
			AND `wi`.`locationId` = " . $DC . "
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
	 * @param integer DC `distributioncenters`.`id`
	 * @return array mixed
	 */
	public function getRecycleInvoices($startDate, $endDate, $DC)
	{
		$DC = (int)$DC;

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
			`dc`.`name` AS DC_name,
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

		LEFT JOIN `DistributionCenters` AS dc 
			ON (`ri`.`locationId` = `dc`.`id` AND `ri`.`locationType` = 'DC')

		WHERE
			`ri`.`locationType` = 'DC' 
			AND `ri`.`locationId` = " . $DC . "
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
	public function getDiversionRate($recycled, $waste)
	{
		if($recycled == 0)
		{
			return 0;
		}
		else
		{
			return (double) round(($recycled / ($waste + $recycled)), 2);
		}
		
	}


	/**
	 * @param string startDate date in format YYYY-MM-DD
	 * @param string endDate date in format YYYY-MM-DD
	 * @param integer DC `distributioncenters`.`id`
	 * @return array mixed
	 */
	function getSingleDCCost($startDate, $endDate, $DC) 
	{
		$DC = (int)$DC;
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
			`dc`.`name` AS DC_name,
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
			`DistributionCenters` AS dc ON `dc`.`id` = `wi`.`locationId`

		LEFT OUTER JOIN 
			`WasteInvoiceServices` AS ws ON `wi`.`id` = `ws`.`invoiceId`

		LEFT OUTER JOIN 
			`SupportRequestServiceTypes` AS srst ON `ws`.`serviceId` = `srst`.`id`

		LEFT OUTER JOIN 
			`WasteInvoiceFees` AS wf ON `wi`.`id` = `wf`.`invoiceId`

		WHERE 
			`wi`.`locationType` = 'DC' 
			AND `ws`.`unitID` = 1
			AND (`wi`.`invoiceDate` BETWEEN '" . $startDate . "' AND '" . $endDate . "')
			AND `wi`.`locationId` = " . $DC . "

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
			`dc`.`name` AS DC_name,
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
			`DistributionCenters` AS dc ON (`dc`.`id` = `ri`.`locationId`)

		WHERE 
			`ri`.`locationType` = 'DC' 
			AND `ri`.`locationId` = " . $DC . "
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
	 * @param integer DC `distributioncenters`.`id`
	 * @param integer iDisplayStart "x" in "LIMIT x,y"
	 * @param integer iDisplayLength "y" in "LIMIT x,y"
	 * @param string sortColumn "x" in "ORDER BY `x`"
	 * @param string sortDir "x" in "ORDER BY `colname` x"
	 * @return array mixed 
	 */
	public function getInvoices($DC, $iDisplayStart=0, $iDisplayLength=10000, $sortColumn, $sortDir='DESC') {
        $s = "
        SELECT
            dci.id,
          invoiceDate,
          haulerInvNumber,
          dateSent,
          v.name AS vendorName,
          SUM(CASE feeType
              WHEN 1 THEN tons
              WHEN 4 THEN tons ELSE 0
            END) AS scheduledTons,
          SUM(CASE feeType
              WHEN 2 THEN tons
              WHEN 3 THEN tons
              WHEN 5 THEN tons
              WHEN 6 THEN tons ELSE 0
            END) AS oncallTons,
          SUM(amount) AS cost
        FROM DistributionCenters dc
          LEFT JOIN DistributionCenterInvoices dci
            ON dci.distributionCenterId = dc.id
          LEFT JOIN DistributionCenterInvoicesFees dcif
            ON dci.id = dcif.invoiceId
          LEFT JOIN Vendors v
            ON v.id = dci.vendorId
        WHERE dci.status = 'Yes' AND dc.id = ".$DC."
        GROUP BY invoiceDate,
                 haulerInvNumber,
                 dateSent
        ORDER BY " . $sortColumn . " " . $sortDir . "";
        if($iDisplayLength>0) {
            $s .= " LIMIT " . $iDisplayStart . ", " . $iDisplayLength;
        }

        $q = $this->db->query($s);
        $rows = $q->result('array');

        $this->db->select('FOUND_ROWS() as i');
        $query = $this->db->get();
        $row = $query->row();

        return array(
            'rows_count' => $row->i,
            'rows' => $rows
        );
	}

    public function getRebates($DC, $iDisplayStart=0, $iDisplayLength=10000, $sortColumn, $sortDir='DESC') {
        $s = "
            SELECT
              ri.invoiceDate,
              v.name AS vendorName,
              m.name AS materialName,
              SUM(rim.quantity) AS tons,
              SUM((quantity * pricePOUnit) - IFNULL(feeAmount, 0)) AS rebate
            FROM DistributionCenters dc
              LEFT JOIN RecyclingInvoices ri
                ON ri.locationId = dc.id AND ri.status = 'YES'
              LEFT JOIN RecyclingInvoicesMaterials rim
                ON ri.id = rim.invoiceId
              LEFT JOIN RecyclingInvoicesFees rif
                ON ri.id = rif.invoiceId
              LEFT JOIN Materials m
                ON m.id = rim.materialId
              LEFT JOIN Vendors v
                ON v.id = ri.vendorId
            WHERE ri.status = 'Yes' AND dc.id = ".$DC."
            GROUP BY ri.invoiceDate,
                     v.name,
                     m.name
            ORDER BY " . $sortColumn . " " . $sortDir . "";
        if($iDisplayLength>0) {
            $s .= " LIMIT " . $iDisplayStart . ", " . $iDisplayLength;
        }

        $q = $this->db->query($s);
        $rows = $q->result('array');

        $this->db->select('FOUND_ROWS() as i');
        $query = $this->db->get();
        $row = $query->row();

        return array(
            'rows_count' => $row->i,
            'rows' => $rows
        );
    }

    public function getSiteInfo($DC, $iDisplayStart=0, $iDisplayLength=10000, $sortColumn, $sortDir='DESC') {
        $s = "
            SELECT
              dc.name AS locationName,
              squareFootage,
              v.name AS vendorName,
              CASE category WHEN 0 THEN 'Trash' ELSE 'Recycling' END AS serviceType,
              c.name AS containerType,
              mp.name AS frequency,
              quantity * rate AS cost
            FROM DistributionCenters dc
              LEFT OUTER JOIN VendorServices vs
                ON vs.locationId = dc.id AND locationType = 'DC'
              LEFT OUTER JOIN Vendors v
                ON v.id = vs.vendorId
              LEFT OUTER JOIN Containers c
                ON c.id = vs.containerId
              LEFT OUTER JOIN monthlyPickups mp
                ON mp.id = vs.schedule
            WHERE dc.status = 'YES' AND CURDATE() < vs.endDate OR vs.endDate = '0000-00-00' AND dc.id = ".$DC."
            ORDER BY " . $sortColumn . " " . $sortDir . "";
        if($iDisplayLength>0) {
            $s .= " LIMIT " . $iDisplayStart . ", " . $iDisplayLength;
        }

        $q = $this->db->query($s);
        $rows = $q->result('array');

        $this->db->select('FOUND_ROWS() as i');
        $query = $this->db->get();
        $row = $query->row();

        return array(
            'rows_count' => $row->i,
            'rows' => $rows
        );
    }
	/**
	 * @param integer DC `distributioncenters`.`id`
	 * @param integer iDisplayStart "x" in "LIMIT x,y"
	 * @param integer iDisplayLength "y" in "LIMIT x,y"
	 * @param string sortColumn "x" in "ORDER BY `x`"
	 * @param string sortDir "x" in "ORDER BY `colname` x"
	 * @return array mixed 
	 */
	function getSupportRequests($DC, $iDisplayStart=0, $iDisplayLength=10000, $sortColumn='`sr`.`timeStamp`', $sortDir='DESC')
	{
		$DC = (int)$DC;

		$s = "
		SELECT 
			SQL_CALC_FOUND_ROWS
			`dc`.`name` AS location,
			`sr`.`id` AS service_id,
			DATE_FORMAT(`sr`.`timeStamp`, '%m/%d/%Y') AS r_date,
			CONCAT(`sr`.`firstName`, ' ', `sr`.`lastName`) AS contact,
			`sr`.`phone`,
			`sr`.`notes` AS description,
			`sr`.`complete`,
			IF(`sr`.`complete` = 1, 'Y', 'N') AS complete_word

		FROM 
			`SupportRequests` AS sr

		INNER JOIN `DistributionCenters` AS dc 
			ON (
				`dc`.`id` = `sr`.`locationId` 
				AND `sr`.`locationType` = 'DC'
			)

		WHERE
			`sr`.`locationId` = " . $DC . "

		ORDER BY
			" . $sortColumn . " " . $sortDir . "";
        if($iDisplayLength>0) {
            $s .= " LIMIT " . $iDisplayStart . ", " . $iDisplayLength;
        }

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
	 * @param integer DC `distributioncenters`.`id`
	 * @param integer iDisplayStart "x" in "LIMIT x,y"
	 * @param integer iDisplayLength "y" in "LIMIT x,y"
	 * @param string sortColumn "x" in "ORDER BY `x`"
	 * @param string sortDir "x" in "ORDER BY `colname` x"
	 * @return array mixed 
	 */
	function getVendorsByInvoices($DC, $iDisplayStart=0, $iDisplayLength=10000, $sortColumn='last_updated', $sortDir='DESC')
	{
		$DC = (int)$DC;

		$s = "
		SELECT 
			SQL_CALC_FOUND_ROWS
			`dc`.`name` AS location,
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

		INNER JOIN `DistributionCenters` AS dc 
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
			`wi`.`locationType` = 'DC'
			AND `wi`.`locationId` = " . $DC . "


		UNION


		SELECT 
			`dc`.`name` AS location,
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

		INNER JOIN `DistributionCenters` AS dc 
			ON (
				`dc`.`id` = `ri`.`locationId` 
				AND `ri`.`locationType` = 'DC'
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
			`ri`.`locationType` = 'DC'
			AND `ri`.`locationId` = " . $DC . "

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

    public function getDCDashboardRecyclingRebateChartInfo($startDate, $endDate, $distributioncenter_id) {
        $info = $this->db->query("
            SELECT
              dc.name AS dcName,
              SUM(IFNULL(quantity, 0) * IFNULL(pricePOUnit, 0)) - SUM(IFNULL(feeAmount, 0)) AS rebate
            FROM RecyclingInvoices ri
              LEFT JOIN RecyclingInvoicesMaterials rim
                ON ri.id = rim.invoiceId
              LEFT JOIN RecyclingInvoicesFees rif
                ON ri.id = rif.invoiceId
              LEFT JOIN DistributionCenters dc
                ON dc.id = ri.locationId
            WHERE ri.invoiceDate BETWEEN '{$startDate}' AND '{$endDate}' ".($distributioncenter_id>0 ? ' AND dc.id = '.$distributioncenter_id : '')."
            GROUP BY dc.name");

        return $info->result();
    }

    public function getDCDashboardRecyclingRecyclingTypesChartInfo($startDate, $endDate, $distributioncenter_id) {
        $info = $this->db->query("
            SELECT
              m.name AS materialName,
              SUM(quantity) AS tons
            FROM RecyclingInvoices ri
              LEFT JOIN RecyclingInvoicesMaterials rim
                ON ri.id = rim.invoiceId
              LEFT JOIN Materials m
                ON m.id = rim.materialId
              LEFT JOIN DistributionCenters dc
                ON dc.id = ri.locationId
            WHERE ri.invoiceDate BETWEEN '{$startDate}' AND '{$endDate}' ".($distributioncenter_id>0 ? ' AND dc.id = '.$distributioncenter_id : '')."
            GROUP BY m.name");

        return $info->result();
    }

    public function getDCDashboardRecyclingDatagridInfo($startDate, $endDate, $distributioncenter_id) {
        /*
        $info = $this->db->query("
          SELECT
              dc.id,
              dc.name AS dcName,
              dc.squareFootage,
              SUM(CASE materialId
                  WHEN 1 THEN quantity
                  WHEN 6 THEN quantity ELSE 0
                END) AS 'Paper',
              SUM(CASE materialId
                  WHEN 5 THEN quantity
                  WHEN 8 THEN quantity ELSE 0
                END) AS 'Plastic',
              SUM(CASE materialId
                  WHEN 3 THEN quantity
                  WHEN 4 THEN quantity ELSE 0
                END) AS 'Metal',
              SUM(CASE materialId
                  WHEN 1 THEN 0
                  WHEN 6 THEN 0
                  WHEN 5 THEN 0
                  WHEN 8 THEN 0
                  WHEN 3 THEN 0
                  WHEN 4 THEN 0 ELSE quantity
                END) AS 'Other',
              SUM(quantity * pricePOUnit) - SUM(IFNULL(feeAmount, 0)) AS 'Rebate'
            FROM DistributionCenters dc
              LEFT JOIN RecyclingInvoices ri
                ON ri.locationId = dc.id AND ri.status = 'YES'
              LEFT JOIN RecyclingInvoicesMaterials rim
                ON ri.id = rim.invoiceId
              LEFT JOIN RecyclingInvoicesFees rif
                ON ri.id = rif.invoiceId
              LEFT JOIN Materials m
                ON m.id = rim.materialId
            WHERE ri.invoiceDate BETWEEN '{$startDate}' AND '{$endDate}' ".($distributioncenter_id>0 ? ' AND dc.id = '.$distributioncenter_id : '')."
            GROUP BY dc.id,
                     dc.name,
                     dc.squareFootage");
        */
        //NOT IN (1, 3, 5, 6, 4, 8)
        $info = $this->db->query('
            SELECT
              ri.locationId,
              dc.name AS dcName,
              m.name,
              CONCAT(m.name, "($)") AS cost,
              SUM(rim.quantity) AS quantity,
              SUM(rim.pricePOUnit * rim.quantity) AS pricePOUnit
            FROM RecyclingInvoices ri
              LEFT JOIN RecyclingInvoicesMaterials rim
                ON ri.id = rim.invoiceId
              LEFT JOIN Materials m
                ON m.id = rim.materialId
              LEFT JOIN DistributionCenters dc
                ON dc.id = ri.locationId
            WHERE ri.invoiceDate BETWEEN "'.$startDate.'" AND "'.$endDate.'" '.($distributioncenter_id > 0 ? ' AND ri.locationId = '.$distributioncenter_id : '').'
            GROUP BY ri.locationId, m.name');
        /*$info = $this->db->query('
        SELECT
          dc.name AS dcName,
          SUM(IF(materialId=1, quantity, 0)) AS total_quantity_1,
          SUM(IF(materialId=1, pricePOUnit, 0)) AS total_pricePOUnit_1,
          SUM(IF(materialId=3, quantity, 0)) AS total_quantity_3,
          SUM(IF(materialId=3, pricePOUnit, 0)) AS total_pricePOUnit_3,
          SUM(IF(materialId=5, quantity, 0)) AS total_quantity_5,
          SUM(IF(materialId=5, pricePOUnit, 0)) AS total_pricePOUnit_5,
          SUM(IF(materialId=6, quantity, 0)) AS total_quantity_6,
          SUM(IF(materialId=6, pricePOUnit, 0)) AS total_pricePOUnit_6,
          SUM(IF(materialId=4, quantity, 0)) AS total_quantity_4,
          SUM(IF(materialId=4, pricePOUnit, 0)) AS total_pricePOUnit_4,
          SUM(IF(materialId=8, quantity, 0)) AS total_quantity_8,
          SUM(IF(materialId=8, pricePOUnit, 0)) AS total_pricePOUnit_8,
          SUM(IF(materialId NOT IN (1, 3, 5, 6, 4, 8), quantity, 0)) AS total_quantity_0,
          SUM(IF(materialId NOT IN (1, 3, 5, 6, 4, 8), pricePOUnit, 0)) AS total_pricePOUnit_0
        FROM
          (
            SELECT
              ri.locationId,
              rim.materialId,
              SUM(rim.quantity) AS quantity,
              SUM(rim.pricePOUnit*rim.quantity) AS pricePOUnit
            FROM RecyclingInvoices ri
              LEFT JOIN RecyclingInvoicesMaterials rim
                ON ri.id = rim.invoiceId
            WHERE ri.invoiceDate BETWEEN "'.$startDate.'" AND "'.$endDate.'" '.($distributioncenter_id > 0 ? ' AND ri.locationId = '.$distributioncenter_id : '').'
              GROUP BY ri.locationId, rim.materialId
           ) t LEFT JOIN DistributionCenters dc ON t.locationId = dc.id
              GROUP BY locationId
         ORDER BY dc.name');*/
        $r = $info->result();
        //echo $this->db->last_query();
        return $r;
    }

    function getDCDashboardRecyclingTotalTonnageTotalPOPriceChartsInfo($startDate, $endDate, $distributioncenter_id) {
        $info = $this->db->query('
        SELECT
          m.name AS materialName,
          SUM(rim.quantity) AS quantity,
          SUM(rim.pricePOUnit) AS pricePOUnit
        FROM RecyclingInvoices ri
          LEFT JOIN RecyclingInvoicesMaterials rim
            ON ri.id = rim.invoiceId
          LEFT JOIN Materials m
            ON rim.materialId = m.id
        WHERE ri.invoiceDate BETWEEN "'.$startDate.'" AND "'.$endDate.'" '.($distributioncenter_id > 0 ? ' AND ri.locationId = '.$distributioncenter_id : '').'
        GROUP BY m.name');
        $r = $info->result();
        //echo $this->db->last_query();

        return $r;
    }
}


