<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
include 'application/controllers/ReadOnly.php';

class DataHolder extends Placeholder {
	public function __construct($data) {
		foreach ($data as $k=>$v) {
			$this->{$k} = $v;
		}
	}	
}

class RecyclingInvoiceReadOnly extends ReadOnly {
	
	public function __construct() {
		parent::__construct();
		
		$this->load->library('form_validation');
		$this->form_validation->set_message('greater_than', "The %s is required.");
	}
	
	function index() {
		$this->addEdit();
	}
	
	public function delete($id) {
		$id = (int)$id;
		
		$this->load->model('admin/RecyclingInvoicesModel');
		
		if ($this->RecyclingInvoicesModel->deleteById($id)) {
			$this->session->set_flashdata('info', 'Recycling Purchase Order #'.$id.' has been successfully deleted.');
		} else {
			$this->session->set_flashdata('info', 'Recycling Purchase Order #'.$id.' was not found!');
		}
		
		redirect('admin/RecyclingInvoice/history');
	}
	
	public function ajaxList() {
		$this->load->model('admin/RecyclingInvoicesModel');
		
		header('Content-type: application/json');
		
		$sortColumn = null;
		$sortDir = null;
		
		if ($this->input->get('iSortingCols') > 0) {
			if ($this->input->get('bSortable_0')) {
				switch ($this->input->get('iSortCol_0')) {
					case 0:
						$sortColumn = 'invoiceDate';
						break;
					case 1:
						$sortColumn = 'poNumber';
						break;
					case 2:
						$sortColumn = 'invoiceDate';
						break;
					case 3:
						$sortColumn = 'dateSent';
						break;
					case 4:
						$sortColumn = 'location';
						break;
					case 5:
						$sortColumn = 'vendor';
						break;
					case 6:
						$sortColumn = 'material';
						break;
					case 7:
						$sortColumn = 'quantity';
						break;
					case 8:
						$sortColumn = 'total';
						break;
					case 10:
						$sortColumn = 'status';
						break;			
				}
				
				if ($this->input->get('sSortDir_0') == 'asc') {
					$sortDir = 'ASC';
				} else {
					$sortDir = 'DESC';
				}
			}		
		}
		
		$data = $this->RecyclingInvoicesModel->getList($this->assigns['_companyId'], $this->input->get('iDisplayStart'), $this->input->get('iDisplayLength'), $this->input->get('sSearch'), $sortColumn, $sortDir);
		$ajaxData = array();
		$this->load->helper('dates');
		
		foreach ($data['data'] as $item) {
			$ajaxData[] = array(
				'DT_RowClass' => 'grade' . ($item->status == 'YES' ? 'A':'X'),			
				'<a title="Edit invoice #'.$item->id.'" href="'.base_url().'admin/RecyclingInvoice/AddEdit/'.$item->id.'">' . SQLToUSDate($item->invoiceDate) . '</a>',
				'<a title="Edit invoice #'.$item->id.'" href="'.base_url().'admin/RecyclingInvoice/AddEdit/'.$item->id.'">' . $item->poNumber . '</a>',
				SQLToUSDate($item->invoiceDate),
				SQLToUSDate($item->dateSent),
				$item->location,
				$item->vendor,
				(empty($item->material) ? '-':$item->material),
				(empty($item->quantity) ? '0':$item->quantity ),
				(empty($item->total) ? '0.00':$item->total),
				'-',
				($item->status == 'YES' ? 'Complete':'Incomplete')
			);
		}
		
		echo json_encode(array(
			'aaData' => $ajaxData,
			'iTotalRecords' => $data['records'],
			'iTotalDisplayRecords' => $data['records']
		));
	}
	
	public function history() {
		$this->load->view('admin/recycling_invoice_read_only/recycling_invoices_list', $this->assigns);
	}
	
	public function autocomplete_required($input) {
		if (empty($input)) {
			$this->form_validation->set_message('autocomplete_required', 'The %s field must be autocompleted!');
			return false;
		}
		
		return true;
	}
	
