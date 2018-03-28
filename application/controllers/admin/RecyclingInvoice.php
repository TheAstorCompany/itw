<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
include 'application/controllers/admin/Auth.php';

class DataHolder extends Placeholder {
	public function __construct($data) {
		foreach ($data as $k=>$v) {
			$this->{$k} = $v;
		}
	}	
}

class RecyclingInvoice extends Auth {
    
	public function __construct() {
		parent::__construct();
		
		$this->load->model('admin/RecyclingInvoicesModel');
		$this->load->library('form_validation');
		$this->form_validation->set_message('greater_than', "The %s is required.");
	}
	
	public function delete($id) {
		$id = (int)$id;
		
		if ($this->RecyclingInvoicesModel->deleteById($id)) {
			$this->session->set_flashdata('info', 'Recycling Purchase Order #'.$id.' has been successfully deleted.');
		} else {
			$this->session->set_flashdata('info', 'Recycling Purchase Order #'.$id.' was not found!');
		}
		
		redirect('admin/RecyclingInvoice/history');
	}
	
	public function ajaxList($type=null) {
		$sortColumn = null;
		$sortDir = null;
		
		if ($this->input->get('iSortingCols') > 0) {
			if ($this->input->get('bSortable_0')) {
				switch ($this->input->get('iSortCol_0')) {
					case 0:
						$sortColumn = 'id';
						break;
					case 1:
						$sortColumn = 'poNumber';
						break;
					case 2:
						$sortColumn = 'invoiceDate';
						break;
					case 3:
						$sortColumn = 'location';
						break;
					case 4:
						$sortColumn = 'vendor';
						break;
					case 5:
						$sortColumn = 'material';
						break;
					case 6:
						$sortColumn = 'quantity';
						break;
					case 7:
						$sortColumn = 'invoicePriceUnit';
						break;
                    case 8:
                        $sortColumn = 'invoicePPT';
                        break;
                    case 9:
                        $sortColumn = 'poPriceUnit';
                        break;
                    case 10:
                        $sortColumn = 'poPPT';
                        break;
                    case 11:
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
		
		$this->load->helper('dates');
		
		$searchFilter = array();
		$searchFilter['searchToken'] = $this->input->get('sSearch');
		if($this->input->get('invoiceDateStart')!='') {
		    $searchFilter['invoiceDateStart'] = USToSQLDate($this->input->get('invoiceDateStart'));
		}
		if($this->input->get('invoiceDateEnd')!='') {
		    $searchFilter['invoiceDateEnd'] = USToSQLDate($this->input->get('invoiceDateEnd'));
		}
		if($this->input->get('distributionCenterId')!='') {
		    $searchFilter['distributionCenterId'] = intval($this->input->get('distributionCenterId'));
		}
        if($this->input->get('status')!='') {
            $searchFilter['status'] = $this->input->get('status');
        }

		$data = $this->RecyclingInvoicesModel->getList($this->assigns['_companyId'], $this->input->get('iDisplayStart'), $this->input->get('iDisplayLength'), $searchFilter, $sortColumn, $sortDir);
		$ajaxData = array();
		
		foreach ($data['data'] as $item) {
			$ajaxData[] = array(
				'DT_RowClass' => 'grade' . ($item->status == 'YES' ? 'A' : 'X'),			
				'<a title="Edit invoice #'.$item->id.'" href="'.base_url().'admin/RecyclingInvoice/AddEdit/'.$item->id.'">' . $item->id . '</a>',
				'<a title="Edit invoice #'.$item->id.'" href="'.base_url().'admin/RecyclingInvoice/AddEdit/'.$item->id.'">' . $item->poNumber . '</a>',
				SQLToUSDate($item->invoiceDate),
				$item->location,
				$item->vendor,
				(empty($item->material) ? '-' : implode('<br />', explode(';', $item->material))),
				(empty($item->quantity) ? '0' : implode('<br />', explode(';', $item->quantity))),
                number_format(floatval($item->poPriceUnit), 2),
                number_format(floatval($item->poPPT), 2),
				number_format(floatval($item->invoicePriceUnit), 2),
                number_format(floatval($item->invoicePPT), 2),
				($item->status == 'YES' ? 'Complete' : 'Incomplete')
			);
		}
		
		if($type=='csv') {
		    return array(
			'aaData' => $ajaxData,
			'iTotalRecords' => $data['records'],
			'iTotalDisplayRecords' => $data['records']
		    );
		} else {
		    header('Content-type: application/json');
		    echo json_encode(array(
			    'aaData' => $ajaxData,
			    'iTotalRecords' => $data['records'],
			    'iTotalDisplayRecords' => $data['records']
		    ));
		}
	}
	
	public function csvList() {
	    //export
	    $this->load->helper('download');
	    $file = fopen('php://temp/maxmemory:'. (12*1024*1024), 'r+');
	    fputcsv($file, array(
		'ID#',
		'PO#',
		'Invoice Date',
		'Location',
		'Vendor',
		'Material',
		'Qty',
		'Invoice Price',
		'PO Price',
		'Complete?'
	    ));
	    
	    $data = $this->ajaxList('csv');

	    foreach ($data['aaData'] as $row) {
            fputcsv($file, array(
                strip_tags($row[0]),
                strip_tags($row[1]),
                strip_tags($row[2]),
                strip_tags($row[3]),
                strip_tags($row[4]),
                strip_tags(str_replace('<br />', "\n", $row[5])),
                strip_tags(str_replace('<br />', "\n", $row[6])),
                strip_tags($row[7]),
                strip_tags($row[8]),
                strip_tags($row[9])
            ));
	    }

	    rewind($file);
	    $csv = stream_get_contents($file);
	    fclose($file);

	    force_download('RecyclingInvoiceList.csv', $csv);	    
	}
	
	public function history() {
	    $this->assigns['data'] = new Placeholder();
	    
	    $this->load->model('distributioncentersmodel');
	    $this->assigns['data']->distributionCenterIdOptions = $this->distributioncentersmodel->getAllDC();
	    $this->load->view('admin/recycling_invoice/recycling_invoices_list', $this->assigns);
	}
	
	public function autocomplete_required($input) {
		if (empty($input)) {
			$this->form_validation->set_message('autocomplete_required', 'The %s field must be autocompleted!');
			return false;
		}
		
		return true;
	}

    public function usdate($input) {
        $pattern = '/^([0-9]{2})\/([0-9]{2})\/([0-9]{4})$/';
        preg_match($pattern, $input, $matches, PREG_OFFSET_CAPTURE);
        if(count($matches)==4) {
            $dd = intval($matches[2][0]);
            $mm = intval($matches[1][0]);
            $yyyy = intval($matches[3][0]);

            if($dd>=1 && $dd<=31 && $mm>=1 && $mm<=12 && $yyyy>=1990 && $yyyy<=3000) {
                return true;
            }
        }

        $this->form_validation->set_message('usdate', 'The %s field must be valid(mm/dd/yyyy) date!');
        return false;
    }
	
	public function addEdit() {
		$this->assigns['data'] = new Placeholder();
		$id = (int)$this->uri->segment(4);
		
		$this->load->model('MaterialsModel');
					
		$this->assigns['data']->selMaterials= array();		
		$this->assigns['data']->feeOption= array();
		
		if ($_POST) {
			$this->assigns['data']->MaterialsLoad = array();
			for ($i=0; $i<8; $i++) {
			    $this->assigns['data']->selMaterials[$i] = $_POST['invoiceMaterial'][$i];
			    
			    $this->assigns['data']->MaterialsLoad[$i] = new DataHolder(array());
			    $this->assigns['data']->MaterialsLoad[$i]->quantity = (isset($_POST['quantity'][$i])) ? $_POST['quantity'][$i] : '';
			    $this->assigns['data']->MaterialsLoad[$i]->pricePerUnit = (isset($_POST['invoicePriceUnit'][$i])) ? $_POST['invoicePriceUnit'][$i]: '';
			    $this->assigns['data']->MaterialsLoad[$i]->pricePOUnit = (isset($_POST['poPriceUnit'][$i])) ? $_POST['poPriceUnit'][$i] : '';
			    $this->assigns['data']->MaterialsLoad[$i]->unitId = (isset($_POST['unitId'][$i])) ? $_POST['unitId'][$i] : '';
			}
			$this->assigns['data']->fees = array();
			for ($i=0; $i<3; $i++) {
			    $this->assigns['data']->feeOption[$i] = $_POST['feeType'][$i];
				
				$this->assigns['data']->fees[$i] = new DataHolder(array());
			    $this->assigns['data']->fees[$i]->feeAmount = (isset($_POST['fee'][$i]))?$_POST['fee'][$i]:'';
			    $this->assigns['data']->fees[$i]->waived = (isset($_POST['wived'][$i]))?1:0;
			}					
			
			$this->assigns['data']->poNumber = $_POST['poNumber'];
			$this->assigns['data']->trailerNumber = $_POST['trailerNumber'];
			$this->assigns['data']->BOLNumber = $_POST['BOLNumber'];
			$this->assigns['data']->CBRENumber = $_POST['CBRENumber'];
			$this->assigns['data']->invoiceDate = $_POST['invoiceDate'];
			$this->assigns['data']->status = $_POST['status'];
			$this->assigns['data']->dateSent = $_POST['dateSent'];
			$this->assigns['data']->internalNotes = $_POST['internalNotes'];
			
			
			$this->load->library('form_validation');

			$this->form_validation->set_rules('vendor', 'Vendor', 'required|trim');
			$this->form_validation->set_rules('vendorId', 'Vendor', 'callback_autocomplete_required|trim');
			
			$this->form_validation->set_rules('locationName', 'Location', 'required|trim');
			$this->form_validation->set_rules('locationId', 'Location', 'callback_autocomplete_required|trim');
			$this->form_validation->set_rules('locationType', '', 'trim');

            $this->form_validation->set_rules('invoiceDate', '', 'callback_usdate|required|trim');

			/*
			$this->form_validation->set_rules('poDate', 'PO Date', 'required|date|trim');
			$this->form_validation->set_rules('poNumber', 'PO Number', 'required|trim');
			$this->form_validation->set_rules('trailerNumber', 'Trailer Number', 'required|date|trim');

			$this->form_validation->set_rules('internalNotes', '', 'trim');
			$this->form_validation->set_rules('dateSent', '', 'trim');
			$this->form_validation->set_rules('status', '', 'trim');
			*/
			
			if ($this->form_validation->run() == true) {
			    $this->load->model('admin/RecyclingInvoicesFeesModel');
			    $this->load->model('admin/RecyclingInvoicesMaterialsModel');
			    $this->load->model('admin/WasteInvoicesModel');			    
			    $this->load->helper('dates');
			    				
				$data = $_POST;
				$data['invoiceDate'] = USToSQLDate($data['invoiceDate']);
				//$data['poDate'] = USToSQLDate($data['poDate']);
				$data['dateSent'] = (!empty($data['dateSent']) ? USToSQLDate($data['dateSent']) : '0000-00-00');
				$lastId = $id;	
				
				$fees = array();
				for($i=0; $i<3; $i++) {
				    $fees[$i] = array(
					'feeType' => $_POST['feeType'][$i],
					'feeAmount' => $_POST['fee'][$i],
					'waived' => isset($_POST['wived'][$i])?(($_POST['wived'][$i]=='on')?1:0):0					
				    );
				}
				
				$WasteInvoice = $this->WasteInvoicesModel->getWasteInvoiceByVL($_POST['vendorId'], $_POST['locationId']);
				
				$MaterialsRI = array();
				for($i=0; $i<8; $i++) {
				    $MaterialsRI[$i] = array(
						'invoiceNumber' => isset($WasteInvoice->invoiceNumber)?$WasteInvoice->invoiceNumber:0,
						'materialId' => $_POST['invoiceMaterial'][$i],
						'quantity' => isset($_POST['quantity'][$i])?$_POST['quantity'][$i]:0,
						'pricePerUnit' => isset($_POST['invoicePriceUnit'][$i])?$_POST['invoicePriceUnit'][$i]:0,				
						'pricePOUnit' => isset($_POST['poPriceUnit'][$i])?$_POST['poPriceUnit'][$i]:0,					
						'unit' => isset($_POST['unitId'][$i]) ? $_POST['unitId'][$i] : 0,					
						'invoiceDate' => isset($_POST['invoiceDate']) ? USToSQLDate($_POST['invoiceDate']) : 0					
				    );
				}
				
				if ($id) {
					$data['total'] = 0;
												
					$materials = $this->RecyclingInvoicesMaterialsModel->getByInvoiceId($id);
					
					foreach ($materials as $material) {
						$data['total'] += $material->pricePerUnit * $material->quantity;
					}
					
					$this->RecyclingInvoicesModel->update($id, $this->assigns['_companyId'], $data);
					$this->session->set_flashdata('info', 'The Recycling invoice has been successfully updated.');	
				} else {
					$lastId = $this->RecyclingInvoicesModel->add($this->assigns['_companyId'], $data);
					
					$this->load->model('admin/RecyclingInvoicesInfoModel');
					
					$this->session->set_flashdata('info', 'The Recycling invoice has been successfully added.');
					
					$id = $lastId;
				}
								
				$this->RecyclingInvoicesFeesModel->addFees($id, $fees);
				$this->RecyclingInvoicesMaterialsModel->addMaterials($id, $MaterialsRI);
				
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
				$this->load->model('admin/RecyclingInvoicesFeesModel');
				$this->load->model('admin/RecyclingInvoicesMaterialsModel');				
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

				$this->assigns["data"]->iDate = $this->assigns["data"]->invoiceDate;
				$this->assigns["data"]->releaseNumber = $this->assigns["data"]->poNumber;
				$this->assigns['data']->MaterialsLoad = $this->RecyclingInvoicesMaterialsModel->getByInvoiceId($id);
				
				$this->assigns['data']->fees = $this->RecyclingInvoicesFeesModel->getByInvoiceId($id);
			} else {
				$this->assigns["data"]->iDate = null;
				$this->assigns["data"]->releaseNumber = null;
			}
			
			$this->assigns['data']->unitId = array();
			for ($i=0;$i<8;$i++) {
			    if(isset($this->assigns['data']->MaterialsLoad[$i]))
				$this->assigns['data']->unitId[$i] = $this->assigns['data']->MaterialsLoad[$i]->unit;
			    else
				$this->assigns['data']->unitId[$i] = "";
			}
			
			$this->assigns['data']->selMaterials[0] = 0;
			$this->assigns['data']->selMaterials[1] = 0;
			$this->assigns['data']->selMaterials[2] = 0;
			$this->assigns['data']->selMaterials[3] = 0;
			$this->assigns['data']->selMaterials[4] = 0;
			$this->assigns['data']->selMaterials[5] = 0;
			$this->assigns['data']->selMaterials[6] = 0;
			$this->assigns['data']->selMaterials[7] = 0;
			
			$this->assigns['data']->feeOption[0] = 0;
			$this->assigns['data']->feeOption[1] = 0;
			$this->assigns['data']->feeOption[2] = 0;
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
		//$this->load->model('MaterialsModel');
		//$this->assigns['data']->materialOptions = $this->MaterialsModel->getList($this->assigns['_companyId']);
		
		$this->assigns['data']->unitOptions = array(
			0 => '- Please select -',
			1 => 'Tons',
			2 => 'Lbs',
			3 => 'Bales',
			4 => 'Bulbs',
			5 => 'Boxes',
			6 => 'Units',
		);
		
		$this->assigns['data']->feeOptions = $this->RecyclingInvoicesModel->getFeeTypeOptions();
		
		$this->assigns['data']->statusOptions = array(
			'NO' => 'No',
			'YES' => 'Yes',
		);
		
		$this->assigns["data"]->allMaterials = array(0 => '- Please select -') + $this->MaterialsModel->getListForSelect();
		

		$this->load->view('admin/recycling_invoice/recycling_invoice_add', $this->assigns);
	}
	
	function loadMaterialInfo(){
	    $this->load->helper('dates');
	    $materialId = $_POST['materialId'];
            $distributionCenterId = $_POST['distributionCenterId'];
	    $invoiceDate = USToSQLDate($_POST['invoiceDate']);
	    if($materialId!=0 && $invoiceDate!='') {
                $resRate = $this->RecyclingInvoicesModel->getMarketRates($materialId, $distributionCenterId, $invoiceDate);		
                $resPO = $this->RecyclingInvoicesModel->getMaterialUnit($materialId);

                $data = new DataHolder(array());
                $data->unitId = $resPO->unit;
                $data->invoiceRate = isset($resRate->invoiceRate) ? $resRate->invoiceRate : '';
                $data->poRate = isset($resRate->poRate) ? $resRate->poRate : '';
                $data->invoiceDate = $invoiceDate;
                echo json_encode($data);
	    }
            
	    echo null;
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
		if($this->input->post('materialId') && $this->input->post("unitId")) {
			$this->RecyclingInvoicesModel->addMaterialUnit((int)$this->uri->segment(4), $this->input->post('materialId'), $this->input->post("unitId"));
		}
	}

//	public function addInvoiceMaterial() {
//
//		$this->load->model('admin/RecyclingInvoicesModel');
//		$id = (int)$this->uri->segment(4);
//
//		$invoiceArray = unserialize($this->session->userdata("invoiceArray"));
//
//		$invoiceArray[$id]["materials"][] = array("release"=> $this->input->post('poNumber'),
//											 "date"=> $this->input->post('date'),
//											 "materialId"=> $this->input->post('materialId'),
//											 "quantity"=> (int) $this->input->post('qVal'),
//											 "pricePerUnit" => (float)$this->input->post('ppu'));
//
//
//
//		if(isset($invoiceArray[$id]["allPrice"])) {
//			$invoiceArray[$id]["allPrice"] += ($this->input->post('qVal')*$this->input->post('ppu'));
//		} else {
//			$invoiceArray[$id]["allPrice"] = $this->input->post('qVal')*$this->input->post('ppu');
//		}
//		$this->assigns["data"] = new DataHolder(array());
//		$this->assigns["data"]->allMaterials = $this->RecyclingInvoicesModel->getAllSavedMaterials($id);
//
//		$this->assigns["data"]->invoiceSum = $invoiceArray[$id];
//		$this->assigns["data"]->allFees = $this->feeOptions();
//		$this->session->set_userdata('invoiceArray', serialize($invoiceArray));
//		$this->load->view('admin/recycling_invoice/feesList', $this->assigns);
//
//	}
	
//	public function addInvoiceFee() {
//		
//		$this->load->model('admin/RecyclingInvoicesModel');
//		$id = (int)$this->uri->segment(4);
//		
//		$invoiceArray = unserialize($this->session->userdata("invoiceArray"));
//		
//		$invoiceArray[$id]["fees"][] = array("release"=> $this->input->post('poNumber'),
//				"date"=> $this->input->post('date'),
//				"fee"=> (float)$this->input->post('fee'),
//				"feeType"=> $this->input->post('feeType'),
//				"waived" => $this->input->post('waived'));
//		
//		
//		if(! $this->input->post('waived')) {
//			if(isset($invoiceArray[$id]["allPrice"])) {
//				$invoiceArray[$id]["allPrice"] += ($this->input->post('fee'));
//			} else {
//				$invoiceArray[$id]["allPrice"] = $this->input->post('fee');
//			}
//		}
//		$this->assigns["data"] = new DataHolder(array());
//		$this->assigns["data"]->allMaterials = $this->RecyclingInvoicesModel->getAllSavedMaterials($id);
//		$this->assigns["data"]->allFees = $this->feeOptions();
//		$this->assigns["data"]->invoiceSum = $invoiceArray[$id];
//		$this->session->set_userdata('invoiceArray', serialize($invoiceArray));
//		$this->load->view('admin/recycling_invoice/feesList', $this->assigns);		
//	}
	
//	function deleteInvoicePart() {
//		$this->load->model('admin/RecyclingInvoicesModel');
//		$id = (int)$this->uri->segment(4);
//		
//		$invoiceArray = unserialize($this->session->userdata("invoiceArray"));
//		
//		$temp = $invoiceArray[$id][$this->input->post('part')][$this->input->post('id')];
//		
//		if($this->input->post('part') == "materials") {
//			$invoiceArray[$id]["allPrice"] -= ($temp["quantity"]*$temp["pricePerUnit"]);
//		} else if (! $temp["waived"]){
//			$invoiceArray[$id]["allPrice"] -= $temp["fee"];
//		}
//		
//		unset($invoiceArray[$id][$this->input->post('part')][$this->input->post('id')]);
//		
//		$this->assigns["data"] = new DataHolder(array());
//		$this->assigns["data"]->allMaterials = $this->RecyclingInvoicesModel->getAllSavedMaterials($id);
//		$this->assigns["data"]->allFees = $this->feeOptions();
//		$this->assigns["data"]->invoiceSum = $invoiceArray[$id];
//		$this->session->set_userdata('invoiceArray', serialize($invoiceArray));
//		$this->load->view('admin/recycling_invoice/feesList', $this->assigns);		
//	}
	
//	function deleteOrderPart() {
//		$this->load->model('admin/RecyclingInvoicesModel');
//		$id = (int)$this->uri->segment(4);
//		
//		$orderArray = unserialize($this->session->userdata("orderArray"));
//		$invoiceArray = unserialize($this->session->userdata("invoiceArray"));
//		
//		$temp = $orderArray[$id][$this->input->post('part')][$this->input->post('id')];
//		
//	/*	if($this->input->post('part') == "materials") {
//			if (!isset($temp["quantity"])) {
//				$temp["quantity"] = 0;
//			}
//			
//			$orderArray[$id]["allPrice"] -= ($temp["quantity"]*$temp["pricePerUnit"]);
//		}
//		*/
//		$this->updateOrderTotal($orderArray[$id]);
//		// else if (! $temp["waived"]){
//		//	$orderArray[$id]["allPrice"] -= $temp["fee"];
//		//}
//		
//		if ($orderArray[$id]["allPrice"] < 0) {
//			$orderArray[$id]["allPrice"] = 0;
//		}
//		
//		unset($orderArray[$id][$this->input->post('part')][$this->input->post('id')]);
//		
//		$this->assigns["data"] = new DataHolder(array());
//		$this->assigns["data"]->allMaterials = $this->RecyclingInvoicesModel->getAllSavedMaterials($id);
//		$this->assigns["data"]->allFees = $this->feeOptions();
//		$this->assigns["data"]->orderSum = $orderArray[$id];
//
//		if (isset($invoiceArray[$id])) {
//			$this->assigns["data"]->invoiceSum = $invoiceArray[$id];
//		}
//		
//		$this->session->set_userdata('orderArray', serialize($orderArray));
//		$this->load->view('admin/recycling_invoice/orderList', $this->assigns);		
//	}
	
//	function orderHtml() {
//		$this->load->model('admin/RecyclingInvoicesModel');
//		$id = (int)$this->uri->segment(4);
//		
//		$orderArray = unserialize($this->session->userdata("orderArray"));
//		$invoiceArray = unserialize($this->session->userdata("invoiceArray"));
//		
//		$this->updateOrderTotal($orderArray[$id]);
//		
//		$this->assigns["data"] = new DataHolder(array());
//		$this->assigns["data"]->allMaterials = $this->RecyclingInvoicesModel->getAllSavedMaterials($id);
//		$this->assigns["data"]->allFees = $this->feeOptions();
//		$this->assigns["data"]->orderSum = $orderArray[$id];
//		
//		if (isset($invoiceArray[$id])) {
//			$this->assigns["data"]->invoiceSum = $invoiceArray[$id];
//		}
//		
//		$this->session->set_userdata('orderArray', serialize($orderArray));
//		$this->load->view('admin/recycling_invoice/orderList', $this->assigns);	
//	}
	
//	function addOrderMaterial() {
//		$this->load->model('admin/RecyclingInvoicesModel');
//		$id = (int)$this->uri->segment(4);
//		
//		$orderArray = unserialize($this->session->userdata("orderArray"));
//		$invoiceArray = unserialize($this->session->userdata("invoiceArray"));
//		
//		$qty = $this->getMaterialQtyFromInvoice($this->input->post('materialId'));	
//		
//		$orderArray[$id]["materials"][] = array (
//			"release"=> $this->input->post('poNumber'),
//			"date"=> $this->input->post('date'),
//			"materialId"=> $this->input->post('materialId'),
//			"pricePerUnit" => (float)$this->input->post('ppu'),
//			"quantity" => $qty,
//		);
//		
//		$this->updateOrderTotal($orderArray[$id]);
//		$this->session->set_userdata('orderArray', serialize($orderArray));
//		
//		/*
//		if(isset($orderArray[$id]["allPrice"])) {
//			$orderArray[$id]["allPrice"] += $this->input->post('ppu') * $qty;
//		} else {
//			$orderArray[$id]["allPrice"] = $this->input->post('ppu') * $qty;
//		}
//		*/
//		$this->assigns["data"] = new DataHolder(array());
//		$this->assigns["data"]->allMaterials = $this->RecyclingInvoicesModel->getAllSavedMaterials($id);
//		$this->assigns["data"]->allFees = $this->feeOptions();
//		$this->assigns["data"]->orderSum = $orderArray[$id];
//		if (isset($invoiceArray[$id])) {
//			$this->assigns["data"]->invoiceSum = $invoiceArray[$id];
//		}
//		
//		
//		$this->load->view('admin/recycling_invoice/orderList', $this->assigns);		
//
//	}
	
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

    function IsUniqueReleaseNumber() {
        echo $this->RecyclingInvoicesModel->isUniqueReleaseNumber($this->input->post('poNumber'), $this->input->post('invoiceId')) ? 'YES' : 'NO';
    }
}


/* End of file WasteInvoice.php */
/* Location: ./application/controllers/admin/RecyclingInvoice.php */