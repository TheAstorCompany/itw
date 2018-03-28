<?php

class CompanyUsers extends CI_Model {
	private $table = 'CompanyUsers';

	public function __construct() {
		parent::__construct();
		$this->load->database();
	}

	public function __destruct() {
		//echo $this->db->last_query();
	}
	
    private function hashPassword($password) {
        $salt = bin2hex(mcrypt_create_iv(32, MCRYPT_DEV_URANDOM));
        $hash = hash('sha256', $salt . $password);

        return $salt . $hash;
    }
	
    private function validatePassword($password, $userPassword) {
        $salt = substr($userPassword, 0, 64);
        $hash = substr($userPassword, 64, 64);

        $password_hash = hash('sha256', $salt . $password);
        
        return !!($hash == $password_hash);
    }

	public function checkLogin($userName, $password, $companyId) {
		$companyId = (int)$companyId;

		$query = $this->db->get_where($this->table,
			array(
				'username' => $userName,
				'companyId' => $companyId,
				'active' => true
			),
			1
		);

		$user = $query->row();
		
		if ($user) {
			if ($this->validatePassword($password, $user->password)) {
				return $user;
			}
		}
		
		return false;
	}

    public function logLogin($username) {
        $this->db->set('username', $username);
        $this->db->set('dt', 'NOW()', false);
        $this->db->set('REMOTE_ADDR', $_SERVER['REMOTE_ADDR']);
        $this->db->set('HTTP_USER_AGENT', $_SERVER['HTTP_USER_AGENT']);
        $this->db->insert('logging');
        return $this->db->insert_id();
    }

	public function UniqueUsername($username, $id = null) {
		if (!$id) {
			return $this->db->query("SELECT COUNT(*) = 0 AS `unique` FROM {$this->table} WHERE username = ?", Array($username))->row()->unique;
		} else {
			return $this->db->query("SELECT COUNT(*) = 0 AS `unique` FROM {$this->table} WHERE username = ? AND id != ?", Array($username, $id))->row()->unique;
		}
	}
	public function getById($userId, $companyId) {
		$userId = (int)$userId;
		$companyId = (int)$companyId;

		$query = $this->db->get_where($this->table,
			array(
				'id' => $userId,
				'companyId' => $companyId,
			),
			1
		);

		return $query->row();
	}

	public function updateAccount($userId, $companyId, $data) {
		$data = $this->cleanFields($data);
		$userId = (int)$userId;
		$companyId = (int)$companyId;

		if (empty($data['password'])) {
			unset($data['password']);
		} else {
			$data['password'] = $this->hashPassword($data['password']);
		}

        if(empty($data['allowStoresVendorsEdit'])) {
            $data['allowStoresVendorsEdit'] = 0;
        }

		$this->db->update($this->table, $data,
			array(
				'id' => $userId,
				'companyId' => $companyId,
			)
		);
	}
	public function addAccount($companyId, $data) {
		$data = $this->cleanFields($data);
		$data['companyId'] = (int)$companyId;
		$data['password'] = $this->hashPassword($data['password']);
		$this->db->insert($this->table, $data);
		return $this->db->insert_id();

	}
	public function Delete($userId) {
		$this->db->update($this->table, 
			Array(
				'active'=>false
			),
			array(
				'id' => $userId				
			)
		);
		
		if ($this->db->affected_rows() == 0) {
			$this->db->delete($this->table, Array('id' => $userId));
		}
	}

	public function getList($companyId, $start, $length, $searchToken = null, $orderColumn = null, $orderDir = null) {
		$companyId = (int)$companyId;

		$this->db->select('SQL_CALC_FOUND_ROWS '.$this->table.'.*', false);

		if (!empty($searchToken)) {
			$this->db->or_like(
				array(
					'id' => $searchToken,
					'active' => $searchToken,
					'accessLevel' => $searchToken,
					'firstName' => $searchToken,
					'lastName' => $searchToken,
					'title' => $searchToken,
					'email' => $searchToken,
					'phone' => $searchToken,
					'lastUpdated' => $searchToken
				)			
			);
		}


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