	public function addEdit() {
		$this->assigns['data'] = new Placeholder();
		$id = (int)$this->uri->segment(4);
		
		if ($_POST) {
			header("Location: " . base_url());
			$this->load->library('form_validation');

			$this->form_validation->set_rules('vendor', 'Vendor', 'required|trim');
			$this->form_validation->set_rules('vendorId', 'Vendor', 'callback_autocomplete_required|trim');
			
			$this->form_validation->set_rules('locationName', 'Location', 'required|trim');
			$this->form_validation->set_rules('locationId', 'Location', 'callback_autocomplete_required|trim');
			$this->form_validation->set_rules('locationType', '', 'trim');
			
			/*
			$this->form_validation->set_rules('poDate', 'PO Date', 'required|date|trim');
			$this->form_validation->set_rules('poNumber', 'PO Number', 'required|trim');
			$this->form_validation->set_rules('trailerNumber', 'Trailer Number', 'required|date|trim');

			$this->form_validation->set_rules('internalNotes', '', 'trim');
			$this->form_validation->set_rules('dateSent', '', 'trim');
			$this->form_validation->set_rules('status', '', 'trim');
			*/
			
			if ($this->form_validation->run() == true) {
				$this->load->model('admin/RecyclingInvoicesModel');
				$this->load->helper('dates');
				
				$data = $_POST;
				$data['invoiceDate'] = USToSQLDate($data['invoiceDate']);
				//$data['poDate'] = USToSQLDate($data['poDate']);
				//$data['dateSent'] = USToSQLDate($data['dateSent']);
				$lastId = $id;
				
				if ($id) {
					$this->load->model('admin/RecyclingInvoicesFeesModel');
					$this->load->model('admin/RecyclingInvoicesMaterialsModel');
					$data['total'] = 0;
					
					$fees = $this->RecyclingInvoicesFeesModel->getByInvoiceId($id);
					
					foreach ($fees as $fee) {
						$data['total'] += $fee->feeAmount;
					}
					
					$materials = $this->RecyclingInvoicesMaterialsModel->getByInvoiceId($id);
					
					foreach ($materials as $material) {
						$data['total'] += $material->pricePerUnit * $material->quantity;
					}
					
					$this->RecyclingInvoicesModel->update($id, $this->assigns['_companyId'], $data);
					$this->session->set_flashdata('info', 'The Recycling invoice has been successfully updated.');	
				} else {
					$data['invoiceDate'] = date('Y-m-d');
					$lastId = $this->RecyclingInvoicesModel->add($this->assigns['_companyId'], $data);
					
					$this->load->model('admin/RecyclingInvoicesFeesModel');
					$this->load->model('admin/RecyclingInvoicesMaterialsModel');
					$this->load->model('admin/RecyclingInvoicesInfoModel');
					
					$data = $this->session->userdata(get_class($this));
					$total = 0;
					
					/*
					if (isset($data['fees'])) {
						foreach ($data['fees'] as $fee) {
							$this->RecyclingInvoicesFeesModel->add($lastId, (array)$fee);
							$total += $fee->feeAmount;
						}
					}
					
					if (isset($data['invoices'])) {
						foreach ($data['invoices'] as $invoice) {
							$invoice->invoiceDate = USToSQLDate($invoice->invoiceDate);
							$this->RecyclingInvoicesInfoModel->add($lastId, (array)$invoice);
						}
					}
					
					if (isset($data['materials'])) {
						foreach ($data['materials'] as $material) {
							//$material->materialDate = USToSQLDate($recycling->materialDate);
							$this->RecyclingInvoicesMaterialsModel->add($lastId, (array)$material);
							$total += $material->quantity * $material->pricePerUnit;
						}
					}
					*/
					
					$this->RecyclingInvoicesModel->update($lastId, $this->assigns['_companyId'], array('total' => $total));
					
					$this->session->set_flashdata('info', 'The Recycling invoice has been successfully added.');
				}
				
				
				$this->session->unset_userdata(get_class($this));
				if ($this->input->post('action') == 2) {
					redirect('admin/RecyclingInvoice/AddEdit');	
				} else {
					redirect('admin/RecyclingInvoice/AddEdit/' . $lastId);
				}
				
			}
			
			$this->assigns['errors'] = validation_errors();
			
		} else {
			$this->session->unset_userdata(get_class($this));
			
			if ($id) {
				
				$this->load->model('admin/RecyclingInvoicesModel');
				$this->load->model('admin/VendorsModel');
				
				$this->load->helper('dates');
				
				$this->assigns['data'] = $this->RecyclingInvoicesModel->getById($this->assigns['_companyId'], $id);
				
				if (empty($this->assigns['data'])) {
					$this->session->set_flashdata('info', 'Recycling Purchase Order #'.$id.' was not found!');
					redirect('admin/RecyclingInvoice/history');
				}
				
				$this->assigns['data']->poDate = SQLToUSDate($this->assigns['data']->poDate);
				$this->assigns['data']->dateSent = SQLToUSDate($this->assigns['data']->dateSent);
				$this->assigns['data']->invoiceDate = SQLToUSDate($this->assigns['data']->invoiceDate);
				
				$this->assigns['data']->vendor = $this->VendorsModel->getById($this->assigns['data']->vendorId, $this->assigns['_companyId'])->name;
				
				if ($this->assigns['data']->locationType == 'DC') {
					//DC
					$this->load->model('admin/DistributionCentersModel');
					$tempData = $this->DistributionCentersModel->getById($this->assigns['_companyId'], $this->assigns['data']->locationId);
					$this->assigns['data']->locationName = $tempData?$tempData->name:"";					
				} else {
					//store					
					if ($this->assigns['data']->locationId) {					
						$this->load->model('admin/StoresModel');
						$this->assigns['data']->locationName = $this->StoresModel->getById($this->assigns['_companyId'], $this->assigns['data']->locationId)->location;
					} else {
						$this->assigns['data']->locationName = '';
					}
				}
				
				$temp = $this->RecyclingInvoicesModel->getAllSavedMaterials($id);
				$temp[0] = '- Please select -';
				ksort($temp);

				$this->assigns["data"]->allMaterials = $temp;

				$this->assigns["data"]->iDate = $this->assigns["data"]->invoiceDate;
				$this->assigns["data"]->releaseNumber = $this->assigns["data"]->poNumber;

			} else {
				$this->assigns["data"]->allMaterials = array();
				$this->assigns["data"]->iDate = null;
				$this->assigns["data"]->releaseNumber = null;
			}
		}
		
		if($id) {
			$invoiceArray = unserialize($this->session->userdata("invoiceArray"));

			if(isset($invoiceArray[$id])) {
				$this->assigns["data"]->invoiceSum = $invoiceArray[$id];
			} else {
				$invoiceArray[$id] = $this->RecyclingInvoicesModel->getInvoiceFeeFromDB($id);
				$this->session->set_userdata('invoiceArray', serialize($invoiceArray));
				$this->assigns["data"]->invoiceSum = $invoiceArray[$id];
			}

			$orderArray = unserialize($this->session->userdata("orderArray"));
			
			if(isset($orderArray[$id])) {
				$this->updateOrderTotal($orderArray[$id]);
				
				$this->assigns["data"]->orderSum = $orderArray[$id];
			} else {
				$orderArray[$id] = $this->RecyclingInvoicesModel->getOrderFeeFromDB($id);
				
				$this->updateOrderTotal($orderArray[$id]);
				
				$this->session->set_userdata('orderArray', serialize($orderArray));
				$this->assigns["data"]->orderSum = $orderArray[$id];				
			}
		}
		$this->load->model('MaterialsModel');
		$this->assigns['data']->materialOptions = $this->MaterialsModel->getList($this->assigns['_companyId']);
		$this->assigns["data"]->allFees = $this->feeOptions();
		
		$this->assigns['data']->unitOptions = array(
			0 => '- Please select -',
			1 => 'Tons',
			2 => 'Lbs',
			3 => 'Bales',
			4 => 'Bulbs',
			5 => 'Boxes',
			6 => 'Units',
		);
		
		$this->assigns['data']->feeOptions = $this->feeOptions();
		
		$this->assigns['data']->statusOptions = array(
			'NO' => 'No',
			'YES' => 'Yes',
		);

		$this->load->view('admin/recycling_invoice_read_only/recycling_invoice_add', $this->assigns);
	}
	
