<?php
include_once dirname(__FILE__).'/../basemodel.php';

class StoresModel extends BaseModel {
	private $table = 'Stores';
	
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
		
		$this->db->select("(
			SELECT SUM(wis.quantity) FROM WasteInvoiceServices as wis
			WHERE wis.invoiceId in (
				SELECT wi.id FROM WasteInvoices as wi
				WHERE 
					wi.locationType = 'STORE' AND 
					wi.locationId = {$this->table}.id AND 
					wi.invoiceDate BETWEEN '$currentYear-$currentMonth-01' AND '$currentYear-$currentMonth-31'
			)
		) as waste", false);
		
		$this->db->select("(
			SELECT SUM(rim.quantity) FROM RecyclingInvoicesMaterials as rim
			WHERE rim.invoiceId in (
				SELECT ri.id FROM RecyclingInvoices as ri
				WHERE 
					ri.locationType = 'STORE' AND 
					ri.locationId = {$this->table}.id AND 
					ri.invoiceDate BETWEEN '$currentYear-$currentMonth-01' AND '$currentYear-$currentMonth-31'
			)
		) as recycling", false);

		if (!empty($searchToken)) {
			if(!is_array($searchToken)) {
				$this->db->or_like(
					array(
						'district' => $searchToken,
						'districtName' => $searchToken,
						'addressLine1' => $searchToken,
						'city' => $searchToken,
						'States.name' => $searchToken,
						'postCode' => $searchToken,
						'location' => $searchToken
						//'lastUpdated' => $searchToken
					)			
				);
			} else {
				if(isset($searchToken['filter_status']) && $searchToken['filter_status']!=0) {
					if($searchToken['filter_status']==1) {
						$this->db->where('status =', '');
					} else {
						$this->db->where('status !=', '');
					}
				}
				if(isset($searchToken['filter_container']) && $searchToken['filter_container']!=0) {
					$locationIds = array(0);
					$tmp_db = $this->load->database('default', TRUE, TRUE);
					$tmp_db->select('locationId');
					$rows = $tmp_db->get_where('VendorServices', array('containerId' => $searchToken['filter_container']))->result_array();
					foreach ($rows as $row) {
						$locationIds[] = $row['locationId'];
					}
					$this->db->where_in('location', $locationIds);
				}
				if(isset($searchToken['filter_search']) && $searchToken['filter_search']!='') {
                    if($searchToken['filter_field']==1) {
                        $this->db->where('location LIKE "%'.addslashes($searchToken['filter_search']).'%"');
                    } elseif($searchToken['filter_field']==2) {
                        $this->db->where('district LIKE "%'.addslashes($searchToken['filter_search']).'%"');
                    } elseif($searchToken['filter_field']==3) {
                        $this->db->where('addressLine1 LIKE "%'.addslashes($searchToken['filter_search']).'%"');
                    } elseif($searchToken['filter_field']==4) {
                        $this->db->where('City LIKE "%'.addslashes($searchToken['filter_search']).'%"');
                    } elseif($searchToken['filter_field']==5) {
                        $this->db->where('States.name LIKE "%'.addslashes($searchToken['filter_search']).'%"');
                    } elseif($searchToken['filter_field']==6) {
                        $this->db->where('postCode LIKE "%'.addslashes($searchToken['filter_search']).'%"');
                    } elseif($searchToken['filter_field']==7) {
                        $this->db->where('phone LIKE "%'.addslashes($searchToken['filter_search']).'%"');
                    } else {
                        $this->db->where('(
                            district LIKE "%'.addslashes($searchToken['filter_search']).'%" OR
                            location LIKE "%'.addslashes($searchToken['filter_search']).'%" OR
                            addressLine1 LIKE "%'.addslashes($searchToken['filter_search']).'%" OR
                            city LIKE "%'.addslashes($searchToken['filter_search']).'%" OR
                            States.name LIKE "%'.addslashes($searchToken['filter_search']).'%" OR
                            postCode LIKE "%'.addslashes($searchToken['filter_search']).'%" OR
                            phone LIKE "%'.addslashes($searchToken['filter_search']).'%")');
                    }
                }
            }
        }
		//$this->db->where('debug', true);
		if ($orderColumn) {
			$this->db->order_by($orderColumn, $orderDir);
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
	
	public function add($companyId, $data) {
		$companyId = (int)$companyId;
		
		$data = $this->cleanFields($data);
		$data['companyId'] = $companyId;
		/*
		ALTER TABLE `Stores` ADD `county` VARCHAR( 255 ) NULL DEFAULT NULL AFTER `addressLine2`; 
		ALTER TABLE `Stores` ADD `franchise` TINYINT( 1 ) NULL DEFAULT NULL AFTER `districtName`;
		ALTER TABLE `Stores` ADD `salesRanking` VARCHAR( 10 ) NULL DEFAULT NULL AFTER `franchise`;
		*/
		/*
		 * ALTER TABLE `stores` ADD `serviceType` INT NULL DEFAULT NULL AFTER `salesRanking` 
		 */
		$this->db->insert($this->table, $data);
		$dcId = $this->db->insert_id();
		$this->addEvent('store_setup', $dcId, 'add');
		return $dcId;	
	}
	
	public function update($dcId, $companyId, $data) {
		$dcId = (int)$dcId;
		$companyId = (int)$companyId;
		
		$data = $this->cleanFields($data);
		if(empty($data["districtId"])) {
			$data["districtId"] = null;
		}
		
		$this->db->update($this->table,
			$data,
			array(
				'id' => $dcId,
				'companyId' => $companyId,
			)
		); 
		$this->addEvent('store_setup', $dcId, 'change');
	}
	
	public function Delete($dcId) {
		$dcId = (int)$dcId;

        $query = $this->db->get_where('RecyclingCharges', array('locationType' => 'STORE', 'locationId' => $dcId));
        foreach($query->result() as $row) {
            $this->db->delete('RecyclingChargeItems', array('recyclingChargeId' => $row->id));
            $this->db->delete('RecyclingChargesFees', array('recyclingChargeId' => $row->id));
            $this->db->delete('RecyclingCharges', array('id' => $row->id));
        }

        $query = $this->db->get_where('RecyclingInvoices', array('locationType' => 'STORE', 'locationId' => $dcId));
        foreach($query->result() as $row) {
            $this->db->delete('RecyclingInvoicesFees', array('invoiceId' => $row->id));
            $this->db->delete('RecyclingInvoicesMaterials', array('invoiceId' => $row->id));
            $this->db->delete('RecyclingInvoicesInfo', array('invoiceId' => $row->id));
            $this->db->delete('RecyclingInvoices', array('id' => $row->id));
        }

        $query = $this->db->get_where('SupportRequests', array('locationType' => 'STORE', 'locationId' => $dcId));
        foreach($query->result() as $row) {
            $this->db->delete('SupportRequestTasks', array('supportRequestId' => $row->id));
            $this->db->delete('SupportRequests', array('id' => $row->id));
        }

        $query = $this->db->get_where('VendorServices', array('locationType' => 'STORE', 'locationId' => $dcId));
        foreach($query->result() as $row) {
            $this->db->delete('VendorServices', array('id' => $row->id));
        }

        $query = $this->db->get_where('WasteInvoices', array('locationType' => 'STORE', 'locationId' => $dcId));
        foreach($query->result() as $row) {
            $this->db->delete('WasteInvoiceFees', array('invoiceId' => $row->id));
            $this->db->delete('WasteInvoiceServices', array('invoiceId' => $row->id));
            $this->db->delete('WasteInvoices', array('id' => $row->id));
        }

        $query = $this->db->get_where('LampRequests', array('locationType' => 'STORE', 'locationId' => $dcId));
        foreach($query->result() as $row) {
            $this->db->delete('LampRequestsItems', array('requestId' => $row->id));
            $this->db->delete('LampRequests', array('id' => $row->id));
        }

        $query = $this->db->get_where('ConstructionInvoices', array('locationType' => 'STORE', 'locationId' => $dcId));
        foreach($query->result() as $row) {
            $this->db->delete('ConstructionInvoicesFees', array('invoiceId' => $row->id));
            $this->db->delete('ConstructionInvoices', array('id' => $row->id));
        }

        $this->db->delete('Stores', array('id' => $dcId));
		$this->addEvent('store_setup', $dcId, 'delete');

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