<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
include 'application/controllers/Front.php';

class Dcexample extends Front {

	
	public function DashBoard() {
		$this->index();
	}
	
	public function index() {
		$this->load->view("dcexample", $this->assigns);
	}
	
	public function Store($tab) {
		$this->assigns["_main_controller"] = "Company";
		
		$this->assigns["_controller"] = $tab;
		$this->load->view("dcexample", $this->assigns);
	}
	
	public function DistributionCenters($tab) {
		$this->assigns["_main_controller"] = "DistributionCenters";
	
		$this->assigns["_controller"] = $tab;
		$this->load->view("dcexample", $this->assigns);
	}	
	
	public function Stores($tab, $view) {
		$this->assigns["_main_controller"] = "Stores";
	
		$this->assigns["_controller"] = $tab;
		
		
		if($view == "Region") {
			$this->load->view("regionexample", $this->assigns);
		} elseif ($view == "District") {
			$this->load->view("districtexample", $this->assigns);
		} elseif ($view == "Store") {
			$this->load->view("storeexample", $this->assigns);
		} else {
			$this->load->view("dcexample", $this->assigns);
		}
		
		
	}	
	
	
}

/* End of file front.php */
/* Location: ./application/controllers/front.php */