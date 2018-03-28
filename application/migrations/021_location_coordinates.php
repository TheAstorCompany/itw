<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_Location_coordinates extends CI_Migration {

    public function up() {
    	
    	$q = $this->db->query("
    		ALTER TABLE DistributionCenters ADD COLUMN lat FLOAT(10,6)
    	");
    	
    	$this->db->query("
				ALTER TABLE DistributionCenters ADD COLUMN lng FLOAT(10,6)
	   	");
    	
    	   		
   		$this->db->query("
			ALTER TABLE Stores ADD COLUMN lat FLOAT(10,6)
   		");
   		
   		$this->db->query("
   			ALTER TABLE Stores ADD COLUMN lng FLOAT(10,6) 
   		");
   		
    }
}