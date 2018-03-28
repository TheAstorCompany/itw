<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
include 'application/controllers/admin/Auth.php';

class DataHolder extends Placeholder {
	public function __construct($data) {
		foreach ($data as $k=>$v) {
			$this->{$k} = $v;
		}
	}	
}

class WasteInvoice extends Auth {
	
	public function __construct() {
		parent::__construct();
		$this->assigns['data'] = new Placeholder();
		$this->load->library('form_validation');
		$this->form_validation->set_message('greater_than', "The %s is required.");
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
						$sortColumn = 'WasteInvoices.invoiceNumber';
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
				'<a title="Edit invoice #'.$item->invoiceNumber.'" href="'.base_url().'admin/WasteInvoice/AddEdit/'.$item->id.'">' . (empty($item->invoiceNumber) ? 'None':$item->invoiceNumber) . '</a>',
				SQLToUSDate($item->invoiceDate),
				SQLToUSDate($item->dateSent),
				$item->locationName,
				$item->vendorName,
				(empty($item->material) ? '-' : implode('<br />', explode(';', $item->material))),
				(empty($item->quantity) ? '0' : implode('<br />', explode(';', $item->quantity))),
				(empty($item->trashFee) ? '0':$item->trashFee),
				(empty($item->fee) ? '0':$item->fee),
				(empty($item->tax) ? '0':$item->tax),
				(empty($item->total) ? '0.00':$item->trashFee + $item->fee + $item->tax),
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
		$this->load->view('admin/waste_invoice/waste_invoice_list', $this->assigns);
	}
	
	public function getScheduledServices() {
        $this->load->helper('dates');

		$locationId = $this->input->get('locationId');
		$vendorId = $this->input->get('vendorId');
		$locationType = $this->input->get('locationType');
        $invoiceDate = USToSQLDate($this->input->get('invoiceDate'));
		$data_only = $this->input->get('data_only');

        if($invoiceDate=='') {
            $invoiceDate = date('Y-m-d');
        }

		$data = new DataHolder(array());
		
		if ($locationId && $vendorId) {
			$this->load->model('admin/VendorServices');
			$list = $this->VendorServices->getByLocationAndVendor($locationId, $locationType, $vendorId, $invoiceDate);
			
			if (!empty($list)) {
				$data = new DataHolder($list);
				
				$this->load->model('admin/Containers');
				$this->assigns['containers'] = $this->Containers->getListForSelect($this->assigns['_companyId']);

			}
		}
		
		if($data_only) {
			$data_arr = array();
			foreach ($data as $k=>$item) {
				$data_arr[] = $item;
			}
			echo json_encode($data_arr);			
		} else {
			$this->assigns['data'] = $data;
			$this->load->view('admin/waste_invoice/scheduled_services', $this->assigns);
		}	
	}
	
