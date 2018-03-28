<?php 
defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_Astor_new_user extends CI_Migration {
	
	public function up() {
	
		$this->db->query("
			insert into CompanyUsers (username, password, firstName, lastName, title, email, phone, accessLevel, active, companyid)
				values (
				'Sara', '057ba03d6c44104863dc7361fe4578965d1887360f90a0895882e58a6248fc86596dff7ca04eb17b0184fd32a7229d2c28d71de29752b6663a2653f0bf9904b3',
				'Sara', 'Sara',
				'N/A',
				'Sara@astorrecycling.com', '555-555-5555',
				'ADMIN', 1,
				1);
		");
	}	
	
	public function down() {
		$this->db->query("
			DELETE FROM CompanyUsers WHERE email = 'Sara@astorrecycling.com'
		");
	}
}