	function feeOptions() {
		return array(
			0 => '- Please select -',
			1 => 'Freight Charge',
			2 => 'Fuel Charge',
			3 => 'Stop Charge',
			4 => 'Tax',
			5 => 'Other',
			6 => 'Repair',
		);
	}
	
	function SaveData() {
		$user = $this->session->userdata('USER');

		$id = (int)$this->uri->segment(4);
		
		if(! $id) {
			header("Location: " . base_url() . "admin/RecyclingInvoice/AddEdit");
		}
		if($this->input->post('action') == "cancel") {
			$this->session->set_userdata('invoiceArray', null);
			$this->session->set_userdata('orderArray', null);
			header("Location: " . base_url() . "admin/RecyclingInvoice/AddEdit/" . $id);
			die();
		} else {
			$this->load->model('admin/RecyclingInvoicesModel');
			$invoiceArray = unserialize($this->session->userdata("invoiceArray"));
			if(isset($invoiceArray[$id])) {
				$this->RecyclingInvoicesModel->saveInvoiceFee($id, $invoiceArray[$id]);
			}
			
			$orderArray = unserialize($this->session->userdata("orderArray"));
			if(isset($orderArray[$id])) {
				$this->RecyclingInvoicesModel->saveOrderFee($id, $orderArray[$id]);
			}
			$this->session->set_userdata('invoiceArray', null);
			$this->session->set_userdata('orderArray', null);
			
			$user = $this->session->userdata('USER');
			if($user->accessLevel == 'ADMIN') {
				$this->RecyclingInvoicesModel->saveNotes($id, $this->input->post('status'), $this->input->post('dateSent'), $this->input->post('internalNotes'));
			}
			$this->session->set_flashdata('info', 'The Recycling invoice has been successfully added.');
			redirect(base_url() . "admin/RecyclingInvoice/AddEdit/" . $id);
			die();
		}
	}
	
	
	
