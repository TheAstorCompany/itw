<?php

class VendorContacts extends CI_Model {
	private $table = 'VendorContacts';

	public function __construct() {
		parent::__construct();
		$this->load->database();
	}

	public function __destruct() {
		//		echo $this->db->last_query();
	}	
	
	public function addVendorContact($vendorId, $data) {
		$data = $this->cleanFields($data);
		$data['vendorId'] = (int)$vendorId;		
		$this->db->insert($this->table, $data);
		return $this->db->insert_id();	
	}

    public function add($contact) {
        $this->db->insert($this->table, $contact);
        return $this->db->insert_id();
    }
	
	public function delete($contactId) {
		$this->db->delete($this->table, Array('id' => $contactId));		
	}

	public function getList($vendorId) {
		$vendorId = (int)$vendorId;
		$this->db->select('*', false);
		$query = $this->db->get_where($this->table,
			array(
				'vendorId' => $vendorId
			)	
		);
		return $query->result();
	}

    public function addUpdateVendorContacts($vendorId, $contacts) {
        $notForDelete = array();
        foreach($contacts as $contact) {
            $contact = (array)$contact;
            $contact = $this->cleanFields($contact);
            $contact['vendorId'] = (int)$vendorId;
            if (!isset($contact['id'])) {
                //add
                $this->db->insert($this->table, $contact);
                $notForDelete[] = $this->db->insert_id();
            } else if ($contactId = (int)$contact['id']) {
                //update
                $notForDelete[] = $contactId;
                $this->db->where(
                    array('id' => $contactId));
                $this->db->update($this->table, $contact);
            }
        }
        if (count($notForDelete) > 0) {
            //remove old
            $this->db->query("DELETE FROM {$this->table} WHERE id NOT IN (" . implode(',', $notForDelete) . ") AND vendorId = " . $vendorId);
        } else {
            //remove all
            $this->db->query("DELETE FROM {$this->table} WHERE vendorId = " . $vendorId);
        }
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