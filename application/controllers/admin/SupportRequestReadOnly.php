<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
include 'application/controllers/ReadOnly.php';

class SupportRequestReadOnly extends ReadOnly {

	public function __construct() {
		parent::__construct();
		$this->load->model('admin/SupportRequestServiceTypes');
		$this->load->model('admin/Containers');
		$this->assigns['data'] = new Placeholder();

		$this->assigns['data']->SupportRequestServiceTypes = $this->SupportRequestServiceTypes->getListForSelect($this->assigns['_companyId']);
		$this->assigns['data']->SupportRequestContainers = $this->Containers->getListForSelect($this->assigns['_companyId']);
		$this->assigns['data']->resolvedOptions = array(
		1 => 'Yes',
		0 => 'No'
		);
	}
	public function task() {
		if ('XMLHttpRequest' == @$_SERVER['HTTP_X_REQUESTED_WITH']) {
			$result = array('html'=>'', 'error'=>'');
			$this->assigns['taskData'] = new Placeholder();
			$this->assigns['tasks'] = unserialize($this->session->userdata('tasks'));
			if (!is_array($this->assigns['tasks'])) {
				$this->assigns['tasks'] = array();
			}
			switch ($this->input->get_post('action')) {
				case 'delete':
					unset($this->assigns['tasks'][$this->input->get_post('index')]);
					break;
				default:
					$this->load->library('form_validation');
					$this->form_validation->set_message('greater_than', "The %s is required.");
					$this->form_validation->set_rules('containerId', 'Container','required|trim');
					$this->form_validation->set_rules('purposeType', 'For','required|trim');
					$this->form_validation->set_rules('quantity', 'Quantity','numeric|trim');
					$this->form_validation->set_rules('purposeId', 'Purpuse','numeric|required|greater_than[0]|trim');
					if ($this->form_validation->run() == true) {
						if (array_key_exists('containerId', $_POST)) {
							if ($_POST['containerId'] == '0') {
								$_POST['containerId'] = NULL;
							}
						}
						$this->assigns['tasks'][] = (object)$_POST;
					} else {
						$result['error'] = validation_errors();
					}
			}
			//list
			$this->session->set_userdata('tasks', serialize($this->assigns['tasks']));
			ob_start();
			$this->load->view('admin/support_request_read_only/tasks_ajax', $this->assigns);
			$result['html'] = ob_get_clean();
			header('content-type:application/json');
			echo json_encode($result);
		}
	}
	public function index() {
		if ($this->input->post('form_submit')) {
			$this->assigns['tasks'] = unserialize($this->session->userdata('tasks'));
			if (!is_array($this->assigns['tasks'])) {
				$this->assigns['tasks'] = array();
			}
			$this->initRules();
			if ($this->form_validation->run() == true) {
				$this->load->model('admin/SupportRequestModel');

				if (!$this->assigns['_isAdmin']) {
					unset($_POST['resolved']);
					unset($_POST['lastUpdated']);
					unset($_POST['internalNotes']);
				}

				/*
				if (!$this->SupportRequestModel->add($this->assigns['_companyId'], $this->session->userdata('USER')->id, $_POST, $this->assigns['tasks'])) {
					$this->session->set_flashdata('info', 'An Unexpected error occurred during adding support request');
				} else {
					//adding was successfull
					$this->session->set_flashdata('info', 'The request was successfully added.');
					redirect('admin/SupportRequest/index');
						
					return;
				}
				*/
			}
			//we have errors..
		} else {
			$this->session->set_userdata('tasks', serialize(array()));
		}
		$this->load->view('admin/support_request_read_only/support_request_add', $this->assigns);
	}
	
	public function delete($id) {
		$id = (int)$id;
		
		$this->load->model('admin/SupportRequestModel');
		
		/*
		if ($this->SupportRequestModel->deleteById($id)) {
			$this->session->set_flashdata('info', 'Request #'.$id.' has been successfully deleted.');
		} else {
			$this->session->set_flashdata('info', 'Request #'.$id.' was not found!');
		}
		*/
		
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
		$this->form_validation->set_rules('firstName', 'First name', 'trim');
		$this->form_validation->set_rules('lastName', 'Last name', 'trim');
		$this->form_validation->set_rules('email', 'Email', 'trim|valid_email');
		$this->form_validation->set_rules('phone', 'Phone','trim');

		$this->form_validation->set_rules('locationName', 'location Name','required|trim');
		$this->form_validation->set_rules('locationId', 'location Name',' callback_autocomplete_required|numeric|trim');
		$this->form_validation->set_rules('locationType', 'Location type','trim');
		$this->form_validation->set_rules('po', 'PO#','trim');
		$this->form_validation->set_rules('crbe', 'CBRE #','trim');

		//$this->form_validation->set_rules('serviceTypeId', 'Purpuse','numeric|required|greater_than[0]|trim');
		//$this->form_validation->set_rules('containerId', 'Container','numeric|required|greater_than[0]|trim');

		//$this->form_validation->set_rules('quantity', 'Quantity','numeric|required|trim');

		//$this->form_validation->set_rules('deliveryDate', 'Delivery date','date|required|trim');
		//$this->form_validation->set_rules('removalDate', 'Removal date','date|trim');
		//$this->form_validation->set_rules('containerId', 'Container', 'numeric|required|trim');
		//$this->form_validation->set_rules('wasteRecycle', 'Waste or Recycle', 'numeric|required|trim');

		//$this->form_validation->set_rules('description', 'Description','required|trim');
		$this->form_validation->set_rules('internalNotes', 'Internal notes','trim');
		$this->form_validation->set_rules('resolved', 'Resolved','numeric|trim');
	}

