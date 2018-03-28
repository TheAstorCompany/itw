<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
include 'application/controllers/admin/Auth.php';

class DataHolder extends Placeholder {
	public function __construct($data) {
		foreach ($data as $k=>$v) {
			$this->{$k} = $v;
		}
	}	
}

class DistributionCenters extends Auth {
	private function formatServiceTitle(&$item) {
		$this->load->model('admin/Containers');
		$this->load->model('admin/ScheduleModel');
		
		$containerOptions = $this->Containers->getListForSelect($this->assigns['_companyId']);
        $scheduleOptions = $this->ScheduleModel->getListForSelect($this->assigns['_companyId']);
        
		$item->title = sprintf("<a href='%s'>%s</a> %d x %s • %s • %s for $%s <br /> <a href='%s'>%s</a> <a href='mailto:%s'>%s</a>", 
	    	base_url().'admin/Vendors/AddEdit/'.$item->vendorId,
		strtoupper($item->vendorName),		
	       	$item->quantity,
	       	$containerOptions[$item->containerId],
	       	$scheduleOptions[$item->schedule],
	       	$this->day2name($item->days),
	       	$item->rate,
		base_url().'admin/Vendors/AddEdit/'.$item->vendorId,
		$item->vendorPhone,
		$item->vendorEmail,
		$item->vendorEmail
	    );		
	}
	
	public function index() {
		redirect('admin/ManageCompany/Peoples');
	}
	
