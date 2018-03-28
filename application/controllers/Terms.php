<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
include 'application/controllers/Front.php';

class Terms extends Front {

	public function index()
	{
		$this->assigns['data'] = new Placeholder();
		$this->load->view('terms', $this->assigns);
	}
}

/* End of file Privacy.php */
/* Location: ./application/controllers/Privacy.php */