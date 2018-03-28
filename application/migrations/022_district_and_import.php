<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_District_and_import extends CI_Migration {
	
	private function removeQuoting($text) {
		$text = trim($text);
		
		return trim($text, '`');
	}
	
	private function isColumnExists($tableName, $columnName) {
		$columnName = $this->removeQuoting($columnName);

    	$q = $this->db->query("SHOW CREATE TABLE $tableName");
    	
    	
    	$result = (array)$q->row();  	
    	$regexp='/`'.$columnName.'`/mi';
    	preg_match($regexp,$result['Create Table'],$matches);
    	
    	if (!empty($matches)) {
    		return true;
    	}
    	
    	return false;
	}

    public function up() {
    	$query = $this->db->query("SELECT COUNT(id) as c FROM SupportRequestServiceTypes");
    	
    	if ($query->row()->c == 0) {
	    	$this->db->query("
				INSERT INTO `SupportRequestServiceTypes` (`id`, `name`, `companyId`) VALUES
				(1, 'Pickup', 1),
				(2, 'Haul', 1),
				(3, 'Relamping', 1),
				(4, 'Setup New Service', 1),
				(5, 'Support Call', 1),
				(6, 'Other', 1)
	    	");
    	}
    	
    	
    	$this->db->query("
    	CREATE TABLE IF NOT EXISTS District (
			number int(11) unsigned, 
			name varchar(50),
			unique index(number, name)
			) ENGINE = InnoDB;
    	");
    	
    	if (!$this->isColumnExists('Stores', 'districtId')) {
    		$this->db->query("ALTER TABLE `Stores` ADD `districtId` INT( 11 ) UNSIGNED NULL DEFAULT NULL;");
    		$this->db->query("ALTER TABLE `Stores` ADD INDEX `fk_district` ( `districtId` );");
	    	$this->db->query("
	    		ALTER TABLE `Stores` ADD FOREIGN KEY ( `districtId` ) REFERENCES `District` (
					`number`
				) ON DELETE SET NULL ON UPDATE SET NULL ;"
	    	);
    	}

    	$query = $this->db->query("SELECT id FROM States WHERE code = 'PR'");
    	if (empty($query->row()->id)) {
	    	$this->db->query("INSERT INTO `States` (
								`id` ,
								`name` ,
								`code` ,
								`region`
								)
								VALUES (
								NULL , 'Puerto Rico', 'PR', NULL
								);"
	    	);
    	}
    	
    	$this->db->query("UPDATE `States` SET `code` = 'WI' WHERE `States`.`id` =49;");
    	
    	$this->db->query("UPDATE `States` SET `code` = 'DE' WHERE `States`.`id` =8;");
    	
    	$query = $this->db->query("SELECT id FROM States WHERE code = 'DC'");
    	if (empty($query->row()->id)) {
	    	$this->db->query("
	    		INSERT INTO `States` (
					`id` ,
					`name` ,
					`code` ,
					`region`
					)
					VALUES (
					NULL , 'Washington DC', 'DC', 'West'
				)
			");
    	}
    		
    }
}