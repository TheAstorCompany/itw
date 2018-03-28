<?php
include_once dirname(__FILE__).'/../basemodel.php';

class RecyclingInvoicesModel extends BaseModel {
	private $table = 'RecyclingInvoices';
	
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
	
	public function getList($companyId, $start, $length, $searchFilter = null, $orderColumn = null, $orderDir = null) {
		$companyId = (int)$companyId;
		
		$searchToken = null;
		if(is_array($searchFilter)) {
		    $searchToken = $searchFilter['searchToken'];
		} elseif ($searchFilter!=null) {
		    $searchToken = $searchFilter;		    
		}	    

		$this->db->select('SQL_CALC_FOUND_ROWS ri.*, IF(locationType = "DC", (SELECT name FROM DistributionCenters WHERE id = locationId), (SELECT location FROM Stores WHERE id = locationId) ) as location', false);
		$this->db->select('v.name AS vendor');
        $this->db->select('m.name AS material');
        $this->db->select('rim.quantity AS quantity');

		//$this->db->select('(SELECT GROUP_CONCAT(m.`name` SEPARATOR ";") FROM Materials m LEFT JOIN RecyclingInvoicesMaterials rim ON m.id = rim.materialId  WHERE rim.invoiceId = '.$this->table.'.id)  AS material', false);
		//$this->db->select('(SELECT GROUP_CONCAT(rim.`quantity` SEPARATOR ";") FROM RecyclingInvoicesMaterials rim WHERE  rim.invoiceId = '.$this->table.'.id)  AS quantity', false);

		//$this->db->select('(SELECT SUM(rim.`quantity` * (rim.`pricePOUnit` - rim.`pricePerUnit`)) FROM RecyclingInvoicesMaterials rim WHERE  rim.invoiceId = '.$this->table.'.id)  AS total_charge1', false);
		//$this->db->select('(SELECT SUM(rif.`feeAmount`) FROM RecyclingInvoicesFees rif WHERE rif.invoiceId = '.$this->table.'.id AND rif.feeType != 13) AS total_charge2', false);
		//$this->db->select('(SELECT SUM(rif.`feeAmount`) FROM RecyclingInvoicesFees rif WHERE rif.invoiceId = '.$this->table.'.id AND rif.feeType = 13) AS total_charge3', false);

        $this->db->select('rim.`pricePOUnit`*rim.`quantity` AS poPriceUnit', false);
        $this->db->select('rim.`pricePerUnit`*rim.`quantity` AS invoicePriceUnit', false);
        $this->db->select('rim.`pricePOUnit` AS invoicePPT', false);
        $this->db->select('rim.`pricePerUnit` AS poPPT', false);
		
		$this->db->join('Vendors v', 'vendorId = v.id', 'left');
        $this->db->join('RecyclingInvoicesMaterials rim', 'ri.id = rim.invoiceId', 'left');
        $this->db->join('Materials m', 'rim.materialId = m.id', 'left');

		if(is_array($searchFilter)) {
		    if(!empty($searchFilter['invoiceDateStart'])) {
			    $this->db->where('ri.invoiceDate >=', $searchFilter['invoiceDateStart']);
		    }
		    if(!empty($searchFilter['invoiceDateEnd'])) {
			    $this->db->where('ri.invoiceDate <=', $searchFilter['invoiceDateEnd']);
		    }
		    if(!empty($searchFilter['distributionCenterId'])) {
			    $this->db->where('ri.locationId =', $searchFilter['distributionCenterId']);
		    }
            if(!empty($searchFilter['status'])) {
                $this->db->where('ri.status =', $searchFilter['status']);
            }
		}
		if (!empty($searchToken)) {
			$tempToken = trim(strtoupper($searchToken));
			
			if (($tempToken == 'INCOMPLETE') || ($tempToken == 'COMPLETE')) {
				if ($tempToken == 'INCOMPLETE') {
					$tempToken = 'NO';
					$this->db->where('((ri.status = "") OR (ri.status IS NULL) OR (ri.status = \''.$tempToken.'\'))', null, false);
				} else {
					$tempToken = 'YES';
					$this->db->where('ri.status', $tempToken);
				}
			} else {
				$this->db->or_like(
					array(
						'ri.invoiceDate' => $searchToken,
						'ri.poNumber' => $searchToken,
						'ri.invoiceDate' => $searchToken,
						'ri.dateSent' => $searchToken,
						'IF(locationType = "DC", (SELECT name FROM DistributionCenters WHERE id = locationId), (SELECT location FROM Stores WHERE id = locationId) )' => $searchToken,
						'v.name' => $searchToken,
						'ri.status' => $searchToken,
						'm.name' => $searchToken,
					)			
				);
			}
		}
		

		if ($orderColumn) {
			$this->db->order_by($orderColumn, $orderDir);
		}

		$query = $this->db->get_where('RecyclingInvoices ri',
			array(
				'ri.companyId' => $companyId
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
                
                $this->addEvent('recycling_invoice', $id, 'add');
		
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
                
                $this->addEvent('recycling_invoice', $rcId, 'change');
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

        $this->db->where('invoiceId', $id);
        $this->db->delete('RecyclingInvoicesFees');

        $this->db->where('invoiceId', $id);
        $this->db->delete('RecyclingInvoicesInfo');

        $this->db->where('invoiceId', $id);
        $this->db->delete('RecyclingInvoicesMaterials');

        $this->db->where('id', $id);
        $this->db->delete('RecyclingInvoices');
        $r = !!$this->db->affected_rows();
            
        $this->addEvent('recycling_invoice', $id, 'delete');
            
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
	
	public function addMaterialUnit($invoiceID, $materialID, $unitID) {
		$this->db->query(sprintf("INSERT IGNORE INTO RecyclingInvoiceAddedMaterials (invoiceId, materialId, unitId)
											VALUES(%d, %d, %d)",
									$invoiceID, $materialID, $unitID));
	}
	
	public function getMarketRates($materialId, $distributionCenterId, $invoiceDate) {	
		$this->db->where('startDate <=', $invoiceDate);
		$this->db->where('stopDate >=', $invoiceDate);
		$query = $this->db->get_where("MarketRate",
			array(
                            'materialId' => $materialId,
                            'distributionCenterId' => $distributionCenterId
			),
			1
		);		
		return $query->row();
	}
	
	public function getMaterialUnit($materialId) {	
		$query = $this->db->get_where("Materials",
			array(
				'id' => $materialId
			),
			1
		);		
		return $query->row();
	}
	
	public function getAllSavedMaterials($invoiceID) {
		$temp = $this->db->query("SELECT riam.id, m.name FROM RecyclingInvoiceAddedMaterials AS riam
    									LEFT OUTER JOIN Materials m ON m.id = riam.materialId
											WHERE riam.invoiceId = " . (int)$invoiceID);

		$result = array();
		
		foreach ($temp->result() as $item) {
			$result[$item->id] = $item->name;
		}
		
		return $result;
	}

    public function isUniqueReleaseNumber($poNumber, $invoiceId) {
        $this->db->select('COUNT(*) AS cr');

        $query = $this->db->get_where('RecyclingInvoices',
            array(
                'id !=' => $invoiceId,
                'poNumber =' => $poNumber
            )
        );

        $row = $query->row();

        return ($row->cr==0);
    }
	
	public function saveInvoiceFee($id, $data) {
		$this->load->helper('dates');
		$this->db->query("DELETE FROM RecyclingInvoicesMaterials WHERE invoiceId = " . (int)$id);
		
		$total = 0;// materials + fees   (deto ne sa waived)
		$totalFees = 0; // fees bez waived
		$totalWaivedFees = 0; // samo waived fees
		
		
		if(isset($data["materials"])) {
			foreach($data["materials"] as $temp) {
				$total += $temp["quantity"]* $temp["pricePerUnit"];
				$materials = $this->db->query("SELECT * FROM RecyclingInvoiceAddedMaterials WHERE id = " . $temp["materialId"]);
				$materialData = $materials->result("array");
				
				$this->db->query(sprintf("INSERT INTO RecyclingInvoicesMaterials 
														(invoiceId, invoiceNumber, invoiceDate, quantity, unit,
															pricePerUnit, materialId) VALUES (%d, \"%s\", \"%s\",
															%d, %d, %f, %d)",
											$id, $temp["release"], USToSQLDate($temp["date"]), 
											$temp["quantity"],
											$materialData[0]["unitId"], 
											$temp["pricePerUnit"], $materialData[0]["materialId"]));
			}
		}
			
		$this->db->query("DELETE FROM RecyclingInvoicesFees WHERE invoiceId = " . (int)$id);
		
		if(isset($data["fees"])) {
			foreach($data["fees"] as $temp) {
				
				if($temp["waived"]) {
					$totalWaivedFees += $temp["fee"];
				} else {
					$total += $temp["fee"];
					$totalFees += $temp["fee"];
				}
				
				
				$this->db->query(sprintf("INSERT INTO RecyclingInvoicesFees
						(invoiceId, feeType, feeAmount, waived) 
						VALUES (%d, \"%s\", %f, %d)",
						$id, 
						$temp["feeType"],
						$temp["fee"],
						$temp["waived"]));
			}
		}

		$this->db->query(sprintf("UPDATE RecyclingInvoices SET total = %f, totalFees = %f, totalWaivedFees = %f WHERE id = %d",
						$total, $totalFees, $totalWaivedFees, $id));
		
	}
	
	public function saveOrderFee($id, $data) {
		$this->load->helper('dates');
		$this->db->query("DELETE FROM RecyclingPurchaseOrder WHERE invoiceId = " . (int)$id);
		
		if(isset($data["materials"])) {
			foreach($data["materials"] as $temp) {
				$materials = $this->db->query("SELECT * FROM RecyclingInvoiceAddedMaterials WHERE id = " . $temp["materialId"]);
				$materialData = $materials->result("array");
					
				$this->db->query(sprintf("INSERT INTO RecyclingPurchaseOrder
						(invoiceId, PONumber, PODate, materialId, unitId,
						pricePerUnit, total, quantity) VALUES (%d, \"%s\", \"%s\",
						%d, %d, %f, %d, %d)",
						
						$id, $temp["release"], USToSQLDate($temp["date"]),
						$materialData[0]["materialId"], $materialData[0]["unitId"],
						$temp["pricePerUnit"], 0, $temp["quantity"]));
			}
		}
	}
	
	public function saveNotes($id, $status, $date, $note) {
		$this->load->helper('dates');
		$this->db->query(sprintf("UPDATE RecyclingInvoices SET dateSent=\"%s\", internalNotes=\"%s\", status=\"%s\" WHERE id = %d",
				USToSQLDate($date), addslashes($note), $status, $id));
	}
	
	public function getInvoiceFeeFromDB($id) {
		$this->load->helper('dates');
		$returnData = array();
		$returnData["allPrice"] = 0;

		$temp = $this->db->query("SELECT rim.*, riam.id AS RIAM FROM RecyclingInvoicesMaterials AS rim
    									LEFT OUTER JOIN RecyclingInvoiceAddedMaterials AS riam ON 
                        					rim.materialId = riam.materialId AND
                        					rim.unit = riam.unitId AND
                        					rim.invoiceId = riam.invoiceId
										WHERE rim.invoiceId = " . $id);
		
		
		foreach($temp->result("array") as $tmp) {
			$returnData["allPrice"] += ($tmp["quantity"]*$tmp["pricePerUnit"]);
			$returnData["materials"][] = array("release"=> $tmp["invoiceNumber"],
											 "date"=> SQLToUSDate($tmp["invoiceDate"]),
											 "materialId"=> $tmp["RIAM"],
											 "quantity"=> $tmp["quantity"],
											 "pricePerUnit" => $tmp["pricePerUnit"]);
		}
		
		
		$temp = $this->db->query("SELECT * FROM RecyclingInvoicesFees WHERE invoiceId = " . $id);
		
		foreach($temp->result("array") as $tmp) {
			if(! $tmp["waived"]) {
				$returnData["allPrice"] += $tmp["feeAmount"];
			}
			$returnData["fees"][] = array(	"release"=> $tmp["invoiceId"],
											"date"=> "",
											"fee"=> $tmp["feeAmount"],
											"feeType"=> $tmp["feeType"],
											"waived" => $tmp["waived"]);
		}
		return $returnData;
	}
	
	public function getOrderFeeFromDB($id) {
		$this->load->helper('dates');
		$returnData = array();
		$returnData["allPrice"] = 0;
	
		
		$temp = $this->db->query("SELECT rim.*, riam.id AS RIAM FROM RecyclingPurchaseOrder AS rim
    										LEFT OUTER JOIN RecyclingInvoiceAddedMaterials AS riam ON 
						       					rim.materialId = riam.materialId AND
						       					rim.unitId = riam.unitId AND
						       					rim.invoiceId = riam.invoiceId
											WHERE rim.invoiceId = " . $id);
		
		foreach($temp->result("array") as $tmp) {
			$returnData["allPrice"] += $tmp["pricePerUnit"];
		
			$returnData["materials"][] = array(
										"release"=> $tmp["invoiceId"],
										"date"=> SQLToUSDate($tmp["PODate"]),
										"materialId"=> $tmp["RIAM"],
										"pricePerUnit" => $tmp["pricePerUnit"],
										"quantity" => $tmp["quantity"]);
		}
		return $returnData;
	}
}