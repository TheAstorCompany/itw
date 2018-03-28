<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');


class Placeholder {
	public function __get($name) {
		return null;
	}
}

class ReadOnly extends CI_Controller {
	private $requestedURL;
	
	protected $assigns = array(
		'_loggedUser' => '',
		'_controller' => '',
		'_main_controller' => '',
		'_action'	 => '',
		'_companyId'	 => 1,
		'_companies'	 => array(),
		'_isAdmin'	=> false,
	);
	
	public function __construct() {
		parent::__construct();
		$this->load->library('session');
		
		if (!defined('MIGRATION')) {
			$this->load->model('Companies');
			define('MIGRATION', $this->Companies->getCurrentMigrationVersion());
		}

		$this->requestedURL = $this->session->userdata('REQUESTED_URL');
		

			$user = $this->session->userdata('USER');

			
			$this->assigns['_loggedUser'] = null;
			$this->assigns['_controller'] = $this->uri->segment(2);
			
			$action = $this->uri->segment(3);
			$this->assigns['_action'] = (empty($action) ? 'index' : $action);
			$this->assigns['_isAdmin'] = null;
			$this->assigns['_main_controller'] = $this->uri->segment(1);

	}
	
	public function updateHeaderUserInfo($firstName, $lastName) {
		$user = $this->session->userdata('USER');
		$user->loggedUser = strtoupper(substr($firstName,0,1))  . '. ' . ucfirst($lastName);
		$this->session->set_userdata('USER', $user);
	}
	
	public function index() {
		$errors = array();
		$username = $this->input->post('username');
		$password = $this->input->post('password');
		
                
                
                
		if ($this->input->post('login')) {
			
			if (empty($username)) {
				$errors[] = 'Username is required.';
			}
			
			if (empty($password)) {
				$errors[] = 'Password is required.';
			}
			
			if (empty($errors)) {
				$this->load->model('admin/CompanyUsers');
				
				$user = $this->CompanyUsers->checkLogin($username, $password, $this->assigns['_companyId']);
				
				if (!empty($user)) {
					$this->session->set_userdata('USER', $user);
					$this->updateHeaderUserInfo($user->firstName, $user->lastName);
					
					if (!empty($this->requestedURL)) {
						$this->session->unset_userdata('REQUESTED_URL');
						redirect($this->requestedURL);
						
						return;
					}
					
					redirect('Company');
					return;
				}
				
				$errors[] = 'Wrong username or password.';
			}
			
		}
		
		$this->assigns['errors'] = $errors;
		$this->assigns['username'] = $username;
		$this->assigns['hideAdminMenu'] = true;
		$this->assigns['_controller'] = 'Front';
		
		$this->load->model('companies');
		$this->assigns['companiesList'] = $this->companies->getList(); 

		$this->load->view('admin/login', $this->assigns);
	}
	
	public function logOut() {
		$this->session->unset_userdata('USER');
		$this->session->set_flashdata('info', 'You have been successfully logged out.');
		redirect('Auth');
	}
}

/* End of file front.php */
/* Location: ./application/controllers/front.php */