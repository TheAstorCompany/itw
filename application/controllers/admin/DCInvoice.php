<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
include 'application/controllers/admin/Auth.php';

class DataHolder extends Placeholder {
	public function __construct($data) {
		foreach ($data as $k=>$v) {
			$this->{$k} = $v;
		}
	}	
}

class DCInvoice extends Auth {
    
	public function __construct() {
		parent::__construct();
		
		$this->load->library('form_validation');
		$this->form_validation->set_message('greater_than', "The %s is required.");
		
		$this->load->model('admin/DistributionCenterInvoicesModel');
	}

    public function checkInvoiceIsNotDuplicate(){
        $distributionCenterId = intval($_POST['distributionCenterId']);
        $vendorId = intval($_POST['vendorId']);
        $haulerInvNumber = trim($_POST['haulerInvNumber']);

        if($vendorId == 0 || $distributionCenterId == 0 || empty($haulerInvNumber))
            die();

        echo $this->DistributionCenterInvoicesModel->getInvoiceCount($distributionCenterId, $vendorId, $haulerInvNumber)> 0 ? "1" : "0";
    }


    public function add($is_readonly=false) {
		$this->load->model('distributioncentersmodel');

        $invoiceId = intval($this->uri->segment(4));
		$this->assigns['data'] = new Placeholder();
        $this->assigns['data']->feeOptions = $this->loadFeesOptions();

		if ($_POST) {

			
			$this->assigns['data']->distributionCenterId = $_POST['distributionCenterId'];
			$this->assigns['data']->vendorId = $_POST['vendorId'];
			$this->assigns['data']->vendorName = $_POST['vendorName'];
			$this->assigns['data']->invoiceDate = $_POST['invoiceDate'];
			$this->assigns['data']->haulerInvNumber = $_POST['haulerInvNumber'];
			$this->assigns['data']->remitTo = $_POST['remitTo'];
			$this->assigns['data']->monthlyServicePeriodM = $_POST['monthlyServicePeriodM'];
			$this->assigns['data']->monthlyServicePeriodY = $_POST['monthlyServicePeriodY'];
			$this->assigns['data']->status = $_POST['status'];
			$this->assigns['data']->dateSent = $_POST['dateSent'];
			$this->assigns['data']->internalNotes = $_POST['internalNotes'];
			$this->assigns['data']->invoiceFees = $_POST['invoiceFees'];
			$this->assigns['data']->invoiceFeesAmount = $_POST['invoiceFeesAmount'];
			$this->assigns['data']->invoiceFeesTons = $_POST['invoiceFeesTons'];
			
			$this->load->library('form_validation');
			
			$this->form_validation->set_rules('distributionCenterId', 'DC Name', 'required|trim');
			$this->form_validation->set_rules('vendorName', 'Vendor', 'required|trim');
			$this->form_validation->set_rules('vendorId', 'Vendor', 'callback_autocomplete_required|trim');			
			$this->form_validation->set_rules('invoiceDate', 'Date', 'required|trim');
			$this->form_validation->set_rules('haulerInvNumber', 'Hauler Inv#', 'required|trim');
			$this->form_validation->set_rules('monthlyServicePeriodM', 'Monthly(Month) Service Period', 'required|trim');
			$this->form_validation->set_rules('monthlyServicePeriodY', 'Monthly(Year) Service Period', 'required|trim');
			//ALTER TABLE `DistributionCenterInvoices` CHANGE `monthlyServicePeriod` `monthlyServicePeriodM` VARCHAR( 255 ) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL;
			//ALTER TABLE `DistributionCenterInvoices` ADD `monthlyServicePeriodY` INT( 4 ) NOT NULL AFTER `monthlyServicePeriodM` 

			if ($this->form_validation->run() == true) {
				$this->load->helper('dates');
			    				
				$data = $_POST;
				$data['invoiceDate'] = USToSQLDate($data['invoiceDate']);
				if(empty($data['dateSent'])) {
					unset($data['dateSent']);
				} else {
					$data['dateSent'] = USToSQLDate($data['dateSent']);
				}
				
				if($invoiceId > 0) {
					$this->DistributionCenterInvoicesModel->update($invoiceId, $data);
					$this->session->set_flashdata('info', 'The DC invoice has been successfully updated.');
				} else {
					$invoiceId = $this->DistributionCenterInvoicesModel->add($data);
					$this->session->set_flashdata('info', 'The DC invoice has been successfully added.');
					
					$this->session->set_flashdata('info', 'The Waste invoice has been successfully added.');
				}
				$fees = array();
                $this->load->model('MaterialFeeTypeModel');;
				foreach($_POST['invoiceFees'] as $key=>$invoiceFee) {
                    $invoiceFee = intval($invoiceFee);
                    if($invoiceFee>0 && !empty($_POST['invoiceFeesAmount'][$key])) {
						$fees[] = array('invoiceId'=>$invoiceId, 'feeType'=>$invoiceFee, 'amount'=>floatval($_POST['invoiceFeesAmount'][$key]), 'tons'=>$_POST['invoiceFeesTons'][$key]);	
					}
				}
				$this->DistributionCenterInvoicesModel->addFees($invoiceId, $fees);				
				if($this->input->post('save_type') == 3) {
					redirect('admin/DCInvoice/Add?vendor_id='.$_POST['vendorId'].'&invoice_date='.$_POST['invoiceDate']);
				} elseif ($this->input->post('save_type') == 2) {
					redirect('admin/DCInvoice/Add');						
				} else {
					redirect('admin/DCInvoice/addEdit/'.$invoiceId);						
				}
			}
			
			$this->assigns['errors'] = validation_errors();
		} else {
			if($invoiceId > 0) {
				$this->assigns['data'] = $this->DistributionCenterInvoicesModel->getById($invoiceId);
				$this->assigns['data']->invoiceFees = array();
				$this->assigns['data']->invoiceFeesTons = array();
				$this->assigns['data']->invoiceFeesAmount = array();
				$materialCharges = $this->DistributionCenterInvoicesModel->getFees($this->assigns['data']->id);
				for($i=0; $i<count($materialCharges); $i++) {
					$this->assigns['data']->invoiceFees[$i]	= $materialCharges[$i]->feeType;
					$this->assigns['data']->invoiceFeesAmount[$i] = $materialCharges[$i]->amount;
					$this->assigns['data']->invoiceFeesTons[$i] = $materialCharges[$i]->tons;
				}
			} else {
				$this->assigns['data']->distributionCenterId = 0;
				$this->assigns['data']->vendorId = '';
				$this->assigns['data']->vendorName = '';
				$this->assigns['data']->invoiceDate = '';
				$this->assigns['data']->haulerInvNumber = '';
				$this->assigns['data']->accountNumber = '';
				$this->assigns['data']->remitTo = '';
				$this->assigns['data']->monthlyServicePeriodM = '';
				$this->assigns['data']->monthlyServicePeriodY = '';
				$this->assigns['data']->status = 'NO';
				$this->assigns['data']->dateSent = '';
				$this->assigns['data']->internalNotes = '';
				$this->assigns['data']->invoiceFees = array();
				if(isset($_GET['vendor_id'])) {
					$this->load->model('admin/VendorsModel');
					$vendorId = intval($_GET['vendor_id']);
					$vendor = $this->VendorsModel->getVendorById($vendorId);
					$this->assigns['data']->vendorId = $vendor->id;
					$this->assigns['data']->vendorName = $vendor->name;
					$this->assigns['data']->invoiceDate = $_GET['invoice_date'];
				}
			}
		}
			
		$this->assigns['data']->distributionCenterIdOptions = $this->distributioncentersmodel->getAllDC();
        $feeTypeOptions = array_merge(array('0' => '- Please select -'), $this->DistributionCenterInvoicesModel->getFeesTypes());



		$this->assigns['data']->feeTypeOptions = $feeTypeOptions;
		$this->assigns['data']->statusOptions = array('NO' => 'No', 'YES' => 'Yes');			
		$this->assigns['data']->invoiceId = $invoiceId;

        $this->assigns['data']->is_readonly = $is_readonly;

		$this->load->view('admin/dc_invoice/dc_invoice_add', $this->assigns);
	}
	
