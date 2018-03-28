<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
include 'application/controllers/admin/Auth.php';

class Accounts extends Auth {

	public function index() {
		$this->load->view('admin/accounts/accounts_edit', $this->assigns);
	}
	public function username_check($str) {
		$this->load->model('admin/CompanyUsers');
		
		if (!$this->CompanyUsers->UniqueUsername($str, (int)$this->uri->segment(4)))
		{
			$this->form_validation->set_message('username_check', 'The %s field must be unique.');
			return FALSE;
		}
		else
		{
			return TRUE;
		}
	}
	private function initRules($flagAddEdit = false, $requirePassword = false) {
		$this->load->library('form_validation');
		
		if ($flagAddEdit) {
			$this->form_validation->set_rules('internalNotes', 'Internal Notes', 'trim');		
			$this->form_validation->set_rules('accessLevel', 'Access Level', 'trim');
			$this->form_validation->set_rules('active', 'Status', 'trim');
			$this->form_validation->set_rules('username', 'Username', 'trim|required|callback_username_check');
		}		
		
		$this->form_validation->set_rules('firstName', 'First name', 'required|trim');
		$this->form_validation->set_rules('lastName', 'Last name', 'required|trim');
		$this->form_validation->set_rules('title', 'title', 'trim');
		$this->form_validation->set_rules('phone', 'Phone', 'required|trim');
		$this->form_validation->set_rules('email', 'Email', 'required|trim');
		if (!$requirePassword) {
			if (!$flagAddEdit) {
				$this->form_validation->set_rules('password', 'Password', 'trim|matches[password2]');
				$this->form_validation->set_rules('password2', 'Password Confirmation', 'trim|matches[password]');
			} else {
				$this->form_validation->set_rules('password', 'Password', 'trim');		
			}
		} else {
			$this->form_validation->set_rules('password', 'Password', 'trim|required');
		}
	}
	
	public function Update() {
		$userId = $this->session->userdata('USER')->id;
		$this->load->model('admin/CompanyUsers');
		$this->load->model('Companies');
		
		$this->assigns['company_data'] = $this->Companies->getById($this->assigns['_companyId']);
		
		if ($this->input->post('update')) {
			$this->initRules();
			
			if ($this->form_validation->run() == true) {
				//update, flash and redirect
				$this->CompanyUsers->updateAccount($userId, $this->assigns['_companyId'], $_POST);
				$this->session->set_flashdata('info', 'Your account has been successfully updated.');
				$this->updateHeaderUserInfo($this->input->post('firstName'), $this->input->post('lastName'));
				
				redirect('admin/Accounts/Update');
				return;
			}
			
			//form did not pass validation
			$this->assigns['data'] = new Placeholder();
			
		} else {
			$this->assigns['data'] = $this->CompanyUsers->getById($userId, $this->assigns['_companyId']);
		}
		
		$this->load->view('admin/accounts/accounts_edit', $this->assigns);
	}
	
