<?php
include_once dirname(__FILE__).'/../basemodel.php';

class VendorServices extends BaseModel {
	private $table = 'VendorServices';

	public function __construct() {
		parent::__construct();
		$this->load->database();
	}

	public function __destruct() {
		//echo $this->db->last_query();
	}
	
	public function getByLocationAndVendor($locationId, $locationType, $vendorId, $invoiceDate) {
		if ($locationType != 'STORE') {
			$locationType = 'DC';
		}
		
		$locationId = (int)$locationId;
		$vendorId = (int)$vendorId;

        $query = $this->db->query("
			SELECT vs.* FROM {$this->table} as vs
			WHERE
				vs.locationType = '$locationType' 
				AND vs.locationId = $locationId 
				AND vs.vendorId = $vendorId 
				AND (vs.endDate > '$invoiceDate' OR !vs.endDate)
		");

		return $query->result();
	}
	
	
	public function getListForSelect($companyId) {
		$companyId = (int)$companyId;
		
		$this->db->select($this->table .'.*, Containers.name as container');
		$this->db->join('Vendors', 'Vendors.id = vendorId');
		$this->db->join('Containers', 'Containers.id = containerId');
		$query = $this->db->get_where($this->table,
			array(
				'Vendors.companyId' => $companyId
			)
		);
		
		$data = $query->result();
		$result = array();
		$result[0] = '- Please select -';
		
		foreach ($data as $item) {
			$result[$item->id] = $item->name . ', ' .  $item->container;
		}
		
		return $result;
	}
	
	public function addVendorService($vendorId, $data) {
		if (isset($data['days']) && is_array($data['days'])) {
			$data['days'] = array_sum($data['days']);
		} else {
			$data['days'] = 0;
		}
        $fees = array();
        if(isset($data['fees'])) {
            $fees = $data['fees'];
        }
		$data = $this->cleanFields($data);
		$data['vendorId'] = (int)$vendorId;		
		$this->db->insert($this->table, $data);
        $vendorservice_id = $this->db->insert_id();

        foreach($fees as $fee) {
            $fee['vendorServiceId'] = $vendorservice_id;
            $this->db->insert('VendorServiceFees', $fee);
        }

		$this->saveEvent($vendorservice_id);
        return $vendorservice_id;
	}	
	
	public function editVendorService($serviceId, $data) {
		if (isset($data['days']) && is_array($data['days'])) {
			$data['days'] = array_sum($data['days']);
		} else {
			$data['days'] = 0;
		}
        $fees = array();
        if(isset($data['fees'])) {
            $fees = $data['fees'];
        }
		$data = $this->cleanFields($data);
		$this->db->where('id', $serviceId);
		$this->db->update($this->table, $data);

        $this->db->delete('VendorServiceFees', Array('vendorServiceId' => $serviceId));
        foreach($fees as $fee) {
            $fee['vendorServiceId'] = $serviceId;
            $this->db->insert('VendorServiceFees', $fee);
        }

		$this->saveEvent($serviceId);
	}

	private function saveEvent($serviceId) {
		$vendorId = $this->db->select("locationId, locationType")->get_where($this->table, array('id' => $serviceId))->row();
		$locationType = '';
		switch ($vendorId->locationType) {
			case 'DC':
				$locationType = 'dc_setup';
				break;
			case 'STORE':
				$locationType = 'store_setup';
				break;
			default:
				$locationType = 'vendor_setup';
		}
		$this->addEvent($locationType, $vendorId->locationId, 'change');
	}

	public function Delete($serviceId) {
		$this->saveEvent($serviceId);
		$this->db->delete($this->table, Array('id' => $serviceId));		
	}

	public function getList($vendorId) {
		$vendorId = (int)$vendorId;

		$query = $this->db->query("
			SELECT vs.*, (
				IF (vs.locationType = 'DC', (
					SELECT name FROM DistributionCenters WHERE id = vs.locationId LIMIT 1
				), (
					SELECT location FROM Stores WHERE id = vs.locationId LIMIT 1
				) ) ) as locationName
			FROM {$this->table} as vs
			WHERE vs.vendorId = $vendorId
		");

		return $query->result();
	}

    public function getVendorServiceFees($serviceId) {
        $serviceId = (int)$serviceId;

        $this->db->select('VendorServiceFees.*, FeeType.name AS feeTypeTitle', false);
        $this->db->join('FeeType', 'VendorServiceFees.feeType = FeeType.id');
        $query = $this->db->get_where('VendorServiceFees',
            array(
                'vendorServiceId' => $serviceId
            )
        );

        return $query->result();
    }
	
	public function getById($id) {
	    $id = (int)$id;		
	    $this->db->select();
	    $query = $this->db->get_where($this->table, array('id' => $id), 1);

        $service = $query->row();

        $query = $this->db->query('SELECT * FROM `VendorServiceFees` WHERE `vendorServiceId` = "'.$service->id.'"');
        $service->fees = $query->result();

	    return $service;
	}
	
	public function getByLocation($locationId, $locationType, $is_history=false) {
		$locationId = (int)$locationId;
		
		if ($locationType != 'STORE') {
			$locationType = 'DC';
		}
		
		if($is_history){
		    $where_history = "AND vs.endDate<now() AND vs.endDate";
		}else{
		    $where_history = "AND (vs.endDate>=now() OR !vs.endDate)";
		}
		
		$query = $this->db->query("
			SELECT vs.*, v.name as vendorName, v.phone as vendorPhone, v.email as vendorEmail
				FROM {$this->table} as vs
				LEFT JOIN Vendors as v ON v.id = vs.vendorId
			WHERE
				vs.locationId = $locationId AND vs.locationType = '$locationType' ".$where_history);
        $services = $query->result();

        foreach($services as $k=>$v) {
            $service = $services[$k];

            $query = $this->db->query('SELECT * FROM `VendorServiceFees` WHERE `vendorServiceId` = "'.$service->id.'"');
            $service->fees = $query->result();
        }

		return $services;
	}
	
	public function getForMaterialCharges($locationId, $vendorId) {
	    $sql = 'SELECT vs.*, mp.pickupsPerMonth, c.weightInLbs
		FROM '.$this->table.' vs LEFT JOIN Vendors v ON v.id = vs.vendorId  LEFT JOIN monthlyPickups mp ON vs.schedule = mp.id LEFT JOIN Containers c ON vs.containerId = c.id
		WHERE vs.locationId = '.$locationId.' 
		    AND vs.vendorId = '.$vendorId.' 
		    AND vs.locationType = "DC"
		    AND vs.schedule !=1
		    AND (vs.endDate>=now() OR !vs.endDate)';
	    $query = $this->db->query($sql);

	    return $query->result();
	}
	
	public function getSavingsData($year) {
        $data = array();
        $year_s = $year - 1;
        $year_e = $year;

        $sql = 'SELECT * FROM States ORDER BY code';
        $query = $this->db->query($sql);
        $states = $query->result();
        foreach ($states as $state) {
            $data[$state->code] = array();
            $sql = 'SELECT SUM(bl.CurrentMonthlyRate) AS s FROM BaselineCosts bl LEFT JOIN Stores strs ON bl.StoreID = strs.location and ifnull(strs.franchise,0)=0 and strs.districtName not like "DR%" LEFT JOIN States stts ON strs.stateId = stts.id WHERE stts.code = "' . $state->code . '"';
            $query = $this->db->query($sql);
            $result = $query->result();
            $data[$state->code]['Baseline'] = round(floatval($result[0]->s), 2);
            for ($m = 0; $m <= 11; $m++) {
                $data[$state->code][date('Y/m', mktime(0, 0, 0, 9 + $m, 1, $year_s))] = 0.0;
            }
        }

        $sql = 'SELECT wi.*, stts.code AS stateCode, DATE_FORMAT(wi.invoiceDate, "%Y/%m") AS ym
		FROM WasteInvoices wi LEFT JOIN Stores strs ON wi.locationId = strs.id AND ifnull(strs.franchise,0)=0 AND strs.districtName NOT LIKE "DR%"
		LEFT JOIN States stts ON strs.stateId = stts.id
		WHERE wi.locationType = "STORE" AND strs.officeLocation != 1 AND CAST(strs.location AS UNSIGNED) <= 15324  AND wi.invoiceDate >= "'.$year_s.'-09-01" AND wi.invoiceDate <= "'.$year_e.'-08-31" AND stts.code IS NOT NULL';
        $query = $this->db->query($sql);
        $invoices = $query->result();
        foreach ($invoices as $invoice) {
            $sql = 'SELECT SUM(wis.rate) AS s FROM WasteInvoiceServices wis WHERE wis.invoiceId = "' . $invoice->id . '" AND wis.serviceTypeId = 1';
            $query = $this->db->query($sql);
            $result = $query->result();

            if(isset($data[$invoice->stateCode][$invoice->ym])) {
                $data[$invoice->stateCode][$invoice->ym] += round(floatval($result[0]->s), 2);
            }
        }

        /*
        $me = intval(date('Ym', mktime(0, 0, 0, 9, 1, $year_e))) - intval(date('Ym', mktime(0, 0, 0, date('m'), 1, date('Y'))));
        for ($m = 0; $m < $me; $m++) {
            $dt = date('Y-m-d', mktime(0, 0, 0, date('m') + $m, 1, date('Y')));
            $ym = date('Y/m', mktime(0, 0, 0, date('m') + $m, 1, date('Y')));

            $sql = 'SELECT SUM(vs.rate) AS s, stts.code AS stateCode
		    FROM VendorServices vs 
		    LEFT JOIN Stores strs ON vs.locationId = strs.location and ifnull(strs.franchise,0)=0 and strs.districtName not like "DR%" 
		    LEFT JOIN States stts ON strs.stateId = stts.id
		    WHERE (vs.enddate > "' . $dt . '" OR vs.enddate = "0000-00-00") AND locationType = "Store" AND strs.status = "YES"
		    GROUP BY stateCode';
            $query = $this->db->query($sql);
            $services = $query->result();
            foreach ($services as $service) {
                if (!empty($service->stateCode)) {
                    $data[$service->stateCode][$ym] += round(floatval($service->s), 2);
                }
            }
        }
        */

        return $data;
	}

    public function getSavingsExtrasFeesData($year) {
        $data = array();
        $year_s = $year - 1;
        $year_e = $year;

        $sql = '
            SELECT
              stts.code AS stateCode,
              DATE_FORMAT(wi.invoiceDate, "%Y/%m") AS ym,
              SUM(wis.rate) AS "Extras"
            FROM WasteInvoices wi
              LEFT JOIN Stores strs
                ON wi.locationId = strs.id
              LEFT JOIN States stts
                ON strs.stateId = stts.id
              LEFT JOIN WasteInvoiceServices wis
                ON wis.invoiceId = wi.id AND wis.serviceTypeId <> 1
            WHERE wi.locationType = "STORE"
            AND wi.invoiceDate >= "'.$year_s.'-09-01"
            AND wi.invoiceDate <= "'.$year_e.'-08-31"
            AND stts.code IS NOT NULL
            GROUP BY 1, 2';

        $query = $this->db->query($sql);
        $rows = $query->result();
        foreach($rows as $row) {
            $k = $row->stateCode.'_'.$row->ym;
            if(!isset($data[$k])) {
                $data[$k] = array();
            }
            $data[$k]['extras'] = $row->Extras;
        }

        $sql = '
            SELECT
              stts.code AS stateCode,
              DATE_FORMAT(wi.invoiceDate, "%Y/%m") AS ym,
              SUM(IF(wif.feeType IN (2, 4, 12, 13, 9, 7, 16), wif.feeAmount, 0)) AS "Fees1",
              SUM(IF(wif.feeType NOT IN (2, 4, 12, 13, 9, 7, 16), wif.feeAmount, 0)) AS "Fees2"
            FROM WasteInvoices wi
              LEFT JOIN Stores strs
                ON wi.locationId = strs.id
              LEFT JOIN States stts
                ON strs.stateId = stts.id
              LEFT JOIN WasteInvoiceFees wif
                ON wif.invoiceId = wi.id
            WHERE wi.locationType = "STORE"
            AND wi.invoiceDate >= "'.$year_s.'-09-01"
            AND wi.invoiceDate <= "'.$year_e.'-08-31"
            AND stts.code IS NOT NULL
            GROUP BY 1, 2';

        $query = $this->db->query($sql);
        $rows = $query->result();
        foreach($rows as $row) {
            $k = $row->stateCode.'_'.$row->ym;
            if(!isset($data[$k])) {
                $data[$k] = array();
            }
            $data[$k]['mainCost'] = $row->Fees1;
            $data[$k]['fees'] = $row->Fees2;
        }

        return $data;
    }
	/*
	 * 
	 */
	private function cleanFields($data) {
		$tableFields = $this->db->list_fields($this->table);
		$result = array();

		foreach ($tableFields as $field) {
			if (array_key_exists($field, $data)) {
				$result[$field] = $data[$field];
			}
		}

		return $result;
	}
	
}
