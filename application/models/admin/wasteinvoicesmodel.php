<?php
include_once dirname(__FILE__).'/../basemodel.php';

class WasteInvoicesModel extends BaseModel {
	private $table = 'WasteInvoices';
	
	public function __construct() {
		parent::__construct();
		$this->load->database();	
	}
	
	public function getById($companyId, $id) {
		$companyId = (int)$companyId;
		$id = (int)$id;
		
		$query = $this->db->get_where($this->table,
			array(
				'companyId' => $companyId,
				'id' => $id
			),
			1
		);
		
		return $query->row(); 
	}
	
	public function getWasteInvoiceByVL($vendorId, $locationId) {		
		$query = $this->db->get_where($this->table,
			array(
				'vendorId' => $vendorId,
				'locationId' => $locationId
			),
			1
		);
		
		return $query->row(); 
	}
		
	public function getByDates($companyId, $start_date, $end_date, $type, $force=false) {
	    $type = ( $type == 1 ) ? 'DC' : 'STORE';
	    $columns = array(
	        'WasteInvoices.id',
	        'WasteInvoices.invoiceDate',
			'WasteInvoices.invoiceMonth',
            'WasteInvoices.invoiceYear',
	        'WasteInvoices.invoiceNumber',
	        'WasteInvoices.vendorId',
	        'WasteInvoices.vendorName',
	        'WasteInvoices.locationId',
	        'WasteInvoices.locationType',
	        'WasteInvoices.locationName',
	        'WasteInvoices.dateSent',
	        'WasteInvoices.internalNotes',
	        'WasteInvoices.lastUpdated',
	        'WasteInvoices.status',
	        'WasteInvoices.companyId',
	        'WasteInvoices.total',
	        'Vendors.remitTo',
	        'Vendors.number AS vendor_number',
	        'Stores.location AS store_location',
            'Stores.stateId',
	        'DistributionCenters.name AS dc_location',
	        'DistributionCenters.number AS dc_number',
	    );

	    $this->db->select($columns);
	    $this->db->from($this->table);
	    $this->db->join('Vendors', 'Vendors.id=WasteInvoices.vendorId', 'left');
	    $this->db->join('Stores', 'Stores.id=WasteInvoices.locationId AND WasteInvoices.locationType="STORE"', 'left');
	    $this->db->join('DistributionCenters', 'DistributionCenters.id = WasteInvoices.locationId AND WasteInvoices.locationType="DC"', 'left');
	    $this->db->where('WasteInvoices.companyId', $companyId);
	    
	    $this->db->where('WasteInvoices.invoiceDate >=', $start_date);
	    $this->db->where('WasteInvoices.invoiceDate <=', $end_date);
		$this->db->where('WasteInvoices.locationType =', $type);
        $this->db->where('Vendors.cityVendor =', 0);
        if(!$force) {
            $this->db->where('WasteInvoices.status', 'NO');
        }

        $query = $this->db->get();
		$results = array();
        foreach ( $query->result('array') as $row ) {
            $results[$row['id']] = $row;
        }
        return $results;
	}


