<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
include 'application/controllers/admin/Auth.php';

class ManageCompany extends Auth {

	public function index() {
		$this->peoples();
	}
	public function peoples() {
		$this->load->view('admin/accounts/accounts_list', $this->assigns);
	}
	public function vendors() {
		$this->load->view('admin/vendors/vendors_list', $this->assigns);
	}
	public function dc() {
		$this->load->view('admin/dc/dc_list', $this->assigns);
	}
	public function stores() {
		$this->load->model('admin/Containers');
		$this->assigns['data'] = new Placeholder();
		
		$this->assigns['data']->filter_containerOptions = $this->Containers->getListForSelect($this->assigns['_companyId']);
		$this->assigns['data']->filter_statusOptions = array('0'=>'All', '1'=>'Inactive', '2'=>'Active');
		$this->load->view('admin/stores/stores_list', $this->assigns);
	}
}
/* End of file ManageCompany.php */
/* Location: ./application/controllers/admin/ManageCompany.php */