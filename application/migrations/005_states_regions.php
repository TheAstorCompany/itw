<?php 
defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_States_regions extends CI_Migration {
	
	public function up() {
	
		$this->db->query("
			UPDATE States SET region = 'West'
			WHERE  code in ('WA','OR','CA','NV','ID','UT','AZ','MT','WY','CO','NM','AK','HI')
		");
		
		$this->db->query("
			UPDATE States SET region = 'Midwest'
			WHERE  code in ('ND','SD','NE','MN','IA','MO','WI','IL','IN','KY', 'VI')
		");
		
		$this->db->query("
			UPDATE States SET region = 'South'
			WHERE  code in ('KS','OK','TX','AR','LA','MS','TN','AL','PR','FL')
		");
		
		$this->db->query("
			UPDATE States SET region = 'East'
			WHERE  code in ('MI','OH','WV','VA','MD','DE','NJ','NY','CT','RI','MA','VT','NH','ME','PA')
		");
		
		$this->db->query("
			UPDATE States SET region = 'Southeast'
			WHERE  code in ('NC','SC','GA','AL','FL','DW')
		");
		
		$this->db->query("
			ALTER TABLE Materials ADD COLUMN `EnergySaves` FLOAT DEFAULT 0
		");
		
		$this->db->query("
			ALTER TABLE Materials ADD COLUMN `CO2Saves` FLOAT DEFAULT 0
		");
		
		$this->db->query("
			UPDATE Materials SET EnergySaves = 14000, CO2Saves = 10 WHERE id = 3
		");
		
		$this->db->query("
			UPDATE Materials SET EnergySaves = 42, CO2Saves = 0.34 WHERE id = 2
		");
		
		$this->db->query("
			UPDATE Materials SET EnergySaves = 5774, CO2Saves = 1.3 WHERE id = 5
		");
		
		$this->db->query("
			UPDATE Materials SET EnergySaves = 642, CO2Saves = 0 WHERE id = 4
		");
		
		$this->db->query("
			UPDATE Materials SET EnergySaves = 390, CO2Saves = 2.5 WHERE id = 1
		");
		
		$this->db->query("
			UPDATE Materials SET EnergySaves = 4100, CO2Saves = 2.5 WHERE id = 6
		");
		
		
	}	
	
	public function down() {
		$this->db->query("
			UPDATE States SET region = NULL
		");
		
		$this->db->query("
			ALTER TABLE Materials DROP COLUMN `EnergySaves`
		");
		
	}
}