	public function calculateTotal(&$result) {
		$data = $this->session->userdata(get_class($this));
		$materialItems = isset($data['materials']) ? $data['materials'] : array();
		
		$feeItems = isset($data['fees']) ? $data['fees'] :  array();
		
		$total = 0;
		
		foreach ($materialItems as $item) {
			$total += (double)$item->pricePerUnit * (int)$item->quantity;
		}
		
		foreach ($feeItems as $item) {
			$total += (double)$item->feeAmount;
		}
		
		setlocale(LC_MONETARY, 'en_US');
		$result['total'] = money_format('%.2n', $total);
		//workaround for stripping dollar currecy sign from amount.
		//this problem does not exists on any server.
		$result['total'] = str_replace('$', '', $result['total']);
	}
	
	public function addFee() {
		$id = (int)$this->uri->segment(4);
		
		$result = array(
			'error' => '',
			'result' => array()
		);
		
		if ($_POST) {
			header("Location: " . base_url());
			$this->load->library('form_validation');
			
			$this->form_validation->set_rules('feeType', 'Fee type', 'numeric|required|greater_than[0]|trim');
			$this->form_validation->set_rules('feeAmount', 'Fee amount', 'numeric|required|trim');
			
			if ($this->form_validation->run() == true) {
				if ($id) {
					$this->load->model('admin/RecyclingInvoicesFeesModel');
					$this->RecyclingInvoicesFeesModel->add($id, $_POST);
				} else {
					$data = $this->session->userdata(get_class($this));
					$data['fees'][] = new DataHolder($_POST);
					$this->session->set_userdata(get_class($this), $data);
				}
			} else {
				$result['error'] = validation_errors(); 
			}
		}
		
			
		if ($id) {
			$this->load->model('admin/RecyclingInvoicesFeesModel');
			$result['result'] = $this->RecyclingInvoicesFeesModel->getList($id);

			$data = $this->session->userdata(get_class($this));
			$data['fees'] = $result['result'];
			$this->session->set_userdata(get_class($this), $data);
		} else {
			$data = $this->session->userdata(get_class($this));
			$result['result'] = isset($data['fees']) ? $data['fees'] : array();
		}
		
		$this->calculateTotal($result);
		header('Content-type: application/json');
		echo json_encode($result);
	}
	
	
	public function deleteFee() {
		$result = array(
			'error' => '',
			'result' => array()
		);
		
		if ($_POST) {
			header("Location: " . base_url());
			
			$id = (int)$this->uri->segment(4);
			$feeId = (int)$this->input->post('feeId');
			
			if ($id > 0) {
				$this->load->model('admin/RecyclingInvoicesFeesModel');
				$this->RecyclingInvoicesFeesModel->delete($feeId);
			} else {
				$data = $this->session->userdata(get_class($this));
				$temp = $data['fees'];
				$data['fees'] = array();

				unset($temp[$feeId]);
				
				
				foreach ($temp as $item) {
					$data['fees'][] = $item;
				}
				
				$this->session->set_userdata(get_class($this), $data);	
			}
		}
		
		if ($id) {
			$this->load->model('admin/RecyclingInvoicesFeesModel');
			$result['result'] = $this->RecyclingInvoicesFeesModel->getList($id);

			$data = $this->session->userdata(get_class($this));
			$data['fees'] = $result['result'];
			$this->session->set_userdata(get_class($this), $data);
		} else {
			$data = $this->session->userdata(get_class($this));
			$result['result'] = isset($data['fees']) ? $data['fees'] : array();
		}
		
		$this->calculateTotal($result);
		header('Content-type: application/json');
		echo json_encode($result);
	}
	
