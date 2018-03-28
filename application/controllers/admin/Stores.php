<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
include 'application/controllers/admin/Auth.php';

class DataHolder extends Placeholder {
	public function __construct($data) {
		foreach ($data as $k=>$v) {
			$this->{$k} = $v;
		}
	}	
}

class Stores extends Auth {
	
	public function autocomplete_required($input) {
		if (empty($input)) {
			$this->form_validation->set_message('autocomplete_required', 'The %s field must be autocompleted!');
			return false;
		}

		return true;
	}
	
	private function formatServiceTitle(&$item) {
		$this->load->model('admin/Containers');
		$this->load->model('admin/ScheduleModel');
		
		$containerOptions = $this->Containers->getListForSelect($this->assigns['_companyId']);
		$scheduleOptions = $this->ScheduleModel->getListForSelect($this->assigns['_companyId']);
		$scheduleOptions[0] = '';
        
		$item->title = sprintf("%s %s • %d x %s • %s • %s for %s $%s", 
	    	strtoupper($item->vendorName),
			$item->vendorPhone,
			$item->quantity,
			isset($containerOptions[$item->containerId]) ? $containerOptions[$item->containerId] : '&nbsp;',
			$scheduleOptions[$item->schedule],
			$this->day2name($item->days),
			($item->category==0 ? 'Waste' : 'Recycling'),
			$item->rate
	    );		
	}
	
	public function index() {
		redirect('admin/ManageCompany/Peoples');
	}
	