    public function getByDatesForExpenses($companyId, $start_date, $end_date) {
        $columns = array(
            'WasteInvoices.id',
            'WasteInvoices.invoiceDate',
            'WasteInvoices.invoiceMonth',
            'WasteInvoices.invoiceNumber',
            'DATE_FORMAT(WasteInvoices.dateSent, "%Y/%m") AS monthProcessed',
            'WasteInvoices.vendorId',
            'WasteInvoices.vendorName',
            'WasteInvoices.locationId',
            'WasteInvoices.locationType',
            'WasteInvoices.locationName',
            'WasteInvoices.dateSent',
            'WasteInvoices.internalNotes',
            'WasteInvoices.lastUpdated',
            'WasteInvoices.status',
            'WasteInvoices.companyId',
            'WasteInvoices.total',
            'Vendors.remitTo',
            'Vendors.number AS vendor_number',
            'Stores.location AS store_location',
            'Stores.stateId',
        );

        $this->db->select($columns);
        $this->db->from($this->table);
        $this->db->join('Vendors', 'Vendors.id=WasteInvoices.vendorId', 'left');
        $this->db->join('Stores', 'Stores.id=WasteInvoices.locationId AND WasteInvoices.locationType="STORE"', 'left');
        $this->db->where('WasteInvoices.companyId', $companyId);

        $this->db->where('WasteInvoices.dateSent >=', $start_date);
        $this->db->where('WasteInvoices.dateSent <=', $end_date);
        $this->db->where('WasteInvoices.locationType =', 'STORE');
        $this->db->where('WasteInvoices.status', 'YES');
 	    $this->db->where('Stores.officeLocation', '0');

        $this->db->order_by('WasteInvoices.dateSent', 'ASC');

        $query = $this->db->get();
        $results = array();
        foreach ( $query->result('array') as $row ) {
            $results[$row['id']] = $row;
        }

        return $results;
    }

