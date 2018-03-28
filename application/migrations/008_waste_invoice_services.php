<?php 
defined('BASEPATH') OR exit('No direct script access allowed');

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of 008_waste_invoice_services
 *
 * @author joekesov
 */
class Migration_Waste_invoice_services extends CI_Migration
{
    // Properties defined here
    
    //Constructor
    
    // Methods defined here
    ////////////////////////////////////////////////////////////////////////////
    public function up() {
        $this->db->query("
            ALTER TABLE `WasteInvoiceServices`
                ADD COLUMN `CBRENumber`  varchar(255) NULL AFTER `invoiceId`;
        ");
        
    }
    
    public function down() {
    }
}

?>
