<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
class Placeholder {
	public function __get($name) {
		return null;
	}	
}

class Auth extends CI_Controller {
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
		$this->output->set_header('Pragma: no-cache');		
		$this->load->library('session');
		
		if (!defined('MIGRATION')) {
			$this->load->model('Companies');
			define('MIGRATION', $this->Companies->getCurrentMigrationVersion());
		}

		$this->requestedURL = $this->session->userdata('REQUESTED_URL');
		
		if ($this->router->fetch_class() != 'Auth') {
			$user = $this->session->userdata('USER');
			if ($user == false) {
				
				if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && $_SERVER['HTTP_X_REQUESTED_WITH'] == 'XMLHttpRequest') {
					header('Content-type: application/json');
					echo json_encode(array(
						'error' => 'expired'
					));

					die();
				}
				
				$this->requestedURL = $_SERVER['REQUEST_URI'];
				
				$this->session->set_userdata('REQUESTED_URL', $this->requestedURL);
				redirect('/admin/Auth');
				
				return;
			}
			
			$this->assigns['_loggedUser'] = $user->loggedUser;
			$this->assigns['_controller'] = $this->uri->segment(2);
			
			$action = $this->uri->segment(3);
			$this->assigns['_action'] = (empty($action) ? 'index' : $action);
			$this->assigns['_isAdmin'] = !!($user->accessLevel == 'ADMIN');
			
			if (!$this->assigns['_isAdmin'] && (($action != 'Update') && ($this->assigns['_controller'] != 'Help'))) {
                if(!($this->assigns['_controller']=='DCInvoice' && $this->assigns['_action']=='view')) {
                    redirect('/Stores');
                }
			}
		}
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

                    $this->CompanyUsers->logLogin($username);
					
					if (!empty($this->requestedURL)) {
						$this->session->unset_userdata('REQUESTED_URL');
						redirect($this->requestedURL);
						
						return;
					}
					
					redirect('admin/SupportRequest/index');
					return;
				}
				
				$errors[] = 'Wrong username or password.';
			}
			
		}
		
		$this->assigns['errors'] = $errors;
		$this->assigns['username'] = $username;
		$this->assigns['hideAdminMenu'] = true;
		
		$this->load->model('companies');
		$this->assigns['companiesList'] = $this->companies->getList(); 

		$this->load->view('admin/login', $this->assigns);
	}
	
	public function logOut() {
		$this->session->unset_userdata('USER');
		$this->session->set_flashdata('info', 'You have been successfully logged out.');
		redirect('/admin/Auth');
	}	
		
	protected function day2name($days) {
		if (is_array($days)) {
			$days = array_sum($days);
		}
		$result = array();
		$arrDays = array(			
        	2 => 'Mo AM',
        	4 => 'Tu AM',
        	8 => 'We AM',
        	16 => 'Th AM',
        	32 => 'Fri AM',
        	64 => 'Sat AM',
        	128 => 'Su AM',            
            256 => 'Mo PM',
        	512 => 'Tu PM',
        	1024 => 'We PM',
        	2048 => 'Th PM',
        	4096 => 'Fri PM',
        	8192 => 'Sat PM',
        	16384 => 'Su PM',
        );
        
        foreach($arrDays as $mask=>$day) {
        	if (($days & $mask) > 1) {
        		$result[] = $day;
        	}
        }
        
        return implode(" ", $result);
	}
}

/* End of file AdminBase.php */
/* Location: ./application/controllers/admin/AdminBase.php */