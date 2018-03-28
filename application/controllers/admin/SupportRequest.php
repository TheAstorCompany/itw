<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
include 'application/controllers/admin/Auth.php';

class SupportRequest extends Auth {

	public function __construct() {
		parent::__construct();
		$this->load->model('admin/SupportRequestServiceTypes');
		$this->load->model('admin/Containers');
		$this->assigns['data'] = new Placeholder();

		$this->assigns['data']->SupportRequestServiceTypes = $this->SupportRequestServiceTypes->getListForSelect($this->assigns['_companyId']);
		$this->assigns['data']->SupportRequestContainers = $this->Containers->getListForSelect($this->assigns['_companyId']);
        $this->assigns['data']->SupportRequestContainersAll = $this->Containers->getAll($this->assigns['_companyId']);
		$this->assigns['data']->resolvedOptions = array(
            1 => 'Yes',
            0 => 'No'
		);
	}

	public function task() {
        $result = array('html'=>'', 'error'=>'');
        $this->assigns['tasks'] = $this->loadTasksFromPost();
        $this->load->library('form_validation');
        $this->form_validation->set_message('greater_than', "The %s is required.");
        $this->form_validation->set_rules('containerId', 'Container','required|trim');
        $this->form_validation->set_rules('purposeType', 'For','required|trim');
        $this->form_validation->set_rules('quantity', 'Quantity','numeric|trim');
        $this->form_validation->set_rules('purposeId', 'Purpuse','numeric|required|greater_than[0]|trim');
        $this->form_validation->set_rules('serviceDate', 'Service date', 'date|required|trim');

        if ($this->form_validation->run() == true) {
            if (array_key_exists('containerId', $_POST)) {
                if ($_POST['containerId'] == '0') {
                    $_POST['containerId'] = NULL;
                }
            }

            $this->load->model('admin/SupportRequestModel');
            $this->load->helper('dates');
            $task = $_POST;
            if(isset($task['serviceDate']) && $task['serviceDate']=='') {
                $task['serviceDate'] = null;
            } else {
                $task['serviceDate'] = USToSQLDate($task['serviceDate']);
            }
            if(isset($task['deliveryDate']) && $task['deliveryDate']=='') {
                $task['deliveryDate'] = null;
            } else {
                $task['deliveryDate'] = USToSQLDate($task['deliveryDate']);
            }
            if(isset($task['removalDate']) && $task['removalDate']=='') {
                $task['removalDate'] = null;
            } else {
                $task['removalDate'] = USToSQLDate($task['removalDate']);
            }

            $task['id'] = $this->SupportRequestModel->addTask($task);
            $this->assigns['tasks'][] = (object)$task;
        } else {
            $result['error'] = validation_errors();
        }

        $result['html'] = $this->load->view('admin/support_request/tasks_ajax', $this->assigns, true);
        header('content-type:application/json');
        echo json_encode($result);
	}

    public function deleteTask() {
        if(isset($_POST['task_id'])) {
            $this->load->model('admin/SupportRequestModel');

            $taskId = intval($_POST['task_id']);
            $this->SupportRequestModel->deleteTask($taskId);
        }
    }

    private function loadTasksFromPost() {
        $data = array();

        if(isset($_POST['existing_tasks'])) {
            foreach($_POST['existing_tasks'] as $item) {
                if(isset($item['containerId']) && empty($item['containerId'])) {
                    unset($item['containerId']);
                }
                $data[] = (object)$item;
            }
            unset($_POST['existing_tasks']);
        }

        return $data;
    }

