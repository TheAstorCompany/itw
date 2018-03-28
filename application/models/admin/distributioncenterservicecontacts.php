<?php

class DistributionCenterServiceContacts extends CI_Model {
    private $table = 'DistributionCenterServiceContacts';

    public function __construct() {
        parent::__construct();
        $this->load->database();
    }

    public function getById($id) {
        $id = (int)$id;
        $this->db->select();

        $query = $this->db->get_where($this->table, array("{$this->table}.id" => $id), 1);

        return $query->row();
    }

    public function add($distributionCenterId, $data) {
        $distributionCenterId = (int)$distributionCenterId;

        $data = $this->cleanFields($data);
        $data['distributionCenterId'] = (int)$distributionCenterId;

        $this->db->insert($this->table, $data);

        return $this->db->insert_id();
    }

    public function update($data) {
        $contactId = intval($data['serviceContactId']);
        $data = $this->cleanFields($data);

        $this->db->update($this->table, $data, array('id'=>$contactId));
    }

    public function delete($contactId) {
        $contactId = (int)$contactId;

        $this->db->delete($this->table,
            Array(
                'id' => $contactId
            )
        );
    }

    public function getList($distributionCenterId) {
        $distributionCenterId = (int)$distributionCenterId;

        $this->db->select('*', false);
        $query = $this->db->get_where($this->table,
            array(
                'distributionCenterId' => $distributionCenterId
            )
        );

        return $query->result();
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