<?php 
defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_Astor_v2_part2 extends CI_Migration {
	
	public function up() {
		$this->db->query("
			ALTER TABLE `SupportRequests` ADD `locationType` ENUM( 'STORE', 'DC' ) NULL AFTER `locationName` 			
		");
		
		$this->db->query("
			CREATE UNIQUE INDEX uniq
				ON RecyclingInvoiceAddedMaterials (invoiceId, materialId, unitId)
		");
		
		$this->db->query("
			ALTER TABLE `VendorServices` ADD `locationId` INT NOT NULL 
		");
		
		$this->db->query("
			ALTER TABLE `VendorServices` ADD `locationType` ENUM( 'STORE', 'DC' ) NOT NULL 
		");
		
		$this->db->query("
			ALTER TABLE `VendorServices` ADD INDEX ( `vendorId` , `locationId` , `locationType` ) ;
		");
		
		$this->db->query("
			ALTER TABLE `VendorServices` ADD `category` TINYINT NOT NULL DEFAULT '0' AFTER `purposeId` ,
				ADD INDEX ( `category` ) 
		");
		
		$this->db->query("
			ALTER TABLE `VendorServices` ADD `startDate` DATE NOT NULL ,
				ADD `endDate` DATE NOT NULL ,
				ADD INDEX ( `startDate` , `endDate` ) 
		");
		
		$this->db->query("
			ALTER TABLE `RecyclingCharges` ADD `locationId` BIGINT( 20 ) NOT NULL ,
				ADD `locationName` VARCHAR( 64 ) NOT NULL ,
				ADD `locationType` ENUM( 'STORE', 'DC' ) NOT NULL
		");
		
		$this->db->query("
			ALTER TABLE `RecyclingChargesFees` ADD `waived` BOOLEAN NOT NULL;
		");
		
		$this->db->query("
			ALTER TABLE `RecyclingCharges` ADD `invoiceNumber` VARCHAR( 64 ) NOT NULL
		");
	}
	
	public function down() {
	}
}