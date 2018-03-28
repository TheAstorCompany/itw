<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Maps extends CI_Controller {

	public function __construct() {
		parent::__construct();
		$this->load->model("mapsmodel");
	}
	
	public function DashBoard() {
		$this->index();
	}

	
	public function DC ($period) {
		$this->mapsmodel->calculateData("DC", $period);
	}
	
	public function Store ($period) {
		$this->mapsmodel->calculateData("STORE", $period);
	}	
}

/* End of file front.php */
/* Location: ./application/controllers/front.php */