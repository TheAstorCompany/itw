<?php
include_once dirname(__FILE__).'/../basemodel.php';

class LampRequestsModel extends BaseModel {
    private $table = 'LampRequests';

    public function __construct() {
        parent::__construct();
        $this->load->database();
    }

    public function getItemOptions() {
        $fees = array();

        $this->db->select('*');
        $this->db->from('Materials');
        $this->db->where('isLamp', 1);
        $query = $this->db->get();
        $rows = $query->result();

        foreach($rows as $row) {
            $fees[$row->id] = $row->name;
        }

        return $fees;
    }

    public function getStatusOptions() {
        return array('Pending' => 'Pending', 'PickUp'=>'Pick Up', 'SentToEverlights' => 'Sent To Everlights', 'Complete' => 'Complete');
    }

    public function getById($id) {
        $id = (int)$id;
        $this->db->select($this->table.'.*, IF(`LampRequests`.locationType="STORE", `Stores`.location, `DistributionCenters`.name) AS locationName', false);
        $this->db->join('DistributionCenters', 'DistributionCenters.id = LampRequests.locationId', 'left');
        $this->db->join('Stores', 'Stores.id = LampRequests.locationId', 'left');
        $this->db->where($this->table.'.id', $id);
        $query = $this->db->get($this->table, 1);

        return $query->row();
    }

    public function add($data) {
        $data = $this->cleanFields($data);

        $this->db->insert($this->table, $data);

        $id = $this->db->insert_id();

        $this->addEvent('lamp_request', $id, 'add');

        return $id;
    }

    public function update($requestId, $data) {
        $data = $this->cleanFields($data);

        $this->db->update($this->table,
            $data,
            array(
                'id' => $requestId
            )
        );

        $this->addEvent('lamp_request', $requestId, 'change');
    }

    public function addItems($requestId, $data) {
        $requestId = (int)$requestId;
        $this->db->delete('LampRequestsItems', array('requestId' => $requestId));

        foreach ($data as $row) {
            $this->db->insert('LampRequestsItems', $row);
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

    public function deleteById($requestId) {
        $requestId = (int)$requestId;

        $this->db->delete('LampRequestsItems', array('requestId' => $requestId));
        $r = $this->db->delete($this->table, array('id' => $requestId));

        $this->addEvent('lamp_request', $requestId, 'delete');

        return $r;
    }

    public function getItems($requestId) {
        $requestId = (int)$requestId;

        $this->db->select('*', false);
        $query = $this->db->get_where('LampRequestsItems',
            array(
                'requestId' => $requestId
            )
        );

        return $query->result();
    }

    public function getList($start, $length, $searchFilter = array(), $orderColumn = null, $orderDir = null) {
        $this->db->select('SQL_CALC_FOUND_ROWS LampRequests.*, IF(`LampRequests`.locationType="STORE", `Stores`.location, `DistributionCenters`.name) AS locationName', false);
        $this->db->select('(SELECT GROUP_CONCAT(lri.`materialId` SEPARATOR \';\') FROM LampRequestsItems lri WHERE lri.requestId = LampRequests.id ORDER BY lri.`materialId`) AS materialCharges', false);
        //$this->db->select('(SELECT GROUP_CONCAT(lri.`quantity` SEPARATOR \';\') FROM LampRequestsItems lri WHERE lri.requestId = LampRequests.id ORDER BY lri.`materialId`) AS materialChargesQuantity', false);

        $this->db->join('DistributionCenters', 'DistributionCenters.id = LampRequests.locationId', 'left');
        $this->db->join('Stores', 'Stores.id = LampRequests.locationId', 'left');

        if(isset($searchFilter['requestDateStart'])) {
            $this->db->where($this->table.'.requestDate >=', $searchFilter['requestDateStart']);
        }
        if(isset($searchFilter['requestDateEnd'])) {
            $this->db->where($this->table.'.requestDate <=', $searchFilter['requestDateEnd']);
        }
        if(isset($searchFilter['status'])) {
            $this->db->where($this->table.'.status', $searchFilter['status']);
        }
        if(!empty($searchFilter['searchToken'])) {
            $searchToken = trim($searchFilter['searchToken']);
            $this->db->where($this->table.'.id LIKE "%'.$searchToken.'%"
            OR `Stores`.location LIKE "%'.$searchToken.'%"
            OR '.$this->table.'.invoiceDate LIKE "%'.$searchToken.'%"
            OR invoiceNumber LIKE "%'.$searchToken.'%"
            OR '.$this->table.'.cbreNumber LIKE "%'.$searchToken.'%"');
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

    public function getPendingList() {
        $result = $this->db->query('SELECT lr.id AS requestId, s.* FROM LampRequests lr LEFT JOIN Stores s ON lr.locationId = s.id WHERE lr.locationType = "STORE" AND lr.status = "Pending"');
        return $result->result();
    }

    public function getLRI() {
        $query = $this->db->query('SELECT
              requestId,
              materialId,
              SUM(lampQuantity) AS lampQuantity,
              SUM(boxQuantity) AS boxQuantity
              FROM LampRequestsItems
            GROUP BY requestId, materialId');

        $rows = $query->result();
        $result = array();
        foreach($rows as $row) {
            if(!array_key_exists($row->requestId, $result)) {
                $result[$row->requestId] = array();
            }
            $result[$row->requestId][$row->materialId] = array('lampQuantity'=>$row->lampQuantity, 'boxQuantity'=>$row->boxQuantity);
        }

        return $result;
    }

    public function setStatusForRequest($requestId, $status) {
        $this->db->update($this->table, array('status' => $status), array('id' => $requestId));
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