<?php
include_once dirname(__FILE__).'/../basemodel.php';

class ConstructionInvoicesModel extends BaseModel {
    private $table = 'ConstructionInvoices';

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
    }

    public function getById($id) {
        $id = (int)$id;
        $this->db->select($this->table.'.*, IF(`ConstructionInvoices`.locationType="STORE", `Stores`.location, `DistributionCenters`.name) AS locationName, Vendors.name AS vendorName', false);
        $this->db->join('DistributionCenters', 'DistributionCenters.id = ConstructionInvoices.locationId', 'left');
        $this->db->join('Stores', 'Stores.id = ConstructionInvoices.locationId', 'left');
        $this->db->join('Vendors', 'Vendors.id = ConstructionInvoices.vendorId');
        $this->db->where($this->table.'.id', $id);
        $query = $this->db->get($this->table, 1);

        return $query->row();
    }

    public function add($data) {
        $data = $this->cleanFields($data);

        $this->db->insert($this->table, $data);

        $id = $this->db->insert_id();

        $this->addEvent('construction_invoice', $id, 'add');

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

        $this->addEvent('construction_invoice', $invoiceId, 'change');
    }

    public function addFees($invoiceId, $data) {
        $invoiceId = (int)$invoiceId;
        $this->db->delete('ConstructionInvoicesFees', array('invoiceId' => $invoiceId));

        foreach ($data as $row) {
            $this->db->insert('ConstructionInvoicesFees', $row);
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

        $this->db->delete('ConstructionInvoicesFees', array('invoiceId' => $invoiceId));
        $r = $this->db->delete($this->table, array('id' => $invoiceId));

        $this->addEvent('construction_invoice', $invoiceId, 'delete');

        return $r;
    }

    public function getFees($invoiceId) {
        $invoiceId = (int)$invoiceId;

        $this->db->select('*', false);
        $query = $this->db->get_where('ConstructionInvoicesFees',
            array(
                'invoiceId' => $invoiceId
            )
        );

        return $query->result();
    }

    public function getHaulerInvoiceSubmissionReport($dateFrom, $dateTo) {
        $this->db->select('ConstructionInvoices.*, IF(`ConstructionInvoices`.locationType="STORE", `Stores`.location, `DistributionCenters`.name) AS locationName, Vendors.number AS vendorNumber, Vendors.name AS vendorName', false);
        $this->db->from('ConstructionInvoices');
        $this->db->join('DistributionCenters', 'DistributionCenters.id = ConstructionInvoices.locationId', 'left');
        $this->db->join('Stores', 'Stores.id = ConstructionInvoices.locationId', 'left');
        $this->db->join('Vendors', 'Vendors.id = ConstructionInvoices.vendorId');
        $this->db->where('ConstructionInvoices.status', 'NO');
        $this->db->where('ConstructionInvoices.invoiceDate BETWEEN "'.$dateFrom.'" AND "'.$dateTo.'" ', NULL, FALSE);
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
        $this->db->select('SQL_CALC_FOUND_ROWS '.$this->table.'.*, IF(`ConstructionInvoices`.locationType="STORE", `Stores`.location, `DistributionCenters`.name) AS locationName, Vendors.name AS vendorName', false);
        $this->db->select('(SELECT GROUP_CONCAT(cif.`feeType` SEPARATOR \';\') FROM ConstructionInvoicesFees cif WHERE cif.invoiceId = '.$this->table.'.id) AS materialCharges', false);

        $this->db->join('DistributionCenters', 'DistributionCenters.id = ConstructionInvoices.locationId', 'left');
        $this->db->join('Stores', 'Stores.id = ConstructionInvoices.locationId', 'left');
        $this->db->join('Vendors', 'Vendors.id = ConstructionInvoices.vendorId');

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
                        'IF(`ConstructionInvoices`.locationType="STORE", `Stores`.location, `DistributionCenters`.name)' => $searchToken,
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

        $result = $query->result();

        $this->db->select('FOUND_ROWS() as i');
        $query = $this->db->get();

        $row = $query->row();

        return array(
            'records' => $row->i,
            'data' => $result
        );
    }

    public function invoiceIsNotDuplicate($invoiceId, $locationId, $locationType, $vendorId, $monthlyServicePeriodM, $monthlyServicePeriodY) {
        $this->db->select('COUNT(*) AS cr');

        $query = $this->db->get_where($this->table,
            array(
                'locationId' => $locationId,
                'locationType' => $locationType,
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