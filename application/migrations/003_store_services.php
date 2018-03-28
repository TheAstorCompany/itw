<?php 
defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_Store_services extends CI_Migration {
	
	public function up() {
		
		$this->db->query("
			INSERT INTO StoreServiceDurations (`name`, `companyId`) 
			VALUES ('Temporary', 1), ('Permanent', 1), ('Other', 1)
		");
		
		$this->db->query("
			INSERT INTO StoreServicePurposes (`name`, `companyId`) 
			VALUES ('Normal Service', 1), ('Pickup', 1), ('Haul', 1), ('Other', 1)
		");
		
	}	
	
	public function down() {
		$this->db->query("
			DELETE FROM StoreServicePurposes
		");
		
		$this->db->query("
			DELETE FROM StoreServiceDurations
		");
	}
}