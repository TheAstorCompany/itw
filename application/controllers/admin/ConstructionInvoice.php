<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
include 'application/controllers/admin/Auth.php';

class DataHolder extends Placeholder {
    public function __construct($data) {
        foreach ($data as $k=>$v) {
            $this->{$k} = $v;
        }
    }
}

class ConstructionInvoice extends Auth {

    public function __construct() {
        parent::__construct();

        $this->load->library('form_validation');
        $this->form_validation->set_message('greater_than', "The %s is required.");

        $this->load->model('admin/ConstructionInvoicesModel');
    }

    public function lawsonnumber_mask($input) {
        if(empty($input)) {
            return true;
        }
        if(!preg_match("/[0-9]{6}-[0-9]{6}/i", $input)) {
            $this->form_validation->set_message('lawsonnumber_mask', 'Lawson # must be ######-###### format!');
            return false;
        }

        return true;
    }

    public function budget_mask($input) {
        if(empty($input)) {
            return true;
        }
        if(!preg_match("/[0-9]{6}/i", $input)) {
            $this->form_validation->set_message('budget_mask', 'Budget # must be ###### format!');
            return false;
        }

        return true;
    }

    public function add($is_readonly=false) {
        $invoiceId = intval($this->uri->segment(4));
        $this->assigns['data'] = new Placeholder();

        if ($_POST) {

            $this->assigns['data']->locationId = $_POST['locationId'];
            $this->assigns['data']->locationType = $_POST['locationType'];
            $this->assigns['data']->vendorId = $_POST['vendorId'];
            $this->assigns['data']->vendorName = $_POST['vendorName'];
            $this->assigns['data']->invoiceDate = $_POST['invoiceDate'];
            $this->assigns['data']->haulerInvNumber = $_POST['haulerInvNumber'];
            $this->assigns['data']->monthlyServicePeriodM = $_POST['monthlyServicePeriodM'];
            $this->assigns['data']->monthlyServicePeriodY = $_POST['monthlyServicePeriodY'];
            $this->assigns['data']->budgetNumber = $_POST['budgetNumber'];
            $this->assigns['data']->lawsonNumber = $_POST['lawsonNumber'];
            $this->assigns['data']->status = $_POST['status'];
            $this->assigns['data']->dateSent = $_POST['dateSent'];
            $this->assigns['data']->internalNotes = $_POST['internalNotes'];
            $this->assigns['data']->invoiceFees = $_POST['invoiceFees'];
            $this->assigns['data']->invoiceFeesAmount = $_POST['invoiceFeesAmount'];
            $this->assigns['data']->invoiceFeesTons = $_POST['invoiceFeesTons'];

            $this->load->library('form_validation');

            $this->form_validation->set_rules('locationId', 'Location Name or ID', 'required|trim');
            $this->form_validation->set_rules('locationName', 'Location Name or ID', 'trim');
            $this->form_validation->set_rules('locationType', 'Location Name or ID', 'trim');
            $this->form_validation->set_rules('vendorName', 'Vendor', 'required|trim');
            $this->form_validation->set_rules('vendorId', 'Vendor', 'callback_autocomplete_required|trim');
            $this->form_validation->set_rules('invoiceDate', 'Date', 'required|trim');
            $this->form_validation->set_rules('haulerInvNumber', 'Hauler Inv#', 'required|trim');
            //$this->form_validation->set_rules('monthlyServicePeriodM', 'Monthly(Month) Service Period', 'required|trim');
            //$this->form_validation->set_rules('monthlyServicePeriodY', 'Monthly(Year) Service Period', 'required|trim');
            $this->form_validation->set_rules('budgetNumber', 'Budget #', 'callback_budget_mask|trim');
            $this->form_validation->set_rules('lawsonNumber', 'Lawson #', 'callback_lawsonnumber_mask|trim');

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
                    $this->ConstructionInvoicesModel->update($invoiceId, $data);
                    $this->session->set_flashdata('info', 'The Construction invoice has been successfully updated.');
                } else {
                    $invoiceId = $this->ConstructionInvoicesModel->add($data);
                    $this->session->set_flashdata('info', 'The Construction invoice has been successfully added.');

                    if(!$this->ConstructionInvoicesModel->invoiceIsNotDuplicate($invoiceId, $data['locationId'], $data['locationType'], $data['vendorId'], $data['monthlyServicePeriodM'], $data['monthlyServicePeriodY'])) {
                        $this->session->set_flashdata('popupmessage', 'There is already an invoice entered for this month, please verify this is not a duplicate');
                    }
                }
                $fees = array();
                foreach($_POST['invoiceFees'] as $key=>$invoiceFee) {
                    $invoiceFee = intval($invoiceFee);
                    if($invoiceFee>0 && !empty($_POST['invoiceFeesAmount'][$key])) {
                        $fees[] = array('invoiceId'=>$invoiceId, 'feeType'=>$invoiceFee, 'amount'=>floatval($_POST['invoiceFeesAmount'][$key]), 'tons'=>$_POST['invoiceFeesTons'][$key]);
                    }
                }
                $this->ConstructionInvoicesModel->addFees($invoiceId, $fees);
                if($this->input->post('save_type') == 3) {
                    redirect('admin/ConstructionInvoice/Add?vendor_id='.$_POST['vendorId'].'&invoice_date='.$_POST['invoiceDate']);
                } elseif ($this->input->post('save_type') == 2) {
                    redirect('admin/ConstructionInvoice/Add');
                } else {
                    redirect('admin/ConstructionInvoice/addEdit/'.$invoiceId);
                }
            }

