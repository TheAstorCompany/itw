<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
include 'application/controllers/Front.php';

class RequestService extends Front {

	public function index() {
		$this->load->model('Companies');
		$company = $this->Companies->getById($this->assigns['_companyId']);
		$this->assigns['company'] = $company;
		
		if ($this->input->post('submit')) {
			
			$this->load->library('form_validation');
			
			$this->form_validation->set_rules('firstName', 'First Name', 'required|trim');
			$this->form_validation->set_rules('lastName', 'Last Name', 'trim');			
			$this->form_validation->set_rules('phone', 'Phone', 'trim');
			$this->form_validation->set_rules('message', 'Message', 'required|trim');
			$this->form_validation->set_rules('email', 'email', 'required|trim|valid_email');
			
			$this->form_validation->set_rules('location', 'Location', 'required|trim');
			$this->form_validation->set_rules('locationId', 'Location', 'callback_autocomplete_required|trim');
			
			if ($this->form_validation->run() == true) {
				
				$this->load->library('email');
				$user = $this->session->userdata('USER');
				
				$this->email->from(ASTOR_EMAIL, ASTOR_NAME);
				$this->email->reply_to($this->input->get_post('email'), 
				"{$this->input->get_post('firstName')}  {$this->input->get_post('lastName')}");
				$this->email->to(ASTOR_EMAIL);
			
				$message = "
Support Request from {$this->input->get_post('firstName')}  {$this->input->get_post('lastName')}\n
email: {$this->input->get_post('email')}\n
Phone: {$this->input->get_post('phone')}\n
Location: {$this->input->get_post('location')}\n
Message:\n
\t{$this->input->get_post('message')}";
			
				$this->email->subject("Support Request from {$this->input->get_post('firstName')}  {$this->input->get_post('lastName')}");
				$this->email->message($message);				
				$this->email->send();

				$this->session->set_flashdata('info', 'Your request has been sent.');
			
				redirect('RequestService');
				return;
			}		
			
		}
		
		$this->assigns['data'] = new Placeholder();
		$this->load->view("requestservice", $this->assigns);
	}
	public function autocompleteLocation() {
		$this->load->model('admin/Autocomplete');		
		
		header("Content-Type: application/json");
		echo json_encode($this->Autocomplete->supportRequestLocation($this->input->get('term'), $this->assigns['_companyId']));
	}
	public function autocomplete_required($input) {
		if (empty($input)) {
			$this->form_validation->set_message('autocomplete_required', 'The %s field must be autocompleted!');
			return false;
		}
		return true;
	}
}

/* End of file front.php */
/* Location: ./application/controllers/front.php */