	public function addInvoice() {
		$id = (int)$this->uri->segment(4);
		
		$result = array(
			'error' => '',
			'result' => array()
		);
		
		if ($_POST) {
			header("Location: " . base_url());
			$this->load->library('form_validation');
			
			$this->form_validation->set_rules('invoiceDate', 'Invoice Date', 'date|required|trim');
			$this->form_validation->set_rules('invoiceNumber', 'Invoice number', 'required|trim');
			$this->form_validation->set_rules('pricePerTon', 'Price per ton', 'numeric|required|trim');
			
			if ($this->form_validation->run() == true) {
				if ($id) {
					$this->load->model('admin/RecyclingInvoicesInfoModel');
					$this->load->helper('dates');
					
					$data = $_POST;
					$data['invoiceDate'] = USToSQLDate($data['invoiceDate']);
					
					$this->RecyclingInvoicesInfoModel->add($id, $data);
				} else {
					$data = $this->session->userdata(get_class($this));
					$data['invoices'][] = new DataHolder($_POST);
					$this->session->set_userdata(get_class($this), $data);
				}
			} else {
				$result['error'] = validation_errors(); 
			}
		}
		
			
		if ($id) {
			$this->load->model('admin/RecyclingInvoicesInfoModel');
			$result['result'] = $this->RecyclingInvoicesInfoModel->getList($id);
			$this->load->helper('dates');
			
			foreach ($result['result'] as &$invoice) {
				$invoice->invoiceDate = SQLToUSDate($invoice->invoiceDate); 
			}
			
			$data = $this->session->userdata(get_class($this));
			$data['invoices'] = $result['result'];
			$this->session->set_userdata(get_class($this), $data);
		} else {
			$data = $this->session->userdata(get_class($this));
			$result['result'] = isset($data['invoices']) ? $data['invoices'] : array();
		}
		
		$this->calculateTotal($result);
		header('Content-type: application/json');
		echo json_encode($result);
	}
	
	
	public function deleteInvoice() {
		$result = array(
			'error' => '',
			'result' => array()
		);
		
		if ($_POST) {
			header("Location: " . base_url());
			$id = (int)$this->uri->segment(4);
			$invoiceId = (int)$this->input->post('invoiceId');
			
			if ($id > 0) {
				$this->load->model('admin/RecyclingInvoicesInfoModel');
				$this->RecyclingInvoicesInfoModel->delete($invoiceId);
				
			} else {
				$data = $this->session->userdata(get_class($this));
				$temp = $data['invoices'];
				$data['invoices'] = array();

				unset($temp[$invoiceId]);
				
				
				foreach ($temp as $item) {
					$data['invoices'][] = $item;
				}
				
				$this->session->set_userdata(get_class($this), $data);	
			}
		}
		
		if ($id) {
			$this->load->model('admin/RecyclingInvoicesInfoModel');
			$result['result'] = $this->RecyclingInvoicesInfoModel->getList($id);
			
			$this->load->helper('dates');
			
			foreach ($result['result'] as &$invoice) {
				$invoice->invoiceDate = SQLToUSDate($invoice->invoiceDate); 
			}
			
			$data = $this->session->userdata(get_class($this));
			$data['invoices'] = $result['result'];
			$this->session->set_userdata(get_class($this), $data);
		} else {
			$data = $this->session->userdata(get_class($this));
			$result['result'] = isset($data['invoices']) ? $data['invoices'] : array();
		}
		
		$this->calculateTotal($result);
		header('Content-type: application/json');
		echo json_encode($result);
	}
	
	
	public function addMaterial() {
		$id = (int)$this->uri->segment(4);
		
		$result = array(
			'error' => '',
			'result' => array()
		);
		
		if ($_POST) {
			$this->load->library('form_validation');
			header("Location: " . base_url());
			$this->form_validation->set_rules('quantity', 'Quantity', 'numeric|required|trim');
			$this->form_validation->set_rules('pricePerUnit', 'Price per unit', 'numeric|required|trim');
			$this->form_validation->set_rules('materialId', 'Material','numeric|required|greater_than[0]|trim');
			$this->form_validation->set_rules('unit', 'Unit','numeric|required|greater_than[0]|trim');
			
			if ($this->form_validation->run() == true) {
				if ($id) {
					$this->load->model('admin/RecyclingInvoicesMaterialsModel');				
					$this->RecyclingInvoicesMaterialsModel->add($id, $_POST);
					
				} else {
					$data = $this->session->userdata(get_class($this));
					$data['materials'][] = new DataHolder($_POST);
					$this->session->set_userdata(get_class($this), $data);
				}
			} else {
				$result['error'] = validation_errors(); 
			}
		}
		
			
		if ($id) {
			$this->load->model('admin/RecyclingInvoicesMaterialsModel');
			$result['result'] = $this->RecyclingInvoicesMaterialsModel->getList($id);

			$data = $this->session->userdata(get_class($this));
			$data['materials'] = $result['result'];
			$this->session->set_userdata(get_class($this), $data);
		} else {
			$data = $this->session->userdata(get_class($this));
			$result['result'] = isset($data['materials']) ? $data['materials'] : array();
		}
		
		$this->calculateTotal($result);
		header('Content-type: application/json');
		echo json_encode($result);
	}
	
	
	public function deleteMaterial() {
		$result = array(
			'error' => '',
			'result' => array()
		);
		
		if ($_POST) {
			header("Location: " . base_url());
			$id = (int)$this->uri->segment(4);
			$materialId = (int)$this->input->post('materialId');
			
			if ($id > 0) {
				$this->load->model('admin/RecyclingInvoicesMaterialsModel');
				$this->RecyclingInvoicesMaterialsModel->delete($materialId);
				
			} else {
				$data = $this->session->userdata(get_class($this));
				$temp = $data['materials'];
				$data['materials'] = array();

				unset($temp[$invoiceId]);
				
				
				foreach ($temp as $item) {
					$data['materials'][] = $item;
				}
				
				$this->session->set_userdata(get_class($this), $data);	
			}
		}
		
		if ($id) {
			$this->load->model('admin/RecyclingInvoicesMaterialsModel');
			$result['result'] = $this->RecyclingInvoicesMaterialsModel->getList($id);
			
			$data = $this->session->userdata(get_class($this));
			$data['materials'] = $result['result'];
			$this->session->set_userdata(get_class($this), $data);
		} else {
			$data = $this->session->userdata(get_class($this));
			$result['result'] = isset($data['materials']) ? $data['materials'] : array();
		}
		
		$this->calculateTotal($result);
		header('Content-type: application/json');
		echo json_encode($result);
	}
	
