<?php
defined('BASEPATH') OR exit('No direct script access allowed');
/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of 011_recycling_charge_items
 *
 * @author joekesov
 */
class Migration_Recycling_charge_items extends CI_Migration
{
    // Properties defined here
    
    //Constructor
    
    // Methods defined here
    ////////////////////////////////////////////////////////////////////////////
    public function up() {
        $this->db->query("
            ALTER TABLE `RecyclingChargeItems`
                ADD COLUMN `CBRENumber`  varchar(255) NULL AFTER `recyclingChargeId`;
        ");
        
    }
    
    public function down() {
    }
}

?>