	public function index() {
		if ($this->input->post('form_submit')) {
//			$this->assigns['tasks'] = unserialize($this->session->userdata('tasks'));
//			if (!is_array($this->assigns['tasks'])) {
//				$this->assigns['tasks'] = array();
//			}
			$this->initRules();
			if ($this->form_validation->run() == true) {
				$this->assigns['taskData'] = new Placeholder();
				
				$this->assigns['tasks'] = array();
				
				if (array_key_exists('containerId', $_POST)) {
				    if ($_POST['containerId'] == '0') {
					    $_POST['containerId'] = NULL;
				    }
				}
				
				$this->assigns['tasks'][0]->purposeId = $this->input->post('purposeId');
				$this->assigns['tasks'][0]->purposeType = $this->input->post('purposeType');
				$this->assigns['tasks'][0]->quantity = ($this->input->post('quantity'))?$this->input->post('quantity'):0;
				$this->assigns['tasks'][0]->containerId = ($this->input->post('containerId'))?$this->input->post('containerId'):null;
				$this->assigns['tasks'][0]->serviceDate = $this->input->post('serviceDate');
				$this->assigns['tasks'][0]->deliveryDate = ($this->input->post('deliveryDate'))?$this->input->post('deliveryDate'):null;
				$this->assigns['tasks'][0]->removalDate = ($this->input->post('removalDate'))?$this->input->post('removalDate'):null;
				$this->assigns['tasks'][0]->description = ($this->input->post('description'))?$this->input->post('description'):null;
								
				$this->load->model('admin/SupportRequestModel');

				if (!$this->assigns['_isAdmin']) {
					unset($_POST['resolved']);
					unset($_POST['lastUpdated']);
					unset($_POST['internalNotes']);
				}

				if (!$this->SupportRequestModel->add($this->assigns['_companyId'], $this->session->userdata('USER')->id, $_POST, $this->assigns['tasks'])) {
					$this->session->set_flashdata('info', 'An Unexpected error occurred during adding support request');
				} else {
					//adding was successfull
					$this->session->set_flashdata('info', 'The request was successfully added.');
					redirect('admin/SupportRequest/index');
						
					return;
				}
			}
			//we have errors..
		} else {
			$this->session->set_userdata('tasks', serialize(array()));
		}
		$this->load->view('admin/support_request/support_request_add', $this->assigns);
	}
	
	public function delete($id) {
		$id = (int)$id;
		
		$this->load->model('admin/SupportRequestModel');
		
		if ($this->SupportRequestModel->deleteById($id)) {
			$this->session->set_flashdata('info', 'Request #'.$id.' has been successfully deleted.');
		} else {
			$this->session->set_flashdata('info', 'Request #'.$id.' was not found!');
		}
		
		redirect('admin/SupportRequest/history');		
	}
	
	public function autocomplete_required($input) {
		if (empty($input)) {
			$this->form_validation->set_message('autocomplete_required', 'The %s field must be autocompleted!');
			return false;
		}

		return true;
	}
	private function initRules() {
		$this->load->library('form_validation');

		$this->form_validation->set_message('greater_than', "The %s is required.");
		$this->form_validation->set_rules('firstName', 'First name', 'required|trim');
		$this->form_validation->set_rules('lastName', 'Last name', 'trim');
		$this->form_validation->set_rules('email', 'Email', 'trim|valid_email');
		$this->form_validation->set_rules('phone', 'Phone','required|trim');

		$this->form_validation->set_rules('locationName', 'Location Name','required|trim');
		$this->form_validation->set_rules('locationId', 'Location Name',' callback_autocomplete_required|numeric|trim');
		$this->form_validation->set_rules('vendorName', 'Vendor Name','required|trim');
		$this->form_validation->set_rules('vendorId', 'Vendor Name',' callback_autocomplete_required|numeric|trim');
		$this->form_validation->set_rules('locationType', 'Location type','trim');
		$this->form_validation->set_rules('po', 'PO#','trim');
		$this->form_validation->set_rules('cbre', 'CBRE #','required|trim');

		//$this->form_validation->set_rules('serviceTypeId', 'Purpuse','numeric|required|greater_than[0]|trim');
		//$this->form_validation->set_rules('containerId', 'Container','numeric|required|greater_than[0]|trim');

		//$this->form_validation->set_rules('quantity', 'Quantity','numeric|required|trim');

		//$this->form_validation->set_rules('deliveryDate', 'Delivery date','date|required|trim');
		//$this->form_validation->set_rules('removalDate', 'Removal date','date|trim');
		//$this->form_validation->set_rules('containerId', 'Container', 'numeric|required|trim');
		//$this->form_validation->set_rules('wasteRecycle', 'Waste or Recycle', 'numeric|required|trim');

		$this->form_validation->set_rules('description', 'Description','trim');
		$this->form_validation->set_rules('internalNotes', 'Internal notes','trim');
		$this->form_validation->set_rules('resolved', 'Resolved','numeric|trim');
		
		$this->form_validation->set_rules('containerId', 'Container','required|trim');
		$this->form_validation->set_rules('purposeType', 'For','required|trim');
		$this->form_validation->set_rules('quantity', 'Quantity','numeric|trim');
		$this->form_validation->set_rules('purposeId', 'Purpuse','numeric|required|greater_than[0]|trim');
		$this->form_validation->set_rules('serviceDate', 'Service date', 'date|required|trim');
	}

