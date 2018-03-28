<?php
include_once dirname(__FILE__).'/../basemodel.php';

class DistributionCenterInvoicesModel extends BaseModel {
	private $table = 'DistributionCenterInvoices';

	public function __construct() {
		parent::__construct();
		$this->load->database();
	}
	
	public function getFeesTypes() {
        $fees = array();

        $this->db->select('*');
        $this->db->from('MaterialFeeType');
        $query = $this->db->get();
        $rows = $query->result();

        foreach($rows as $row) {
            $fees[$row->id] = $row->name;
        }

        return $fees;
        /*return array(
            '1' => 'Trash Rate',
            '2' => 'Rolloff Trash Haul',
            '3' => 'Rolloff Trash Disposal Per Ton',
            '4' => 'Rec Rate',
            '5' => 'Temp Roll Off Haul',
            '6' => 'Temp Rolloff Disposal',
            '7' => 'Extra/Bulk P/U',
            '8' => 'Rental',
            '9' => 'Delivery',
            '10' => 'Lock',
            '11' => 'Fuel',
            '12' => 'Other',
            '13' => 'Tax',
            '14' => 'Finance Fee'
        );*/
	}
	
	public function getById($id) {
		$id = (int)$id;
		$this->db->select($this->table.'.*, DistributionCenters.name AS dcName, Vendors.name AS vendorName', false);
		
		$this->db->join('DistributionCenters', 'DistributionCenters.id = DistributionCenterInvoices.distributionCenterId');
		$this->db->join('Vendors', 'Vendors.id = DistributionCenterInvoices.vendorId');
		$this->db->where($this->table.'.id', $id);
		$query = $this->db->get($this->table, 1);
		
		return $query->row();
	}
	
	public function add($data) {
            $data = $this->cleanFields($data);
		
            $this->db->insert($this->table, $data);
            
            $id = $this->db->insert_id();
            
            $this->addEvent('dc_invoice', $id, 'add');
		
            return $id;	
	}	
	
	public function update($invoiceId, $data) {
            $data = $this->cleanFields($data);

            $this->db->update($this->table,
                $data,
                array(
                    'id' => $invoiceId
                )
            );

            $this->addEvent('dc_invoice', $invoiceId, 'change');
	}	
	
	public function addFees($invoiceId, $data) {
	    $invoiceId = (int)$invoiceId;
	    $this->db->delete('DistributionCenterInvoicesFees', array('invoiceId' => $invoiceId));

	    foreach ($data as $row) {
		    $this->db->insert('DistributionCenterInvoicesFees', $row);
	    }	
	}	
	
	public function delete($contactId) {
	    $contactId = (int)$contactId;

	    $this->db->delete($this->table,
		    Array(
			    'id' => $contactId
		    )
	    );		
	}

    public function deleteById($invoiceId) {
        $invoiceId = (int)$invoiceId;

        $this->db->delete('DistributionCenterInvoicesFees', array('invoiceId' => $invoiceId));
        $r = $this->db->delete($this->table, array('id' => $invoiceId));

        return $r;
    }

	public function getFees($distributionCenterId) {
	    $distributionCenterId = (int)$distributionCenterId;

	    $this->db->select('*', false);
	    $query = $this->db->get_where('DistributionCenterInvoicesFees',
		    array(
			    'invoiceId' => $distributionCenterId
		    )	
	    );

	    return $query->result();
	}
	
	public function getHaulerInvoiceSubmissionReport($dateFrom, $dateTo) {
		$this->db->select('DistributionCenterInvoices.*, DistributionCenters.number AS distributioncenterNumber, DistributionCenters.accountNumber AS accountNumber, Vendors.number AS vendorNumber, Vendors.name AS vendorName', false);
		$this->db->from('DistributionCenterInvoices');
		$this->db->join('DistributionCenters', 'DistributionCenters.id = DistributionCenterInvoices.distributionCenterId');
		$this->db->join('Vendors', 'Vendors.id = DistributionCenterInvoices.vendorId');		
		$this->db->where('DistributionCenterInvoices.status', 'NO');
		$this->db->where('DistributionCenterInvoices.invoiceDate BETWEEN "'.$dateFrom.'" AND "'.$dateTo.'" ', NULL, FALSE);
		$query = $this->db->get();
		$invoices = $query->result();
		
		foreach($invoices as &$invoice) {
			$invoice->fees = array();
			$this->db->select('*', false);
			$query = $this->db->get_where('DistributionCenterInvoicesFees', array('invoiceId' => $invoice->id));
			$fees = $query->result();			
			foreach($fees as $fee) {
				if(!isset($invoice->fees[$fee->feeType])) {
					$invoice->fees[$fee->feeType] = 0.0;
				}
				$invoice->fees[$fee->feeType] += floatval($fee->amount);
			}
			
			$this->db->where(array('id' => $invoice->id));
			$this->db->update('DistributionCenterInvoices', array('status' => 'YES', 'dateSent'=>date('Y-m-d')));
		}
		
		return $invoices;	
	}
	
	public function getList($start, $length, $searchToken = null, $orderColumn = null, $orderDir = null) {
		$this->db->select('SQL_CALC_FOUND_ROWS '.$this->table.'.*, DistributionCenters.name AS dcName, Vendors.name AS vendorName', false);
		$this->db->select('(SELECT GROUP_CONCAT(dcif.`feeType` SEPARATOR \';\') FROM DistributionCenterInvoicesFees dcif WHERE dcif.invoiceId = '.$this->table.'.id) AS materialCharges', false);
		
		$this->db->join('DistributionCenters', 'DistributionCenters.id = DistributionCenterInvoices.distributionCenterId');
		$this->db->join('Vendors', 'Vendors.id = DistributionCenterInvoices.vendorId');	
		
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
						$this->table.'.id' => $searchToken,
						$this->table.'.invoiceDate' => $searchToken,
						'DistributionCenters.name' => $searchToken,
                        'haulerInvNumber' => $searchToken,
						'Vendors.name' => $searchToken,
						$this->table.'.status' => $searchToken
					)			
				);
			}
		}
		
		if ($orderColumn) {
			$this->db->order_by($orderColumn, $orderDir);
		}

		$query = $this->db->get_where($this->table,
			array(
				$this->table . '.id >' => 0
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

    public function getInvoiceCount($distributionCenterId, $vendorId, $haulerInvNumber){
        $this->db->select('COUNT(*) AS cr');

        $query = $this->db->get_where($this->table,
            array(
                'distributionCenterId' => $distributionCenterId,
                'vendorId' => $vendorId,
                'haulerInvNumber' => $haulerInvNumber
            ),
            1
        );

        $row = $query->row();

        return $row->cr;
    }
	
	public function invoiceIsNotDuplicate($invoiceId, $distributionCenterId, $vendorId, $monthlyServicePeriodM, $monthlyServicePeriodY) {
		$this->db->select('COUNT(*) AS cr');

		$query = $this->db->get_where($this->table,
			array(
				'distributionCenterId' => $distributionCenterId,
				'vendorId' => $vendorId,
				'monthlyServicePeriodM' => $monthlyServicePeriodM,
				'monthlyServicePeriodY' => $monthlyServicePeriodY,
				'id !=' => $invoiceId
			),
			1
		);
		
		$row = $query->row();
		
		return ($row->cr == 0);		
	}	
	
	private function cleanFields($data) {
		$tableFields = $this->db->list_fields($this->table);
		$result = array();
		if(is_array($data)) {
			foreach ($tableFields as $field) {
				if (array_key_exists($field, $data)) {
					$result[$field] = $data[$field];
				}
			}
		}
		return $result;
	}
	
}
