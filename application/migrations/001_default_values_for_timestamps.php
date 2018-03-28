<?php 
defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_Default_values_for_timestamps extends CI_Migration {
	
	public function up() {

		$this->db->query("
			UPDATE `RecyclingInvoices` set lastUpdated = CURRENT_TIMESTAMP where lastUpdated is null
		");
		
		$this->db->query("
			UPDATE `WasteInvoices` set lastUpdated = CURRENT_TIMESTAMP where lastUpdated is null
		");
		
		$this->db->query("
			ALTER TABLE `RecyclingInvoices` CHANGE COLUMN `lastUpdated` `lastUpdated` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP  ;
		");
		
		$this->db->query("
			ALTER TABLE `WasteInvoices` CHANGE COLUMN `lastUpdated` `lastUpdated` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP ;
		");
		
		$this->db->query("
			ALTER TABLE `Stores` CHANGE COLUMN `lastUpdated` `lastUpdated` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP  ;
		");
		
		$this->db->query("
			ALTER TABLE `DistributionCenters` CHANGE COLUMN `lastUpdated` `lastUpdated` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP ;
		");
		
	}	
	
	public function down() {
		$this->db->query("
			ALTER TABLE `RecyclingInvoices` CHANGE COLUMN `lastUpdated` `lastUpdated` TIMESTAMP DEFAULT NULL;
		");
		
		$this->db->query("
			ALTER TABLE `WasteInvoices` CHANGE COLUMN `lastUpdated` `lastUpdated` TIMESTAMP DEFAULT NULL;
		");
		
		$this->db->query("
			ALTER TABLE `Stores` CHANGE COLUMN `lastUpdated` `lastUpdated` TIMESTAMP DEFAULT NULL;
		");
		
		$this->db->query("
			ALTER TABLE `DistributionCenters` CHANGE COLUMN `lastUpdated` `lastUpdated` TIMESTAMP DEFAULT NULL;
		");
	}
}