<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
include 'application/controllers/ReadOnly.php';

class DataHolder extends Placeholder {
	public function __construct($data) {
		foreach ($data as $k=>$v) {
			$this->{$k} = $v;
		}
	}	
}

class RecyclingChargesReadOnly extends ReadOnly {
	
	public function history() {
		$this->load->view('admin/recycling_charges_read_only/recycling_charges_list', $this->assigns);
	}
	
	public function delete($id) {
		$id = (int)$id;
		
		$this->load->model('admin/RecyclingChargesModel');
		
		if ($this->RecyclingChargesModel->Delete($id)) {
			$this->session->set_flashdata('info', 'Recycling Charges #'.$id.' has been successfully deleted.');
		} else {
			$this->session->set_flashdata('info', 'Recycling Charges  #'.$id.' was not found!');
		}
		
		redirect('admin/RecyclingCharges/history');
	}
	
	function index() {
		$this->addEdit();
	}
	
	public function ajaxList() {
		$this->load->model('admin/RecyclingChargesModel');
		
		header('Content-type: application/json');
		
		$sortColumn = null;
		$sortDir = null;
		
		if ($this->input->get('iSortingCols') > 0) {
			if ($this->input->get('bSortable_0')) {
				switch ($this->input->get('iSortCol_0')) {
					case 0:
						$sortColumn = 'invoiceNumber';
						break;
					case 1:
						$sortColumn = 'location';
						break;
					case 2:
						$sortColumn = 'vendor';
						break;
					case 3:
						$sortColumn = 'material';
						break;
					case 4:
						$sortColumn = 'quantity';
						break;
					case 5:
						$sortColumn = 'total';
						break;
					case 6:
						$sortColumn = 'rebate';
						break;
					case 7:
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
		
		$data = $this->RecyclingChargesModel->getList($this->assigns['_companyId'], $this->input->get('iDisplayStart'), $this->input->get('iDisplayLength'), $this->input->get('sSearch'), $sortColumn, $sortDir);
		$ajaxData = array();
		$this->load->helper('dates');
		
		foreach ($data['data'] as $item) {
			//$total = $item->total + $item->fees;
			$ajaxData[] = array(
				'DT_RowClass' => 'grade' . ($item->status == 'YES' ? 'A':'X'),			
				'<a title="Edit Recycling Charges #'.$item->id.'" href="'.base_url().'admin/RecyclingCharges/AddEdit/'.$item->id.'">&nbsp;' . $item->invoiceNumber . '</a>',
				$item->location,
				$item->vendor,
				(empty($item->material) ? '-':$item->material),
				(empty($item->quantity) ? '0':$item->quantity ),
				(empty($item->total) ? '0.00': number_format($item->total, 2)),
				(empty($item->rebate) ? '0.00': number_format($item->rebate, 2)),
				($item->status == 'YES' ? 'Complete':'Incomplete')
			);
		}
		
		echo json_encode(array(
			'aaData' => $ajaxData,
			'iTotalRecords' => $data['records'],
			'iTotalDisplayRecords' => $data['records']
		));
	}
	
	public function __construct() {
		parent::__construct();
		$this->assigns['data'] = new Placeholder();
		$this->load->library('form_validation');
		$this->form_validation->set_message('greater_than', "The %s is required.");
		$this->load->model('MaterialsModel');
		$this->assigns['data']->materialOptions = $this->MaterialsModel->getList($this->assigns['_companyId'], 3);

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
	}	
	
	public function autocomplete_required($input) {
		if (empty($input)) {
			$this->form_validation->set_message('autocomplete_required', 'The %s field must be autocompleted!');
			return false;
		}		
		return true;
	}
	
	public function addCharge() {
		if ('XMLHttpRequest' == @$_SERVER['HTTP_X_REQUESTED_WITH']) {
			$result = array('html'=>'', 'error'=>'');						 
			$this->assigns['charges'] = unserialize($this->session->userdata('charges'));
			if (!is_array($this->assigns['charges'])) {
				$this->assigns['charges'] = array();
			}
			$this->assigns['fees'] = unserialize($this->session->userdata('fees'));
			if (!is_array($this->assigns['fees'])) {
				$this->assigns['fees'] = array();
			}
			switch ($this->input->get_post('action')) {
				case 'delete':
					unset($this->assigns['charges'][$this->input->get_post('index')]);
					break;
				default:
					$this->load->library('form_validation');					
					$this->form_validation->set_message('greater_than', "The %s is required.");
					$this->form_validation->set_rules('materialId', 'Material','required|trim|greater_than[0]');		
					$this->form_validation->set_rules('quantity', 'Quantity','required|numeric|trim');
					$this->form_validation->set_rules('unitId', 'Unit','required|trim|greater_than[0]');
					$this->form_validation->set_rules('pricePerTon', 'Price Per Unit','numeric|required|trim');					
					if ($this->form_validation->run() == true) {
						$this->assigns['charges'][] = (object)$_POST;
					} else {
						$result['error'] = validation_errors();
					}
			}
			//total
			$this->assigns['total'] = $this->calculateTotal2();						
			//list
			$this->session->set_userdata('charges', serialize($this->assigns['charges']));
			ob_start();
			$this->load->view('admin/recycling_charges_read_only/charges_and_fees_ajax.php', $this->assigns);
			$result['html'] = ob_get_clean();
			header('content-type:application/json');
			echo json_encode($result);
		}
	}
	public function addFee() {
		if ('XMLHttpRequest' == @$_SERVER['HTTP_X_REQUESTED_WITH']) {
			$result = array('html'=>'', 'error'=>'');						 
			$this->assigns['charges'] = unserialize($this->session->userdata('charges'));
			if (!is_array($this->assigns['charges'])) {
				$this->assigns['charges'] = array();
			}
			$this->assigns['fees'] = unserialize($this->session->userdata('fees'));
			if (!is_array($this->assigns['fees'])) {
				$this->assigns['fees'] = array();
			}
			switch ($this->input->get_post('action')) {
				case 'delete':
					unset($this->assigns['fees'][$this->input->get_post('index')]);
					break;
				default:
					$this->load->library('form_validation');					
					$this->form_validation->set_message('greater_than', "The %s is required.");
					$this->form_validation->set_rules('feeType', 'Fee Type','required|trim|greater_than[0]');	
					$this->form_validation->set_rules('fee', 'Fee','numeric|required|trim');					
					if ($this->form_validation->run() == true) {
						$this->assigns['fees'][] = (object)$_POST;
					} else {
						$result['error'] = validation_errors();
					}
			}
			//total
			$this->assigns['total'] = $this->calculateTotal2();						
			//list
			$this->session->set_userdata('fees', serialize($this->assigns['fees']));
			ob_start();
			$this->load->view('admin/recycling_charges_read_only/charges_and_fees_ajax.php', $this->assigns);
			$result['html'] = ob_get_clean();
			header('content-type:application/json');
			echo json_encode($result);
		}
	}
	private function calculateTotal2() {
		$result = 0.0;
		if (isset($this->assigns['charges']) && is_array($this->assigns['charges'])) {
			foreach($this->assigns['charges'] as $charge) {
				$result += $charge->pricePerTon * $charge->quantity;
			}
		}
		if (isset($this->assigns['fees']) && is_array($this->assigns['fees'])) {
			foreach($this->assigns['fees'] as $fee) {
				if (!@$fee->waived) {
					$result += $fee->fee;
				}
			}
		}
		return $result;
	}
	public function addEdit() {
		$id = (int)$this->uri->segment(4);		
		if ($_POST) {
			header("Location: " . base_url());
			$this->load->library('form_validation');
			
			$this->form_validation->set_rules('locationName', 'location Name','required|trim');
			$this->form_validation->set_rules('locationId', 'Location ID', 'callback_autocomplete_required|numeric|trim');
			$this->form_validation->set_rules('locationType', 'Location type','trim');
			
			$this->form_validation->set_rules('date', 'Invoice date', 'date|required|trim');
			$this->form_validation->set_rules('vendorNumber', 'Vendor', 'required|trim');
			$this->form_validation->set_rules('invoiceNumber', 'Invoice Number', 'required|trim');
			$this->form_validation->set_rules('vendorId', 'Vendor', 'callback_autocomplete_required|trim');
			
			//$this->form_validation->set_rules('vendor', 'Vendor', 'required|trim');
			//$this->form_validation->set_rules('vendorLocation', 'Location', 'required|trim');
			
			$this->form_validation->set_rules('internalNotes', '', 'trim');
			$this->form_validation->set_rules('dateSent', '', 'trim');
			$this->form_validation->set_rules('status', '', 'trim');								
			if ($this->form_validation->run() == true) {
				$this->load->model('admin/RecyclingChargesModel');
				$this->load->helper('dates');
				
				$data = $_POST;
				//echo "<pre>";	print_r($data);		die();
				$data['date'] = USToSQLDate($data['date']);
				$data['dateSent'] = USToSQLDate($data['dateSent']);
				$lastId = $id;
				if ($id) {
					$this->RecyclingChargesModel->update($id, $this->assigns['_companyId'], $data);
					$this->session->set_flashdata('info', 'The Recycling charge has been successfully updated.');	
				} else {
					$lastId = $this->RecyclingChargesModel->add($this->assigns['_companyId'], $data);
					$this->session->set_flashdata('info', 'The Recycling charge has been successfully added.');
				}
				
				if (($fees = unserialize($this->session->userdata('fees'))) && count($fees) > 0) {					
					$this->load->model('admin/RecyclingChargesFeesModel', 'fees');
					$this->fees->AddUpdate($lastId, $fees);
					$this->session->set_userdata('fees', '');	
				}
				//charges
				if (($charges = unserialize($this->session->userdata('charges'))) && count($charges) > 0) {					
					$this->load->model('admin/RecyclingChargeItemsModel', 'charges');
					$this->charges->AddUpdate($lastId, $charges);
					$this->session->set_userdata('charges', '');	
				}
				$this->session->unset_userdata(get_class($this));
				if ($this->input->post('action') == 2) {
					redirect('admin/RecyclingCharges/AddEdit');	
				} else {
					redirect('admin/RecyclingCharges/AddEdit/' . $lastId);
				}
				
			} else {
				//tmp for refresh
				$this->assigns['charges'] = unserialize($this->session->userdata('charges'));
				if (!is_array($this->assigns['charges'])) {
					$this->assigns['charges'] = array();
				}
				$this->assigns['fees'] = unserialize($this->session->userdata('fees'));
				if (!is_array($this->assigns['fees'])) {
					$this->assigns['fees'] = array();
				}
				//total
				$this->assigns['total'] = $this->calculateTotal2();
			}			
			$this->assigns['errors'] = validation_errors();
			
		} else {
			$this->session->set_userdata('fees', '');
			$this->session->set_userdata('charges', '');
			$this->session->unset_userdata(get_class($this));			
			if ($id) {
				$this->load->model('admin/RecyclingChargesModel');
				$this->load->helper('dates');
				$data = (array)$this->RecyclingChargesModel->getById($this->assigns['_companyId'], $id);
				
				if (empty($data)) {
					$this->session->set_flashdata('info', 'Recycling Charges  #'.$id.' was not found!');
					redirect('admin/RecyclingCharges/history');
				}
				
				$this->assigns['data'] = (array)$this->assigns['data'];					
				$this->assigns['data'] = array_merge($this->assigns['data'], $data);
				$this->assigns['data'] = (object)$this->assigns['data'];
				$this->assigns['data']->date = SQLToUSDate($this->assigns['data']->date);                
				$this->assigns['data']->dateSent = SQLToUSDate($this->assigns['data']->dateSent);
				if (isset($this->assigns['data']->fees)) {
					$this->assigns['fees'] = $this->assigns['data']->fees;
					$this->session->set_userdata('fees', serialize($this->assigns['fees']));
				}
				if (isset($this->assigns['data']->charges)) {
					$this->assigns['charges'] = $this->assigns['data']->charges;
					$this->session->set_userdata('charges', serialize($this->assigns['charges']));
				}
			}
		}		
		$this->load->view('admin/recycling_charges_read_only/recycling_charges_add', $this->assigns);
	}
	
	public function calculateTotal(&$result) {
		$data = $this->session->userdata(get_class($this));
		$recyclingItems = isset($data['recyclings']) ? $data['recyclings'] : array();
		$feeItems = isset($data['fees']) ? $data['fees'] :  array();
		
		$total = 0;
		
		foreach ($recyclingItems as $item) {
			$total += (double)$item->pricePerTon;
		}
		
		foreach ($feeItems as $item) {
			$total += (double)$item->fee;
		}
		
		setlocale(LC_MONETARY, 'en_US');
		$result['total'] = money_format('%.2n', $total);
	}
	/*
	public function addFee() {
		$id = (int)$this->uri->segment(4);
		
		$result = array(
			'error' => '',
			'result' => array()
		);
		
		if ($_POST) {
			$this->load->library('form_validation');
			
			$this->form_validation->set_rules('feeType', 'Fee type', 'numeric|required|greater_than[0]|trim');
			$this->form_validation->set_rules('fee', 'Fee amount', 'numeric|required|trim');
			
			if ($this->form_validation->run() == true) {
				if ($id) {
					$this->load->model('admin/RecyclingChargesFeesModel');
					$this->RecyclingChargesFeesModel->add($id, $_POST);
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
			$this->load->model('admin/RecyclingChargesFeesModel');
			$result['result'] = $this->RecyclingChargesFeesModel->getList($id);
			
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
	*/
	public function addRecycling() {
		$id = (int)$this->uri->segment(4);
		
		$result = array(
			'error' => '',
			'result' => array()
		);
		
		$this->load->helper('dates');
		
        
		if ($_POST) {
			header("Location: " . base_url());
			$this->load->library('form_validation');
			
			$this->form_validation->set_rules('materialId', 'Material','numeric|required|greater_than[0]|trim');
			$this->form_validation->set_rules('unitId', 'Unit','numeric|required|greater_than[0]|trim');
			$this->form_validation->set_rules('materialDate', 'Date', 'date|required|trim');
			$this->form_validation->set_rules('pricePerTon', 'Price per ton', 'numeric|required|trim');
			
			
			if ($this->form_validation->run() == true) {
				if ($id) {
                    $data = $_POST;
					$data['materialDate'] = USToSQLDate($data['materialDate']);
					 
					$this->load->model('admin/RecyclingChargeItemsModel');
					$this->RecyclingChargeItemsModel->add($id, $data);
				} else {
                    
                    
                    
					$data = $this->session->userdata(get_class($this));
					$data['recyclings'][] = new DataHolder($_POST);
					$this->session->set_userdata(get_class($this), $data);
				}
			} else {
				$result['error'] = validation_errors(); 
			}
		}
		
			
		if ($id) {
			$this->load->model('admin/RecyclingChargeItemsModel');
			$result['result'] = $this->RecyclingChargeItemsModel->getList($id);
			
			foreach ($result['result'] as &$item) {
				$item->materialDate = SQLToUSDate($item->materialDate);
			} 
			
			$data = $this->session->userdata(get_class($this));
			$data['recyclings'] = $result['result'];
			$this->session->set_userdata(get_class($this), $data);
		} else {
			$data = $this->session->userdata(get_class($this));
			$result['result'] = isset($data['recyclings']) ? $data['recyclings'] : array();
		}
		
		$this->calculateTotal($result);
		header('Content-type: application/json');
		echo json_encode($result);
	}
	
	public function deleteRecycleItem() {
		$result = array(
			'error' => '',
			'result' => array()
		);
		
		if ($_POST) {
			header("Location: " . base_url());
			$id = (int)$this->uri->segment(4);
			$recyclingItemId = $this->input->post('recyclingItemId');
			
			if ($id) {
				$this->load->model('admin/RecyclingChargeItemsModel');
				$this->RecyclingChargeItemsModel->delete($id);
			} else {
				$data = $this->session->userdata(get_class($this));
				$temp = $data['recyclings'];
				$data['recyclings'] = array();
				
				unset($temp[$recyclingItemId]);
				
				
				foreach ($temp as $item) {
					$data['recyclings'][] = $item;
				}
				
				$this->session->set_userdata(get_class($this), $data);	
			}
		}
		
		if ($id) {
			$this->load->model('admin/RecyclingChargeItemsModel');
			$result['result'] = $this->RecyclingChargeItemsModel->getList($id);
			
			foreach ($result['result'] as &$item) {
				$item->materialDate = SQLToUSDate($item->materialDate);
			} 
			
			$data = $this->session->userdata(get_class($this));
			$data['recyclings'] = $result['result'];
			$this->session->set_userdata(get_class($this), $data);
		} else {
			$data = $this->session->userdata(get_class($this));
			$result['result'] = isset($data['recyclings']) ? $data['recyclings'] : array();
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
			
		} else {
			$data = $this->session->userdata(get_class($this));
			$result['result'] = isset($data['fees']) ? $data['fees'] : array();
		}
		
		$this->calculateTotal($result);
		header('Content-type: application/json');
		echo json_encode($result);
	}
	
	public function autocompleteVendorByLocation() {
		$this->load->model('admin/Autocomplete');
		
		
		header("Content-Type: application/json");
		echo json_encode($this->Autocomplete->vendorByLocation($this->input->get('term'), $this->assigns['_companyId']));
	}
	
	public function autocompleteVendorByNumber() {
		$this->load->model('admin/Autocomplete');
		header("Content-Type: application/json");
		echo json_encode($this->Autocomplete->vendorByNumber($this->input->get('term'), $this->assigns['_companyId']));
	}
	public function autocompleteVendor() {
		$this->load->model('admin/Autocomplete');
		header("Content-Type: application/json");
		echo json_encode($this->Autocomplete->vendorBySomething($this->input->get('term'), $this->assigns['_companyId']));
	}
	
}


/* End of file WasteInvoice.php */
/* Location: ./application/controllers/admin/RecyclingCharges.php */