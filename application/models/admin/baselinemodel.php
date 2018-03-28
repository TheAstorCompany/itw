<?php

/**
 * Created by PhpStorm.
 * User: palsandeep
 * Date: 6/8/17
 * Time: 10:24 AM
 */
class BaselineModel extends CI_Model {
    public function __construct() {
        parent::__construct();
        $this->load->database();
    }

}