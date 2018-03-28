<?php
defined('BASEPATH') OR exit('No direct script access allowed');
/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of 012_distribution_center_services
 *
 * @author joekesov
 */
class Migration_Distribution_center_services extends CI_Migration
{
    // Properties defined here
    
    //Constructor
    
    // Methods defined here
    ////////////////////////////////////////////////////////////////////////////
    public function up() {
        // DistributionCenterServices
        $this->db->query("
            ALTER TABLE `DistributionCenterServices`
                MODIFY COLUMN `days`  smallint(4) UNSIGNED NULL DEFAULT NULL AFTER `schedule`;
        ");
        
        $this->db->query("
            ALTER TABLE `DistributionCenterServices`
                CHANGE COLUMN `renewalDate` `endDate`  date NULL DEFAULT NULL AFTER `rate`;
        ");
        
        $this->db->query("
            ALTER TABLE `DistributionCenterServices`
                ADD COLUMN `startDate`  date NULL AFTER `rate`;
        ");
        
        // StoreServices
        $this->db->query("
            ALTER TABLE `StoreServices`
                MODIFY COLUMN `days`  smallint(4) UNSIGNED NULL DEFAULT NULL AFTER `schedule`;
        ");
        
        $this->db->query("
            ALTER TABLE `StoreServices`
                CHANGE COLUMN `renewalDate` `endDate`  date NULL DEFAULT NULL AFTER `rate`;
        ");
        
        $this->db->query("
            ALTER TABLE `StoreServices`
                ADD COLUMN `startDate`  date NULL AFTER `rate`;
        ");
    }
    
    public function down() {
    }
}

?>