	public function addEdit() {
        $user = $this->session->userdata('USER');
        $this->assigns['is_read_only'] = ($user->allowStoresVendorsEdit!=1);
		
		$id = (int)$this->uri->segment(4);
					
		$this->load->model('admin/StoresModel');
		$this->load->model('States');
		$this->load->model('admin/StoreServiceDurations');
		$this->load->model('admin/StoreServicePurposes');
		$this->load->model('admin/Containers');
		$this->load->model('admin/ScheduleModel');
        $this->load->model('FeeTypeModel');

        $this->load->helper('dates');
						
		if ($this->input->post('addupdate')) {
			$this->load->library('form_validation');
			$this->form_validation->set_message('greater_than', "The %s field is required.");
			
			$this->form_validation->set_rules('notes', 'Notes', 'trim');
			$this->form_validation->set_rules('status', 'Status', 'trim');			
			$this->form_validation->set_rules('location', 'Location#', 'required|trim');
			$this->form_validation->set_rules('district', 'District#', 'required|trim');
			$this->form_validation->set_rules('districtId', 'District#', 'callback_autocomplete_required|numeric|trim');

			$this->form_validation->set_rules('addressLine1', '', 'required|trim');
			$this->form_validation->set_rules('addressLine2', '', 'trim');
			$this->form_validation->set_rules('city', '', 'required|trim');
			$this->form_validation->set_rules('stateId', '', 'required|trim');
			$this->form_validation->set_rules('phone', '', 'required|trim');
			$this->form_validation->set_rules('fax', '', 'trim');
			$this->form_validation->set_rules('districtName', '', 'trim');
            $this->form_validation->set_rules('county', '', 'trim');
            $this->form_validation->set_rules('open24hours', '', 'trim');
            $this->form_validation->set_rules('officeLocation', '', 'trim');
            $this->form_validation->set_rules('franchise', '', 'trim');
            $this->form_validation->set_rules('payFees', '', 'trim');
            $this->form_validation->set_rules('startDateS', '', 'trim');
            $this->form_validation->set_rules('endDateS', '', 'trim');

			if ($this->form_validation->run() == true) {
				//update, flash and redirect
				if (!$this->assigns['_isAdmin']) {
					unset($_POST['notes']);
					unset($_POST['status']);								
				}

                if (isset($_POST['startDateS'])) {
                    $_POST['startDate'] = USToSQLDate($_POST['startDateS']);
                    unset($_POST['startDateS']);
                }

                if (isset($_POST['endDateS'])) {
                    $_POST['endDate'] = USToSQLDate($_POST['endDateS']);
                    unset($_POST['endDateS']);
                }

				if ($id) {
					$this->StoresModel->update($id, $this->assigns['_companyId'], $_POST);
					$this->session->set_flashdata('info', 'The Store has been successfully updated.');					
				} else {
					$id = $this->StoresModel->add($this->assigns['_companyId'], $_POST);
					$this->session->set_flashdata('info', 'The Store has been successfully added.');
					$this->load->model('admin/VendorServices');
					//update users & services
					$contactsData = $this->session->userdata(get_class($this));
					$this->load->model('admin/StoreContacts');
					//$this->load->model('admin/StoreServices');
					if (isset($contactsData['contacts']) && count($contactsData['contacts']) > 0) {
						foreach ($contactsData['contacts'] as $contact) {
							$this->StoreContacts->add($id, (array)$contact);
						}
					}
					if (isset($contactsData['services']) && count($contactsData['services']) > 0) {
						foreach ($contactsData['services'] as $service) {
							if ($service->days) {
								$temp = 0;
								
								foreach ($service->days as $day) {
									$temp += $day;	
								}
								
								$service->days = $temp;
							} else {
								$service->days = 0;
							}
							
							//$this->StoreServices->add($id, (array)$service);
							$service->locationId = $id;
							$service->locationType = 'DC';
							
							$this->VendorServices->addVendorService($service->vendorId, (array)$service);
						}
					}
				}
				
				redirect('admin/Stores/AddEdit/' . $id);
				return;
			}			
			//form did not pass validation
			$this->assigns['data'] = new Placeholder();
			
		} else {
			if ($id) {
				$this->assigns['data'] = $this->StoresModel->getById($this->assigns['_companyId'], $id);
				
				if (empty($this->assigns['data'])) {
					$this->session->set_flashdata('info', 'Store #' . $id . ' does not exists!');
					redirect('admin/Stores/AddEdit/');
					
					return;	
				}

                $this->assigns['data']->startDateS = SQLToUSDate($this->assigns['data']->startDate);
                $this->assigns['data']->endDateS = SQLToUSDate($this->assigns['data']->endDate);
				
				$this->load->model('admin/StoreContacts');
				$this->assigns['data']->StoreContacts = $this->StoreContacts->getList($id);				
			} else {
				$this->session->unset_userdata(get_class($this));
				$this->assigns['data'] = new Placeholder();
			}
		}

		if (!$this->assigns['data']->status) {
			$this->assigns['data']->status = '00';
		}
		
		$this->assigns['data']->statusOptions = array(
            1 => 'Active',
            0 => 'Inactive'
        );
		
		$this->assigns['data']->serviceTypeOptions = array('0'=>'- Please select -', '1'=>'Waste', '2'=>'Lamp', '3'=>'Waste/Lamp');
        
        $this->assigns['data']->statesOptions = $this->States->getListForSelect();
        $this->assigns['data']->durationOptions = $this->StoreServiceDurations->getListForSelect($this->assigns['_companyId']);
        $this->assigns['data']->purposeOptions = $this->StoreServicePurposes->getListForSelect($this->assigns['_companyId']);
        $this->assigns['data']->containerOptions = $this->Containers->getListForSelect($this->assigns['_companyId']);
        $this->assigns['data']->scheduleOptions = $this->ScheduleModel->getListForSelect($this->assigns['_companyId']);
        $this->assigns['data']->feeOptions = $this->FeeTypeModel->getListForSelect();
        
        $this->load->model('MaterialsModel');
		$this->assigns['data']->materialOptions = $this->MaterialsModel->getList($this->assigns['_companyId'], 2);
		
		$this->assigns['data']->unitOptions = array(
			0 => '- Please select -',
			1 => 'Tons',
			2 => 'Lbs',
			3 => 'Bales',
			4 => 'Bulbs',
			5 => 'Boxes',
			6 => 'Units',
		);


        
		$this->load->view('admin/stores/store_addedit', $this->assigns);				
	}
	
	
	public function ajaxList() {
		header('Content-type: application/json');
		
		echo json_encode($this->getStores());
	}

