<?php
class ReportsModel extends CI_Model {

	public function __construct() {
		parent::__construct();
		$this->load->database();
	}
	
	public function getQuickBooksReport($companyID, $startDate, $endDate, $type) {
		$condition = " ";
		$returnData[] = array("Name", "Total Price", "Invoice Date", "Type", "PO#", "Trailer Number");
		
		if($startDate) {
			$condition .= " AND invoiceDate >= '" . $startDate . "' ";
		}
		if($endDate) {
			$condition .= " AND invoiceDate <= '" . $endDate . "' ";
		}
		if($type == 1) {
			$condition .= " AND locationType = 'DC'";
		} else if ($type == 2) {
			$condition .= " AND locationType = 'STORE'";
		}
		

		$invoiceData = $this->db->query("SELECT *,
		    IF(locationType = 'DC', 
				   (SELECT name FROM DistributionCenters WHERE id = locationId), 
				   (SELECT location FROM Stores WHERE id = locationId)) as location,
				   (SELECT SUM(quantity) FROM RecyclingInvoicesMaterials WHERE ri.id = invoiceId) AS quantity,
				   (SELECT SUM(pricePerUnit) FROM RecyclingInvoicesMaterials WHERE ri.id = invoiceId) AS salesPrice
		   FROM RecyclingInvoices AS ri WHERE companyId = " . $companyID . $condition);
		

		foreach ($invoiceData->result("array") as $temp) {
			$returnData [] = array($temp["location"],
									$temp["total"],
									$temp["invoiceDate"],
									$temp["locationType"],
									$temp["poNumber"],
									$temp["trailerNumber"]);
		}
		
		return $returnData;
	}

    public function getDiversionStores($y, $m) {
        $ds = date('Y-m-d', mktime(0, 0, 0, $m, 1, $y));
        $de = date('Y-m-d', mktime(0, 0, 0, $m+1, 0, $y));

        $result = $this->db->query('
            SELECT
              name,
              SUM(tot) AS value
            FROM (SELECT
                m.name,
                SUM(quantity * weightInLbs * IFNULL(pickupsPerMonth, 1)) AS "tot"
              FROM Stores s
                LEFT JOIN WasteInvoices wi
                  ON wi.locationid = s.id
                LEFT JOIN WasteInvoiceServices wis
                  ON wi.id = wis.invoiceid
                LEFT JOIN Materials m
                  ON m.id = wis.materialid
                LEFT JOIN Containers c
                  ON c.id = wis.containerid
                LEFT JOIN monthlyPickups mp
                  ON mp.id = wis.schedule
              WHERE wi.invoicedate BETWEEN "'.$ds.'" AND "'.$de.'" AND officeLocation <> 1
              GROUP BY m.name
              UNION ALL
              SELECT
                m.name,
                SUM(IFNULL(vs.quantity, 0) * IFNULL(weightInLbs, 0) * IFNULL(pickupsPerMonth, 1)) AS "tot"
              FROM Stores s
                INNER JOIN VendorServices vs
                  ON vs.locationId = s.id AND vs.rate = 0 AND ("'.$ds.'" < IFNULL(vs.enddate, "0000-00-00") OR IFNULL(vs.enddate, "0000-00-00") = "0000-00-00") AND vs.schedule > 1
                INNER JOIN Materials m
                  ON m.id = vs.materialid
                INNER JOIN Containers c
                  ON c.id = vs.containerid
                INNER JOIN monthlyPickups mp
                  ON mp.id = vs.schedule
              WHERE ("'.$ds.'" < IFNULL(s.enddate, "0000-00-00") OR IFNULL(s.enddate, "0000-00-00") = "0000-00-00")
               AND vs.locationType="STORE" AND officeLocation <> 1) t1
            WHERE name IS NOT NULL
            GROUP BY name
            UNION
            SELECT
              "ColumnB" AS name,
              COUNT(*) AS value
            FROM Stores
            WHERE (("'.$ds.'" BETWEEN IFNULL(startDate, "2010-01-01") AND IFNULL(endDate, "2050-01-01")) OR
            (startDate = "0000-00-00" AND "'.$ds.'" < endDate) OR
            ("'.$ds.'" > IFNULL(startDate, "2010-01-01") AND endDate = "0000-00-00") OR
            (startDate = "0000-00-00" AND endDate = "0000-00-00"))
            AND officeLocation <> 1');

        return $result->result();

        
    }

    public function getDiversionDCs($y, $m) {
        $ds = date('Y-m-d', mktime(0, 0, 0, $m, 1, $y));
        $de = date('Y-m-d', mktime(0, 0, 0, $m+1, 0, $y));

        $result = $this->db->query('
            SELECT
              m.name,
              SUM(quantity)*'.TON_KOEFF.'  AS value
            FROM RecyclingInvoicesMaterials rim
              LEFT JOIN Materials m
                ON m.id = rim.materialId
            WHERE invoiceDate BETWEEN "'.$ds.'" AND "'.$de.'"
            GROUP BY m.name
              UNION
            SELECT
              "ColumnC" AS name,
              SUM(tons)*'.TON_KOEFF.' AS value
            FROM DistributionCenterInvoicesFees dcif
              LEFT JOIN DistributionCenterInvoices dci
                ON dci.id = dcif.invoiceId
            WHERE dci.invoiceDate BETWEEN "'.$ds.'" AND "'.$de.'"
              UNION
            SELECT
              "ColumnB" AS name,
              COUNT(*) AS value
            FROM DistributionCenters
            WHERE status = 1');

        return $result->result();
    }

    public function getDiversionCampus($y, $m) {
        $ds = date('Y-m-d', mktime(0, 0, 0, $m, 1, $y));
        $de = date('Y-m-d', mktime(0, 0, 0, $m+1, 0, $y));

        $result = $this->db->query('
            SELECT
              name,
              SUM(tot) AS value
            FROM (SELECT
                m.name,
                SUM(quantity * weightInLbs * IFNULL(pickupsPerMonth, 1)) AS "tot"
              FROM Stores s
                LEFT JOIN WasteInvoices wi
                  ON wi.locationid = s.id
                LEFT JOIN WasteInvoiceServices wis
                  ON wi.id = wis.invoiceid
                LEFT JOIN Materials m
                  ON m.id = wis.materialid
                LEFT JOIN Containers c
                  ON c.id = wis.containerid
                LEFT JOIN monthlyPickups mp
                  ON mp.id = wis.schedule
              WHERE wi.invoicedate BETWEEN "'.$ds.'" AND "'.$de.'" AND officeLocation = 1
              GROUP BY m.name
              UNION ALL
              SELECT
                m.name,
                SUM(IFNULL(vs.quantity, 0) * IFNULL(weightInLbs, 0) * IFNULL(pickupsPerMonth, 1)) AS "tot"
              FROM Stores s
                INNER JOIN VendorServices vs
                  ON vs.locationId = s.id AND vs.rate = 0 AND ("'.$ds.'" < IFNULL(vs.enddate, "0000-00-00") OR IFNULL(vs.enddate, "0000-00-00") = "0000-00-00") AND vs.schedule > 1
                INNER JOIN Materials m
                  ON m.id = vs.materialid
                INNER JOIN Containers c
                  ON c.id = vs.containerid
                INNER JOIN monthlyPickups mp
                  ON mp.id = vs.schedule
              WHERE ("'.$ds.'" < IFNULL(s.enddate, "0000-00-00") OR IFNULL(s.enddate, "0000-00-00") = "0000-00-00")
              AND officeLocation = 1) t1
            WHERE name IS NOT NULL
            GROUP BY name
            UNION
            SELECT
              "ColumnB" AS name,
              COUNT(*) AS value
            FROM Stores
            WHERE (("'.$ds.'" BETWEEN IFNULL(startDate, "2010-01-01") AND IFNULL(endDate, "2050-01-01")) OR
            (startDate = "0000-00-00" AND "'.$ds.'" < endDate) OR
            ("'.$ds.'" > IFNULL(startDate, "2010-01-01") AND endDate = "0000-00-00") OR
            (startDate = "0000-00-00" AND endDate = "0000-00-00"))
            AND officeLocation = 1');

        return $result->result();
    }

	public function getRecyclingInvoices($companyID, $startDate, $endDate, $type) {
		$condition = " ";
		$returnData[] = array("Name", "Invoice Date", "PO#", "Memo", "Quontity", "Sales Price", "Total");

		if($startDate) {
			$condition .= " AND invoiceDate >= '" . $startDate . "' ";
		}
		if($endDate) {
			$condition .= " AND invoiceDate <= '" . $endDate . "' ";
		}
		if($type == 1) {
			$condition .= " AND locationType = 'DC'";
		} else if ($type == 2) {
			$condition .= " AND locationType = 'STORE'";
		}		
		
		
		$invoiceData = $this->db->query("SELECT *,
					(SELECT SUM(quantity) FROM RecyclingInvoicesMaterials WHERE ri.id = invoiceId) AS quantity,
					(SELECT SUM(pricePerUnit) FROM RecyclingInvoicesMaterials WHERE ri.id = invoiceId) AS salesPrice,
						 IF(locationType = 'DC', 
								(SELECT name FROM DistributionCenters WHERE id = locationId), 
								(SELECT location FROM Stores WHERE id = locationId)) as location
						FROM RecyclingInvoices AS ri WHERE companyId = " . $companyID . $condition . "
					ORDER BY locationId, invoiceDate");
		
		foreach( $invoiceData->result("array") as $temp ) {
			$returnData[] = array(
							$temp["location"],
							$temp["invoiceDate"],
							$temp["poNumber"],
							$temp["internalNotes"],
							$temp["quantity"],
							$temp["salesPrice"],
							$temp["total"]);
		}
		return $returnData;

	}
	
	public function getMissingInvoices($ym, $filter=null) {
	    $sql = 'SELECT SQL_CALC_FOUND_ROWS DISTINCT t1.*, v.`name` AS vendorName, v.`number` AS vendorNumber, s.location
		    FROM (SELECT locationId, vendorId FROM VendorServices WHERE locationType = "STORE" AND rate > 0 AND DATE_FORMAT(startDate, "%Y%m") <= "'.$ym.'" AND (DATE_FORMAT(endDate, "%Y%m") >= "'.$ym.'" OR endDate="0000-00-00")) t1
		    LEFT JOIN (SELECT locationId, vendorId FROM WasteInvoices WHERE DATE_FORMAT(invoiceDate, "%Y%m") = "'.$ym.'") t2 ON t1.locationId = t2.locationId AND t1.vendorId = t2.vendorId
		    LEFT JOIN Vendors v ON t1.vendorId = v.id
		    LEFT JOIN Stores s ON t1.locationId = s.id
		    WHERE t2.locationId IS NULL AND t2.vendorId IS NULL';
	    if($filter!=null) {
		    $sql .= ' ORDER BY '.$filter['sortColumn'].' '.$filter['sortDir'].'';
		    $sql .= ' LIMIT '.$filter['start'].', '.$filter['length'].'';
	    }

	    $result = $this->db->query($sql);
	    
	    $orders = $result->result();
	    
	    $this->db->select('FOUND_ROWS() as i');
	    $query = $this->db->get();

	    $row = $query->row();

	    return array(
		    'records' => $row->i,
		    'data' => $orders
	    );
	}
	
	public function getRecyclingInvoicesForDC($companyID, $startDate, $endDate) {
		$condition = '';
		
		if($startDate) {
			$condition .= " AND rc.invoiceDate >= '" . $startDate . "' ";
		}
		if($endDate) {
			$condition .= " AND rc.invoiceDate <= '" . $endDate . "' ";
		}
		
		$result = $this->db->query("
			SELECT dc.id, dc.name FROM RecyclingInvoices as rc
				INNER JOIN DistributionCenters as dc ON dc.id = rc.locationId AND rc.locationType = 'DC' 
			WHERE
				1=1 $condition  
		");

		$dcList = $result->result();
		
		foreach ($dcList as &$dc) {
			
			$result = $this->db->query("
					SELECT rc.* FROM RecyclingInvoices as rc
					WHERE
						1=1 $condition AND rc.locationType = 'DC' AND rc.locationId = {$dc->id}
					ORDER BY rc.invoiceDate ASC
			");
			$orders = $result->result();
	
			
			foreach ($orders as &$order) {
				
				$result = $this->db->query("
					SELECT rpo.*, m.name as materialName FROM RecyclingPurchaseOrder as rpo 
						INNER JOIN Materials as m ON rpo.materialId = m.id
					WHERE
						rpo.invoiceId = {$order->id}
					ORDER BY rpo.PODate ASC
				");
				
				$order->po = $result->result();
				
				$result = $this->db->query("
					SELECT rif.* FROM RecyclingInvoicesFees as rif 
					WHERE
						rif.invoiceId  = {$order->id}
					ORDER BY rif.id ASC
				");
				
				$order->fees = $result->result();
			}
			
			$dc->orders[] = $orders;
		}
		
		return $dcList;
	}

	public function get_schedule_id($schedule_name) {
        $schedules = array('On Call'=>1,
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

        if ( $schedule_name == 'scheduled' ) {
            return $schedules;
        }
        elseif ( array_key_exists($schedule_name, $schedules) ) {
            return $schedules[$schedule_name];
        }
        return false;
	}
}