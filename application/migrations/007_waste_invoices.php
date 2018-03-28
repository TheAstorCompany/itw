<?php 
defined('BASEPATH') OR exit('No direct script access allowed');

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of 006_waste_invoices
 *
 * @author joekesov
 */
class Migration_Waste_invoices extends CI_Migration
{
    // Properties defined here
    
    //Constructor
    
    // Methods defined here
    public function up() {
        $this->db->query("
            ALTER TABLE WasteInvoices
                ADD COLUMN `invoiceNumber`  varchar(78) NULL DEFAULT NULL AFTER `invoiceDate`;
        ");
        
    }
    
    public function down() {
    }
}