	public function autocompleteVendor() {
		$this->load->model('admin/Autocomplete');		
		
		header("Content-Type: application/json");
		echo json_encode($this->Autocomplete->vendor($this->input->get('term'), $this->assigns['_companyId']));
	}
	
	public function addMaterialUnit() {
		$this->load->model('admin/RecyclingInvoicesModel');
		if($this->input->post('materialId') && $this->input->post("unitId")) {
			$this->RecyclingInvoicesModel->addMaterialUnit((int)$this->uri->segment(4), $this->input->post('materialId'), $this->input->post("unitId"));
		}
	}

	public function addInvoiceMaterial() {
		
		$this->load->model('admin/RecyclingInvoicesModel');
		$id = (int)$this->uri->segment(4);

		$invoiceArray = unserialize($this->session->userdata("invoiceArray"));
		
		$invoiceArray[$id]["materials"][] = array("release"=> $this->input->post('poNumber'),
											 "date"=> $this->input->post('date'),
											 "materialId"=> $this->input->post('materialId'),
											 "quantity"=> (int) $this->input->post('qVal'),
											 "pricePerUnit" => (float)$this->input->post('ppu'));
		
		
		
		if(isset($invoiceArray[$id]["allPrice"])) {
			$invoiceArray[$id]["allPrice"] += ($this->input->post('qVal')*$this->input->post('ppu'));
		} else {
			$invoiceArray[$id]["allPrice"] = $this->input->post('qVal')*$this->input->post('ppu');
		}
		$this->assigns["data"] = new DataHolder(array());
		$this->assigns["data"]->allMaterials = $this->RecyclingInvoicesModel->getAllSavedMaterials($id);

		$this->assigns["data"]->invoiceSum = $invoiceArray[$id];
		$this->assigns["data"]->allFees = $this->feeOptions();
		$this->session->set_userdata('invoiceArray', serialize($invoiceArray));
		$this->load->view('admin/recycling_invoice_read_only/feesList', $this->assigns);
		
	}
	
