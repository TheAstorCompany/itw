<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
include 'application/controllers/admin/Auth.php';

class Bids extends  Auth {

    public function index() {

        $this->load->view('admin/Bids/bids_index', $this->assigns);

    }

}