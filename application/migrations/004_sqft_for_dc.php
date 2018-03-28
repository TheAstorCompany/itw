<?php 
defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_Sqft_for_dc extends CI_Migration {
	
	public function up() {
		
		$this->dbforge->add_column('DistributionCenters', array(
				'squareFootage' => array(
					'type' => 'DOUBLE(11,2)',
					'unsigned' => true,
					'default' => '0.00'
				)
			)
		);
		
	}	
	
	public function down() {
	}
}