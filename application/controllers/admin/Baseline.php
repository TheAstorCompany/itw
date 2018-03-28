<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
include 'application/controllers/admin/Auth.php';

class Baseline  extends  Auth {


    public function index() {

        $this->load->view('admin/Baseline/baseline_index', $this->assigns);

    }
}
?>