	public function addEdit() {
		$this->add();
	}

    public function view() {
        $this->add(true);
    }
	
	public function history() {
		$this->load->view('admin/dc_invoice/dc_invoice_list', $this->assigns);
	}
	
	public function ajaxList() {
		//header('Content-type: application/json');
		
		$sortColumn = null;
		$sortDir = null;
		
		if ($this->input->get('iSortingCols') > 0) {
			if ($this->input->get('bSortable_0')) {
				switch ($this->input->get('iSortCol_0')) {
					case 0:
						$sortColumn = 'DistributionCenterInvoices.id';
						break;
					case 1:
						$sortColumn = 'dcName';
						break;
					case 2:
						$sortColumn = 'vendorName';
						break;
					case 3:
						$sortColumn = 'invoiceDate';
						break;
                    case 4:
                        $sortColumn = 'invoiceDate';
                        break;
					case 5:
						$sortColumn = 'DistributionCenterInvoices.id';
						break;
					case 6:
						$sortColumn = 'DistributionCenterInvoices.id';
						break;
					case 7:
						$sortColumn = 'DistributionCenterInvoices.id';
						break;
					case 8:
						$sortColumn = 'DistributionCenterInvoices.id';
						break;
					case 12:
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
		
		$data = $this->DistributionCenterInvoicesModel->getList($this->input->get('iDisplayStart'), $this->input->get('iDisplayLength'), $this->input->get('sSearch'), $sortColumn, $sortDir);
		$ajaxData = array();
		$this->load->helper('dates');
		
		$feesTypes = $this->DistributionCenterInvoicesModel->getFeesTypes();
		
		foreach ($data['data'] as $item) {
			$materialCharges = array();
			$total_cost = 0.0;
			$fees = $this->DistributionCenterInvoicesModel->getFees($item->id);
			foreach($fees as $fee) {
				$materialCharges['material'][] = $feesTypes[$fee->feeType];
				$materialCharges['cost'][] = number_format(floatval($fee->amount), 2);
				$materialCharges['tons'][] = $fee->tons;
				$total_cost += floatval($fee->amount);
			}
			
			$ajaxData[] = array(
				'DT_RowClass' => 'grade' . ($item->status == 'YES' ? 'A':'X'),
				'<a title="Edit invoice #'.$item->id.'" href="'.base_url().'admin/DCInvoice/addEdit/'.$item->id.'">'.$item->id.'</a>',
				$item->dcName,
				$item->vendorName,
				SQLToUSDate($item->invoiceDate),
                $item->haulerInvNumber,
				implode('<br />', $materialCharges['material']),
				implode('<br />', $materialCharges['cost']),
				implode('<br />', $materialCharges['tons']),
				number_format($total_cost, 2),
				($item->status == 'YES' ? 'Complete' : 'Incomplete')
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

        if ($this->DistributionCenterInvoicesModel->deleteById($id)) {
            $this->session->set_flashdata('info', 'DC Invoice #'.$id.' has been successfully deleted.');
        } else {
            $this->session->set_flashdata('info', 'DC Invoice #'.$id.' was not found!');
        }

        redirect('admin/DCInvoice/history');
    }

	public function PopulateMaterialCharges() {
	    
	    $data = array();
	    $data['waste'] = array('cost' => 0.0, 'tonnage' => 0.0);
	    $data['recycling'] = array('cost' => 0.0, 'tonnage' => 0.0);
	    
	    if(isset($_POST['locationId']) && isset($_POST['vendorId'])) {
            $locationId = intval($_POST['locationId']);
            $vendorId = intval($_POST['vendorId']);

            $this->load->model('admin/VendorServices');
            $rows = $this->VendorServices->getForMaterialCharges($locationId, $vendorId);
            foreach($rows as $row) {
                $k = ($row->category==1 ? 'recycling' : 'waste');
                $data[$k]['cost'] += $row->rate;
                //$data[$k]['tonnage'] += $row->quantity;
                $data[$k]['tonnage'] += ($row->quantity * $row->weightInLbs * $row->pickupsPerMonth) / TON_KOEFF;
            }

            $this->load->model('admin/VendorServices');
            $services = $this->VendorServices->getByLocation($locationId, 'DC');
            $feesOptions = $this->loadFeesOptions();
            $data['fees']=array();
            if(count($services))
                foreach($services as $s){
                    if($s->vendorId==$vendorId && is_array($s->fees)) {
                        foreach ($s->fees as $fee) {
                            $fee->name=$feesOptions[$fee->feeType];
                            $data['fees'][] = $fee;
                        }
                    }
                }
	    }

	    header('Content-type: application/json');
	    echo json_encode($data);
	}

    private function loadFeesOptions(){
        $this->load->model('FeeTypeModel');
        return $this->FeeTypeModel->getListForSelect();
    }
}


/* End of file DCInvoice.php */
/* Location: ./application/controllers/admin/DCInvoice.php */