    public function getStoreCount($monthProcessed, $stateId) {
        $query = $this->db->query('SELECT COUNT(*) AS cr FROM `Stores` WHERE `stateId` = "'.$stateId.'"
        AND
        (DATE_FORMAT(`startDate`, "%Y/%m") <= "'.$monthProcessed.'" OR `startDate` IS NULL OR `startDate`= "0000-00-00 00:00:00")
        AND
        (DATE_FORMAT(`endDate`, "%Y/%m") >= "'.$monthProcessed.'" OR `endDate` IS NULL OR `endDate`= "0000-00-00 00:00:00")');

        $r = $query->result();

        return count($r)>0 ? $r[0]->cr : 0;
    }
	
	public function getList($companyId, $start, $length, $searchToken = null, $orderColumn = null, $orderDir = null) {
		$companyId = (int)$companyId;

		$this->db->select('SQL_CALC_FOUND_ROWS '.$this->table.'.*'/*, wis.trashFee '*/, false);
		
		
		//$this->db->join('WasteInvoiceServices as wis',"wis.invoiceId = {$this->table}.id", 'left');
		
		$this->db->select("(
			SELECT SUM(wis.rate) FROM WasteInvoiceServices as wis
			WHERE
				wis.invoiceId = {$this->table}.id
		) as trashFee");
		
		$this->db->select("(
			SELECT SUM(wif.feeAmount) FROM WasteInvoiceFees as wif
			WHERE
				wif.invoiceId = {$this->table}.id AND wif.feeType != 4
		) as fee", false);
		
		$this->db->select("(
			SELECT SUM(wif.feeAmount) FROM WasteInvoiceFees as wif
			WHERE
				wif.invoiceId = {$this->table}.id AND wif.feeType = 4
		) as tax", false);
				
		$this->db->select('(SELECT GROUP_CONCAT(m.`name` SEPARATOR ";") FROM Materials m LEFT JOIN WasteInvoiceServices wis ON m.id = wis.materialId  WHERE wis.invoiceId = '.$this->table.'.id) AS material', false);
		$this->db->select('(SELECT GROUP_CONCAT(wis.`quantity` SEPARATOR ";") FROM WasteInvoiceServices wis WHERE wis.invoiceId = '.$this->table.'.id) AS quantity', false);
		
		if (!empty($searchToken)) {
			$tempToken = trim(strtoupper($searchToken));
			
			if (($tempToken == 'INCOMPLETE') || ($tempToken == 'COMPLETE')) {
				if ($tempToken == 'INCOMPLETE') {
					$tempToken = 'NO';
					$this->db->where('(('.$this->table.'.status = "") OR ('.$this->table.'.status IS NULL) OR ('.$this->table.'.status = \''.$tempToken.'\'))', null, false);
				} else {
					$tempToken = 'YES';
					$this->db->where($this->table.'.status', $tempToken);
				}
			} else {
                $this->db->or_where($this->table.'.invoiceNumber =', $searchToken);
                $this->db->or_where($this->table.'.locationName =', $searchToken);
                $this->db->or_where($this->table.'.vendorName =', $searchToken);
                $this->db->or_where($this->table.'.status =', $searchToken);
			}
		}
		

		if ($orderColumn) {
			$this->db->order_by($orderColumn, $orderDir);
		}

		$query = $this->db->get_where($this->table,
			array(
				$this->table . '.companyId' => $companyId
			),
			$length,
			$start
		);
        //echo $this->db->last_query();
		$result = $query->result();

		$this->db->select('FOUND_ROWS() as i');
		$query = $this->db->get();

		$row = $query->row();

		return array(
			'records' => $row->i,
			'data' => $result
		);
	}
	
	public function add($companyId, $data) {
            $companyId = (int)$companyId;

            $data = $this->cleanFields($data);
            $data['companyId'] = $companyId;

            $this->db->insert($this->table, $data);
            
            $id = $this->db->insert_id();

            $this->addEvent('waste_invoice', $id, 'add');

            return $id;	
	}
	
	public function update($rcId, $companyId, $data) {
            $rcId = (int)$rcId;
            $companyId = (int)$companyId;

            $data = $this->cleanFields($data);

            $this->db->update($this->table,
                $data,
                array(
                    'id' => $rcId,
                    'companyId' => $companyId,
                )
            );
            
            $this->addEvent('waste_invoice', $rcId, 'change');
	}
	
	public function Delete($dcId) {
		$dcId = (int)$dcId;
		
		$this->db->update($this->table,
			Array(
				"status"=>false
			),
			array(
				'id' => $dcId				
			)
		);
		
		if ($this->db->affected_rows() == 0) {
			$this->db->delete($this->table,
				Array(
					'id' => $dcId
				)
			);
		}
	}
	
	public function deleteById($id) {
        $id = (int)$id;

        $this->db->where('id', $id);
        $this->db->delete('WasteInvoices');
        $r = !!$this->db->affected_rows();

        $this->db->where('invoiceId', $id);
        $this->db->delete('WasteInvoiceFees');

        $this->db->where('invoiceId', $id);
        $this->db->delete('WasteInvoiceServices');
            
        $this->addEvent('waste_invoice', $id, 'delete');

        return $r;
	}
	
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
		
	public function updateToYes($rcId) {
		$rcId = (int)$rcId;

		$this->db->set('status', 'YES');
		$this->db->set('dateSent', date('Y-m-d'));
		$this->db->set('lastUpdated', 'NOW()', false);
		$this->db->where('id', $rcId);
		$this->db->update($this->table);
	}
	
	public function isUniqueInvoice($locationId, $invoiceNumber) {
		$this->db->select('COUNT(*) AS cr');

		$query = $this->db->get_where($this->table,
			array(
				'locationId' => $locationId,
				'invoiceNumber' => $invoiceNumber
			),
			1
		);
		
		$row = $query->row();
		
		return ($row->cr == 0);
		
	}

    public function getInvoiceCount($locationId, $vendorId, $invoiceNumber){
        $this->db->select('COUNT(*) AS cr');

        $query = $this->db->get_where($this->table,
            array(
                'locationId' => $locationId,
                'vendorId' => $vendorId,
                'invoiceNumber' =>$invoiceNumber
            ),
            1
        );

        $row = $query->row();

        return $row->cr;
    }

    public function invoiceIsNotDuplicate($invoiceId, $locationId, $vendorId) {

        $this->db->select('COUNT(*) AS cr');

        $query = $this->db->get_where($this->table,
			array(
				'locationId' => $locationId,
				'vendorId' => $vendorId,
				'id !=' => $invoiceId
			),
			1
		);
		
		$row = $query->row();
		
		return ($row->cr == 0);		
	}
}