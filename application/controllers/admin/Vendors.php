<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
include 'application/controllers/admin/Auth.php';

class Vendors extends Auth {

	public function index() {
		$this->load->view('admin/vendors/vendors_edit', $this->assigns);
	}
	public function __construct() {
		parent::__construct();
		//form did not pass validation			
		$this->assigns['data'] = new Placeholder();
		//Duration
        $this->load->model("admin/VendorServiceDurations");
        $this->assigns['vendorServiceDurations'] = $this->VendorServiceDurations->getListForSelect($this->assigns['_companyId']);
		//Purpose
		$this->load->model("admin/VendorServicePurposes");
        $this->assigns['vendorServicePurposes'] = $this->VendorServicePurposes->getListForSelect($this->assigns['_companyId']);
        $this->load->model("admin/Containers");
        $this->assigns['containers'] = $this->Containers->getListForSelect($this->assigns['_companyId']);        
        $this->load->model("admin/ScheduleModel");
        $this->assigns['schedule'] = $this->ScheduleModel->getListForSelect($this->assigns['_companyId']);
	}
	
	private function formatServiceTitle(&$item) {
		$item->title = sprintf("%d x %s • %s • %s for %s $%s", 
	    	//strtoupper($item->vendorName),
	       	@$item->quantity,
	       	@$this->assigns['containers'][$item->containerId],
	       	@$this->assigns['schedule'][$item->schedule],
	       	@$this->day2name($item->days),
		$item->category==0 ? 'Watse' : 'Recycling',
	       	@$item->rate
	    );	

	    return $item;
	}
	
	public function AddEdit() {
        $user = $this->session->userdata('USER');
        $this->assigns['is_read_only'] = ($user->allowStoresVendorsEdit!=1);

        $vendorId = $this->uri->segment(4);
		$this->assigns['vendorId'] = $vendorId;
		$this->load->model('admin/VendorsModel');
		$this->load->model('States');
		$this->assigns['statesOptions'] = $this->States->getListForSelect();
        //array_unshift($this->assigns['statesOptions'], '');
        $this->assigns['statesOptions'] = array('0'=>'') + $this->assigns['statesOptions'];

		if ($this->input->post('addupdate')) {
            $_POST['cityVendor'] = isset($_POST['cityVendor']) ? 1 : 0;
            $_POST['astorBilled'] = isset($_POST['astorBilled']) ? 1 : 0;
            
			$this->load->library('form_validation');
			
			if ($this->assigns['_isAdmin']) {
				$this->form_validation->set_rules('notes', 'Notes', 'trim');
				$this->form_validation->set_rules('active', 'Status', 'trim');				
			}
			
			$this->form_validation->set_rules('name', 'Vendor Name', 'required|trim');
			$this->form_validation->set_rules('number', 'Vendor#', 'required|trim');

			$this->form_validation->set_rules('remitTo', '', 'trim');
			$this->form_validation->set_rules('addressLine1', '', 'trim');
			$this->form_validation->set_rules('addressLine2', '', 'trim');
			$this->form_validation->set_rules('city', '', 'trim');
			$this->form_validation->set_rules('state', '', 'trim');
			$this->form_validation->set_rules('phone', '', 'trim');
			$this->form_validation->set_rules('fax', '', 'trim');
			$this->form_validation->set_rules('website', '', 'trim');
			
			$this->form_validation->set_rules('email', 'Email', 'trim|valid_email');						
			
			if ($this->form_validation->run() == true) {
				//update, flash and redirect
				if (!$this->assigns['_isAdmin']) {
					unset($_POST['notes']);
					unset($_POST['status']);								
				}
				if (!isset($_POST['lastUpdated'])) {
					$_POST['lastUpdated'] = date('Y-m-d h:i');
				}

				if ($vendorId) {
					$this->VendorsModel->updateVendor($vendorId, $this->assigns['_companyId'], $_POST);
					$this->session->set_flashdata('info', 'The vendor has been successfully updated.');					
				} else {
					$vendorId = $this->VendorsModel->addVendor($this->assigns['_companyId'], $_POST);
					$this->session->set_flashdata('info', 'The vendor has been successfully added.');					
					//services
					if (($services = $this->session->userdata('services')) && is_array($services)) {
						$this->load->model('admin/VendorServices');
						foreach($services as $service) {
							unset($service->id);
							$this->VendorServices->addVendorService($vendorId, (array)$service);
						}
						$this->session->unset_userdata('services');
					}	
				}

				redirect('admin/Vendors/AddEdit/' . $vendorId);
				return;
			}
		} else {
			if ($vendorId) {
				$this->assigns['data'] = $this->VendorsModel->getById($vendorId, $this->assigns['_companyId']);

				$this->load->model('admin/VendorContacts');
				$this->assigns['data']->vendorContacts = $this->VendorContacts->getList($vendorId);
				if (count($this->assigns['data']->vendorContacts) > 0) {
					$this->assigns['vendorContacts'] = $this->assigns['data']->vendorContacts;
				}

				$this->load->model('admin/VendorServices');
				$this->assigns['data']->vendorServices = $this->VendorServices->getList($vendorId);				
				foreach($this->assigns['data']->vendorServices as $k=>$service) {
				    $this->assigns['data']->vendorServices[$k] = $this->formatServiceTitle($this->assigns['data']->vendorServices[$k]);						        	
				}
			} else {
				$this->assigns['data'] = new Placeholder();
				$this->session->unset_userdata('services');
			}
		}
		
		$this->assigns['data']->activeOptions = array(
		    1 => 'Active',
		    0 => 'Inactive'
		);                

		$this->load->view('admin/vendors/vendors_addedit', $this->assigns);
	}

