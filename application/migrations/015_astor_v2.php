<?php 
defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_Astor_v2 extends CI_Migration {
	
	public function up() {
		$this->db->query("
			drop table if exists `SupportRequestTasks`;
		");
		
		$this->db->query("
			drop table if exists `SupportRequests`;
		");
		
		$this->db->query("
			create table if not exists `SupportRequests` (
				`id` bigint not null auto_increment,
				`locationId` bigint not null,
				`locationName` varchar(64) not null,
				`firstName` varchar(64) default null,
				`lastName`  varchar(64) default null,
				`phone`	    varchar(64) default null,
				`email`	    varchar(128) default null,
				`po`	    varchar(64) default null,
				`cbre`	    varchar(64) default null,
				`userId` bigint not null,
				`complete` bool default 0,
				`lastUpdated` date not null,
				`timeStamp` timestamp default CURRENT_TIMESTAMP,
				`notes` text default null,
				`companyId` int not null default 1,
				primary key(`id`),
				foreign key(`companyId`) references `Companies`(`id`) on delete cascade,
				foreign key(`userId`) references `CompanyUsers`(`id`) on delete cascade
			)ENGINE = InnoDB;
		");
		
		
		$res = $this->getTableFK('WasteInvoiceServices', 'containerId');
	
		if (!empty($res)) {
			$this->db->query("
				ALTER TABLE `WasteInvoiceServices` DROP FOREIGN KEY `$res` 
			");
		}
	
		$this->db->query("
			drop table if exists `Containers`;
		");
		$this->db->query("
			create table `Containers` (
				`id` int not null auto_increment,
				`name`	varchar(64) not null,
				`companyId` int not null default 1,
				primary key(`id`),
				foreign key(`companyId`) references `Companies`(`id`) on delete cascade
			)ENGINE = InnoDB;
		");
		
		$this->db->query("
			INSERT INTO `Containers` (`id`, `name`, `companyId`) VALUES
			(1, 'Compactor 42yd', 1),
			(2, 'Compactor 40yd', 1),
			(3, 'Compactor 34yd', 1),
			(4, 'Compactor 30yd', 1),
			(5, 'Compactor 20yd', 1),
			(6, 'Rolloff 40yd', 1),
			(7, 'Rolloff 30yd', 1),
			(8, 'Rolloff 20yd', 1),
			(9, 'Rolloff 15yd', 1),
			(10, 'Rolloff 10yd', 1),
			(11, 'Opentop 8yd', 1),
			(12, 'Opentop 7yd', 1),
			(13, 'Opentop 6yd', 1),
			(14, 'Opentop 5yd', 1),
			(15, 'Opentop 4yd', 1),
			(16, 'Opentop 3yd', 1),
			(17, 'Opentop 2yd', 1),
			(18, 'Opentop 1yd', 1),
			(19, 'Opentop 1.5yd', 1),
			(20, 'Opentop 0.5yd', 1),
			(21, 'Trash Hand', 1),
			(22, 'Recycle 8yd', 1),
			(23, 'Recycle 6yd', 1),
			(24, 'Recycle 4yd', 1),
			(25, 'Recycle 2yd', 1),
			(26, 'Recycle 96gal', 1),
			(27, 'Recycle 64gal', 1),
			(28, 'Recycle Toter', 1),
			(29, 'Recycle Bale', 1),
			(30, 'Recycle Drum', 1),
			(31, 'Recycle Hand', 1);
		");
		
		$this->db->query("
			create table if not exists `SupportRequestTasks` (
				`id` bigint not null auto_increment,
				`purposeId` int not null,
				`purposeType` bool not null,
				`quantity` int not null default 0,
				`containerId` int not null,
				`serviceDate` date default null,
				`deliveryDate` date default null,
				`removalDate` date default null,
				`description` text default '',
				`supportRequestId` bigint not null,
				primary key(`id`),
				foreign key(`supportRequestId`) references `SupportRequests`(`id`) on delete cascade,
				foreign key(`containerId`) references `Containers`(`id`) on delete cascade
			)ENGINE = InnoDB;
		");
		
		$this->db->query("
			DROP TABLE IF EXISTS SupportRequestContainers
		");
		
		if (!$this->isColumnExists('WasteInvoiceFees', 'waived')) {
			$this->db->query("
				ALTER TABLE `WasteInvoiceFees` ADD `waived` BOOL NOT NULL DEFAULT '0',
				ADD INDEX ( `waived` )
			");
		}
		
		if (!$this->isColumnExists('WasteInvoiceServices', 'category')) {
			$this->db->query("
				ALTER TABLE `WasteInvoiceServices` ADD `category` BOOL NOT NULL DEFAULT '0' AFTER `serviceTypeId` 
			");
		}
		
		if (!$this->isColumnExists('WasteInvoiceServices', 'containerId')) {
			$this->db->query("
				ALTER TABLE `WasteInvoiceServices` ADD `containerId` INT NOT NULL AFTER `category` ,
				ADD INDEX ( `containerId` ) 
			");
		}
		
		$this->db->query("
			UPDATE `WasteInvoiceServices` SET `containerId` =1
		");
		
		
		$res = $this->getTableFK('WasteInvoiceServices', 'containerId');

		$this->db->query("
			ALTER TABLE `WasteInvoiceServices` ADD FOREIGN KEY ( `containerId` ) REFERENCES `Containers` (
				`id`
			) ON DELETE CASCADE ;
		");
		
		if (!$this->isColumnExists('WasteInvoiceServices', 'rate')) {
			$this->db->query("
				ALTER TABLE `WasteInvoiceServices` CHANGE `trashFee` `rate` DECIMAL( 10, 2 ) NULL DEFAULT NULL 
			");
		}
		
		if (!$this->isColumnExists('WasteInvoiceServices', 'durationId')) {
			$this->db->query("
				ALTER TABLE `WasteInvoiceServices` ADD `durationId` INT NOT NULL DEFAULT '1',
				ADD INDEX ( `durationId` ) 
			");
		}
		
		if (!$this->isColumnExists('WasteInvoiceServices', 'schedule')) {
			$this->db->query("
				ALTER TABLE `WasteInvoiceServices` ADD `schedule` TINYINT( 4 ) NOT NULL DEFAULT '0',
				ADD INDEX ( `schedule` ) 
			");
		}
		
		if (!$this->isColumnExists('WasteInvoiceServices', 'days')) {
			$this->db->query("
				ALTER TABLE `WasteInvoiceServices` ADD `days` SMALLINT( 4 ) UNSIGNED NOT NULL DEFAULT '0'
			");
		}
		
		if (!$this->isColumnExists('WasteInvoiceServices', 'startDate')) {
			$this->db->query("
				ALTER TABLE `WasteInvoiceServices` ADD `startDate` DATE NULL ,
				ADD `endDate` DATE NULL 
			");
			$this->db->query("
				ALTER TABLE `WasteInvoiceServices` ADD INDEX ( `startDate` , `endDate` );
			");
		}
		
		$this->db->query("
			create table if not exists `RecyclingInvoiceAddedMaterials` (
				`id` bigint not null auto_increment,
				`invoiceId` bigint not null,
				`materialId` int not null,
				`unitId`     int not null,
				primary key(`id`),
				foreign key(`invoiceId`) references `RecyclingInvoices`(`id`) on delete cascade,
				foreign key(`materialId`) references `Materials`(`id`) on delete cascade
			)Engine=InnoDB;
		");
		
		if (!$this->isColumnExists('RecyclingInvoicesMaterials', 'invoiceNumber')) {
			$this->db->query("
				ALTER TABLE `RecyclingInvoicesMaterials` ADD `invoiceNumber` VARCHAR( 45 ) NOT NULL AFTER `invoiceId` 
			");
		}
		
		if (!$this->isColumnExists('RecyclingInvoicesMaterials', 'invoiceDate')) {
			$this->db->query("
				ALTER TABLE `RecyclingInvoicesMaterials` ADD `invoiceDate` DATE NOT NULL AFTER `invoiceNumber` 
			");
		}
		
		if ($this->isColumnExists('RecyclingInvoicesMaterials', 'CBRENumber')) {
			$this->db->query("
				ALTER TABLE `RecyclingInvoicesMaterials` DROP `CBRENumber` 
			");
		}
		
		if (!$this->isColumnExists('RecyclingInvoicesFees', 'waived')) {
			$this->db->query("
				ALTER TABLE `RecyclingInvoicesFees` ADD `waived` BOOL NOT NULL DEFAULT '0',
				ADD INDEX ( `waived` ) 
			");
		}
		
		if (!$this->isColumnExists('RecyclingInvoices', 'CBRENumber')) {
			$this->db->query("
				ALTER TABLE `RecyclingInvoices` ADD `CBRENumber` VARCHAR( 45 ) NOT NULL 
			");
		}
		
		if (!$this->isColumnExists('RecyclingInvoices', 'totalFees')) {
			$this->db->query("
				ALTER TABLE `RecyclingInvoices` ADD `totalFees` DECIMAL( 10, 3 ) NOT NULL DEFAULT '0'
			");
		}
		
		$this->db->query("
			UPDATE `RecyclingInvoices` AS ri SET ri.totalFees = ( 
				SELECT SUM( feeAmount ) FROM RecyclingInvoicesFees WHERE invoiceId = ri.id
					AND waived =0 
			) 
		");
		
		if (!$this->isColumnExists('RecyclingInvoices', 'totalWaivedFees')) {
			$this->db->query("
				ALTER TABLE `RecyclingInvoices` ADD `totalWaivedFees` DECIMAL( 10, 3 ) NOT NULL DEFAULT '0'
			");
		}
		
		$this->db->query("
			UPDATE `RecyclingInvoices` AS ri SET ri.totalWaivedFees = ( 
				SELECT SUM( feeAmount ) FROM RecyclingInvoicesFees WHERE invoiceId = ri.id
					AND waived =1 
			)
		");
		
		$this->db->query("
			create table if not exists `RecyclingPurchaseOrder` (
				`id` bigint not null auto_increment,
				`invoiceId` bigint not null,
				`PONumber` varchar(45),
				`PODate`  date,
				`materialId` int not null,
				`unitId`     int not null,
				`pricePerUnit` decimal(10,3),
				`total` decimal(10,3),
				primary key(`id`),
				foreign key(`invoiceId`) references `RecyclingInvoices`(`id`) on delete cascade,
				foreign key(`materialId`) references `Materials`(`id`) on delete cascade
			)Engine=InnoDB;
		");
		
	}
	
	public function down() {
	}
	
	private function isColumnExists($tableName, $columnName) {
		$columnName = $this->removeQuoting($columnName);

    	$q = $this->db->query("SHOW CREATE TABLE $tableName");
    	
    	
    	$result = (array)$q->row();  	
    	$regexp='/`'.$columnName.'`/mi';
    	preg_match($regexp,$result['Create Table'],$matches);
    	
    	if (!empty($matches)) {
    		return true;
    	}
    	
    	return false;
	}
	
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
	
	private function removeQuoting($text) {
		$text = trim($text);
		
		return trim($text, '`');
	}
}