	public function addInvoiceFee() {
		
		$this->load->model('admin/RecyclingInvoicesModel');
		$id = (int)$this->uri->segment(4);
		
		$invoiceArray = unserialize($this->session->userdata("invoiceArray"));
		
		$invoiceArray[$id]["fees"][] = array("release"=> $this->input->post('poNumber'),
				"date"=> $this->input->post('date'),
				"fee"=> (float)$this->input->post('fee'),
				"feeType"=> $this->input->post('feeType'),
				"waived" => $this->input->post('waived'));
		
		
		if(! $this->input->post('waived')) {
			if(isset($invoiceArray[$id]["allPrice"])) {
				$invoiceArray[$id]["allPrice"] += ($this->input->post('fee'));
			} else {
				$invoiceArray[$id]["allPrice"] = $this->input->post('fee');
			}
		}
		$this->assigns["data"] = new DataHolder(array());
		$this->assigns["data"]->allMaterials = $this->RecyclingInvoicesModel->getAllSavedMaterials($id);
		$this->assigns["data"]->allFees = $this->feeOptions();
		$this->assigns["data"]->invoiceSum = $invoiceArray[$id];
		$this->session->set_userdata('invoiceArray', serialize($invoiceArray));
		$this->load->view('admin/recycling_invoice_read_only/feesList', $this->assigns);		
	}
	
	function deleteInvoicePart() {
		$this->load->model('admin/RecyclingInvoicesModel');
		$id = (int)$this->uri->segment(4);
		
		$invoiceArray = unserialize($this->session->userdata("invoiceArray"));
		
		$temp = $invoiceArray[$id][$this->input->post('part')][$this->input->post('id')];
		
		if($this->input->post('part') == "materials") {
			$invoiceArray[$id]["allPrice"] -= ($temp["quantity"]*$temp["pricePerUnit"]);
		} else if (! $temp["waived"]){
			$invoiceArray[$id]["allPrice"] -= $temp["fee"];
		}
		
		unset($invoiceArray[$id][$this->input->post('part')][$this->input->post('id')]);
		
		$this->assigns["data"] = new DataHolder(array());
		$this->assigns["data"]->allMaterials = $this->RecyclingInvoicesModel->getAllSavedMaterials($id);
		$this->assigns["data"]->allFees = $this->feeOptions();
		$this->assigns["data"]->invoiceSum = $invoiceArray[$id];
		$this->session->set_userdata('invoiceArray', serialize($invoiceArray));
		$this->load->view('admin/recycling_invoice_read_only/feesList', $this->assigns);		
	}
	
	function deleteOrderPart() {
		$this->load->model('admin/RecyclingInvoicesModel');
		$id = (int)$this->uri->segment(4);
		
		$orderArray = unserialize($this->session->userdata("orderArray"));
		$invoiceArray = unserialize($this->session->userdata("invoiceArray"));
		
		$temp = $orderArray[$id][$this->input->post('part')][$this->input->post('id')];
		
	/*	if($this->input->post('part') == "materials") {
			if (!isset($temp["quantity"])) {
				$temp["quantity"] = 0;
			}
			
			$orderArray[$id]["allPrice"] -= ($temp["quantity"]*$temp["pricePerUnit"]);
		}
		*/
		$this->updateOrderTotal($orderArray[$id]);
		// else if (! $temp["waived"]){
		//	$orderArray[$id]["allPrice"] -= $temp["fee"];
		//}
		
		if ($orderArray[$id]["allPrice"] < 0) {
			$orderArray[$id]["allPrice"] = 0;
		}
		
		unset($orderArray[$id][$this->input->post('part')][$this->input->post('id')]);
		
		$this->assigns["data"] = new DataHolder(array());
		$this->assigns["data"]->allMaterials = $this->RecyclingInvoicesModel->getAllSavedMaterials($id);
		$this->assigns["data"]->allFees = $this->feeOptions();
		$this->assigns["data"]->orderSum = $orderArray[$id];

		if (isset($invoiceArray[$id])) {
			$this->assigns["data"]->invoiceSum = $invoiceArray[$id];
		}
		
		$this->session->set_userdata('orderArray', serialize($orderArray));
		$this->load->view('admin/recycling_invoice_read_only/orderList', $this->assigns);		
	}
	
