<?php 
defined('BASEPATH') OR exit('No direct script access allowed');
/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of 010_recycling_invoices_materials
 *
 * @author joekesov
 */
class Migration_Recycling_invoices_materials extends CI_Migration
{
    // Properties defined here
    
    //Constructor
    
    // Methods defined here
    ////////////////////////////////////////////////////////////////////////////
    public function up() {
        $this->db->query("
            ALTER TABLE `RecyclingInvoicesMaterials`
                ADD COLUMN `CBRENumber`  varchar(255) NULL AFTER `materialId`;
        ");
        
    }
    
    public function down() {
    }
}

?>
