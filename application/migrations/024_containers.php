<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_Containers extends CI_Migration {

    public function up() {
  	
    	$this->db->query("INSERT INTO Containers (name, companyId, containerType) VALUES(\"Bulk Items\", 1, \"Other\")");
   		
    }
}