            $this->assigns['errors'] = validation_errors();
        } else {
            if($invoiceId > 0) {
                $this->assigns['data'] = $this->ConstructionInvoicesModel->getById($invoiceId);
                $this->assigns['data']->invoiceFees = array();
                $this->assigns['data']->invoiceFeesTons = array();
                $this->assigns['data']->invoiceFeesAmount = array();
                $materialCharges = $this->ConstructionInvoicesModel->getFees($this->assigns['data']->id);
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

        $this->assigns['data']->feeTypeOptions = array_merge(array('0' => '- Please select -'), $this->ConstructionInvoicesModel->getFeesTypes());
        $this->assigns['data']->statusOptions = array('NO' => 'No', 'YES' => 'Yes');
        $this->assigns['data']->invoiceId = $invoiceId;

        $this->assigns['data']->is_readonly = $is_readonly;

        $this->load->view('admin/construction_invoice/construction_invoice_add', $this->assigns);
    }

    public function addEdit() {
        $this->add();
    }

    public function view() {
        $this->add(true);
    }

    public function history() {
        $this->load->view('admin/construction_invoice/construction_invoice_list', $this->assigns);
    }

    public function ajaxList() {
        $sortColumn = null;
        $sortDir = null;

        if ($this->input->get('iSortingCols') > 0) {
            if ($this->input->get('bSortable_0')) {
                switch ($this->input->get('iSortCol_0')) {
                    case 0:
                        $sortColumn = 'ConstructionInvoices.id';
                        break;
                    case 1:
                        $sortColumn = 'locationName';
                        break;
                    case 2:
                        $sortColumn = 'vendorName';
                        break;
                    case 3:
                        $sortColumn = 'invoiceDate';
                        break;
                    case 4:
                        $sortColumn = 'haulerInvNumber';
                        break;
                    case 5:
                        $sortColumn = 'ConstructionInvoices.id';
                        break;
                    case 6:
                        $sortColumn = 'ConstructionInvoices.id';
                        break;
                    case 7:
                        $sortColumn = 'ConstructionInvoices.id';
                        break;
                    case 8:
                        $sortColumn = 'ConstructionInvoices.id';
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

        $data = $this->ConstructionInvoicesModel->getList($this->input->get('iDisplayStart'), $this->input->get('iDisplayLength'), $this->input->get('sSearch'), $sortColumn, $sortDir);
        $ajaxData = array();
        $this->load->helper('dates');

        $feesTypes = $this->ConstructionInvoicesModel->getFeesTypes();

        foreach ($data['data'] as $item) {
            $materialCharges = array();
            $total_cost = 0.0;
            $fees = $this->ConstructionInvoicesModel->getFees($item->id);
            foreach($fees as $fee) {
                $materialCharges['material'][] = $feesTypes[$fee->feeType];
                $materialCharges['cost'][] = number_format(floatval($fee->amount), 2);
                $materialCharges['tons'][] = $fee->tons;
                $total_cost += floatval($fee->amount);
            }

            $ajaxData[] = array(
                'DT_RowClass' => 'grade' . ($item->status == 'YES' ? 'A' : 'X'),
                '<a title="Edit invoice #'.$item->id.'" href="'.base_url().'admin/ConstructionInvoice/addEdit/'.$item->id.'">'.$item->id.'</a>',
                $item->locationName,
                $item->vendorName,
                SQLToUSDate($item->invoiceDate),
                $item->haulerInvNumber,
                (isset($materialCharges['material']) ? implode('<br />', $materialCharges['material']) : ''),
                (isset($materialCharges['cost']) ? implode('<br />', $materialCharges['cost']) : ''),
                (isset($materialCharges['tons']) ? implode('<br />', $materialCharges['tons']) : ''),
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

        if ($this->ConstructionInvoicesModel->deleteById($id)) {
            $this->session->set_flashdata('info', 'DC Invoice #'.$id.' has been successfully deleted.');
        } else {
            $this->session->set_flashdata('info', 'DC Invoice #'.$id.' was not found!');
        }

        redirect('admin/ConstructionInvoice/history');
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
                $data[$k]['tonnage'] += $row->quantity;
            }
        }

        header('Content-type: application/json');
        echo json_encode($data);
    }
}


/* End of file DCInvoice.php */
/* Location: ./application/controllers/admin/DCInvoice.php */
