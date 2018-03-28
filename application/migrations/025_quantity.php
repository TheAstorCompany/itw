<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_Quantity extends CI_Migration {

    public function up() {  	
    	$this->db->query("ALTER TABLE RecyclingPurchaseOrder ADD COLUMN `quantity` int(10) unsigned DEFAULT NULL");   		
    }
}