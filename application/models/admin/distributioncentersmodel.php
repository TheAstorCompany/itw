<?php
include_once dirname(__FILE__).'/../basemodel.php';

class DistributionCentersModel extends BaseModel {
	private $table = 'DistributionCenters';
	
	public function __construct() {
		parent::__construct();
		$this->load->database();	
	}
	
	public function getById($companyId, $dcId) {
		$companyId = (int)$companyId;
		$dcId = (int)$dcId;
		
		$query = $this->db->get_where($this->table,
			array(
				'companyId' => $companyId,
				'id' => $dcId
			),
			1
		);
		
		return $query->row(); 
	}
	
	public function getList($companyId, $start, $length, $searchToken = null, $orderColumn = null, $orderDir = null) {
		$companyId = (int)$companyId;
		
		$currentYear = date('Y');
		$currentMonth = date('m');

		$this->db->select('SQL_CALC_FOUND_ROWS '.$this->table.'.*', false);
		$this->db->select('States.name as state');
		$this->db->join('States', 'stateId = States.id', 'left');
		//$this->db->join('WasteInvoices as wi', 'wi.locationType = \'DC\' AND wi.locationId = '.$this->table.'.id AND wi.invoiceDate BETWEEN '.$currentYear.'-'.$currentMonth.'-01 AND '.$currentYear.'-'.$currentMonth.'-31', 'left', true);
		$this->db->select("(
			SELECT SUM(wis.quantity) FROM WasteInvoiceServices as wis
			WHERE wis.invoiceId in (
				SELECT wi.id FROM WasteInvoices as wi
				WHERE 
					wi.locationType = 'DC' AND 
					wi.locationId = {$this->table}.id AND 
					wi.invoiceDate BETWEEN '$currentYear-$currentMonth-01' AND '$currentYear-$currentMonth-31'
			)
		) as waste", false);
		
		$this->db->select("(
			SELECT SUM(rim.quantity) FROM RecyclingInvoicesMaterials as rim
			WHERE rim.invoiceId in (
				SELECT ri.id FROM RecyclingInvoices as ri
				WHERE 
					ri.locationType = 'DC' AND 
					ri.locationId = {$this->table}.id AND 
					ri.invoiceDate BETWEEN '$currentYear-$currentMonth-01' AND '$currentYear-$currentMonth-31'
			)
		) as recycling", false);
		
		if (!empty($searchToken)) {
			$this->db->or_like(
				array(
					//'district' => $searchToken,
					//'districtName' => $searchToken,
					'addressLine1' => $searchToken,
					'city' => $searchToken,
					'States.name' => $searchToken,
					'zip' => $searchToken,
					'lastUpdated' => $searchToken,
					'DistributionCenters.name' => $searchToken
				)			
			);
		}
		

		if ($orderColumn) {
			$this->db->order_by($orderColumn, $orderDir);
		}

		$query = $this->db->get_where($this->table,
			array(
				$this->table.'.companyId' => $companyId
			),
			$length,
			$start
		);

		$result = $query->result();
		//echo $this->db->last_query();
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
		$dcId = $this->db->insert_id();
		$this->addEvent('dc_setup', $dcId, 'add');
		return $dcId;
	}
	
	public function update($dcId, $companyId, $data) {
		$dcId = (int)$dcId;
		$companyId = (int)$companyId;
		
		$data = $this->cleanFields($data);
		
		$this->db->update($this->table,
			$data,
			array(
				'id' => $dcId,
				'companyId' => $companyId,
			)
		);
		$this->addEvent('dc_setup', $dcId, 'change');
	}
	
	public function Delete($dcId) {
        $dcId = (int)$dcId;

        $query = $this->db->get_where('RecyclingCharges', array('locationType' => 'DC', 'locationId' => $dcId));
        foreach($query->result() as $row) {
            $this->db->delete('RecyclingChargeItems', array('recyclingChargeId' => $row->id));
            $this->db->delete('RecyclingChargesFees', array('recyclingChargeId' => $row->id));
            $this->db->delete('RecyclingCharges', array('id' => $row->id));
        }

        $query = $this->db->get_where('RecyclingInvoices', array('locationType' => 'DC', 'locationId' => $dcId));
        foreach($query->result() as $row) {
            $this->db->delete('RecyclingInvoicesFees', array('invoiceId' => $row->id));
            $this->db->delete('RecyclingInvoicesMaterials', array('invoiceId' => $row->id));
            $this->db->delete('RecyclingInvoicesInfo', array('invoiceId' => $row->id));
            $this->db->delete('RecyclingInvoices', array('id' => $row->id));
        }

        $query = $this->db->get_where('SupportRequests', array('locationType' => 'DC', 'locationId' => $dcId));
        foreach($query->result() as $row) {
            $this->db->delete('SupportRequestTasks', array('supportRequestId' => $row->id));
            $this->db->delete('SupportRequests', array('id' => $row->id));
        }

        $query = $this->db->get_where('VendorServices', array('locationType' => 'DC', 'locationId' => $dcId));
        foreach($query->result() as $row) {
            $this->db->delete('VendorServices', array('id' => $row->id));
        }

        $query = $this->db->get_where('WasteInvoices', array('locationType' => 'DC', 'locationId' => $dcId));
        foreach($query->result() as $row) {
            $this->db->delete('WasteInvoiceFees', array('invoiceId' => $row->id));
            $this->db->delete('WasteInvoiceServices', array('invoiceId' => $row->id));
            $this->db->delete('WasteInvoices', array('id' => $row->id));
        }

        $query = $this->db->get_where('LampRequests', array('locationType' => 'DC', 'locationId' => $dcId));
        foreach($query->result() as $row) {
            $this->db->delete('LampRequestsItems', array('requestId' => $row->id));
            $this->db->delete('LampRequests', array('id' => $row->id));
        }

        $query = $this->db->get_where('ConstructionInvoices', array('locationType' => 'DC', 'locationId' => $dcId));
        foreach($query->result() as $row) {
            $this->db->delete('ConstructionInvoicesFees', array('invoiceId' => $row->id));
            $this->db->delete('ConstructionInvoices', array('id' => $row->id));
        }

        $query = $this->db->get_where('DistributionCenterInvoices', array('distributionCenterId' => $dcId));
        foreach($query->result() as $row) {
            $this->db->delete('DistributionCenterInvoicesFees', array('invoiceId' => $row->id));
            $this->db->delete('DistributionCenterInvoices', array('id' => $row->id));
        }

        $this->db->delete('DistributionCenters', array('id' => $dcId));
		$this->addEvent('dc_setup', $dcId, 'delete');
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
}