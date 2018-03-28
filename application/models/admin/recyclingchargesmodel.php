<?php
include_once dirname(__FILE__).'/../basemodel.php';

class RecyclingChargesModel extends BaseModel {
	private $table = 'RecyclingCharges';
	
	public function __construct() {
		parent::__construct();
		$this->load->database();	
	}
	
	public function getById($companyId, $rcId) {
		$companyId = (int)$companyId;
		$rcId = (int)$rcId;
		
		$query = $this->db->get_where($this->table,
			array(
				'companyId' => $companyId,
				'id' => $rcId
			),
			1
		);
		$result = $query->row(); 
		$this->load->model('admin/RecyclingChargesFeesModel', 'fees');
		$result->fees = $this->fees->getList($result->id);
		$this->load->model('admin/RecyclingChargeItemsModel', 'charges');
		$result->charges = $this->charges->getList($result->id);
		return $result; 
	}
	
	public function getList($companyId, $start, $length, $searchToken = null, $orderColumn = null, $orderDir = null) {
		$companyId = (int)$companyId;

		$this->db->select('SQL_CALC_FOUND_ROWS '.$this->table.'.*, IF(locationType = "DC", (SELECT name FROM DistributionCenters WHERE id = locationId), (SELECT location FROM Stores WHERE id = locationId) ) as location', false);
		$this->db->select("(
			SELECT SUM(rci.quantity * rci.pricePerTon) FROM RecyclingChargeItems as rci
			WHERE
				recyclingChargeId = RecyclingCharges.id
		) as rebate", false);
		$this->db->select("(
			SELECT SUM(rcf.fee) FROM RecyclingChargesFees as rcf
			WHERE
				recyclingChargeId = RecyclingCharges.id
			AND
				rcf.waived = 0
		) as fees", false);
		$this->db->select("(
			(
				SELECT SUM(rcf.fee) FROM RecyclingChargesFees as rcf
				WHERE
					recyclingChargeId = RecyclingCharges.id
				AND
					rcf.waived = 0
			) + (
				SELECT SUM(rci.quantity * rci.pricePerTon) FROM RecyclingChargeItems as rci
				WHERE
					recyclingChargeId = RecyclingCharges.id
			)
		) as total", false);
		$this->db->select('(SELECT GROUP_CONCAT(m.`name` SEPARATOR ";") FROM Materials m LEFT JOIN RecyclingChargeItems rcm ON m.id = rcm.materialId  WHERE rcm.recyclingChargeId = '.$this->table.'.id) AS material', false);
		$this->db->select('(SELECT GROUP_CONCAT(rcm.`quantity` SEPARATOR ";") FROM RecyclingChargeItems rcm WHERE rcm.recyclingChargeId = '.$this->table.'.id) AS quantity', false);
		$this->db->select('(SELECT SUM(rcm.`quantity` * rcm.`pricePerTon`) FROM RecyclingChargeItems rcm WHERE rcm.recyclingChargeId = '.$this->table.'.id) AS total_charge1', false);
		$this->db->select('(SELECT SUM(rcf.`fee`) FROM RecyclingChargesFees rcf WHERE rcf.recyclingChargeId = '.$this->table.'.id AND rcf.feeType != 13) AS total_charge2', false);
		$this->db->select('(SELECT SUM(rcf.`fee`) FROM RecyclingChargesFees rcf WHERE rcf.recyclingChargeId = '.$this->table.'.id AND rcf.feeType = 13) AS total_charge3', false);	
		$this->db->select('Vendors.name as vendor');
		
		$this->db->join('Vendors', 'vendorId = Vendors.id', 'left');
		
		if ($orderColumn) {
			$this->db->order_by($orderColumn, $orderDir);
		}
		
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
					$this->db->or_like(
						array(
							'invoiceNumber' => $searchToken,
							'IF(locationType = "DC", (SELECT name FROM DistributionCenters WHERE id = locationId), (SELECT location FROM Stores WHERE id = locationId) )' => $searchToken,
							'Vendors.name' => $searchToken,
							'Materials.name' => $searchToken,
							"(
								(
									SELECT SUM(rcf.fee) FROM RecyclingChargesFees as rcf
									WHERE
										recyclingChargeId = RecyclingCharges.id
									AND
										rcf.waived = 0
								) + (
									SELECT SUM(rci.quantity * rci.pricePerTon) FROM RecyclingChargeItems as rci
									WHERE
										recyclingChargeId = RecyclingCharges.id
								)
							 )" => $searchToken,
							"(
								SELECT SUM(rci.quantity * rci.pricePerTon) FROM RecyclingChargeItems as rci
								WHERE
									recyclingChargeId = RecyclingCharges.id
							 )" => $searchToken,
						)			
					);
				}
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
                
                $this->addEvent('recycling_charge', $id, 'add');
		
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
                
                $this->addEvent('recycling_charge', $rcId, 'change');
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
                
                $this->addEvent('recycling_charge', $dcId, 'delete');
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