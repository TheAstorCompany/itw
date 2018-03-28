<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
include 'application/controllers/admin/Auth.php';

class DataHolder extends Placeholder {
    public function __construct($data) {
        foreach ($data as $k=>$v) {
            $this->{$k} = $v;
        }
    }
}

class LampRequest extends Auth {

    public function __construct() {
        parent::__construct();

        $this->load->library('form_validation');
        $this->form_validation->set_message('greater_than', "The %s is required.");

        $this->load->model('admin/LampRequestsModel');
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
            $this->assigns['data']->address = $_POST['address'];
            $this->assigns['data']->phone = $_POST['phone'];
            $this->assigns['data']->invoiceNumber = $_POST['invoiceNumber'];
            $this->assigns['data']->requestDate = $_POST['requestDate'];
            $this->assigns['data']->bolDate = $_POST['bolDate'];
            $this->assigns['data']->requestDate = $_POST['requestDate'];
            $this->assigns['data']->cbreNumber = $_POST['cbreNumber'];

            $this->assigns['data']->status = $_POST['status'];
            $this->assigns['data']->dateSent = $_POST['dateSent'];
            $this->assigns['data']->internalNotes = $_POST['internalNotes'];

            $this->assigns['data']->invoiceItems = $_POST['invoiceItems'];
            $this->assigns['data']->invoiceItemsLampQuantity = $_POST['invoiceItemsLampQuantity'];
            $this->assigns['data']->invoiceItemsBoxQuantity = $_POST['invoiceItemsBoxQuantity'];

            $this->load->library('form_validation');

            $this->form_validation->set_rules('locationId', 'Location Name or ID', 'required|trim');
            $this->form_validation->set_rules('locationName', 'Location Name or ID', 'trim');
            $this->form_validation->set_rules('locationType', 'Location Name or ID', 'trim');
            $this->form_validation->set_rules('cbreNumber', 'CBRE#', 'required|trim');

            if ($this->form_validation->run() == true) {
                $this->load->helper('dates');

                $data = $_POST;
                if(empty($data['bolDate'])) {
                    unset($data['bolDate']);
                } else {
                    $data['bolDate'] = USToSQLDate($data['bolDate']);
                }
                if(empty($data['requestDate'])) {
                    unset($data['requestDate']);
                } else {
                    $data['requestDate'] = USToSQLDate($data['requestDate']);
                }
                if(empty($data['invoiceDate'])) {
                    unset($data['invoiceDate']);
                } else {
                    $data['invoiceDate'] = USToSQLDate($data['invoiceDate']);
                }
                if(empty($data['dateSent'])) {
                    unset($data['dateSent']);
                } else {
                    $data['dateSent'] = USToSQLDate($data['dateSent']);
                }

                if($invoiceId > 0) {
                    $this->LampRequestsModel->update($invoiceId, $data);
                    $this->session->set_flashdata('info', 'The Lamp request has been successfully updated.');
                } else {
                    $invoiceId = $this->LampRequestsModel->add($data);
                    $this->session->set_flashdata('info', 'The Lamp request has been successfully added.');
                }
                $items = array();
                foreach($_POST['invoiceItems'] as $key=>$materialId) {
                    $materialId = intval($materialId);
                    if($materialId>0) {
                        $items[] = array('requestId'=>$invoiceId, 'materialId'=>$materialId, 'lampQuantity'=>floatval($_POST['invoiceItemsLampQuantity'][$key]), 'boxQuantity'=>floatval($_POST['invoiceItemsBoxQuantity'][$key]));
                    }
                }
                $this->LampRequestsModel->addItems($invoiceId, $items);
                 if($this->input->post('save_type') == 3) {
                    redirect('admin/LampRequest/Add?vendor_id='.$_POST['vendorId'].'&invoice_date='.$_POST['requestDate']);
                } elseif ($this->input->post('save_type') == 2) {
                    redirect('admin/LampRequest/Add');
                } else {
                    redirect('admin/LampRequest/addEdit/'.$invoiceId);
                }
            }

            $this->assigns['errors'] = validation_errors();
        } else {
            if($invoiceId > 0) {
                $this->assigns['data'] = $this->LampRequestsModel->getById($invoiceId);
                $this->assigns['data']->invoiceItems = array();
                $this->assigns['data']->invoiceItemsLampQuantity = array();
                $this->assigns['data']->invoiceItemsBoxQuantity = array();
                $materialCharges = $this->LampRequestsModel->getItems($this->assigns['data']->id);
                for($i=0; $i<count($materialCharges); $i++) {
                    $this->assigns['data']->invoiceItems[$i]	= $materialCharges[$i]->materialId;
                    $this->assigns['data']->invoiceItemsLampQuantity[$i] = $materialCharges[$i]->lampQuantity;
                    $this->assigns['data']->invoiceItemsBoxQuantity[$i] = $materialCharges[$i]->boxQuantity;
                }
            } else {
                $this->assigns['data']->locationId = 0;
                $this->assigns['data']->locationType = '';
                $this->assigns['data']->locationName = '';
                $this->assigns['data']->address = '';
                $this->assigns['data']->phone = '';
                $this->assigns['data']->invoiceNumber ='';
                $this->assigns['data']->requestDate = date('m/d/Y');
                $this->assigns['data']->bolDate = '';
                $this->assigns['data']->requestDate = '';
                $this->assigns['data']->cbreNumber = '';

                $this->assigns['data']->status = 'Pending';
                $this->assigns['data']->dateSent = '';
                $this->assigns['data']->internalNotes = '';

                $this->assigns['data']->invoiceItems = array();
            }
        }

