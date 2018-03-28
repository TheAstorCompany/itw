<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_Feetype extends CI_Migration {

    public function up() {
    	$this->db->query("CREATE  TABLE `astor`.`FeeType` (
          `id` INT NOT NULL AUTO_INCREMENT ,
          `name` VARCHAR(255) NULL ,
          PRIMARY KEY (`id`) )
        ");
        
        $this->db->query("INSERT INTO `astor`.`FeeType` (`name`)
                          VALUES ('Frieght Charge'),
                                 ('Fuel Charge'),
                                 ('Stop Charge'),
                                 ('Tax'),
                                 ('Other'),
                                 ('Repair'),
                                 ('Rental'),
                                 ('Enviromental'),
                                 ('Lock'),
                                 ('Delivery'),
                                 ('Credit')");
    }
}