<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_Services_and_containers extends CI_Migration {

    public function up() {
   		$this->db->query("
			ALTER TABLE `Containers` ADD `containerType` ENUM( 'Opentop', 'Rolloff', 'Compactor', 'Dumpster', 'Other' ) NOT NULL;
   		");
   		
   		$this->db->query("
			UPDATE `Containers` SET `containerType` = 
			IF (name LIKE 'Opentop%', 'Opentop',
			IF (name LIKE 'Rolloff%', 'Rolloff',
			IF (name LIKE 'Compactor%', 'Compactor',
			IF (name LIKE 'Dumpster%', 'Dumpster',
			'Other'))))
   		");
   		
   		$this->db->query('DELETE FROM `VendorServices`');
   		
   		$this->db->query("
   			ALTER TABLE `VendorServices` ADD `materialId` INT NOT NULL 
   		");
   		
   		$this->db->query("
			ALTER TABLE `VendorServices` ADD INDEX ( `materialId` ) 
   		");
   		
   		$this->db->query("
			ALTER TABLE `VendorServices` ADD FOREIGN KEY ( `materialId` ) REFERENCES `Materials` (
			`id`
			) ON DELETE CASCADE ;   		
   		");
   		
   		
    }
}