        $itemOptions = array('0' => '- Please select -');
        $itemOptions += $this->LampRequestsModel->getItemOptions();
        $this->assigns['data']->ItemOptions = $itemOptions;
        $this->assigns['data']->statusOptions = $this->LampRequestsModel->getStatusOptions();
        $this->assigns['data']->invoiceId = $invoiceId;

        $this->assigns['data']->is_readonly = $is_readonly;

        $this->load->view('admin/lamp_request/lamp_request_add', $this->assigns);
    }

    public function addEdit() {
        $this->add();
    }

    public function view() {
        $this->add(true);
    }

    public function history() {
        $this->load->model('ParametersModel');

        $this->assigns['data'] = new Placeholder();
        $this->assigns['data']->mailTo = $this->ParametersModel->getByName('mailTo');
        $this->assigns['data']->filterOptions = $this->LampRequestsModel->getStatusOptions();

        $this->load->view('admin/lamp_request/lamp_request_list', $this->assigns);
    }

    public function ajaxList($type=null) {
        $sortColumn = null;
        $sortDir = null;

        if ($this->input->get('iSortingCols') > 0) {
            if ($this->input->get('bSortable_0')) {
                switch ($this->input->get('iSortCol_0')) {
                    case 0:
                        $sortColumn = 'LampRequests.id';
                        break;
                    case 1:
                        $sortColumn = 'locationName';
                        break;
                    case 2:
                        $sortColumn = 'requestDate';
                        break;
                    case 3:
                        $sortColumn = 'cbreNumber';
                        break;
                    case 4:
                        $sortColumn = 'invoiceNumber';
                        break;
                    case 5:
                        $sortColumn = 'requestDate';
                        break;
                    case 6:
                        $sortColumn = 'bolDate';
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

        $this->load->helper('dates');

        $searchFilter = array();
        $searchFilter['searchToken'] = $this->input->get('sSearch');
        if($this->input->get('filter_requestDateStart')!='') {
            $searchFilter['requestDateStart'] = USToSQLDate($this->input->get('filter_requestDateStart'));
        }
        if($this->input->get('filter_requestDateEnd')!='') {
            $searchFilter['requestDateEnd'] = USToSQLDate($this->input->get('filter_requestDateEnd'));
        }
        if($this->input->get('filter_complete')!='All') {
            $searchFilter['status'] = $this->input->get('filter_complete');
        }        

        $data = $this->LampRequestsModel->getList($this->input->get('iDisplayStart'), $this->input->get('iDisplayLength'), $searchFilter, $sortColumn, $sortDir);
        $ajaxData = array();
        $this->load->helper('dates');

        //$itemsTypes = $this->LampRequestsModel->getItemOptions();
        $statusOptions = $this->LampRequestsModel->getStatusOptions();

        foreach ($data['data'] as $row) {
            /*
            $materialCharges = array();
            $items = $this->LampRequestsModel->getItems($row->id);

            foreach($items as $item) {
                $materialCharges['material'][] = $itemsTypes[$item->materialId];
                $materialCharges['quantity'][] = $itemsTypes[$item->quantity];
            }
            */

            $ajaxData[] = array(
                'DT_RowClass' => 'gradeA status'.$row->status,
                '<a title="Edit request #'.$row->id.'" href="'.base_url().'admin/LampRequest/addEdit/'.$row->id.'">'.$row->id.'</a>',
                $row->locationName,
                SQLToUSDate($row->requestDate),
                $row->cbreNumber,
                $row->invoiceNumber,
                SQLToUSDate($row->requestDate),
                SQLToUSDate($row->bolDate),
                //implode('<br />', explode(';', $row->materialCharges)),
                //implode('<br />', explode(';', $row->materialChargesQuantity)),
                $statusOptions[$row->status]
            );

        }

        if($type=='csv') {
            return array(
                'aaData' => $ajaxData,
                'iTotalRecords' => $data['records'],
                'iTotalDisplayRecords' => $data['records']
            );
        } else {
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
            'Request#',
            'Location',
            'Request Date',
            'CBRE#',
            'Invoice#',
            'Invoice Date',
            'BOL Date',
            'Status'
        ));

        $data = $this->ajaxList('csv');

        foreach ($data['aaData'] as $row) {
            fputcsv($file, array(
                strip_tags($row[0]),
                strip_tags($row[1]),
                strip_tags($row[2]),
                strip_tags($row[3]),
                strip_tags($row[4]),
                strip_tags($row[5]),
                strip_tags($row[6]),
                strip_tags($row[7])
            ));
        }

        rewind($file);
        $csv = stream_get_contents($file);
        fclose($file);

        force_download('LampRequestHistory.csv', $csv);
    }

    public function delete($id) {
        $id = (int)$id;

        if ($this->LampRequestsModel->deleteById($id)) {
            $this->session->set_flashdata('info', 'DC Invoice #'.$id.' has been successfully deleted.');
        } else {
            $this->session->set_flashdata('info', 'DC Invoice #'.$id.' was not found!');
        }

        redirect('admin/LampRequest/history');
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

    public function SendRequestsToEverLights() {
        $this->load->model('States');
        $states = $this->States->getList();

        $rows = $this->LampRequestsModel->getPendingList();
        $lris = $this->LampRequestsModel->getLRI();
        $html = '';
        $html .= '<table border="1" cellspacing="0" width="1100px">';
        $html .= '<tr>
                <th>Store #</th>
                <th>Address</th>
                <th>City</th>
                <th>State</th>
                <th>Zip</th>
                <th>Phone</th>
                <th>4ft lamps</th>
                <th>8ft lamps</th>
                <th>HID #</th>
                <th>4ft Lamp Boxes</th>
                <th>8ft Lamp Boxes</th>
                <th>HID Boxes</th>
                <th>Scheduled Date</th>
            </tr>';
        $sd = date('m/d/Y');
        foreach($rows as $row) {
            $lri = array_key_exists($row->requestId, $lris) ? $lris[$row->requestId] : array();
            $html .= '<tr>
                <td>'.$row->location.'</td>
                <td>'.$row->addressLine1.'</td>
                <td>'.$row->city.'</td>
                <td>'.(isset($states[$row->stateId]) ? $states[$row->stateId]->code : $row->stateId).'</td>
                <td>'.$row->postCode.'</td>
                <td>'.$row->phone.'</td>
                <td>'.(isset($lri[17]) ? $lri[17]['lampQuantity'] : 0).'</td>
                <td>'.(isset($lri[16]) ? $lri[16]['lampQuantity'] : 0).'</td>
                <td>'.(isset($lri[18]) ? $lri[18]['lampQuantity'] : 0).'</td>
                <td>'.(isset($lri[17]) ? $lri[17]['boxQuantity'] : 0).'</td>
                <td>'.(isset($lri[16]) ? $lri[16]['boxQuantity'] : 0).'</td>
                <td>'.(isset($lri[18]) ? $lri[18]['boxQuantity'] : 0).'</td>
                <td>'.$sd.'</td>
            </tr>';
        }
        $html .= '</table>';

        if(isset($_POST['action'])) {
            $to  = $_POST['mailTo'];
            $subject = $_POST['mailSubject'];
            $message = '
                <html>
                <head>
                    <title>'. $subject.'</title>
                </head>
                <body>
                '.$html.'
                </body>
                </html>';

            $this->load->library('email');

            $this->email->from('astor@astorrecycling.com', 'Astor');
            $this->email->to($to);

            $this->email->subject($subject);
            $this->email->message($message);

            if($this->email->send()) {
                foreach($rows as $row) {
                    $this->LampRequestsModel->setStatusForRequest($row->requestId, 'SentToEverlights');
                }
                echo 'OK';
            } else {
                echo 'ERROR: '.$this->email->print_debugger();
            }

        } else {
            echo $html;
        }
    }

    public function SaveMailTo() {
        $this->load->model('ParametersModel');

        if(isset($_POST['mailTo'])) {
            $this->ParametersModel->setByName('mailTo', $_POST['mailTo']);
            echo 'OK';
        } else {
            echo 'ERROR';
        }
    }
}