	function orderHtml() {
		$this->load->model('admin/RecyclingInvoicesModel');
		$id = (int)$this->uri->segment(4);
		
		$orderArray = unserialize($this->session->userdata("orderArray"));
		$invoiceArray = unserialize($this->session->userdata("invoiceArray"));
		
		$this->updateOrderTotal($orderArray[$id]);
		
		$this->assigns["data"] = new DataHolder(array());
		$this->assigns["data"]->allMaterials = $this->RecyclingInvoicesModel->getAllSavedMaterials($id);
		$this->assigns["data"]->allFees = $this->feeOptions();
		$this->assigns["data"]->orderSum = $orderArray[$id];
		
		if (isset($invoiceArray[$id])) {
			$this->assigns["data"]->invoiceSum = $invoiceArray[$id];
		}
		
		$this->session->set_userdata('orderArray', serialize($orderArray));
		$this->load->view('admin/recycling_invoice_read_only/orderList', $this->assigns);	
	}
	
	function addOrderMaterial() {
		$this->load->model('admin/RecyclingInvoicesModel');
		$id = (int)$this->uri->segment(4);
		
		$orderArray = unserialize($this->session->userdata("orderArray"));
		$invoiceArray = unserialize($this->session->userdata("invoiceArray"));
		
		$qty = $this->getMaterialQtyFromInvoice($this->input->post('materialId'));	
		
		$orderArray[$id]["materials"][] = array (
			"release"=> $this->input->post('poNumber'),
			"date"=> $this->input->post('date'),
			"materialId"=> $this->input->post('materialId'),
			"pricePerUnit" => (float)$this->input->post('ppu'),
			"quantity" => $qty,
		);
		
		$this->updateOrderTotal($orderArray[$id]);
		$this->session->set_userdata('orderArray', serialize($orderArray));
		
		/*
		if(isset($orderArray[$id]["allPrice"])) {
			$orderArray[$id]["allPrice"] += $this->input->post('ppu') * $qty;
		} else {
			$orderArray[$id]["allPrice"] = $this->input->post('ppu') * $qty;
		}
		*/
		$this->assigns["data"] = new DataHolder(array());
		$this->assigns["data"]->allMaterials = $this->RecyclingInvoicesModel->getAllSavedMaterials($id);
		$this->assigns["data"]->allFees = $this->feeOptions();
		$this->assigns["data"]->orderSum = $orderArray[$id];
		if (isset($invoiceArray[$id])) {
			$this->assigns["data"]->invoiceSum = $invoiceArray[$id];
		}
		
		
		$this->load->view('admin/recycling_invoice_read_only/orderList', $this->assigns);		

	}
	
	function getMaterialQtyFromInvoice($materialId) {
		$invoiceArray = unserialize($this->session->userdata("invoiceArray"));
		
		foreach ($invoiceArray as $k=>$v) {
			foreach ($v['materials'] as $item) {
				if ($item['materialId'] == $materialId) {
					return $item['quantity'];
				}
			}
		}
		
		return 1;
	}
	
	function updateOrderTotal(&$orderArray) {
		$total = 0;
		
		if (isset($orderArray['materials'])) {
			foreach ($orderArray['materials'] as $k=>$v) {
					if (!isset($v['quantity']) || empty($v['quantity'])) {
						$v['quantity'] = 1;
					}
					
					$total += $v['pricePerUnit'] * $v['quantity'];
			}
		}

		$invoiceArray = unserialize($this->session->userdata("invoiceArray"));
		
		if (isset($invoiceArray[(int)$this->uri->segment(4)]['fees'])) {
			$temp = $invoiceArray[(int)$this->uri->segment(4)]['fees'];
			
			foreach ($temp as $v) {
				if ($v['waived'] == '0') {
					$total += $v['fee'];
				}
			}
		}
		
		$orderArray['allPrice'] = $total;
	}
	
}


/* End of file WasteInvoice.php */
/* Location: ./application/controllers/admin/RecyclingInvoice.php */