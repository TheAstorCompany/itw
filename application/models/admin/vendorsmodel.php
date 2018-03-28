<?php
include_once dirname(__FILE__).'/../basemodel.php';

class VendorsModel extends BaseModel {
	private $table = 'Vendors';

	public function __construct() {
		parent::__construct();
		$this->load->database();
	}

	public function __destruct() {
		//		echo $this->db->last_query();
	}
	
	public function getById($vendorId, $companyId) {
		$vendorId = (int)$vendorId;
		$companyId = (int)$companyId;
		$this->db->select($this->table . '.*');
		$this->db->select('States.name as state');
        $this->db->join('States', 'stateId = States.id', "left");
        		
		$query = $this->db->get_where($this->table,
			array(
				"{$this->table}.id" => $vendorId,
				'companyId' => $companyId,
			),
			1
		);

		return $query->row();
	}
	
	public function getVendorById($vendorId) {
		$vendorId = (int)$vendorId;
		$this->db->select($this->table . '.*');
		$this->db->select('States.name as state');
        $this->db->join('States', 'stateId = States.id', "left");
        		
		$query = $this->db->get_where($this->table, array("{$this->table}.id" => $vendorId), 1);

		return $query->row();
	}

    public function getVendorByNameAndAddress($vendorName, $vendorAddress) {

        $this->db->select($this->table . '.*');
        $this->db->select('States.name as state');
        $this->db->join('States', 'stateId = States.id', "left");

        $query = $this->db->get_where($this->table, array("{$this->table}.name" => $vendorName, "{$this->table}.addressLine1" => $vendorAddress), 1);

        return $query->row();
    }

    public function getLocation($locationId) {

        $query = $this->db->query('
            SELECT * FROM (
            SELECT s.location AS locationId, "STORE" AS locationType, status, franchise AS franchise FROM Stores s
            UNION
            SELECT dc.id AS locationId,      "DC"    AS locationType, status, 0 AS franchise FROM DistributionCenters dc) t WHERE t.locationId = '.$locationId.' LIMIT 1');

        $r = $query->result();
        return count($r)>0 ? $r[0] : null;
    }

	public function addVendor($companyId, $data) {
		$data = $this->cleanFields($data);
		$data['companyId'] = (int)$companyId;		
		$this->db->insert($this->table, $data);
		$vendorId = $this->db->insert_id();
		$this->addEvent('vendor_setup', $vendorId, 'add');
		return $vendorId;
	}

	public function updateVendor($vendorId, $companyId, $data) {
		$data = $this->cleanFields($data);
		$vendorId = (int)$vendorId;
		$companyId = (int)$companyId;
		$this->db->update($this->table, $data,
			array(
					'id' => $vendorId,
					'companyId' => $companyId,
			)
		);
		$this->addEvent('vendor_setup', $vendorId, 'change');
	}

	public function Delete($vendorId) {
        $vendorId = (int)$vendorId;

        $query = $this->db->get_where('ConstructionInvoices', array('vendorId' => $vendorId));
        foreach($query->result() as $row) {
            $this->db->delete('ConstructionInvoicesFees', array('invoiceId' => $row->id));
            $this->db->delete('ConstructionInvoices', array('id' => $row->id));
        }

        $query = $this->db->get_where('DistributionCenterInvoices', array('vendorId' => $vendorId));
        foreach($query->result() as $row) {
            $this->db->delete('DistributionCenterInvoicesFees', array('invoiceId' => $row->id));
            $this->db->delete('DistributionCenterInvoices', array('id' => $row->id));
        }

        $query = $this->db->get_where('RecyclingCharges', array('vendorId' => $vendorId));
        foreach($query->result() as $row) {
            $this->db->delete('RecyclingChargeItems', array('recyclingChargeId' => $row->id));
            $this->db->delete('RecyclingChargesFees', array('recyclingChargeId' => $row->id));
            $this->db->delete('RecyclingCharges', array('id' => $row->id));
        }

        $query = $this->db->get_where('RecyclingInvoices', array('vendorId' => $vendorId));
        foreach($query->result() as $row) {
            $this->db->delete('RecyclingInvoicesFees', array('invoiceId' => $row->id));
            $this->db->delete('RecyclingInvoicesMaterials', array('invoiceId' => $row->id));
            $this->db->delete('RecyclingInvoicesInfo', array('invoiceId' => $row->id));
            $this->db->delete('RecyclingInvoices', array('id' => $row->id));
        }

        $query = $this->db->get_where('SupportRequests', array('vendorId' => $vendorId));
        foreach($query->result() as $row) {
            $this->db->delete('SupportRequestTasks', array('supportRequestId' => $row->id));
            $this->db->delete('SupportRequests', array('id' => $row->id));
        }

        $this->db->delete('VendorContacts', array('vendorId' => $vendorId));

        $this->db->delete('VendorServices', array('vendorId' => $vendorId));

        $this->db->delete('Vendors', array('id' => $vendorId));
		$this->addEvent('vendor_setup', $vendorId, 'delete');
	}

	public function getList($companyId, $start, $length, $searchToken = null, $orderColumn = null, $orderDir = null) {
		$companyId = (int)$companyId;
		$this->db->select('SQL_CALC_FOUND_ROWS '.$this->table.'.*', false);
		$this->db->select('States.name as state');
        $this->db->join('States', 'stateId = States.id', "left");

        $searchStatus = -1;

        if (!empty($searchToken)) {
			if(is_array($searchToken)) {
                $searchStatus = intval($searchToken['searchStatus']);
                $searchToken = $searchToken['searchToken'];
            }

            $this->db->where('('.$this->table.'.id LIKE "%'.addslashes($searchToken).'%" OR
                            '.$this->table.'.name LIKE "%'.addslashes($searchToken).'%" OR
							addressLine1 LIKE "%'.addslashes($searchToken).'%" OR
							city LIKE "%'.addslashes($searchToken).'%" OR
							zip LIKE "%'.addslashes($searchToken).'%" OR
							States.name LIKE "%'.addslashes($searchToken).'%" OR
							number LIKE "%'.addslashes($searchToken).'%" OR
							lastUpdated LIKE "%'.addslashes($searchToken).'%")');
		}


		if ($orderColumn) {
			$this->db->order_by($orderColumn, $orderDir);
		}

        if($searchStatus>=0) {
            $this->db->where(array('status' => $searchStatus));
        }

		$query = $this->db->get_where($this->table,
            array(
                'companyId' => $companyId
            ),
            $length,
            $start
		);


		$result = $query->result();

		$this->db->select('FOUND_ROWS() as i');
		$query = $this->db->get();

		$row = $query->row();
		
		return array(
			'records' => $row->i,
			'data' => $result
		);
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