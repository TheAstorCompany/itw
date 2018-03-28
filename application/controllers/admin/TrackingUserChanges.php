<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
include 'application/controllers/admin/Auth.php';

class TrackingUserChanges extends Auth {
    public function __construct() {
        parent::__construct();
        $this->load->model('admin/SupportRequestServiceTypes');
        $this->load->model('admin/Containers');
        $this->assigns['data'] = new Placeholder();

    }
        
    public function index() {
        $this->load->model('admin/TrackingUserChangesModel');
	    $this->load->helper('dates');
        
        $this->assigns['data']->object_types = array(
            'support_request' => 'Support Request', 
			'vendor_setup' => 'Vendor Setup',
			'dc_setup' => 'DC Setup',
			'store_setup' => 'Store Setup',
            'dc_invoice' => 'DC Invoice', 
            'recycling_charge' => 'Recycling Charges', 
            'recycling_invoice' => 'Recycling PO',
            'lamp_request' => 'Lamp Request',
            'construction_invoice' => 'Construction Invoice',
            'waste_invoice' => 'Hauler Invoice');        
        
        $startDate = (isset($_POST['startDate']) ? USToSQLDate($_POST['startDate']) : date('Y-m-d', mktime(0, 0, 0, date("m") - 1, date("d"), date("Y"))));
	    $endDate = (isset($_POST['endDate']) ? USToSQLDate($_POST['endDate']) : date('Y-m-d'));
        
        $this->assigns['data']->tracks = $this->TrackingUserChangesModel->getReportData($startDate, $endDate);

        if(isset($_POST['action']) && $_POST['action']=='csv') {
            header("Cache-Control: public");
            header("Content-Description: File Transfer");
            header("Content-Disposition: attachment; filename=TrackingUserChanges.csv");
            header("Content-Type: application/octet-stream");
            header("Content-Transfer-Encoding: binary");			

            $fp = fopen('php://output', 'w'); 
            
            fputcsv($fp, array('User', 'Type', 'Adds', 'Changes', 'Unique Changes', 'Errors', 'Closed', 'Deletes', 'Total'), ",", '"');
            
	        $total = array('add'=>0, 'change'=>0, 'unique_change'=>0, 'errors'=>0, 'close'=>0, 'delete'=>0, 'total'=>0);
            foreach($this->assigns['data']->tracks as $track) {
                $username = '';
                foreach($track as $k=>$v) {
                    if(!isset($this->assigns['data']->object_types[$k])) {
                        continue;
                    } else {
                        $total['add'] += $v['add'];
                        $total['change'] += $v['change'];
                        $total['unique_change'] += $v['unique_change'];
                        $total['errors'] += $v['errors'];
                        $total['close'] += $v['close'];
                        $total['delete'] += $v['delete'];
                        $total['total'] += ($v['add'] + $v['unique_change'] + $v['close'] + $v['delete']);
		            }
                    
                    if($username!=$track['username']) {
                        $username = $track['username'];
                        fputcsv($fp, array($track['username'], '', '', '', '', '', '', '', ''), ",", '"');
                    }
                    fputcsv($fp, array('', $this->assigns['data']->object_types[$k], $v['add'], $v['change'], $v['unique_change'], $v['errors'], $v['close'], $v['delete'], ($v['add'] + $v['change'] + $v['close'] + $v['delete'])), ",", '"');
                }
            }
	        fputcsv($fp, array('Total', '', $total['add'], $total['change'], $total['unique_change'], $total['errors'], $total['close'], $total['delete'], $total['total']), ",", '"');
	    
            return;
        }
                        
        $this->load->view('admin/tracking_user_changes/tracking_user_changes_list', $this->assigns);
    }
}
