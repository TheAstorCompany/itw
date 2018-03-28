<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_Fk_update extends CI_Migration {

    public function up() {
  	
    	$this->db->query("
			ALTER TABLE `WasteInvoiceServices` CHANGE `durationId` `durationId` INT( 11 ) NULL DEFAULT NULL 
	   	");
   		
    }
}