	public function history() {
		$this->load->view('admin/support_request/support_request_list', $this->assigns);
	}

	public function edit($id) {
		$id = (int)$id;

		$this->load->model('admin/SupportRequestServiceTypes');
		$this->load->model('admin/SupportRequestContainers');
		$this->load->model('admin/SupportRequestModel');


		if (!$this->input->post('submit_form')) {
			$data = (array)$this->SupportRequestModel->getById($this->assigns['_companyId'], $id);
			if (count($data['tasks']) > 0) {
				$this->assigns['tasks'] = $data['tasks'];
			} else {
                $this->assigns['tasks'] = array();
			}
			$this->assigns['data'] = (array)$this->assigns['data'];
			$data['description'] = '';
			$this->assigns['data'] = array_merge($this->assigns['data'], $data);
			$this->assigns['data'] = (object)$this->assigns['data'];
			if (!$this->assigns['data']) {
				$this->session->set_flashdata('info', 'The record you are trying to edit does not exists.');
				redirect('admin/SupportRequest/history');
			}
		} else {
            $this->assigns['tasks'] = $this->loadTasksFromPost();
			
			$this->load->library('form_validation');

			$this->form_validation->set_message('greater_than', "The %s is required.");
			$this->form_validation->set_rules('firstName', 'First name', 'trim');
			$this->form_validation->set_rules('lastName', 'Last name', 'trim');
			$this->form_validation->set_rules('email', 'Email', 'trim|valid_email');
			$this->form_validation->set_rules('phone', 'Phone','trim');

			$this->form_validation->set_rules('locationName', 'Location Name','required|trim');
			$this->form_validation->set_rules('locationId', 'Location Name',' callback_autocomplete_required|numeric|trim');
			$this->form_validation->set_rules('locationType', 'Location type','trim');
			$this->form_validation->set_rules('po', 'PO#','trim');
			$this->form_validation->set_rules('crbe', 'CBRE #','trim');
			
			$this->form_validation->set_rules('description', 'Description', 'trim');
			$this->form_validation->set_rules('internalNotes', 'Internal notes','trim');
			$this->form_validation->set_rules('resolved', 'Resolved','numeric|trim');

			if ($this->form_validation->run() == true) {
				if($_POST['vendorId']==null)
				    unset($_POST['vendorId']);
				$this->load->model('admin/SupportRequestModel');

				if (!$this->assigns['_isAdmin']) {
					unset($_POST['resolved']);
					unset($_POST['lastUpdated']);
					unset($_POST['internalNotes']);
				}

				$this->SupportRequestModel->edit($this->assigns['_companyId'], $this->session->userdata('USER')->id, $id, $_POST, $this->assigns['tasks']);
				$this->session->set_flashdata('info', 'The request #'.$id.' was successfully updated.');
				redirect('admin/SupportRequest/history');

				return;
			}
			
			//we have errors..
			$this->assigns['data']->id = $id;
		}

		$this->load->view('admin/support_request/support_request_edit', $this->assigns);
	}