	public function Delete() {
		if ($vendorId = (int)$this->uri->segment(4)) {
			$this->load->model('admin/VendorsModel');
			$this->VendorsModel->Delete($vendorId);
			$this->session->set_flashdata('info', 'Vendor was successfully deleted.');			
			redirect('admin/ManageCompany/Vendors/');	 	
		}
	}
	
	public function ajaxList() {
		$this->load->model('admin/VendorsModel');
		header('Content-type: application/json');
		
		$sortColumn = null;
		$sortDir = null;
		
		if ($this->input->get('iSortingCols') > 0) {
			if ($this->input->get('bSortable_0')) {
				switch ($this->input->get('iSortCol_0')) {
					case 0:
						$sortColumn = 'status';
						break;
					case 1:
						$sortColumn = 'number';
						break;
					case 2:
						$sortColumn = 'name';
						break;
					case 3:
						$sortColumn = 'addressLine1';
						break;
					case 4:
						$sortColumn = 'city';
						break;
					case 5:
						$sortColumn = 'state';
						break;
					case 6:
						$sortColumn = 'zip';
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

        $searchToken =  $this->input->get('sSearch');
        if(isset($_GET['filter_active'])) {
            $searchToken =  array('searchToken'=>$this->input->get('sSearch'), 'searchStatus'=>$this->input->get('filter_active'));
        }

        $data = $this->VendorsModel->getList($this->assigns['_companyId'], $this->input->get('iDisplayStart'), $this->input->get('iDisplayLength'), $searchToken, $sortColumn, $sortDir);
		$ajaxData = array();
		$editURL = '<a href="'.base_url().'admin/'.$this->assigns['_controller'].'/AddEdit/%d">%s</a>';
		foreach ($data['data'] as $item) {
			$ajaxData[] = array(
				'DT_RowClass' => 'grade' . ($item->status ? 'A':'X'),			
				sprintf($editURL, $item->id, $item->status?'Active':'Inactive'),
				$item->number,
				$item->name,
				$item->addressLine1,
				$item->city,
				$item->state,
				$item->zip,
				date("m/d/Y h:ia", strtotime($item->lastUpdated))
			);
		}
		
		echo json_encode(array(
			'aaData' => $ajaxData,
			'iTotalRecords' => $data['records'],
			'iTotalDisplayRecords' => $data['records']
		));
	}
	
	public function AddContact() {
        $result = array('html'=>'', 'error'=>'');
        $this->assigns['vendorContacts'] = array();

        $this->load->library('form_validation');
        $this->form_validation->set_rules('firstName', 'First Name', 'required|trim');
        $this->form_validation->set_rules('lastName', 'Lirst Name', 'trim');
        $this->form_validation->set_rules('title', 'Title', 'trim');
        $this->form_validation->set_rules('email', 'Email', 'trim|valid_email');
        $this->form_validation->set_rules('phone', 'Phone', 'trim');

        if ($this->form_validation->run() == true) {
            $this->load->model('admin/VendorContacts');

            $contact = $_POST;
            $this->VendorContacts->add($contact);
        } else {
            $result['error'] = validation_errors();
        }

        $vendorId = 0;
        if(isset($_POST['vendorId'])) {
            $vendorId = intval($_POST['vendorId']);
        }

        $this->assigns['vendorContacts'] = $this->VendorContacts->getList($vendorId);

        $result['html'] = $this->load->view('admin/vendors/vendors_contacts_ajax', $this->assigns, true);
        header('content-type:application/json');
        echo json_encode($result);
	}

    public function DeleteContact() {
        if(isset($_POST['contact_id'])) {
            $this->load->model('admin/VendorContacts');

            $contactId = intval($_POST['contact_id']);
            $this->VendorContacts->Delete($contactId);
        }
    }

	public function autocompleteLocation() {
		$this->load->model('admin/Autocomplete');		
		
		header("Content-Type: application/json");
		echo json_encode($this->Autocomplete->VendorServicesLocation($this->input->get('term'), $this->assigns['_companyId']));
	}

	public function autocompleteLocationFillForm() {
		if ($id = $this->input->get_post('vendorServiceId')) {
			$this->load->model('admin/VendorServices');
			if ($result = $this->VendorServices->getById($id)) {
				if ($result->days) {
					$days = array();
					for($i = 1; $i <= 8; $i++) {
						$k = 1 << $i;
						$days[$k] = ($result->days & $k) > 1;
					}
					$result->days = $days;
				}
				header("Content-Type: application/json");
				echo json_encode($result);			
			}
		}
	}

}

/* End of file Vendors.php */
/* Location: ./application/controllers/admin/Vendors.php */