	public function addEdit() {
		$id = (int)$this->uri->segment(4);


        
		$this->load->model('admin/DistributionCentersModel');
		$this->load->model('States');
		$this->load->model('admin/DistributionCenterServiceDurations');
		$this->load->model('admin/DistributionCenterServicePurposes');
		$this->load->model('admin/Containers');
		$this->load->model('admin/ScheduleModel');
        $this->load->model('FeeTypeModel');
						
		if ($this->input->post('addupdate')) {
			
			$this->load->library('form_validation');
			
			$this->form_validation->set_rules('notes', 'Notes', 'trim|');
			$this->form_validation->set_rules('status', 'Status', 'trim');			
			$this->form_validation->set_rules('name', 'DC Name', 'required|trim|htmlspecialchars');
			$this->form_validation->set_rules('number', 'DC#', 'required|trim');
			$this->form_validation->set_rules('zip', 'Zip Code', 'required|trim');

			$this->form_validation->set_rules('addressLine1', '', 'required|trim');
			$this->form_validation->set_rules('addressLine2', '', 'trim');
			$this->form_validation->set_rules('city', '', 'required|trim');
			$this->form_validation->set_rules('stateId', '', 'required|trim');
			$this->form_validation->set_rules('phone', '', 'required|trim');
			$this->form_validation->set_rules('fax', '', 'trim');					
			
			if ($this->form_validation->run() == true) {
				//update, flash and redirect
				if (!$this->assigns['_isAdmin']) {
					unset($_POST['notes']);
					unset($_POST['status']);								
				}
				
				if ($id) {
					$this->DistributionCentersModel->update($id, $this->assigns['_companyId'], $_POST);
					$this->session->set_flashdata('info', 'The DC has been successfully updated.');					
				} else {
					$id = $this->DistributionCentersModel->add($this->assigns['_companyId'], $_POST);
					$this->session->set_flashdata('info', 'The DC has been successfully added.');
					$this->load->model('admin/VendorServices');
					//update users & services
					$contactsData = $this->session->userdata(get_class($this));
					$this->load->model('admin/DistributionCenterContacts');
					//$this->load->model('admin/DistributionCenterServices');
					if (isset($contactsData['contacts']) && count($contactsData['contacts']) > 0) {
						foreach ($contactsData['contacts'] as $contact) {
							$this->DistributionCenterContacts->add($id, (array)$contact);
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
							
							$service->locationId = $id;
							$service->locationType = 'DC';
							
							$this->VendorServices->addVendorService($service->vendorId, (array)$service);
						}
					}
				}
				
				redirect('admin/DistributionCenters/AddEdit/' . $id);
				return;
			}			
			//form did not pass validation
			$this->assigns['data'] = new Placeholder();
			
		} else {
			if ($id) {
				$this->assigns['data'] = $this->DistributionCentersModel->getById($this->assigns['_companyId'], $id);
				
				if (empty($this->assigns['data'])) {
					$this->session->set_flashdata('info', 'DC #' . $id . ' does not exists!');
					redirect('admin/DistributionCenters/AddEdit/');
					
					return;	
				}
				
				$this->load->model('admin/DistributionCenterContacts');
				$this->assigns['data']->DistributionCenterContacts = $this->DistributionCenterContacts->getList($id);
			
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
        
        $this->assigns['data']->statesOptions = $this->States->getListForSelect();
        $this->assigns['data']->durationOptions = $this->DistributionCenterServiceDurations->getListForSelect($this->assigns['_companyId']);
        $this->assigns['data']->purposeOptions = $this->DistributionCenterServicePurposes->getListForSelect($this->assigns['_companyId']);
        $this->assigns['data']->containerOptions = $this->Containers->getListForSelect($this->assigns['_companyId']);
        $this->assigns['data']->scheduleOptions = $this->ScheduleModel->getListForSelect($this->assigns['_companyId']);
        $this->assigns['data']->feeOptions = $this->loadFeesOptions();

        $this->load->model('MaterialsModel');
		$this->assigns['data']->materialOptions = $this->MaterialsModel->getList($this->assigns['_companyId'], 2);

		$this->load->view('admin/dc/dc_addedit', $this->assigns);				
	}
	
	
	public function ajaxList() {
		$this->load->model('admin/DistributionCentersModel');
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
						$sortColumn = 'name';
						break;
					case 2:
						$sortColumn = 'addressLine1';
						break;
					case 3:
						$sortColumn = 'city';
						break;
					case 4:
						$sortColumn = 'stateId';
						break;
					case 5:
						$sortColumn = 'zip';
						break;
					case 6:
						$sortColumn = 'waste';
						break;
					case 10:
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
		
		$data = $this->DistributionCentersModel->getList($this->assigns['_companyId'], $this->input->get('iDisplayStart'), $this->input->get('iDisplayLength'), $this->input->get('sSearch'), $sortColumn, $sortDir);
		$ajaxData = array();
		
		$editURL = '<a href="'.base_url().'admin/'.$this->assigns['_controller'].'/AddEdit/%d">%s</a>';
		
		foreach ($data['data'] as $item) {
			$diversion = 0;
			if ((double)$item->recycling > 0) {
				$diversion = (double)$item->recycling / ((double)$item->recycling + (double)$item->waste);
			}
			
			$ajaxData[] = array(
				'DT_RowClass' => 'grade' . ($item->status ? 'A':'X'),			
				sprintf($editURL, $item->id, $item->status ? 'Active':'Inactive'),
				$item->name,
				$item->addressLine1,
				$item->city,
				$item->state,
				$item->zip,
				(!$item->waste) ? '0':(double)$item->waste,
				(!$item->recycling) ? '0':(double)$item->recycling,
				number_format($diversion,3),
				'-',
				date("m/d/Y h:ia", strtotime($item->lastUpdated))
			);
		}
		
		echo json_encode(array(
			'aaData' => $ajaxData,
			'iTotalRecords' => $data['records'],
			'iTotalDisplayRecords' => $data['records']
		));
	}
	
	public function Delete($id) {
		$id = (int)$id;
		
		$this->load->model('admin/DistributionCentersModel');
		
		$this->DistributionCentersModel->Delete($id);
		$this->session->set_flashdata('info', 'DC was successfully deleted.');	
				
		redirect('admin/ManageCompany/DC/');
	}
	
	public function autocompleteServices() {
		$this->load->model('admin/Autocomplete');
		
		
		header("Content-Type: application/json");
		echo json_encode($this->Autocomplete->vendor($this->input->get('term'), $this->assigns['_companyId']));
	}
	
	public function getService() {
		$id = (int)$this->uri->segment(4);
		$data = array();
		
		if ($id) {
			$this->load->model('admin/VendorServices');
			
			$data = $this->VendorServices->getByLocation($id, 'DC');
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
	
	public function Services() {
		$id = (int)$this->uri->segment(4);
		$is_history = (bool)$this->uri->segment(5);
		
		$this->load->model('admin/DistributionCenterServices');
		
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
			$this->form_validation->set_rules('containerId', 'Container', 'greater_than[0]|trim');
			$this->form_validation->set_rules('materialId', 'Material', 'greater_than[0]|trim');
			$this->form_validation->set_rules('schedule', '', 'trim');
			$this->form_validation->set_rules('rate', '', 'trim');
			$this->form_validation->set_rules('date', '', 'trim');
			
			if ($this->form_validation->run() == true) {
				$this->load->helper('dates');
				
				if (isset($_POST['startDate'])) {
					$_POST['startDate'] = USToSQLDate($_POST['startDate']);
				}
				
				if (isset($_POST['endDate'])) {
					$_POST['endDate'] = USToSQLDate($_POST['endDate']);
				}
				
                if ($id) {
                	$this->load->model('admin/VendorServices');
                	$_POST['locationId'] = $id;
                	$_POST['locationType'] = 'DC';
					
					if(intval($_POST['serviceId'])==0) {
						$this->VendorServices->addVendorService($_POST['vendorId'], $_POST);
					} else {
						$this->VendorServices->editVendorService($_POST['serviceId'], $_POST);
					}	
					
					$result['result'] = $this->VendorServices->getByLocation($id, 'DC');
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
				$this->load->model('admin/VendorServices');
				$result['result'] = $this->VendorServices->getByLocation($id, 'DC', $is_history);//$this->DistributionCenterServices->getList($id);

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
			$result['result'] = $this->VendorServices->getByLocation($id, 'DC');//$this->DistributionCenterServices->getList($id);
		}
		
		foreach ($result['result'] as &$item) {
			$this->formatServiceTitle($item);
		}
		
		header('Content-type: application/json');
		echo json_encode($result);
	}

    public function ServiceContact() {
        $id = (int)$this->uri->segment(4);
        $data = array();

        if ($id) {
            $this->load->model('admin/DistributionCenterServiceContacts');

            $data = $this->DistributionCenterServiceContacts->getById($id);
        }

        header('Content-type: application/json');
        echo json_encode($data);
    }

    public function Contact() {
        $id = (int)$this->uri->segment(4);
        $data = array();

        if ($id) {
            $this->load->model('admin/DistributionCenterContacts');

            $data = $this->DistributionCenterContacts->getById($id);
        }

        header('Content-type: application/json');
        echo json_encode($data);
    }

	public function Contacts() {
		$id = (int)$this->uri->segment(4);
		$this->load->model('admin/DistributionCenterContacts');
		
		$result = Array(
			'error'=>'',
			'result'=>	Array()
		);
		
		if ($_POST) {			
			$this->load->library('form_validation');
			
			$this->form_validation->set_rules('firstName', 'First Name', 'required|trim|htmlspecialchars');
			$this->form_validation->set_rules('lastName', 'Lirst Name', 'trim|htmlspecialchars');
			$this->form_validation->set_rules('title', 'Title', 'trim|htmlspecialchars');
			$this->form_validation->set_rules('email', 'Email', 'trim|valid_email');
			$this->form_validation->set_rules('phone', 'Phone', 'trim');
            $this->form_validation->set_rules('notes', 'notes', 'trim');
			
			if ($this->form_validation->run() == true) {
				if ($id) {
                    if(intval($_POST['contactId'])>0) {
                        $this->DistributionCenterContacts->update($_POST);
                    } else {
                        $this->DistributionCenterContacts->add($id, $_POST);
                    }

					$result['result'] = $this->DistributionCenterContacts->getList($id);
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
				$result['result'] = $this->DistributionCenterContacts->getList($id);
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
		
		$this->load->model('admin/DistributionCenterContacts');
		
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
				$this->DistributionCenterContacts->Delete($contactId);
			}
		}
		
		if ($id > 0) {
			$result['result'] = $this->DistributionCenterContacts->getList($id);
		}
		
		header('Content-type: application/json');
		echo json_encode($result);		
	}

    public function ServiceContacts() {
        $id = (int)$this->uri->segment(4);
        $this->load->model('admin/DistributionCenterServiceContacts');

        $result = Array(
            'error'=>'',
            'result'=>	Array()
        );

        if ($_POST) {
            $this->load->library('form_validation');

            $this->form_validation->set_rules('firstName', 'First Name', 'required|trim|htmlspecialchars');
            $this->form_validation->set_rules('lastName', 'Lirst Name', 'trim|htmlspecialchars');
            $this->form_validation->set_rules('title', 'Title', 'trim|htmlspecialchars');
            $this->form_validation->set_rules('email', 'Email', 'trim|valid_email');
            $this->form_validation->set_rules('phone', 'Phone', 'trim');
            $this->form_validation->set_rules('companyName', 'Company Name', 'trim');
            $this->form_validation->set_rules('notes', 'notes', 'trim');

            if ($this->form_validation->run() == true) {
                if ($id) {
                    if(intval($_POST['serviceContactId'])>0) {
                        $this->DistributionCenterServiceContacts->update($_POST);
                    } else {
                        $this->DistributionCenterServiceContacts->add($id, $_POST);
                    }

                    $result['result'] = $this->DistributionCenterServiceContacts->getList($id);
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
                $result['result'] = $this->DistributionCenterServiceContacts->getList($id);
                $result['error'] = "";
            } else {
                $contactsData = $this->session->userdata(get_class($this));
                $result['result'] = isset($contactsData['contacts']) ? $contactsData['contacts'] : array();
            }
        }

        header('Content-type: application/json');
        echo json_encode($result);
    }

    public function DeleteServiceContact() {
        $id = (int)$this->uri->segment(4);

        $result = Array(
            'error'=>'',
            'result'=>Array()
        );

        $this->load->model('admin/DistributionCenterServiceContacts');

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
                $this->DistributionCenterServiceContacts->Delete($contactId);
            }
        }

        if ($id > 0) {
            $result['result'] = $this->DistributionCenterServiceContacts->getList($id);
        }

        header('Content-type: application/json');
        echo json_encode($result);
    }

    private function loadFeesOptions(){
        $this->load->model('MaterialFeeTypeModel');
        return $this->MaterialFeeTypeModel->get_all();
    }
}

/* End of file DistributionCenters.php */
/* Location: ./application/controllers/admin/DistributionCenters.php */