	private function getHistory() {
		$this->load->model('admin/SupportRequestModel');

		$sortColumn = null;
		$sortDir = null;

		if ($this->input->get('iSortingCols') > 0) {
			if ($this->input->get('bSortable_0')) {
				switch ($this->input->get('iSortCol_0')) {
					case 0:
						$sortColumn = 'id';
						break;
					case 1:
						$sortColumn = 'lastUpdated';
						break;
					case 2:
						$sortColumn = 'locationName';
						break;
                    case 3:
                        $sortColumn = 'cbre';
                        break;
					case 4:
						$sortColumn = 'lastName';
						break;
					case 5:
						$sortColumn = 'phone';
						break;
					case 6:
						$sortColumn = 'purposename';
						break;
					case 7:
						$sortColumn = 'description';
						break;
					case 8:
						$sortColumn = 'containername';
						break;
					case 9:
						$sortColumn = 'serviceDate';
						break;
					case 10:
						$sortColumn = 'complete';
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
            $searchFilter['complete'] = $this->input->get('filter_complete');
        }
		
		$data = $this->SupportRequestModel->getList($this->assigns['_companyId'], $this->input->get('iDisplayStart'), $this->input->get('iDisplayLength'), $searchFilter, $sortColumn, $sortDir);
		$ajaxData = array();
		$this->load->helper('dates');
		
		foreach ($data['data'] as $item) {
			$separatorDate=SQLToUSDate($item->serviceDate);
			if($separatorDate!="")
			    $separatorDate = "• ".$separatorDate;
			$ajaxData[] = array(
				'DT_RowClass' => 'grade' . ($item->complete ? 'A':'X'),			
				'<a title="Edit support request #'.$item->id.'" href="'.base_url().'admin/SupportRequest/edit/'.$item->id.'">' . $item->id . '</a>',
			    SQLToUSDate($item->lastUpdated),
			    $item->locationName,
                $item->cbre,
			    '<a href="mailto:'.$item->email.'">'.$item->firstName . ' ' . $item->lastName .'</a>',
			    $item->phone,
			    $item->purposename,
			    $item->description,
			    $item->containername,
			    $separatorDate,
			    ($item->complete ? 'Complete':'Incomplete')
			);
		}

		return array(
			'aaData' => $ajaxData,
			'iTotalRecords' => $data['records'],
			'iTotalDisplayRecords' => $data['records']
		);
	}	
	
	public function ajaxList() {
		header('Content-type: application/json');
		
		echo json_encode($this->getHistory());
	}
	
	public function csvList() {
		//export
		$this->load->helper('download');
		$file = fopen('php://temp/maxmemory:'. (12*1024*1024), 'r+');
		fputcsv($file, array(
			'Service#',
			'Date',
			'Location',
            'CBRE#',
			'Contact',
			'Phone#',
			'Purpose',
			'Description',
			'Container',
			'Service date',
			'Complete?'
		));
		$data = $this->getHistory();
		foreach ($data['aaData'] as $row) {
			fputcsv($file, array(
				strip_tags($row[0]),
				strip_tags($row[1]),
                strip_tags($row[2]),
				strip_tags($row[3]),
				strip_tags($row[4]),
				strip_tags($row[5]),
				strip_tags(str_replace ( "•", "~", $row[6])),
				strip_tags(str_replace ( "•", "~", $row[7])),
				strip_tags(str_replace ( "•", "~", $row[8])),
				strip_tags(str_replace ( "•", "~", $row[9])),
				strip_tags($row[10])
			));
		}

		rewind($file);
		$csv = stream_get_contents($file);
		fclose($file);

		force_download('SupportHistory.csv', $csv);
	}

	public function autocompleteLocation() {
		$this->load->model('admin/Autocomplete');


		header("Content-Type: application/json");
		echo json_encode($this->Autocomplete->supportRequestLocation($this->input->get('term'), $this->assigns['_companyId']));
	}

    public function checkLocation(){
        $locationId=intval($_POST['id']);
        if($locationId<=0)
            return;

        echo $this->db->query("SELECT * FROM LampRequests WHERE locationId={$locationId} AND (requestDate>'".date("Y-m-d",strtotime("-1 year"))."' OR requestDate IS NULL)")->num_rows;
    }
}

/* End of file SupportRequest.php */
/* Location: ./application/controllers/admin/SupportRequest.php */