	public function services($skipTemplate = false) {
		$type = $this->input->get('type');
		$metod = $this->input->get('metod');

		$this->loadOptions();

		$data = null;

        if(isset($this->assigns['existing_services_fees'])) {
            $data = $this->assigns['existing_services_fees'];
        } else {
            $data = $this->loadServicesAndFeesFromPost();
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
						$this->load->helper('dates');
						$temp->serviceDate = USToSQLDate($temp->serviceDate);
						if($metod=="add")
						    $temp->rate = $temp->quantity * $temp->rate;
						else
						    $temp->rate = $temp->rate;
						
						if ($temp->id > 0) {
							$service_exists = false;
                            /*
                            foreach($data->services as $tmp_service) {
                                if($temp->id==$tmp_service->id) {
                                    $service_exists = true;
                                    break;
                                }
                            }
                            */
                            if(!$service_exists) {
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

		}

        $this->load->model('admin/VendorServices');

		foreach($data->services as &$service) {
		    if(isset($this->assigns['data']->containerOptions[$service->containerId]))
			$service->containerName = $this->assigns['data']->containerOptions[$service->containerId];
            $service->fees = $this->VendorServices->getVendorServiceFees($service->id);
		}
		
		$this->assigns['data']->items = $data;
		$this->assigns['show_errors'] = 1;
		
		if (!$skipTemplate) {
			header('Content-type: application/json');
			$errors = validation_errors();

            echo json_encode(array(
				'html' => $this->load->view('admin/waste_invoice/waste_invoice_services', $this->assigns, true),
				'error' => !empty($errors),
				'form' => $form,
				'data' => $this->assigns['data']->items
			));
		}
	}

    private function loadServicesAndFeesFromPost() {
        $data = new DataHolder(array());
        $data->services = array();
        $data->fees = array();

        if(isset($_POST['existing_services'])) {
            foreach($_POST['existing_services'] as $item) {
                $service = new DataHolder($item);
                $data->services[] = $service;
            }
            unset($_POST['existing_services']);
        }

        if(isset($_POST['existing_fees'])) {
            foreach($_POST['existing_fees'] as $item) {
                $fee = new DataHolder($item);
                $data->fees[] = $fee;
            }
            unset($_POST['existing_fees']);
        }

        return $data;
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
	
	public function invoicenumber_unique($input) {
		$this->load->model('admin/WasteInvoicesModel');
		$id = (int)$this->uri->segment(4);
		if($id==0) {
			if(!$this->WasteInvoicesModel->isUniqueInvoice(intval($_POST['locationId']), $_POST['invoiceNumber'])) {
				$this->form_validation->set_message('invoicenumber_unique', 'The Invoice must be unique!');
				return false;
			}
		}
		
		return true;
	}

    public function invoicenumber_badcharacters($input) {
        if(preg_match('/[^a-zA-Z0-9_\-]/i', $input)) {
            $this->form_validation->set_message('invoicenumber_badcharacters', 'The Invoice must be valid!');
            return false;
        }

        return true;
    }

    public function checkInvoiceIsNotDuplicate(){
        $locationId = intval($_POST['locationId']);
        $vendorId = intval($_POST['vendorId']);
        $invoiceNumber = trim($_POST['invoiceNumber']);

        if($vendorId == 0 || $locationId == 0 || empty($invoiceNumber) || preg_match('/[^a-zA-Z0-9_\-]/i', $invoiceNumber ))
            die();

        $this->load->model('admin/WasteInvoicesModel');
        echo $this->WasteInvoicesModel->getInvoiceCount($locationId, $vendorId, $invoiceNumber)> 0 ? "1" : "0";
    }

	public function add() {
		$this->assigns['data'] = new Placeholder();
		$id = (int)$this->uri->segment(4);

        $this->load->helper('dates');

		if ($_POST) {
			$this->load->library('form_validation');

			$this->form_validation->set_rules('invoiceDate', 'Invoice date', 'date|required|trim');
			
			$this->form_validation->set_rules('vendorName', 'Vendor', 'required|trim');
			$this->form_validation->set_rules('vendorId', 'Vendor', 'callback_autocomplete_required|trim');
			
			$this->form_validation->set_rules('locationName', 'Location', 'required|trim');
			$this->form_validation->set_rules('locationId', 'Location', 'callback_autocomplete_required|trim');
			$this->form_validation->set_rules('locationType', '', 'trim');
			
			$this->form_validation->set_rules('invoiceNumber', 'Invoice', 'callback_invoicenumber_unique');
            $this->form_validation->set_rules('invoiceNumber', 'Invoice', 'callback_invoicenumber_badcharacters');

			$this->form_validation->set_rules('invoiceMonth', 'Invoice Month', 'required|trim');
			$this->form_validation->set_rules('invoiceYear', 'Invoice Year', 'required|trim');

			$this->form_validation->set_rules('internalNotes', '', 'trim');
			$this->form_validation->set_rules('dateSent', '', 'trim');
			$this->form_validation->set_rules('status', '', 'trim');

            $this->form_validation->set_rules('fromOCR', '', 'trim');
			
			if ($this->form_validation->run() == true) {
                $services = $this->loadServicesAndFeesFromPost();

				if (array_key_exists('dateSent', $_POST)) {
					if (empty($_POST['dateSent'])) {
						unset($_POST['dateSent']);
					}
				}
				
				$this->load->model('admin/WasteInvoicesModel');

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
					
					$fees = $services->fees;
					
					foreach ($fees as $fee) {
						if ($fee->waived != 1) {
							$data['total'] += $fee->feeAmount;
						}
					}

					foreach ($services->services as $service) {
						$data['total'] += (double)$service->rate;
					}
					
					$this->WasteInvoiceServicesModel->updateMulti($id, $services->services);
					$this->WasteInvoiceFeesModel->updateMulti($lastId, $services->fees);
					
					$this->WasteInvoicesModel->update($id, $this->assigns['_companyId'], $data);
					$this->session->set_flashdata('info', 'The Hauler invoice has been successfully updated.');	
				} else {
					$data['total'] = 0.0;
					
                    $this->load->model('admin/WasteInvoiceFeesModel');
					$this->load->model('admin/WasteInvoiceServicesModel');
                    $this->load->model('admin/VendorsModel');

					if (is_object($services)) {
						$fees = $services->fees;
						
						foreach ($fees as $fee) {
							if ($fee->waived != 1) {
								$data['total'] += $fee->feeAmount;
							}
						}
						
						foreach ($services->services as $service) {
							$data['total'] += (double)$service->rate;
						}
					}                                        
	
                    $lastId = $this->WasteInvoicesModel->add($this->assigns['_companyId'], $data);
					
					if (is_object($services)) {
						$this->WasteInvoiceServicesModel->addMulti($lastId, $services->services);
						$this->WasteInvoiceFeesModel->addMulti($lastId, $services->fees);
					}

                    $vendor = $this->VendorsModel->getVendorById($data['vendorId']);

                    if($vendor!= null && $vendor->cityVendor) {
                        $this->WasteInvoicesModel->update($lastId, $this->assigns['_companyId'], array('dateSent'=>date('Y-m-d'), 'status'=>'YES'));
                    }
					
					$this->session->set_flashdata('info', 'The Waste invoice has been successfully added.');
                }

                $this->session->unset_userdata(get_class($this));
                if($this->input->post('fromOCR') == 'true') {
                    $this->load->model('admin/VendorsModel');
                    $vendorId = intval($_POST['vendorId']);
                    $vendor = $this->VendorsModel->getVendorById($vendorId);
                    redirect('admin/WasteInvoice/Add?vendor_id='.$vendor->number.'&is_city_vendor='.($vendor->cityVendor ? 'true' : 'false').'&invoice_total='.$data['total'].'&invoice_number='.$data['invoiceNumber'].'&location_id='.$data['locationName'].'&notes='.urlencode($data['internalNotes']));
                } else {
                    if ($this->input->post('action') == 2) {
                        redirect('admin/WasteInvoice/AddEdit');
                    } else {
                        if($this->input->post('save_type') == 3) {
                            //redirect('admin/WasteInvoice/Add?vendor_id='.$_POST['vendorId'].'&invoice_date='.$_POST['invoiceDate']);
                            redirect('admin/WasteInvoice/Add?vendor_id='.$_POST['vendorId']);
                        } elseif ($this->input->post('save_type') == 2) {
                            redirect('admin/WasteInvoice/Add');
                        } else {
                            redirect('admin/WasteInvoice/AddEdit/' . $lastId);
                        }
                    }
                }
				
			}
			
			$this->assigns['errors'] = validation_errors();
			
		} else {

			if ($id) {
				$this->load->model('admin/WasteInvoicesModel');
				$this->load->model('admin/WasteInvoiceFeesModel');
				$this->load->model('admin/WasteInvoiceServicesModel');
				$this->load->model('admin/VendorsModel');

                $this->assigns['existing_services_fees'] = new DataHolder(array(
                    'services' => $this->WasteInvoiceServicesModel->getByInvoiceId($id),
                    'fees' => $this->WasteInvoiceFeesModel->getByInvoiceId($id)
                ));

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
                    $store = @$this->StoresModel->getById($this->assigns['_companyId'], $this->assigns['data']->locationId);
					$this->assigns['data']->locationName = @$store->location;
                    $this->assigns['data']->payFeesYesNo = (@$store->payFees=="1" ? 'Yes' : (@$store->payFees=="0" ? 'No' : ''));
				}
			} elseif(isset($_GET['vendor_id']) && !isset($_GET['invoice_total'])) {
				$this->load->model('admin/VendorsModel');
				$vendorId = intval($_GET['vendor_id']);
				$vendor = $this->VendorsModel->getVendorById($vendorId);
				
				$this->assigns['data'] = new stdClass();
				$this->assigns['data']->vendorId = $vendor->id;
				$this->assigns['data']->vendorName = $vendor->name;
				//$this->assigns['data']->invoiceDate = $_GET['invoice_date'];
                $this->assigns['data']->invoiceDate = "";
				$this->assigns['data']->id = "";
				$this->assigns['data']->status = "";
				$this->assigns['data']->dateSent = "";
				$this->assigns['data']->internalNotes = "";
				$this->assigns['data']->locationName = "";
				$this->assigns['data']->locationId = "";
				$this->assigns['data']->locationType = "";
				$this->assigns['data']->invoiceNumber = "";
				$this->assigns['data']->invoiceMonth = "";
                $this->assigns['data']->payFeesYesNo = "";
			} elseif(isset($_GET['vendor_name']) && isset($_GET['vendor_address'])) {
                $this->load->model('admin/VendorsModel');

                $vendor = $this->VendorsModel->getVendorByNameAndAddress($_GET['vendor_name'], $_GET['vendor_address']);
                $location = $this->VendorsModel->getLocation(intval($_GET['location_id']));


                $this->assigns['data'] = new stdClass();
                $this->assigns['data']->fromOCR = true;

                if($location!=null && $location->status=='NO') {
                    $location = null;
                    $vendor = null;
                    $this->assigns['data']->storeInactive = true;
                } else {
                    $this->assigns['data']->storeInactive = $location!=null ? ($location->status=='NO') : false;
                }

                $this->assigns['data']->vendorId = $vendor!=null ? $vendor->id : '';
                $this->assigns['data']->vendorName = $vendor!=null ? $vendor->name : '';
                $this->assigns['data']->invoiceDate = SQLToUSDate($_GET['invoice_date']);
                $this->assigns['data']->id = "";
                $this->assigns['data']->status = "";
                $this->assigns['data']->dateSent = "";
                $this->assigns['data']->internalNotes = "";
                $this->assigns['data']->locationName = $location!=null ? $location->locationId : '';
                $this->assigns['data']->locationId = $location!=null ? $location->locationId : '';
                $this->assigns['data']->locationType = $location!=null ? $location->locationType : '';
                $this->assigns['data']->locationFranchise = $location!=null ? ($location->franchise==1) : false;
                $this->assigns['data']->invoiceNumber = isset($_GET['invoice_number']) ? preg_replace('/[^a-zA-Z0-9_\-]/i', '', $_GET['invoice_number']) : '';
                $this->assigns['data']->invoiceMonth = "";
                $this->assigns['data']->payFeesYesNo = "";
            }
		}
		
		
		$this->services(true);
		
		$this->load->view('admin/waste_invoice/waste_invoice_add', $this->assigns);
	}

    public function SavingsAll() {
        $year = 2012;
        if(isset($_GET['year'])) {
            $year = intval($_GET['year']);
        }

        $this->_Savings($year, true);
    }
    public function SavingsContract() {
        $year = 2012;
        if(isset($_GET['year'])) {
            $year = intval($_GET['year']);
        }

        $this->_Savings($year, false);
    }

	private function _Savings($year = 2012, $all = true) {
	    $this->load->model('admin/VendorServices');
	    $data = $this->VendorServices->getSavingsData($year);
        $ef = $this->VendorServices->getSavingsExtrasFeesData($year);

        if(!$all) {
            $tmp_data = array();
            foreach($data as $k1=>$row) {
                if($row['Baseline']!=0) {
                    $tmp_data[$k1] = $row;
                }
            }
            $data = $tmp_data;
        }

        header("Cache-Control: public");
	    header("Content-Description: File Transfer");
	    header("Content-Disposition: attachment; filename=Savings_to_Baseline.csv");
	    header("Content-Type: application/octet-stream");
	    header("Content-Transfer-Encoding: binary");			

	    $fp = fopen('php://output', 'w');
	    foreach($data as $k1=>$v1) {
            $row = array('State');
            foreach($v1 as $k2=>$v2) {
                array_push($row, $k2);
                if($k2!='Baseline') {
                    array_push($row, 'Extras');
                    array_push($row, 'Fees');
                }
            }

            array_push($row, 'Current Monthly Save by State');
            array_push($row, 'Current Monthly Save %');
            array_push($row, 'Fiscal Total');
            array_push($row, 'Fiscal Year Savings %');
            array_push($row, 'Current Annual Savings');
            array_push($row, 'Current Annual Savings %');

            fputcsv($fp, $row, ",", '"');
            break;
	    }
	    
	    //$currentK = date('Y/m', mktime(0, 0, 0, date("m"), date("d"), date("Y")));
        $currentK = date('Y/m', mktime(0, 0, 0, 8, 31, $year));
	    foreach($data as $k1=>$v1) {
            $row = array($k1);
            $baseline = 0.0;
            $currentMonthlySaveP = 0.0;
            $currentAnnualSavingsP = 0.0;
            $fiscalTotal = 0.0;
            $fiscalYearSavingsP = 0.0;
            foreach($v1 as $k2=>$v2) {
                $k = $k1.'_'.$k2;
                $v2 += (isset($ef[$k]['mainCost']) ? floatval($ef[$k]['mainCost']) : 0);
                array_push($row, $v2);
                if($k2!='Baseline') {
                    array_push($row, isset($ef[$k]['extras']) ? floatval($ef[$k]['extras']) : 0);
                    array_push($row, isset($ef[$k]['fees']) ? floatval($ef[$k]['fees']) : 0);
                    $fiscalTotal += $v2;
                } else {
                    $baseline = $v2;
                }
            }
            $currentMonthlySaveByState = ($baseline - $v1[$currentK]);
            $currentAnnualSavings = $baseline * 12 - $fiscalTotal;
            if($baseline!=0) {
                $currentMonthlySaveP = round(($currentMonthlySaveByState / $baseline) * 100, 2);
                $fiscalYearSavingsP = round(((($baseline * 12) - $fiscalTotal)/($baseline * 12)) * 100, 2);
                $currentAnnualSavingsP = round(($currentAnnualSavings/($baseline * 12)) * 100, 2);
            }

            array_push($row, $currentMonthlySaveByState);//O
            array_push($row, $currentMonthlySaveP.'%');//P Column P (Current Monthly Save %) should be Column O/Column B
            array_push($row, $fiscalTotal);//Q
            array_push($row, $fiscalYearSavingsP.'%');//R
            array_push($row, $currentAnnualSavings);//S Column S (Current Annual Savings) should be Column Q - (Column B *12)
            array_push($row, $currentAnnualSavingsP.'%');//T Column T (Current Annual Savings %) should be Column S / (Column B *12)

            fputcsv($fp, $row, ",", '"');
	    }

	    fputcsv($fp, array('', ''), ",", '"');
	    
	    function callback_total($n0, $n1) {
            foreach($n1 as $k=>$v) {
                if(!isset($n0[$k])) {
                    $n0[$k] = 0.0;
                }
                $n0[$k] += $v;
            }
            return $n0;
	    }

        function add2columns($arr) {
            $r = array();
            $n = 0;
            foreach($arr as $v) {
                $n++;
                array_push($r, $v);
                if($n>2) {
                    array_push($r, '');
                    array_push($r, '');
                }
            }

            return $r;
        }
        function add2columnsEF($arr1, $arr2) {
            $r = array();
            $n = 0;
            foreach($arr1 as $k=>$v) {
                $n++;
                array_push($r, $v);
                if($n>2) {
                    array_push($r, $arr2[$k]['extras']);
                    array_push($r, $arr2[$k]['fees']);
                }
            }

            return $r;
        }

	    $total_row = array_reduce($data, 'callback_total', array('Total'));
        $total_ef_row = array();
        foreach($total_row as $k1=>&$v1) {
            $total_ef_row[$k1] = array('extras' => 0.0, 'fees' => 0.0);
            foreach($ef as $k2=>$v2) {
                if(strpos($k2, $k1)!==false) {
                    $v1 += $v2['mainCost'];
                    $total_ef_row[$k1]['extras'] += $v2['extras'];
                    $total_ef_row[$k1]['fees'] += $v2['fees'];
                }
            }
        }

	    $plannedSavingByMonth = array_merge(array('Planned Saving by Month', ''), array_fill(0, 12, round($total_row['Baseline']*0.2, 2)));
	    $forecastedSavingByMonth = array('Actual Savings by Month', '');
	    $performanceToPlan = array('Performance to Plan', '');
	    $sharedSavings = array_merge(array('Shared Savings', ''), array_fill(0, 12, 0));
	    $netWalgreensSave = array('Net Walgreens Save', '');	    
	    
	    $currentMonthlySaveByState = ($total_row['Baseline'] - $total_row[$currentK]);
	    $currentMonthlySaveP = 0.0;
	    $fiscalTotal = 0.0;
	    $fiscalYearSavingsP = 0.0;

	    $n = 0;
	    foreach($total_row as $v) {
            $n++;
            if($n<=2) {
                continue;
            }
            array_push($forecastedSavingByMonth, ($total_row['Baseline'] - $v));
            $fiscalTotal += $v;
	    }

        $currentAnnualSavings = $total_row['Baseline']*12 - $fiscalTotal;
	    if($total_row['Baseline']!=0) {
            $currentMonthlySaveP = round($currentMonthlySaveByState/$total_row['Baseline'], 2);
            $fiscalYearSavingsP = round(($total_row['Baseline']*12 - $fiscalTotal) / ($total_row['Baseline']*12), 2);
            $currentAnnualSavingsP = $currentAnnualSavings/$total_row['Baseline']*12;
	    }

        $plannedSavingByMonth_total = 0.0;
	    $forecastedSavingByMonth_total = 0.0;
	    $performanceToPlan_total = 0.0;
	    $sharedSavings_total = 0.0;
	    $netWalgreensSave_total = 0.0;
	    for($n=2; $n<count($forecastedSavingByMonth); $n++) {
            $performanceToPlan[$n] = $forecastedSavingByMonth[$n] - $plannedSavingByMonth[$n];
            $netWalgreensSave[$n] = $forecastedSavingByMonth[$n] - $sharedSavings[$n];

            $plannedSavingByMonth_total += $plannedSavingByMonth[$n];
            $forecastedSavingByMonth_total += $forecastedSavingByMonth[$n];
            $performanceToPlan_total += $performanceToPlan[$n];
            $sharedSavings_total += $sharedSavings[$n];
            $netWalgreensSave_total += $netWalgreensSave[$n];
	    }
	    
	    fputcsv($fp, array_merge(add2columnsEF($total_row, $total_ef_row), array($currentMonthlySaveByState, $currentMonthlySaveP.'%', $fiscalTotal, $fiscalYearSavingsP.'%', $currentAnnualSavings, $currentAnnualSavingsP.'%')), ",", '"');
	    fputcsv($fp, array('', ''), ",", '"');	    
	    fputcsv($fp, array_merge(add2columns($plannedSavingByMonth), array('', '', $plannedSavingByMonth_total)), ",", '"');
	    fputcsv($fp, array_merge(add2columns($forecastedSavingByMonth), array('', '', $forecastedSavingByMonth_total)), ",", '"');
	    fputcsv($fp, array_merge(add2columns($performanceToPlan), array('', '', $performanceToPlan_total)), ",", '"');
	    fputcsv($fp, array_merge(add2columns($sharedSavings), array('', '', $sharedSavings_total)), ",", '"');
	    fputcsv($fp, array_merge(add2columns($netWalgreensSave), array('', '', $netWalgreensSave_total)), ",", '"');
	}

	private function loadOptions() {
		$this->load->model('MaterialsModel');
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
		
		$this->load->model('FeeTypeModel');
		$this->assigns['data']->feeOptions = $this->FeeTypeModel->getListForSelect();
		
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