	public function csvList() {
		//export
		$this->load->helper('download');
		$file = fopen('php://temp/maxmemory:'. (12*1024*1024), 'r+');
		fputcsv($file, array(
			'Status',
			'Store#',
			'District#',
			'Address',
			'City',
			'State',
			'Zip',
			'24-Hour',
			'Office Location',
			'Scheduled Services',
			'Store Phone',
			'Last Updated'
		));
		$data = $this->getStores();
		foreach ($data['aaData'] as $row) {
			fputcsv($file, array(
				strip_tags($row[0]),
				strip_tags($row[1]),
				strip_tags($row[2]),
				strip_tags($row[3]),
				strip_tags($row[4]),
				strip_tags($row[5]),
				strip_tags($row[6]),
				strip_tags($row[7]),
				strip_tags($row[8]),
				strip_tags(str_ireplace(array('&nbsp;', '•', '<br />'), array(' ', '~', "\n"), $row[9])),
				strip_tags($row[10]),
				strip_tags($row[11])				
			));
		}

		rewind($file);
		$csv = stream_get_contents($file);
		fclose($file);

		force_download('Stores.csv', $csv);
	}
	
	private function getStores() {
		$this->load->model('admin/StoresModel');
		$this->load->model('admin/VendorServices');		
		
		$sortColumn = null;
		$sortDir = null;
		
		if ($this->input->get('iSortingCols') > 0) {
			if ($this->input->get('bSortable_0')) {
				switch ($this->input->get('iSortCol_0')) {
					case 0:
						$sortColumn = 'status';
						break;
					case 1:
						$sortColumn = 'CAST(`location` AS UNSIGNED)';
						break;
					case 2:
						$sortColumn = 'CAST(`district` AS UNSIGNED)';
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
						$sortColumn = 'postCode';
						break;
					case 7:
						$sortColumn = 'open24hours';						
						break;					
					case 8:
						$sortColumn = 'officeLocation';						
						break;
					case 11:
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
		
		$searchToken = array();
        $searchToken['filter_field'] = $this->input->get('filter_field');
		$searchToken['filter_status'] = $this->input->get('filter_status');
		$searchToken['filter_container'] = $this->input->get('filter_container');
		$searchToken['filter_search'] = $this->input->get('sSearch');
		$data = $this->StoresModel->getList($this->assigns['_companyId'], $this->input->get('iDisplayStart'), $this->input->get('iDisplayLength'), $searchToken, $sortColumn, $sortDir);
		$ajaxData = array();
		
		$editURL = '<a href="'.base_url().'admin/'.$this->assigns['_controller'].'/AddEdit/%d">%s</a>';
		
		foreach ($data['data'] as $item) {
			$diversion = 0;
			if ((double)$item->recycling > 0) {
				$diversion = (double)$item->recycling / ((double)$item->recycling + (double)$item->waste);
			}
			
			$scheduledServices = $this->VendorServices->getByLocation($item->id, 'STORE');
			$scheduledServicesTitle = '';
			$scheduledServicesNumber = 1;
			foreach ($scheduledServices as &$scheduledService) {
				$this->formatServiceTitle($scheduledService);
				$scheduledServicesTitle .= $scheduledServicesNumber.'.&nbsp;'.$scheduledService->title.'<br />';
				$scheduledServicesNumber++;
			}
			
			$ajaxData[] = array(
				'DT_RowClass' => 'grade' . ($item->status ? 'A':'X'),			
				sprintf($editURL, $item->id, $item->status ? 'Active':'Inactive'),
				$item->location,
				$item->district,
				$item->addressLine1,
				$item->city,
				$item->state,
				$item->postCode,
				$item->open24hours ? 'Y' : 'N',
				$item->officeLocation ? 'Y' : 'N',
				$scheduledServicesTitle,
				$item->phone,
				date("m/d/Y h:ia", strtotime($item->lastUpdated))
			);
		}
		
		return array(
			'aaData' => $ajaxData,
			'iTotalRecords' => $data['records'],
			'iTotalDisplayRecords' => $data['records']
		);
	}
	
	public function Delete($id) {
		$id = (int)$id;
		
		$this->load->model('admin/StoresModel');
		
		$this->StoresModel->Delete($id);
		$this->session->set_flashdata('info', 'DC was successfully deleted.');	
				
		redirect('admin/ManageCompany/Stores/');
	}
	
	public function autocompleteServices() {
		$this->load->model('admin/Autocomplete');
		
		
		header("Content-Type: application/json");
		echo json_encode($this->Autocomplete->vendor($this->input->get('term'), $this->assigns['_companyId']));
	}
	
	public function autocompleteDistrict() {
		$this->load->model('admin/Autocomplete');
		
		header("Content-Type: application/json");
		echo json_encode($this->Autocomplete->district($this->input->get('term')));
		
	}
	
	public function getService() {
		$id = (int)$this->uri->segment(4);
		$data = array();
		
		if ($id) {
			$this->load->model('admin/VendorServices');
			
			$data = $this->VendorServices->getByLocation($id, 'STORE');
			$this->formatServiceTitle($data);
					
			$days = array();
			
            for($i = 1; $i <= 8; $i++) {
            	$k = 1 << $i;
            	$days[$k] = !!(($data->days & $k) > 1);
            }
            
            $data->days = $days;
		}
		
		header('Content-type: application/json');
		echo json_encode($data);
	}
	
	public function Vendor() {
		$id = (int)$this->uri->segment(4);
		$data = array();
		
		if ($id) {
			$this->load->model('admin/VendorsModel');
			$this->load->helper('dates');
			
			$data = $this->VendorsModel->getVendorById($id);
		}
		
		header('Content-type: application/json');
		echo json_encode($data);		
	}
	
	public function Service() {
		$id = (int)$this->uri->segment(4);
		$data = array();
		
		if ($id) {
			$this->load->model('admin/VendorServices');
			$this->load->helper('dates');
			
			$data = $this->VendorServices->getById($id);
					
			$days = array();
			
            for($i = 1; $i <= 16; $i++) {
            	$k = 1 << $i;
            	$days[$k] = !!(($data->days & $k) > 1);
            }
            
            $data->days = $days;
			$data->equipmentDate = SQLToUSDate($data->equipmentDate);
			$data->startDate = SQLToUSDate($data->startDate);
			$data->endDate = SQLToUSDate($data->endDate);
		}
		
		header('Content-type: application/json');
		echo json_encode($data);
	}
	
	public function Services() {
		$id = (int)$this->uri->segment(4);
		$is_history = (bool)$this->uri->segment(5);

		$this->load->model('admin/VendorServices');
		
		$result = Array(
			'error'=>'',
			'result'=>	Array()
		);
		
		if ($_POST) {			
			
			$this->load->library('form_validation');
			$this->form_validation->set_message('greater_than', "The %s field is required.");
			
			$this->form_validation->set_rules('vendorId', 'Vendor', 'required|trim|htmlspecialchars');			
			$this->form_validation->set_rules('durationId', '', 'greater_than[0]|trim');
			$this->form_validation->set_rules('purposeId', '', 'greater_than[0]|trim');
			$this->form_validation->set_rules('quantity', 'Quantity', 'required|trim');
			$this->form_validation->set_rules('unitId', 'Unit', 'greater_than[0]|trim');
			$this->form_validation->set_rules('containerId', 'Container', 'greater_than[0]|trim');
			$this->form_validation->set_rules('materiId', '', 'greater_than[0]|trim');
			$this->form_validation->set_rules('schedule', '', 'trim');
			$this->form_validation->set_rules('rate', '', 'trim');
			$this->form_validation->set_rules('startDate', 'Start Date', 'required|trim');
			
			
			if ($this->form_validation->run() == true) {
				$this->load->helper('dates');


                if (!empty($_POST['equipmentDate'])) {
					$_POST['equipmentDate'] = USToSQLDate($_POST['equipmentDate']);
				} else {
                    unset($_POST['equipmentDate']);
                }

                if (!empty($_POST['startDate'])) {
                    $_POST['startDate'] = USToSQLDate($_POST['startDate']);
                } else {
                    unset($_POST['startDate']);
                }

                if (!empty($_POST['endDate'])) {
                    $_POST['endDate'] = USToSQLDate($_POST['endDate']);
                } else {
                    unset($_POST['endDate']);
                }

				
				if ($id) {
                	$_POST['locationId'] = $id;
                	$_POST['locationType'] = 'STORE';

					if(intval($_POST['serviceId'])==0) {
						$this->VendorServices->addVendorService($_POST['vendorId'], $_POST);
					} else {
						$this->VendorServices->editVendorService($_POST['serviceId'], $_POST);
					}
					
					
					$result['result'] = $this->VendorServices->getByLocation($id, 'STORE');
					$result['error'] = "";
				} else {
					$contactsData = $this->session->userdata(get_class($this));

					$contactsData['services'][] = new DataHolder($_POST);
					$this->session->set_userdata(get_class($this), $contactsData);

					$result['result'] = $contactsData['services'];
				}
			} else {
				$result['error'] = validation_errors();
			}
		
						
		} else {
			if ($id) {
				$result['result'] = $this->VendorServices->getByLocation($id, 'STORE', $is_history);
				$result['error'] = "";
			} else {
				$contactsData = $this->session->userdata(get_class($this));
				$result['result'] = isset($contactsData['services']) ? $contactsData['services'] : array();
			}
		}
		
		foreach ($result['result'] as &$item) {
			$this->formatServiceTitle($item);
		}
		
		header('Content-type: application/json');
		echo json_encode($result);
	}
	
	public function DeleteService() {
		$id = (int)$this->uri->segment(4);
		
		$result = Array(
			'error'=>'',
			'result'=>Array()
		);
		
		$this->load->model('admin/VendorServices');
		
		if (($serviceId = $this->input->get_post('serviceId')) !== false) {

			if (!$id) {
				$servicesData = $this->session->userdata(get_class($this));
				$temp = $servicesData['services'];

				unset($temp[$serviceId]);
				$servicesData['services'] = array();
				
				foreach ($temp as $item) {
					$servicesData['services'][] = $item;
				}
				
				$this->session->set_userdata(get_class($this), $servicesData);
	
				$result['result'] = $servicesData['services'];
			} else {				
				$this->VendorServices->Delete($serviceId);
			}
		}
		
		if ($id > 0) {			
			$result['result'] = $this->VendorServices->getByLocation($id, 'STORE');//$this->StoreServices->getList($id);
			$result['error'] = "";			
		}
		foreach ($result['result'] as &$item) {
			$this->formatServiceTitle($item);
		}
		header('Content-type: application/json');
		echo json_encode($result);
	}
	
	public function Contacts() {
		$id = (int)$this->uri->segment(4);
		$this->load->model('admin/StoreContacts');
		
		$result = Array(
			'error'=>'',
			'result'=>	Array()
		);
		
		if ($_POST) {			
			$this->load->library('form_validation');
			
			$this->form_validation->set_rules('firstName', 'First Name', 'required|trim');
			$this->form_validation->set_rules('lastName', 'Lirst Name', 'trim');
			$this->form_validation->set_rules('title', 'Title', 'trim');
			$this->form_validation->set_rules('email', 'Email', 'trim|valid_email');
			$this->form_validation->set_rules('phone', 'Phone', 'trim');
			
			if ($this->form_validation->run() == true) {
				if ($id) {
					$this->StoreContacts->add($id, $_POST);
					$result['result'] = $this->StoreContacts->getList($id);
					$result['error'] = "";
				} else {
					$contactsData = $this->session->userdata(get_class($this));

					$contactsData['contacts'][] = new DataHolder($_POST);
					$this->session->set_userdata(get_class($this), $contactsData);

					$result['result'] = $contactsData['contacts'];
				}
			} else {
				$result['error'] = validation_errors();
			}
		
						
		} else {
			if ($id) {
				$result['result'] = $this->StoreContacts->getList($id);
				$result['error'] = "";
			} else {
				$contactsData = $this->session->userdata(get_class($this));
				$result['result'] = isset($contactsData['contacts']) ? $contactsData['contacts'] : array();
			}
		}
		
		header('Content-type: application/json');
		echo json_encode($result);
	}
	
	public function DeleteContact() {
		$id = (int)$this->uri->segment(4);
		
		$result = Array(
			'error'=>'',
			'result'=>Array()
		);
		
		$this->load->model('admin/StoreContacts');
		
		if (($contactId = $this->input->get_post('contactId')) !== false) {

			if (!$id) {
				$contactsData = $this->session->userdata(get_class($this));
				$contactsData['huj'] = 1;
				$temp = $contactsData['contacts'];

				unset($temp[$contactId]);
				$contactsData['contacts'] = array();
				
				foreach ($temp as $item) {
					$contactsData['contacts'][] = $item;
				}
				
				$this->session->set_userdata(get_class($this), $contactsData);
				
				$result['result'] = $contactsData['contacts'];
			} else {
				$this->StoreContacts->Delete($contactId);
			}
		}
		
		if ($id > 0) {
			$result['result'] = $this->StoreContacts->getList($id);
		}
		
		header('Content-type: application/json');
		echo json_encode($result);		
	}
}

/* End of file Stores.php */
/* Location: ./application/controllers/admin/Stores.php */