<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
include 'application/controllers/ReadOnly.php';

class DataHolder extends Placeholder {
	public function __construct($data) {
		foreach ($data as $k=>$v) {
			$this->{$k} = $v;
		}
	}	
}

class WasteInvoiceReadOnly extends ReadOnly {
	
	public function __construct() {
		parent::__construct();
		$this->assigns['data'] = new Placeholder();
		$this->load->library('form_validation');
		$this->form_validation->set_message('greater_than', "The %s is required.");
	}
	
	public function index() {
		$this->add();
	}
	
	public function ajaxList() {
		$this->load->model('admin/WasteInvoicesModel');
		
		header('Content-type: application/json');
		
		$sortColumn = null;
		$sortDir = null;
		
		if ($this->input->get('iSortingCols') > 0) {
			if ($this->input->get('bSortable_0')) {
				switch ($this->input->get('iSortCol_0')) {
					case 0:
						$sortColumn = 'WasteInvoices.id';
						break;
					case 1:
						$sortColumn = 'invoiceDate';
						break;
					case 2:
						$sortColumn = 'dateSent';
						break;
					case 3:
						$sortColumn = 'locationName';
						break;
					case 4:
						$sortColumn = 'vendorName';
						break;
					case 5:
						$sortColumn = 'material';
						break;
					case 6:
						$sortColumn = 'quantity';
						break;
					case 7:
						$sortColumn = 'trashFee';
						break;
					case 8:
						$sortColumn = 'fee';
						break;
					case 9:
						$sortColumn = 'tax';
						break;
					case 10:
						$sortColumn = 'total';
						break;
					case 11:
						$sortColumn = 'WasteInvoices.status';
						break;			
				}
				
				if ($this->input->get('sSortDir_0') == 'asc') {
					$sortDir = 'ASC';
				} else {
					$sortDir = 'DESC';
				}
			}		
		}
		
		$data = $this->WasteInvoicesModel->getList($this->assigns['_companyId'], $this->input->get('iDisplayStart'), $this->input->get('iDisplayLength'), $this->input->get('sSearch'), $sortColumn, $sortDir);
		$ajaxData = array();
		$this->load->helper('dates');
		
		foreach ($data['data'] as $item) {
			$ajaxData[] = array(
				'DT_RowClass' => 'grade' . ($item->status == 'YES' ? 'A':'X'),			
				'<a title="Edit invoice #'.$item->id.'" href="'.base_url().'admin/WasteInvoice/AddEdit/'.$item->id.'">' . $item->id . '</a>',
				SQLToUSDate($item->invoiceDate),
				SQLToUSDate($item->dateSent),
				$item->locationName,
				$item->vendorName,
				(empty($item->material) ? '-':$item->material),
				(empty($item->quantity) ? '0':$item->quantity ),
				(empty($item->trashFee) ? '0':$item->trashFee),
				(empty($item->fee) ? '0':$item->fee),
				(empty($item->tax) ? '0':$item->tax),
				(empty($item->total) ? '0.00':$item->total),
				($item->status == 'YES' ? 'Complete':'Incomplete')
			);
		}
		
		echo json_encode(array(
			'aaData' => $ajaxData,
			'iTotalRecords' => $data['records'],
			'iTotalDisplayRecords' => $data['records']
		));
	}
	
	public function delete($id) {
		$id = (int)$id;
		
		$this->load->model('admin/WasteInvoicesModel');
		
		if ($this->WasteInvoicesModel->deleteById($id)) {
			$this->session->set_flashdata('info', 'Hauler Invoice #'.$id.' has been successfully deleted.');
		} else {
			$this->session->set_flashdata('info', 'Hauler Invoice  #'.$id.' was not found!');
		}
		
		redirect('admin/WasteInvoice/history');
	}
	
	public function history() {
		$this->load->view('admin/waste_invoice_read_only/waste_invoice_list', $this->assigns);
	}
	
