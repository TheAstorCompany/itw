<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
include 'application/controllers/admin/Auth.php';

class Help extends Auth {

	public function index() {
		$this->load->model('Companies');
		$company = $this->Companies->getById($this->assigns['_companyId']);
		$this->assigns['company'] = $company;
		
		if ($this->input->post('submit')) {
			
			$this->load->library('form_validation');
			
			$this->form_validation->set_rules('subject', 'Subject', 'required|trim');
			$this->form_validation->set_rules('message', 'Message', 'required|trim');
			
			if ($this->form_validation->run() == true) {
				
				$this->load->library('email');
				$user = $this->session->userdata('USER');
				
				$this->email->from(ASTOR_EMAIL, ASTOR_NAME);
				$this->email->reply_to($user->email, $user->firstName . ' ' . $user->lastName);
				$this->email->to(ASTOR_EMAIL);
			
				$message = <<<EOD
Help request from  {$user->firstName}  {$user->lastName}\n
Subject: {$this->input->post('subject')}\n
Message:\n
\t{$this->input->post('message')}
EOD;
				
				$this->email->subject('Help request - ' . $this->input->post('subject'));
				$this->email->message($message);				
				$this->email->send();

				$this->session->set_flashdata('info', 'Your request has been sent.');
			
				redirect('admin/Help');
				return;
			}
			
			
		}
		
		$this->assigns['data'] = new Placeholder();
		$this->load->view('admin/help', $this->assigns);
	}

}

/* End of file SupportRequest.php */
/* Location: ./application/controllers/admin/Help.php */