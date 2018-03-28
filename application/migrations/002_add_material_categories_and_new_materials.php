<?php 
defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_Add_material_categories_and_new_materials extends CI_Migration {
	
	public function up() {
		$this->dbforge->add_column('Materials', array(
				'categoryId' => array(
					'type' => 'int',
					'unsigned' => true,
					'default' => 1
				)
			)
		);
		
		$this->db->query("
			INSERT INTO Materials (`name`, `companyId`, `categoryId`) 
			VALUES ('MSW', 1, 2), ('Hazardous', 1, 2), ('CND', 1, 2), ('Special - Expired Product', 1, 2)
		");
		
		$this->db->query("
			INSERT INTO Materials (`name`, `companyId`, `categoryId`) 
			VALUES ('Recycling Lot', 1, 3), ('Lamps - 8ft', 1, 3), ('Lamps - 4ft', 1, 3), ('Lamps -HID', 1, 3), ('Electronics', 1, 3), ('Hazardous', 1, 3)
		");
		
	}	
	
	public function down() {
		$this->db->query("
			DELETE FROM Materials WHERE categoryId > 1
		");
	}
}