	public function history() {
		$this->load->view('admin/support_request_read_only/support_request_list', $this->assigns);
	}

	public function edit($id) {
		$id = (int)$id;

		$this->load->model('admin/SupportRequestServiceTypes');
		$this->load->model('admin/SupportRequestContainers');
		$this->load->model('admin/SupportRequestModel');


		if (!$this->input->post('form_submit')) {
			$data = (array)$this->SupportRequestModel->getById($this->assigns['_companyId'], $id);
			if (count($data['tasks']) > 0) {
				$this->assigns['tasks'] = $data['tasks'];
				$this->session->set_userdata('tasks', serialize($this->assigns['tasks']));
			} else {
				$this->session->set_userdata('tasks', serialize(array()));
			}
			$this->assigns['data'] = (array)$this->assigns['data'];
			$this->assigns['data'] = array_merge($this->assigns['data'], $data);
			$this->assigns['data'] = (object)$this->assigns['data'];
			if (!$this->assigns['data']) {
				$this->session->set_flashdata('info', 'The record you are trying to edit does not exists.');
				redirect('admin/SupportRequest/history');
			}
		} else {
			//$this->assigns['data'] = new Placeholder();
			//echo "<pre>";print_r($_POST);
			if (($tasks = unserialize($this->session->userdata('tasks'))) && is_array($tasks)) {
				$this->assigns['tasks'] = $tasks;
			} else {
				$this->assigns['tasks'] = array();
			}
			$this->initRules();
				
			if ($this->form_validation->run() == true) {
				$this->load->model('admin/SupportRequestModel');

				if (!$this->assigns['_isAdmin']) {
					unset($_POST['resolved']);
					unset($_POST['lastUpdated']);
					unset($_POST['internalNotes']);
				}

				//$this->session->set_flashdata('info', 'The request #'.$id.' was successfully updated.');
				redirect('admin/SupportRequest/history');

				return;
			}
			//we have errors..
			$this->assigns['data']->id = $id;
		}

		$this->load->view('admin/support_request_read_only/support_request_edit', $this->assigns);
	}

	public function ajaxList() {
		$this->load->model('admin/SupportRequestModel');

		header('Content-type: application/json');

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
						$sortColumn = 'firstName';
						break;
					case 4:
						$sortColumn = 'phone';
						break;
					case 5:
						$sortColumn = 'notes';
						break;
					case 6:
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

		$data = $this->SupportRequestModel->getList($this->assigns['_companyId'], $this->input->get('iDisplayStart'), $this->input->get('iDisplayLength'), $this->input->get('sSearch'), $sortColumn, $sortDir);
		$ajaxData = array();
		$this->load->helper('dates');
		
		foreach ($data['data'] as $item) {
			$ajaxData[] = array(
				'DT_RowClass' => 'grade' . ($item->complete ? 'A':'X'),			
				'<a title="Edit support request #'.$item->id.'" href="'.base_url().'admin/SupportRequest/edit/'.$item->id.'">' . $item->id . '</a>',
			SQLToUSDate($item->lastUpdated),
			$item->locationName,
				'<a href="mailto:'.$item->email.'">'.$item->firstName . ' ' . $item->lastName .'</a>',
			$item->phone,
			$item->notes,
			($item->complete ? 'Complete':'Incomplete')
			);
		}

		echo json_encode(array(
			'aaData' => $ajaxData,
			'iTotalRecords' => $data['records'],
			'iTotalDisplayRecords' => $data['records']
		));
	}

	public function autocompleteLocation() {
		$this->load->model('admin/Autocomplete');


		header("Content-Type: application/json");
		echo json_encode($this->Autocomplete->supportRequestLocation($this->input->get('term'), $this->assigns['_companyId']));
	}
}

/* End of file SupportRequest.php */
/* Location: ./application/controllers/admin/SupportRequest.php */