	public function getScheduledServices() {
		$locationId = $this->input->get('locationId');
		$vendorId = $this->input->get('vendorId');
		$locationType = $this->input->get('locationType');
		
		$data = new DataHolder(array());
		
		if ($locationId && $vendorId) {
			$this->load->model('admin/VendorServices');
			$list = $this->VendorServices->getByLocationAndVendor($locationId, $locationType, $vendorId);
			
			if (!empty($list)) {
				$data = new DataHolder($list);
				
				$this->load->model('admin/Containers');
				$this->assigns['containers'] = $this->Containers->getListForSelect($this->assigns['_companyId']);

			}
		}
		
		$this->assigns['data'] = $data;
		$this->load->view('admin/waste_invoice_read_only/scheduled_services', $this->assigns);	
	}
	
	public function services($skipTemplate = false) {
		//$this->session->set_userdata(get_class($this), $data);
		$index = $this->input->get('index');
		$type = $this->input->get('type');

		$this->loadOptions();
		
	
		$data = $this->session->userdata(get_class($this));

		if (!is_object($data)) {
			$data = new DataHolder(array());
		}
		
		if (!property_exists($data, 'services')) {
			$data->services = array();
		}
		
		if (!property_exists($data, 'fees')) {
			$data->fees = array();
		}
		
		$form = false;
		$idx = $this->input->post('idx');
		
		if ($_POST) {
			$this->load->library('form_validation');
			$this->form_validation->set_message('greater_than', "The %s field is required.");
			
			if ($type == 'service') {
				
				if ($idx !== false) {
					$temp = $data->services;
					if (isset($temp[$idx])) {
						unset($temp[$idx]);
						
						$data->services = array();
						
						foreach ($temp as $item) {
							$data->services[] = $item;	
						}
					}
				} else {
					$this->form_validation->set_rules('serviceTypeId', 'Service', 'required|greater_than[0]|trim');
					$this->form_validation->set_rules('category', 'For', 'required|trim');
					$this->form_validation->set_rules('containerId', 'Container', 'required|greater_than[0]|trim');
					$this->form_validation->set_rules('materialId', 'Material', 'required|greater_than[0]|trim');
					$this->form_validation->set_rules('rate', 'Rate', 'required|numeric|trim');
					
					if ($this->form_validation->run() == true)  {
						$temp = new DataHolder($_POST);
						
						if (!$temp->quantity) {
							$temp->quantity = 1;
						}
						
						if ($temp->id > 0) {
							if (!array_key_exists($temp->id, $data->services)) {
								$data->services[] = $temp;	
							}
						} else {
							$data->services[] = $temp;
						}
						
						$form = 'service_form';
					}
				}
			} elseif ($type == 'fee') {
				if ($idx !== false) {
					$temp = $data->fees;
					if (isset($temp[$idx])) {
						unset($temp[$idx]);
						
						$data->fees = array();
						
						foreach ($temp as $item) {
							$data->fees[] = $item;	
						}
					}
				} else {
					$this->form_validation->set_rules('feeType', 'Fee type', 'required|greater_than[0]|trim');
					$this->form_validation->set_rules('feeAmount', 'Fee', 'required|numeric|trim');
					
					if ($this->form_validation->run() == true)  {
						$data->fees[] = new DataHolder($_POST);
						$form = 'fee_form';
					}
				}
			}
			
			
			$this->session->set_userdata(get_class($this), $data);
		}

		$this->assigns['data']->items = $data;
		$this->assigns['show_errors'] = 1;
		
		if (!$skipTemplate) {
			header('Content-type: application/json');
			$errors = validation_errors();
			
			echo json_encode(array(
				'html' => $this->load->view('admin/waste_invoice_read_only/waste_invoice_services', $this->assigns, true),
				'error' => !empty($errors),
				'form' => $form,
			));
		}
	}
	