	public function AddEdit() {				
		if (!$this->assigns['_isAdmin']) {
			$this->session->set_flashdata('info', 'You do not have enough permissions to manage users.');
			redirect('admin/SupportRequest/index');
		}
		$userId = (int)$this->uri->segment(4);			
		$this->assigns['userId'] = $userId;
		$this->load->model('admin/CompanyUsers');
		$this->load->model('Companies');
			
		$this->assigns['company_data'] = $this->Companies->getById($this->assigns['_companyId']);
			
		if ($this->input->post('update')) {
			$this->initRules(true, (boolean)!$userId);
			
			if ($this->form_validation->run() == true) {
				//update, flash and redirect
				/*
				$_POST["firstName"] = addslashes($_POST["firstName"]);
				$_POST["lastName"] = addslashes($_POST["lastName"]);
				$_POST["title"] = addslashes($_POST["title"]);
				$_POST["email"] = addslashes($_POST["email"]);
				$_POST["phone"] = addslashes($_POST["phone"]);
				$_POST["username"] = addslashes($_POST["username"]);
				*/

			if ($userId) {
					$this->CompanyUsers->updateAccount($userId, $this->assigns['_companyId'], $_POST);
					$this->session->set_flashdata('info', 'The account has been successfully updated.');
					$this->updateHeaderUserInfo($this->input->post('firstName'), $this->input->post('lastName'));
				} else {
					$userId = $this->CompanyUsers->addAccount($this->assigns['_companyId'], $_POST);
					$this->session->set_flashdata('info', 'The account has been successfully added.');
				}
				redirect('admin/Accounts/AddEdit/' . $userId);
				return;
			}
			
			//form did not pass validation
			$this->assigns['data'] = new Placeholder();
			
		} else {
			if ($userId) {
				$this->assigns['data'] = $this->CompanyUsers->getById($userId, $this->assigns['_companyId']);
			} else {
				$this->assigns['data'] = new Placeholder();
			}
		}			
		$this->assigns['data']->accessLevelOptions = array(
            'USER' => 'User',
                'ADMIN' => 'Admin'
        );
        $this->assigns['data']->activeOptions = array(
            1 => 'Active',
                0 => 'Inactive'
        );
		$this->load->view('admin/accounts/accounts_addedit', $this->assigns);				
	}
	public function Delete() {
		if ($userId = (int)$this->uri->segment(4)) {
			$this->load->model('admin/CompanyUsers');
			$this->CompanyUsers->Delete($userId);
			$this->session->set_flashdata('info', 'User was successfully deleted.');			
			redirect('admin/ManageCompany/Peoples');	 	
		}
	}
	public function Add() {
		$this->assigns['data'] = new Placeholder();
		$this->load->view('admin/accounts/accounts_edit', $this->assigns);
	}
	
	public function ajaxList() {
		$this->load->model('admin/CompanyUsers');
		header('Content-type: application/json');
		
		$sortColumn = null;
		$sortDir = null;
		
		if ($this->input->get('iSortingCols') > 0) {
			if ($this->input->get('bSortable_0')) {
				switch ($this->input->get('iSortCol_0')) {
					case 0:
						$sortColumn = 'active';
						break;
					case 1:
						$sortColumn = 'accessLevel';
						break;
					case 2:
						$sortColumn = 'firstName';
						break;
					case 3:
						$sortColumn = 'lastName';
						break;
					case 4:
						$sortColumn = 'title';
						break;
					case 5:
						$sortColumn = 'email';
						break;
					case 6:
						$sortColumn = 'phone';
						break;
					case 7:
						$sortColumn = 'lastUpdated';
						break;
								
				}
				
				if ($this->input->get('sSortDir_0') == 'asc') {
					$sortDir = 'ASC';
				} else {
					$sortDir = 'DESC';
				}
			}		
		}
		
		$data = $this->CompanyUsers->getList($this->assigns['_companyId'], $this->input->get('iDisplayStart'), $this->input->get('iDisplayLength'), $this->input->get('sSearch'), $sortColumn, $sortDir);
		$ajaxData = array();
		$editURL = '<a href="'.base_url().'admin/'.$this->assigns['_controller'].'/AddEdit/%d">%s</a>';
		$mailURL = '<a href="mailto:%s">%s</a>';
		foreach ($data['data'] as $item) {
			$ajaxData[] = array(
				'DT_RowClass' => 'grade' . ($item->active ? 'A':'X'),			
				sprintf($editURL, $item->id, $item->active?'Active':'Inactive'),
				$item->accessLevel,
				$item->firstName,
				$item->lastName,
				$item->title,
				sprintf($mailURL, $item->email, $item->email),
				$item->phone,
				date("m/d/Y h:ia", strtotime($item->lastUpdated))
			);
		}
		
		echo json_encode(array(
			'aaData' => $ajaxData,
			'iTotalRecords' => $data['records'],
			'iTotalDisplayRecords' => $data['records']
		));
	}
}

/* End of file Accounts.php */
/* Location: ./application/controllers/admin/Accounts.php */