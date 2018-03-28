<?php 
defined('BASEPATH') OR exit('No direct script access allowed');

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of 009_recycling_invoices
 *
 * @author joekesov
 */
class Migration_Recycling_invoices extends CI_Migration
{
    // Properties defined here
    
    //Constructor
    
    // Methods defined here
    ////////////////////////////////////////////////////////////////////////////
    public function up() {
        $this->db->query("
            ALTER TABLE `RecyclingInvoices`
                ADD COLUMN `BOLNumber`  varchar(255) NULL AFTER `total`;
        ");
        
    }
    
    public function down() {
    }
}