	public function addEdit() {
		$this->add();
	}
	
	public function autocomplete_required($input) {
		if (empty($input)) {
			$this->form_validation->set_message('autocomplete_required', 'The %s field must be autocompleted!');
			return false;
		}
		
		return true;
	}
	
	public function add() {

		$this->assigns['data'] = new Placeholder();
		$id = (int)$this->uri->segment(4);
		$services = $this->session->userdata(get_class($this));

		if ($_POST) {
			header("Location: " . base_url());
			$this->load->library('form_validation');

			$this->form_validation->set_rules('invoiceDate', 'Invoice date', 'date|required|trim');
			
			$this->form_validation->set_rules('vendorName', 'Vendor', 'required|trim');
			$this->form_validation->set_rules('vendorId', 'Vendor', 'callback_autocomplete_required|trim');
			
			$this->form_validation->set_rules('locationName', 'Location', 'required|trim');
			$this->form_validation->set_rules('locationId', 'Location', 'callback_autocomplete_required|trim');
			$this->form_validation->set_rules('locationType', '', 'trim');

			$this->form_validation->set_rules('internalNotes', '', 'trim');
			$this->form_validation->set_rules('dateSent', '', 'trim');
			$this->form_validation->set_rules('status', '', 'trim');
			
			if ($this->form_validation->run() == true) {
				if (array_key_exists('dateSent', $_POST)) {
					if (empty($_POST['dateSent'])) {
						unset($_POST['dateSent']);
					}
				}
				
				$this->load->model('admin/WasteInvoicesModel');
				$this->load->helper('dates');				
				$data = $_POST;
				
				$data['invoiceDate'] = USToSQLDate(@$data['invoiceDate']);
				if (isset($data['dateSent'])) {
					$data['dateSent'] = USToSQLDate($data['dateSent']);
				} else {
					$data['dateSent'] = null;		
				}
				$lastId = $id;
				
				if ($id) {
					$this->load->model('admin/WasteInvoiceFeesModel');
					$this->load->model('admin/WasteInvoiceServicesModel');
					$data['total'] = 0;
					
					//$fees = $this->WasteInvoiceFeesModel->getByInvoiceId($id);
					$fees = $services->fees;
					
					foreach ($fees as $fee) {
						if ($fee->waived != 1) {
							$data['total'] += $fee->feeAmount;
						}
					}
					
					//$services = $this->WasteInvoiceServicesModel->getByInvoiceId($id);
					
					foreach ($services->services as $service) {
						$data['total'] += (double)$service->rate * (int)$service->quantity;
					}
					
					$this->WasteInvoiceServicesModel->updateMulti($id, $services->services);
					$this->WasteInvoiceFeesModel->updateMulti($lastId, $services->fees);
					
					$this->WasteInvoicesModel->update($id, $this->assigns['_companyId'], $data);
					$this->session->set_flashdata('info', 'The Hauler invoice has been successfully updated.');	
				} else {
					$lastId = $this->WasteInvoicesModel->add($this->assigns['_companyId'], $data);
					
					$this->load->model('admin/WasteInvoiceFeesModel');
					$this->load->model('admin/WasteInvoiceServicesModel');

					$total = 0;
					if (is_object($services)) {
						$fees = $services->fees;
						
						foreach ($fees as $fee) {
							if ($fee->waived != 1) {
								$total += $fee->feeAmount;
							}
						}
						
						foreach ($services->services as $service) {
							$total += (double)$service->rate * (int)$service->quantity;
						}
					}
					
					$this->WasteInvoicesModel->update($lastId, $this->assigns['_companyId'], array('total' => $total));
					if (is_object($services)) {
						$this->WasteInvoiceServicesModel->addMulti($lastId, $services->services);
						$this->WasteInvoiceFeesModel->addMulti($lastId, $services->fees);
					}					
					$this->session->set_flashdata('info', 'The Waste invoice has been successfully added.');
				}
				
				
				$this->session->unset_userdata(get_class($this));
				if ($this->input->post('action') == 2) {
					redirect('admin/WasteInvoice/AddEdit');	
				} else {
					redirect('admin/WasteInvoice/AddEdit/' . $lastId);
				}
				
			}
			
			$this->assigns['errors'] = validation_errors();
			
		} else {
			$this->session->unset_userdata(get_class($this));
			
			if ($id) {
				$this->load->model('admin/WasteInvoicesModel');
				$this->load->model('admin/WasteInvoiceFeesModel');
				$this->load->model('admin/WasteInvoiceServicesModel');
				$this->load->model('admin/VendorsModel');
				
				$this->load->helper('dates');
				
				$this->session->set_userdata(get_class($this), new DataHolder(array(
					'services' => $this->WasteInvoiceServicesModel->getByInvoiceId($id),
					'fees' => $this->WasteInvoiceFeesModel->getByInvoiceId($id)
				)));

				
				$this->assigns['data'] = $this->WasteInvoicesModel->getById($this->assigns['_companyId'], $id);
				$this->assigns['data']->invoiceDate = SQLToUSDate($this->assigns['data']->invoiceDate);
				$this->assigns['data']->dateSent = SQLToUSDate($this->assigns['data']->dateSent);
				
				$this->assigns['data']->vendor = $this->VendorsModel->getById($this->assigns['data']->vendorId, $this->assigns['_companyId'])->name;
				
				if ($this->assigns['data']->locationType == 'DC') {
					//DC
					$this->load->model('admin/DistributionCentersModel');
					$this->assigns['data']->locationName = @$this->DistributionCentersModel->getById($this->assigns['_companyId'], $this->assigns['data']->locationId)->name;						
				} else {
					//store
					$this->load->model('admin/StoresModel');
					$this->assigns['data']->locationName = @$this->StoresModel->getById($this->assigns['_companyId'], $this->assigns['data']->locationId)->location;
				}
			}
		}
		
		
		$this->services(true);
		
		$this->load->view('admin/waste_invoice_read_only/waste_invoice_add', $this->assigns);
	}
	
