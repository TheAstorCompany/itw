<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_Foreign_keys_fix2 extends CI_Migration {
	
	private function removeQuoting($text) {
		$text = trim($text);
		
		return trim($text, '`');
	}
	/**
	 * 
	 * Resolve real FK name
	 * @param string $tableName
	 * @param string $fkRealName
	 * @author D.Michev
	 */
	private function getTableFK($tableName, $fkRealName) {
		$fkRealName = $this->removeQuoting($fkRealName);

    	$q = $this->db->query("SHOW CREATE TABLE $tableName");
    	
    	$foreignKeys = (array)$q->row();  	
    	$regexp='/CONSTRAINT\s+([^\)]+)\s+FOREIGN KEY\s+\(([^\)]+)\)\s+REFERENCES\s+([^\(^\s]+)\s*\(([^\)]+)\)/mi';
    	preg_match_all($regexp,$foreignKeys['Create Table'],$matches,PREG_SET_ORDER);
    	
    	$result = false;
    	
    	if (!empty($matches)) {
	    	foreach ($matches as $item) {
	    		$item[2] = $this->removeQuoting($item[2]);
	    		
	    		if ($item[2] == $fkRealName) {
	    			$result = $this->removeQuoting($item[1]);
	    			break;
	    		}
	    	}
    	}
    	
    	return $result;
	}

    public function up() {
    	
    	$fk = $this->getTableFK('VendorContacts', 'vendorId');

    	if (!empty($fk)) {
	    	$this->db->query("
				ALTER TABLE `VendorContacts` DROP FOREIGN KEY `$fk`   
	        ");
    	}
        
        $this->db->query("
			ALTER TABLE `VendorContacts` ADD FOREIGN KEY ( `vendorId` ) REFERENCES `Vendors` (
				`id`
			) ON DELETE CASCADE ON UPDATE NO ACTION
        ");

        
        $fk = $this->getTableFK('VendorServices', 'vendorId');
        
        if (!empty($fk)) {
	        $this->db->query("
	        	ALTER TABLE `VendorServices` DROP FOREIGN KEY `$fk`
	        ");
        }
        
        $this->db->query("
			ALTER TABLE `VendorServices` ADD FOREIGN KEY ( `vendorId` ) REFERENCES `Vendors` (
			`id`
			) ON DELETE CASCADE ON UPDATE NO ACTION
        ");
        
        
        $fk = $this->getTableFK('DistributionCenterContacts', 'distributionCenterId');
        
        if (!empty($fk)) {
	        $this->db->query("
	        	ALTER TABLE `DistributionCenterContacts` DROP FOREIGN KEY `$fk`
	        ");
        }
        
        $this->db->query("
			ALTER TABLE `DistributionCenterContacts` ADD FOREIGN KEY ( `distributionCenterId` ) REFERENCES `DistributionCenters` (
			`id`
			) ON DELETE CASCADE ON UPDATE NO ACTION
        ");
        
        
        $fk = $this->getTableFK('DistributionCenterServices', 'distributionCenterId');
        
        if (!empty($fk)) {
	        $this->db->query("
	        	ALTER TABLE `DistributionCenterServices` DROP FOREIGN KEY `$fk`
	        ");
        }
        
        $this->db->query("
			ALTER TABLE `DistributionCenterServices` ADD FOREIGN KEY ( `distributionCenterId` ) REFERENCES `DistributionCenters` (
				`id`
			) ON DELETE CASCADE ON UPDATE NO ACTION
        ");
        
        
        
        $fk = $this->getTableFK('StoreContacts', 'storeId');
        
        if (!empty($fk)) {
	        $this->db->query("
				ALTER TABLE `StoreContacts` DROP FOREIGN KEY `$fk`
	        ");
        }
        
        $this->db->query("
			ALTER TABLE `StoreContacts` ADD FOREIGN KEY ( `storeId` ) REFERENCES `Stores` (
			`id`
			) ON DELETE CASCADE ON UPDATE NO ACTION
        ");
        
        
        $fk = $this->getTableFK('StoreServices', 'storeId');
        
        if (!empty($fk)) {
	        $this->db->query("
				ALTER TABLE `StoreServices` DROP FOREIGN KEY `$fk`
	        ");
        }
        
        $this->db->query("
			ALTER TABLE `StoreServices` ADD FOREIGN KEY ( `storeId` ) REFERENCES `Stores` (
			`id`
			) ON DELETE CASCADE ON UPDATE NO ACTION
        ");
        
    }
    
    public function down() {
    	
    	$fk = $this->getTableFK('VendorContacts', 'vendorId');
    	
    	if (!empty($fk)) {
	        $this->db->query("
				ALTER TABLE `VendorContacts` DROP FOREIGN KEY `$fk`   
	        ");
    	}
        
        $this->db->query("
			ALTER TABLE `VendorContacts` ADD FOREIGN KEY ( `vendorId` ) REFERENCES `Vendors` (
				`id`
			) ON DELETE NO ACTION ON UPDATE NO ACTION
        ");

        
        $fk = $this->getTableFK('VendorServices', 'vendorId');
        
        if (!empty($fk)) {
	        $this->db->query("
	        	ALTER TABLE `VendorServices` DROP FOREIGN KEY `$fk`
	        ");
        }
        
        $this->db->query("
			ALTER TABLE `VendorServices` ADD FOREIGN KEY ( `vendorId` ) REFERENCES `Vendors` (
			`id`
			) ON DELETE NO ACTION ON UPDATE NO ACTION
        ");
        
        
        $fk = $this->getTableFK('DistributionCenterContacts', 'distributionCenterId');
        
        if (!empty($fk)) {
	        $this->db->query("
	        	ALTER TABLE `DistributionCenterContacts` DROP FOREIGN KEY `$fk`
	        ");
        }
        
        $this->db->query("
			ALTER TABLE `DistributionCenterContacts` ADD FOREIGN KEY ( `distributionCenterId` ) REFERENCES `DistributionCenters` (
			`id`
			) ON DELETE NO ACTION ON UPDATE NO ACTION
        ");
        
        
        $fk = $this->getTableFK('DistributionCenterServices', 'distributionCenterId');
        
        if (!empty($fk)) {
	        $this->db->query("
	        	ALTER TABLE `DistributionCenterServices` DROP FOREIGN KEY `$fk`
	        ");
        }
        
        $this->db->query("
			ALTER TABLE `DistributionCenterServices` ADD FOREIGN KEY ( `distributionCenterId` ) REFERENCES `DistributionCenters` (
				`id`
			) ON DELETE NO ACTION ON UPDATE NO ACTION
        ");
        
        
        
        $fk = $this->getTableFK('StoreContacts', 'storeId');
        
        if (!empty($fk)) {
	        $this->db->query("
				ALTER TABLE `StoreContacts` DROP FOREIGN KEY `$fk`
	        ");
        }
        
        $this->db->query("
			ALTER TABLE `StoreContacts` ADD FOREIGN KEY ( `storeId` ) REFERENCES `Stores` (
			`id`
			) ON DELETE NO ACTION ON UPDATE NO ACTION
        ");
        
        
        $fk = $this->getTableFK('StoreServices', 'storeId');
        
        if (!empty($fk)) {
	        $this->db->query("
				ALTER TABLE `StoreServices` DROP FOREIGN KEY `$fk`
	        ");
        }
        
        $this->db->query("
			ALTER TABLE `StoreServices` ADD FOREIGN KEY ( `storeId` ) REFERENCES `Stores` (
			`id`
			) ON DELETE NO ACTION ON UPDATE NO ACTION
        ");
        
        /* ----- new -------------- */
        
        $fk = $this->getTableFK('WasteInvoiceFees', 'invoiceId');
        if (!empty($fk)) {
	        $this->db->query("
				ALTER TABLE `WasteInvoiceFees` DROP FOREIGN KEY `$fk`
	        ");
        }
        
        $this->db->query("
			ALTER TABLE `WasteInvoiceFees` ADD FOREIGN KEY ( `invoiceId` ) REFERENCES `WasteInvoices` (
				`id`
			) ON DELETE CASCADE ON UPDATE NO ACTION ;
        ");
        
        /*-------------------------------------------------------------*/
        $fk = $this->getTableFK('WasteInvoiceServices', 'materialId');
        if (!empty($fk)) {
	        $this->db->query("
				ALTER TABLE `WasteInvoiceServices` DROP FOREIGN KEY `$fk`
	        ");
        }
        
        $this->db->query("
			ALTER TABLE `WasteInvoiceServices` ADD FOREIGN KEY ( `materialId` ) REFERENCES `Materials` (
			`id`
			) ON DELETE CASCADE ON UPDATE NO ACTION ;
        ");
        /*-------------------------------------------------------------*/
        $fk = $this->getTableFK('WasteInvoiceServices', 'invoiceId');
        if (!empty($fk)) {
	        $this->db->query("
				ALTER TABLE `WasteInvoiceServices` DROP FOREIGN KEY `$fk`
	        ");
        }
        
        $this->db->query("
			ALTER TABLE `WasteInvoiceServices` ADD FOREIGN KEY ( `invoiceId` ) REFERENCES `WasteInvoices` (
			`id`
			) ON DELETE CASCADE ON UPDATE NO ACTION ;
        ");  
        /*-------------------------------------------------------------*/
        $this->db->query("
        	ALTER TABLE `SupportRequestTasks` CHANGE `containerId` `containerId` INT( 11 ) NULL DEFAULT NULL
        ");
        /*-------------------------------------------------------------*/
        $this->db->query("
        	ALTER TABLE `VendorServices` CHANGE `days` `days` INT UNSIGNED NULL DEFAULT NULL
        ");
        /*-------------------------------------------------------------*/
        
        
        $fk = $this->getTableFK('WasteInvoices', 'vendorId');
        if (!empty($fk)) {
	        $this->db->query("
				ALTER TABLE `WasteInvoices` DROP FOREIGN KEY `$fk`
	        ");
        }
        
        $this->db->query("
			ALTER TABLE `WasteInvoices` ADD FOREIGN KEY ( `vendorId` ) REFERENCES `Vendors` (
			`id`
			) ON DELETE CASCADE ON UPDATE NO ACTION ;
        ");  
        /*-------------------------------------------------------------*/
        
        $fk = $this->getTableFK('RecyclingInvoices', 'vendorId');
        if (!empty($fk)) {
	        $this->db->query("
				ALTER TABLE `RecyclingInvoices` DROP FOREIGN KEY `$fk`
	        ");
        }
        
        $this->db->query("
			ALTER TABLE `RecyclingInvoices` ADD FOREIGN KEY ( `vendorId` ) REFERENCES `Vendors` (
			`id`
			) ON DELETE CASCADE ON UPDATE NO ACTION ;
        ");  
        /*-------------------------------------------------------------*/
        
        $fk = $this->getTableFK('RecyclingCharges', 'vendorId');
        if (!empty($fk)) {
	        $this->db->query("
				ALTER TABLE `RecyclingCharges` DROP FOREIGN KEY `$fk`
	        ");
        }
        
        $this->db->query("
			ALTER TABLE `RecyclingCharges` ADD FOREIGN KEY ( `vendorId` ) REFERENCES `Vendors` (
			`id`
			) ON DELETE CASCADE ON UPDATE NO ACTION ;
        ");  
        /*-------------------------------------------------------------*/
        
        $fk = $this->getTableFK('RecyclingInvoicesFees', 'invoiceId');
        if (!empty($fk)) {
	        $this->db->query("
				ALTER TABLE `RecyclingInvoicesFees` DROP FOREIGN KEY `$fk`
	        ");
        }
        
        $this->db->query("
			ALTER TABLE `RecyclingInvoicesFees` ADD FOREIGN KEY ( `invoiceId` ) REFERENCES `RecyclingInvoices` (
			`id`
			) ON DELETE CASCADE ON UPDATE NO ACTION ;
        ");  
        /*-------------------------------------------------------------*/
        
        $fk = $this->getTableFK('RecyclingInvoicesInfo', 'invoiceId');
        if (!empty($fk)) {
	        $this->db->query("
				ALTER TABLE `RecyclingInvoicesInfo` DROP FOREIGN KEY `$fk`
	        ");
        }
        
        $this->db->query("
			ALTER TABLE `RecyclingInvoicesInfo` ADD FOREIGN KEY ( `invoiceId` ) REFERENCES `RecyclingInvoices` (
			`id`
			) ON DELETE CASCADE ON UPDATE NO ACTION ;
        ");  
        /*-------------------------------------------------------------*/
        
        $fk = $this->getTableFK('RecyclingInvoicesMaterials', 'invoiceId');
        if (!empty($fk)) {
	        $this->db->query("
				ALTER TABLE `RecyclingInvoicesMaterials` DROP FOREIGN KEY `$fk`
	        ");
        }
        
        $this->db->query("
			ALTER TABLE `RecyclingInvoicesMaterials` ADD FOREIGN KEY ( `invoiceId` ) REFERENCES  `RecyclingInvoices` (
			`id`
			) ON DELETE CASCADE ON UPDATE NO ACTION ;
        ");  
        /*-------------------------------------------------------------*/
        
        
        $fk = $this->getTableFK('RecyclingInvoicesMaterials', 'materialId');
        if (!empty($fk)) {
	        $this->db->query("
				ALTER TABLE `RecyclingInvoicesMaterials` DROP FOREIGN KEY `$fk`
	        ");
        }
        
        $this->db->query("
			ALTER TABLE `RecyclingInvoicesMaterials` ADD FOREIGN KEY ( `materialId` ) REFERENCES `Materials` (
			`id`
			) ON DELETE CASCADE ON UPDATE NO ACTION ;
        ");  
        /*-------------------------------------------------------------*/
        
        
        
        $fk = $this->getTableFK('RecyclingChargesFees', 'recyclingChargeId');
        if (!empty($fk)) {
	        $this->db->query("
				ALTER TABLE `RecyclingChargesFees` DROP FOREIGN KEY `$fk`
	        ");
        }
        
        $this->db->query("
			ALTER TABLE `RecyclingChargesFees` ADD FOREIGN KEY ( `recyclingChargeId` ) REFERENCES  `RecyclingCharges` (
			`id`
			) ON DELETE CASCADE ON UPDATE NO ACTION ;
        ");  
        /*-------------------------------------------------------------*/
        
        $fk = $this->getTableFK('RecyclingChargeItems', 'materialId');
        if (!empty($fk)) {
	        $this->db->query("
				ALTER TABLE `RecyclingChargeItems` DROP FOREIGN KEY `$fk`
	        ");
        }
        
        $this->db->query("
			ALTER TABLE `RecyclingChargeItems` ADD FOREIGN KEY ( `materialId` ) REFERENCES `Materials` (
			`id`
			) ON DELETE CASCADE ON UPDATE NO ACTION ;
        ");  
        /*-------------------------------------------------------------*/
        
        $fk = $this->getTableFK('RecyclingChargeItems', 'recyclingChargeId');
        if (!empty($fk)) {
	        $this->db->query("
				ALTER TABLE `RecyclingChargeItems` DROP FOREIGN KEY `$fk`
	        ");
        }
        
        $this->db->query("
			ALTER TABLE `RecyclingChargeItems` ADD FOREIGN KEY ( `recyclingChargeId` ) REFERENCES `RecyclingCharges` (
			`id`
			) ON DELETE CASCADE ON UPDATE NO ACTION ;
        ");  
        /*-------------------------------------------------------------*/

    }
}