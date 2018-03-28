<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_New_purpose extends CI_Migration {

    public function up() {
    	
    	$q = $this->db->query("
    		SELECT id FROM SupportRequestServiceTypes WHERE id = 6 LIMIT 1
    	");
    	
    	if (!$q->row()) {
	   		$this->db->query("
				INSERT INTO `SupportRequestServiceTypes` (`id`, `name`, `companyId`) VALUES (6, 'Other', '1');
	   		");
    	} else {
	   		$this->db->query("
				UPDATE `SupportRequestServiceTypes` SET name = 'Other' WHERE id = 6 LIMIT 1
	   		");    		
    	}
   		
   		$this->db->query("
			UPDATE `SupportRequestServiceTypes` SET `name` = 'Support Call' WHERE `SupportRequestServiceTypes`.`id` =5 LIMIT 1 ;
   		");
   		
   		$this->db->query("
   			UPDATE `SupportRequestTasks` SET `purposeId` =6 WHERE `purposeId` =5 
   		");
   		
    }
}