	private function loadOptions() {
		$this->load->model('MaterialsModel');
//		
		$this->assigns['data']->materialOptions = $this->MaterialsModel->getList($this->assigns['_companyId'], 2);

		$this->load->model('admin/Containers');
		$this->assigns['data']->containerOptions = $this->Containers->getListForSelect($this->assigns['_companyId']);
		
		$this->assigns['data']->serviceTypeOptions = array(
			0 => '- Please select -',
			1 => 'Normal',
			2 => 'Temporary',
			3 => 'Extra',
		);
		
		$this->assigns['data']->unitOptions = array(
			0 => '- Please select -',
			1 => 'Tons',
			2 => 'Lbs',
			3 => 'Bales',
			4 => 'Bulbs',
			5 => 'Boxes',
			6 => 'Units',
		);
		
		$this->assigns['data']->feeOptions = array(
			0 => '- Please select -',
			1 => 'Freight Charge',
			2 => 'Fuel Charge',
			3 => 'Stop Charge',
			4 => 'Tax',
			5 => 'Other',
			6 => 'Repair',
		);
		
		$this->assigns['data']->statusOptions = array(
			'NO' => 'No',
			'YES' => 'Yes',
		);
		
		$this->load->model('admin/VendorServices');
		
		$this->assigns['data']->ServicesOptions = $this->VendorServices->getListForSelect($this->assigns['_companyId']);
	}

	public function autocompleteVendor() {
		$this->load->model('admin/Autocomplete');		
		
		header("Content-Type: application/json");
		echo json_encode($this->Autocomplete->vendor($this->input->get('term'), $this->assigns['_companyId']));
	}
}


/* End of file WasteInvoice.php */
/* Location: ./application/controllers/admin/WasteInvoice.php */