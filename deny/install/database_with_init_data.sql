SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0;
SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0;
SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='TRADITIONAL';

-- -----------------------------------------------------
-- Table `Companies`
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS `Companies` (
  `id` INT NOT NULL AUTO_INCREMENT ,
  `name` VARCHAR(128) NOT NULL ,
  `addressLine` VARCHAR(255) NULL ,
  `email` VARCHAR(128) NOT NULL ,
  `phone` VARCHAR(45) NOT NULL ,
  PRIMARY KEY (`id`) )
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `SupportRequestServiceTypes`
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS `SupportRequestServiceTypes` (
  `id` INT NOT NULL AUTO_INCREMENT ,
  `name` VARCHAR(128) NULL ,
  `companyId` INT NOT NULL ,
  PRIMARY KEY (`id`) ,
  INDEX `fk_srst_companyId` (`companyId` ASC) ,
  CONSTRAINT `fk_srst_companyId`
    FOREIGN KEY (`companyId` )
    REFERENCES `Companies` (`id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `SupportRequestContainers`
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS `SupportRequestContainers` (
  `id` INT NOT NULL AUTO_INCREMENT ,
  `name` VARCHAR(128) NOT NULL ,
  `companyId` INT NOT NULL ,
  PRIMARY KEY (`id`) ,
  INDEX `fk_src_companyId` (`companyId` ASC) ,
  CONSTRAINT `fk_src_companyId`
    FOREIGN KEY (`companyId` )
    REFERENCES `Companies` (`id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `CompanyUsers`
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS `CompanyUsers` (
  `id` BIGINT NOT NULL AUTO_INCREMENT ,
  `username` VARCHAR(64) NOT NULL ,
  `password` VARCHAR(129) NOT NULL ,
  `firstName` VARCHAR(64) NOT NULL ,
  `lastName` VARCHAR(64) NULL ,
  `title` VARCHAR(64) NULL ,
  `email` VARCHAR(128) NOT NULL ,
  `phone` VARCHAR(64) NULL ,
  `accessLevel` ENUM('USER', 'ADMIN') NOT NULL DEFAULT 'USER' ,
  `active` TINYINT(1) NULL DEFAULT false ,
  `companyId` INT NOT NULL ,
  `internalNotes` TEXT NULL ,
  `lastUpdated` TIMESTAMP NOT NULL ,
  PRIMARY KEY (`id`) ,
  INDEX `fk_cu_companyId` (`companyId` ASC) ,
  CONSTRAINT `fk_cu_companyId`
    FOREIGN KEY (`companyId` )
    REFERENCES `Companies` (`id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `SupportRequests`
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS `SupportRequests` (
  `id` BIGINT NOT NULL AUTO_INCREMENT ,
  `firstName` VARCHAR(64) NULL ,
  `lastName` VARCHAR(64) NULL ,
  `email` VARCHAR(128) NULL ,
  `phone` VARCHAR(64) NULL ,
  `locationId` BIGINT NOT NULL COMMENT 'Fake FK to DC or Store' ,
  `locationType` ENUM('STORE', 'DC') NULL ,
  `wasteRecycle` TINYINT(1) NULL ,
  `serviceTypeId` INT NULL ,
  `quantity` INT NOT NULL DEFAULT 0 ,
  `containerId` INT NULL ,
  `deliveryDate` DATE NULL ,
  `removalDate` DATE NULL ,
  `description` TEXT NULL ,
  `resolved` TINYINT(1) NOT NULL DEFAULT false ,
  `lastUpdated` DATE NULL ,
  `internalNotes` TEXT NULL ,
  `createdBy` BIGINT NULL DEFAULT NULL ,
  `updatedBy` BIGINT NULL DEFAULT NULL ,
  `companyId` INT NOT NULL ,
  PRIMARY KEY (`id`) ,
  INDEX `fk_sr_serviceTypes` (`serviceTypeId` ASC) ,
  INDEX `fk_sr_containers` (`containerId` ASC) ,
  INDEX `fk_sr_companyId` (`companyId` ASC) ,
  INDEX `fk_sr_createdByUserId` (`createdBy` ASC) ,
  INDEX `fk_sr_updatedByUserId` (`updatedBy` ASC) ,
  CONSTRAINT `fk_sr_serviceTypes`
    FOREIGN KEY (`serviceTypeId` )
    REFERENCES `SupportRequestServiceTypes` (`id` )
    ON DELETE SET NULL
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_sr_containers`
    FOREIGN KEY (`containerId` )
    REFERENCES `SupportRequestContainers` (`id` )
    ON DELETE SET NULL
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_sr_companyId`
    FOREIGN KEY (`companyId` )
    REFERENCES `Companies` (`id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_sr_createdByUserId`
    FOREIGN KEY (`createdBy` )
    REFERENCES `CompanyUsers` (`id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_sr_updatedByUserId`
    FOREIGN KEY (`updatedBy` )
    REFERENCES `CompanyUsers` (`id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `Regions`
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS `Regions` (
  `id` INT NOT NULL AUTO_INCREMENT ,
  `name` VARCHAR(45) NULL ,
  `companyId` INT NULL ,
  PRIMARY KEY (`id`) ,
  INDEX `fk_r_companyId` (`companyId` ASC) ,
  CONSTRAINT `fk_r_companyId`
    FOREIGN KEY (`companyId` )
    REFERENCES `Companies` (`id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `States`
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS `States` (
  `id` INT NOT NULL AUTO_INCREMENT ,
  `name` VARCHAR(45) NULL ,
  `code` VARCHAR(2) NULL ,
  `region` VARCHAR(45) NULL ,
  PRIMARY KEY (`id`) )
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `Vendors`
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS `Vendors` (
  `id` INT NOT NULL AUTO_INCREMENT ,
  `name` VARCHAR(128) NOT NULL ,
  `number` VARCHAR(45) NOT NULL ,
  `remitTo` VARCHAR(45) NULL ,
  `addressLine1` VARCHAR(128) NULL ,
  `addressLine2` VARCHAR(128) NULL ,
  `city` VARCHAR(64) NULL ,
  `stateId` INT NULL ,
  `zip` VARCHAR(10) NULL ,
  `regionId` INT NULL ,
  `phone` VARCHAR(64) NULL ,
  `fax` VARCHAR(64) NULL ,
  `website` VARCHAR(255) NULL ,
  `email` VARCHAR(128) NULL ,
  `status` TINYINT(1) NOT NULL DEFAULT false ,
  `notes` TEXT NULL ,
  `companyId` INT NULL ,
  `lastUpdated` TIMESTAMP NULL ,
  PRIMARY KEY (`id`) ,
  INDEX `fk_v_companyId` (`companyId` ASC) ,
  INDEX `fk_vc_regionId` (`regionId` ASC) ,
  INDEX `fk_vc_stateId` (`stateId` ASC) ,
  CONSTRAINT `fk_v_companyId`
    FOREIGN KEY (`companyId` )
    REFERENCES `Companies` (`id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_vc_regionId`
    FOREIGN KEY (`regionId` )
    REFERENCES `Regions` (`id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_vc_stateId`
    FOREIGN KEY (`stateId` )
    REFERENCES `States` (`id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `RecyclingInvoices`
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS `RecyclingInvoices` (
  `id` BIGINT NOT NULL AUTO_INCREMENT ,
  `invoiceDate` DATE NOT NULL ,
  `vendorId` INT NULL ,
  `locationId` BIGINT NOT NULL ,
  `locationType` ENUM('STORE','DC') NOT NULL ,
  `poDate` DATE NULL ,
  `poNumber` VARCHAR(45) NULL ,
  `trailerNumber` VARCHAR(45) NULL ,
  `dateSent` DATE NULL ,
  `internalNotes` TEXT NULL ,
  `invoiceTotal` DECIMAL(10,3) NULL ,
  `companyId` INT NULL ,
  `lastUpdated` TIMESTAMP NULL ,
  `status` ENUM('YES','NO') NULL DEFAULT 'NO' ,
  `total` DECIMAL(10,2) NULL ,
  PRIMARY KEY (`id`) ,
  INDEX `fk_ri_vendorId` (`vendorId` ASC) ,
  INDEX `fk_ri_companyId` (`companyId` ASC) ,
  CONSTRAINT `fk_ri_vendorId`
    FOREIGN KEY (`vendorId` )
    REFERENCES `Vendors` (`id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_ri_companyId`
    FOREIGN KEY (`companyId` )
    REFERENCES `Companies` (`id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `Materials`
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS `Materials` (
  `id` INT NOT NULL AUTO_INCREMENT ,
  `name` VARCHAR(45) NULL ,
  `companyId` INT NULL ,
  PRIMARY KEY (`id`) ,
  INDEX `fk_m_companyId` (`companyId` ASC) ,
  CONSTRAINT `fk_m_companyId`
    FOREIGN KEY (`companyId` )
    REFERENCES `Companies` (`id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `RecyclingInvoicesMaterials`
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS `RecyclingInvoicesMaterials` (
  `id` BIGINT NOT NULL AUTO_INCREMENT ,
  `invoiceId` BIGINT NOT NULL ,
  `quantity` DECIMAL(10,2) NULL ,
  `unit` INT NULL ,
  `pricePerUnit` DECIMAL(10,3) NULL ,
  `materialId` INT NULL ,
  PRIMARY KEY (`id`) ,
  INDEX `fk_rim_invoiceId` (`invoiceId` ASC) ,
  INDEX `fk_rim_materialId` (`materialId` ASC) ,
  CONSTRAINT `fk_rim_invoiceId`
    FOREIGN KEY (`invoiceId` )
    REFERENCES `RecyclingInvoices` (`id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_rim_materialId`
    FOREIGN KEY (`materialId` )
    REFERENCES `Materials` (`id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `VendorContacts`
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS `VendorContacts` (
  `id` INT NOT NULL AUTO_INCREMENT ,
  `firstName` VARCHAR(64) NULL ,
  `lastName` VARCHAR(64) NULL ,
  `title` VARCHAR(45) NULL ,
  `email` VARCHAR(128) NULL ,
  `phone` VARCHAR(64) NULL ,
  `vendorId` INT NULL ,
  PRIMARY KEY (`id`) ,
  INDEX `fk_vendorId` (`vendorId` ASC) ,
  CONSTRAINT `fk_vendorId`
    FOREIGN KEY (`vendorId` )
    REFERENCES `Vendors` (`id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `VendorServiceDurations`
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS `VendorServiceDurations` (
  `id` INT NOT NULL AUTO_INCREMENT ,
  `name` VARCHAR(128) NULL ,
  `companyId` INT NULL ,
  PRIMARY KEY (`id`) ,
  INDEX `fk_vsd_companyId` (`companyId` ASC) ,
  CONSTRAINT `fk_vsd_companyId`
    FOREIGN KEY (`companyId` )
    REFERENCES `Companies` (`id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `VendorServicePurposes`
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS `VendorServicePurposes` (
  `id` INT NOT NULL AUTO_INCREMENT ,
  `name` VARCHAR(128) NULL ,
  `companyId` INT NULL ,
  PRIMARY KEY (`id`) ,
  INDEX `fk_vsp_companyId` (`companyId` ASC) ,
  CONSTRAINT `fk_vsp_companyId`
    FOREIGN KEY (`companyId` )
    REFERENCES `Companies` (`id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `VendorServices`
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS `VendorServices` (
  `id` INT NOT NULL AUTO_INCREMENT ,
  `name` VARCHAR(128) NULL ,
  `durationId` INT NULL ,
  `purposeId` INT NULL ,
  `quantity` FLOAT NULL ,
  `containerId` INT NULL ,
  `schedule` TINYINT NULL ,
  `days` TINYINT NULL ,
  `rate` DECIMAL(10,2) NULL ,
  `renewalDate` DATE NULL ,
  `vendorId` INT NULL ,
  PRIMARY KEY (`id`) ,
  INDEX `fk_vs_durationId` (`durationId` ASC) ,
  INDEX `fk_vs_purposeId` (`purposeId` ASC) ,
  INDEX `fk_vs_vendorId` (`vendorId` ASC) ,
  CONSTRAINT `fk_vs_durationId`
    FOREIGN KEY (`durationId` )
    REFERENCES `VendorServiceDurations` (`id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_vs_purposeId`
    FOREIGN KEY (`purposeId` )
    REFERENCES `VendorServicePurposes` (`id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_vs_vendorId`
    FOREIGN KEY (`vendorId` )
    REFERENCES `Vendors` (`id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `Stores`
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS `Stores` (
  `id` INT NOT NULL AUTO_INCREMENT ,
  `location` VARCHAR(10) NULL ,
  `squareFootage` DOUBLE(11,2) NULL ,
  `open24hours` TINYINT(1) NULL ,
  `district` VARCHAR(10) NULL ,
  `districtName` VARCHAR(64) NULL ,
  `addressLine1` VARCHAR(128) NULL ,
  `addressLine2` VARCHAR(128) NULL ,
  `city` VARCHAR(64) NULL ,
  `postCode` VARCHAR(10) NULL ,
  `stateId` INT NULL ,
  `region` ENUM('EAST', 'WEST', 'SOUTH','MIDWEST','SOUTHWEST') NOT NULL ,
  `phone` VARCHAR(64) NULL ,
  `fax` VARCHAR(64) NULL ,
  `status` ENUM('YES','NO') NOT NULL DEFAULT 'NO' ,
  `companyId` INT NULL ,
  `landlord` VARCHAR(128) NULL COMMENT 'Land owner' ,
  `openDate` DATE NULL ,
  `lastUpdated` TIMESTAMP NULL ,
  `notes` TEXT NULL ,
  PRIMARY KEY (`id`) ,
  INDEX `fk_s_companyId` (`companyId` ASC) ,
  INDEX `fk_s_stateId` (`stateId` ASC) ,
  CONSTRAINT `fk_s_companyId`
    FOREIGN KEY (`companyId` )
    REFERENCES `Companies` (`id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_s_stateId`
    FOREIGN KEY (`stateId` )
    REFERENCES `States` (`id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `StoreContacts`
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS `StoreContacts` (
  `id` INT NOT NULL AUTO_INCREMENT ,
  `firstName` VARCHAR(64) NULL ,
  `lastName` VARCHAR(64) NULL ,
  `title` VARCHAR(45) NULL ,
  `email` VARCHAR(128) NULL ,
  `phone` VARCHAR(64) NULL ,
  `storeId` INT NULL ,
  `contactType` ENUM('REGIONAL','RMM','GENERIC') NULL DEFAULT 'GENERIC' ,
  PRIMARY KEY (`id`) ,
  INDEX `fk_sc_storeId` (`storeId` ASC) ,
  CONSTRAINT `fk_sc_storeId`
    FOREIGN KEY (`storeId` )
    REFERENCES `Stores` (`id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `StoreServiceDurations`
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS `StoreServiceDurations` (
  `id` INT NOT NULL AUTO_INCREMENT ,
  `name` VARCHAR(128) NOT NULL ,
  `companyId` INT NULL ,
  PRIMARY KEY (`id`) ,
  INDEX `fk_ssd_comapnyId` (`companyId` ASC) ,
  CONSTRAINT `fk_ssd_comapnyId`
    FOREIGN KEY (`companyId` )
    REFERENCES `Companies` (`id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `StoreServicePurposes`
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS `StoreServicePurposes` (
  `id` INT NOT NULL AUTO_INCREMENT ,
  `name` VARCHAR(128) NOT NULL ,
  `companyId` INT NULL ,
  PRIMARY KEY (`id`) ,
  INDEX `fk_ssp_companyId` (`companyId` ASC) ,
  CONSTRAINT `fk_ssp_companyId`
    FOREIGN KEY (`companyId` )
    REFERENCES `Companies` (`id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `StoreServices`
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS `StoreServices` (
  `id` INT NOT NULL AUTO_INCREMENT ,
  `name` VARCHAR(128) NULL ,
  `durationId` INT NULL ,
  `purposeId` INT NULL ,
  `quantity` FLOAT NULL ,
  `containerId` INT NULL ,
  `schedule` TINYINT NULL ,
  `days` TINYINT NULL ,
  `rate` DECIMAL(10,2) NULL ,
  `renewalDate` DATE NULL ,
  `storeId` INT NULL ,
  PRIMARY KEY (`id`) ,
  INDEX `fk_ss_storeId` (`storeId` ASC) ,
  INDEX `fk_ss_durationId` (`durationId` ASC) ,
  INDEX `fk_ss_purposeId` (`purposeId` ASC) ,
  CONSTRAINT `fk_ss_storeId`
    FOREIGN KEY (`storeId` )
    REFERENCES `Stores` (`id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_ss_durationId`
    FOREIGN KEY (`durationId` )
    REFERENCES `StoreServiceDurations` (`id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_ss_purposeId`
    FOREIGN KEY (`purposeId` )
    REFERENCES `StoreServicePurposes` (`id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `DistributionCenters`
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS `DistributionCenters` (
  `id` BIGINT NOT NULL AUTO_INCREMENT ,
  `name` VARCHAR(128) NULL ,
  `number` VARCHAR(45) NULL ,
  `addressLine1` VARCHAR(128) NULL ,
  `addressLine2` VARCHAR(128) NULL ,
  `city` VARCHAR(64) NULL ,
  `stateId` INT NULL ,
  `zip` VARCHAR(10) NULL ,
  `district` VARCHAR(10) NOT NULL ,
  `districtName` VARCHAR(64) NOT NULL ,
  `phone` VARCHAR(64) NULL ,
  `fax` VARCHAR(64) NULL ,
  `status` ENUM('YES','NO') NULL DEFAULT 'NO' ,
  `notes` TEXT NULL ,
  `lastUpdated` TIMESTAMP NULL ,
  `companyId` INT NULL ,
  PRIMARY KEY (`id`) ,
  INDEX `fk_dc_companyId` (`companyId` ASC) ,
  INDEX `fk_dc_stateId` (`stateId` ASC) ,
  CONSTRAINT `fk_dc_companyId`
    FOREIGN KEY (`companyId` )
    REFERENCES `Companies` (`id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_dc_stateId`
    FOREIGN KEY (`stateId` )
    REFERENCES `States` (`id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `DistributionCenterContacts`
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS `DistributionCenterContacts` (
  `id` BIGINT NOT NULL AUTO_INCREMENT ,
  `firstName` VARCHAR(64) NULL ,
  `lastName` VARCHAR(64) NULL ,
  `title` VARCHAR(45) NULL ,
  `email` VARCHAR(128) NULL ,
  `phone` VARCHAR(64) NULL ,
  `distributionCenterId` BIGINT NULL ,
  PRIMARY KEY (`id`) ,
  INDEX `fk_dcc_distributionCenterId` (`distributionCenterId` ASC) ,
  CONSTRAINT `fk_dcc_distributionCenterId`
    FOREIGN KEY (`distributionCenterId` )
    REFERENCES `DistributionCenters` (`id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `DistributionCenterServiceDurations`
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS `DistributionCenterServiceDurations` (
  `id` INT NOT NULL AUTO_INCREMENT ,
  `name` VARCHAR(128) NULL ,
  `companyId` INT NULL ,
  PRIMARY KEY (`id`) ,
  INDEX `fk_dcsd_companyId` (`companyId` ASC) ,
  CONSTRAINT `fk_dcsd_companyId`
    FOREIGN KEY (`companyId` )
    REFERENCES `Companies` (`id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `DistributionCenterServicePurposes`
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS `DistributionCenterServicePurposes` (
  `id` INT NOT NULL AUTO_INCREMENT ,
  `name` VARCHAR(128) NULL ,
  `companyId` INT NULL ,
  PRIMARY KEY (`id`) ,
  INDEX `fk_dcsp_companyId` (`companyId` ASC) ,
  CONSTRAINT `fk_dcsp_companyId`
    FOREIGN KEY (`companyId` )
    REFERENCES `Companies` (`id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `DistributionCenterServices`
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS `DistributionCenterServices` (
  `id` INT NOT NULL AUTO_INCREMENT ,
  `name` VARCHAR(128) NULL ,
  `durationId` INT NULL ,
  `purposeId` INT NULL ,
  `quantity` FLOAT NULL ,
  `containerId` INT NULL ,
  `schedule` TINYINT NULL ,
  `days` TINYINT NULL ,
  `rate` DECIMAL(10,2) NULL ,
  `renewalDate` DATE NULL ,
  `distributionCenterId` BIGINT NULL ,
  PRIMARY KEY (`id`) ,
  INDEX `fk_dcs_distributionCenterId` (`distributionCenterId` ASC) ,
  INDEX `fk_dcs_durationId` (`durationId` ASC) ,
  INDEX `fk_dcs_purposeId` (`purposeId` ASC) ,
  CONSTRAINT `fk_dcs_distributionCenterId`
    FOREIGN KEY (`distributionCenterId` )
    REFERENCES `DistributionCenters` (`id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_dcs_durationId`
    FOREIGN KEY (`durationId` )
    REFERENCES `DistributionCenterServiceDurations` (`id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_dcs_purposeId`
    FOREIGN KEY (`purposeId` )
    REFERENCES `DistributionCenterServicePurposes` (`id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `MarketRate`
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS `MarketRate` (
  `id` INT NOT NULL AUTO_INCREMENT ,
  `regionId` INT NULL ,
  `materialId` INT NULL ,
  `lowPrice` DECIMAL(10,2) NULL ,
  `highPrice` DECIMAL(10,2) NULL ,
  `monthValidity` DATE NULL ,
  `offsetPrice` DECIMAL(10,2) NULL DEFAULT 0 COMMENT 'Adjust the price per pickup and location.\nOptional' ,
  `companyId` INT NULL ,
  PRIMARY KEY (`id`) ,
  INDEX `fk_mr_regionId` (`regionId` ASC) ,
  INDEX `fk_mr_materialId` (`materialId` ASC) ,
  INDEX `fk_mr_companyId` (`companyId` ASC) ,
  CONSTRAINT `fk_mr_regionId`
    FOREIGN KEY (`regionId` )
    REFERENCES `Regions` (`id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_mr_materialId`
    FOREIGN KEY (`materialId` )
    REFERENCES `Materials` (`id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_mr_companyId`
    FOREIGN KEY (`companyId` )
    REFERENCES `Companies` (`id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `RecyclingInvoicesFees`
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS `RecyclingInvoicesFees` (
  `id` BIGINT NOT NULL AUTO_INCREMENT ,
  `invoiceId` BIGINT NULL ,
  `feeType` INT NULL ,
  `feeAmount` DECIMAL(10,2) NULL ,
  PRIMARY KEY (`id`) ,
  INDEX `fk_rif_invoiceId` (`invoiceId` ASC) ,
  CONSTRAINT `fk_rif_invoiceId`
    FOREIGN KEY (`invoiceId` )
    REFERENCES `RecyclingInvoices` (`id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `RecyclingInvoicesInfo`
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS `RecyclingInvoicesInfo` (
  `id` BIGINT NOT NULL AUTO_INCREMENT ,
  `invoiceDate` DATE NULL ,
  `invoiceNumber` VARCHAR(45) NULL ,
  `pricePerTon` DECIMAL(10,3) NULL ,
  `invoiceId` BIGINT NULL ,
  PRIMARY KEY (`id`) ,
  INDEX `fk_rii_invoiceId` (`invoiceId` ASC) ,
  CONSTRAINT `fk_rii_invoiceId`
    FOREIGN KEY (`invoiceId` )
    REFERENCES `RecyclingInvoices` (`id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `ci_sessions`
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS `ci_sessions` (
  `session_id` VARCHAR(40) NOT NULL DEFAULT '0' ,
  `ip_address` VARCHAR(16) NOT NULL DEFAULT '0' ,
  `user_agent` VARCHAR(50) NOT NULL ,
  `last_activity` INT(10) UNSIGNED NOT NULL DEFAULT '0' ,
  `user_data` TEXT NOT NULL ,
  PRIMARY KEY (`session_id`) )
ENGINE = MyISAM
DEFAULT CHARACTER SET = latin1
COLLATE = latin1_swedish_ci;


-- -----------------------------------------------------
-- Table `RecyclingCharges`
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS `RecyclingCharges` (
  `id` BIGINT NOT NULL AUTO_INCREMENT ,
  `date` DATE NULL ,
  `vendorId` INT NULL ,
  `vendorLocation` VARCHAR(128) NULL ,
  `vendorNumber` VARCHAR(45) NULL ,
  `status` ENUM('YES','NO') NULL DEFAULT 'NO' ,
  `internalNotes` TEXT NULL ,
  `lastUpdated` DATE NULL ,
  `companyId` INT NULL ,
  `dateSent` DATE NULL ,
  PRIMARY KEY (`id`) ,
  INDEX `fk_rc_vendorId` (`vendorId` ASC) ,
  INDEX `fk_rc_companyId` (`companyId` ASC) ,
  CONSTRAINT `fk_rc_vendorId`
    FOREIGN KEY (`vendorId` )
    REFERENCES `Vendors` (`id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_rc_companyId`
    FOREIGN KEY (`companyId` )
    REFERENCES `Companies` (`id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `RecyclingChargesFees`
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS `RecyclingChargesFees` (
  `id` BIGINT NOT NULL AUTO_INCREMENT ,
  `feeType` INT NULL ,
  `fee` DECIMAL(10,2) NULL ,
  `recyclingChargeId` BIGINT NULL ,
  PRIMARY KEY (`id`) ,
  INDEX `fk_rcf_recyclingChargeId` (`recyclingChargeId` ASC) ,
  CONSTRAINT `fk_rcf_recyclingChargeId`
    FOREIGN KEY (`recyclingChargeId` )
    REFERENCES `RecyclingCharges` (`id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `RecyclingChargeItems`
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS `RecyclingChargeItems` (
  `id` BIGINT NOT NULL AUTO_INCREMENT ,
  `materialDate` DATE NULL ,
  `releaseNumber` VARCHAR(64) NULL ,
  `materialId` INT NULL ,
  `quantity` DECIMAL(10,2) NULL ,
  `unitId` INT NULL ,
  `pricePerTon` DECIMAL(10,2) NULL ,
  `description` TEXT NULL ,
  `recyclingChargeId` BIGINT NULL ,
  PRIMARY KEY (`id`) ,
  INDEX `fk_rci_recycleChargeId` (`recyclingChargeId` ASC) ,
  INDEX `fk_rci_materialId` (`materialId` ASC) ,
  CONSTRAINT `fk_rci_recycleChargeId`
    FOREIGN KEY (`recyclingChargeId` )
    REFERENCES `RecyclingCharges` (`id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_rci_materialId`
    FOREIGN KEY (`materialId` )
    REFERENCES `Materials` (`id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `WasteInvoices`
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS `WasteInvoices` (
  `id` BIGINT NOT NULL AUTO_INCREMENT ,
  `invoiceDate` DATE NULL ,
  `vendorId` INT NULL ,
  `vendorName` VARCHAR(128) NULL ,
  `locationId` INT NULL ,
  `locationType` ENUM('STORE','DC') NULL ,
  `locationName` VARCHAR(128) NULL ,
  `dateSent` DATE NULL ,
  `internalNotes` TEXT NULL ,
  `lastUpdated` TIMESTAMP NULL ,
  `status` ENUM('YES','NO') NULL DEFAULT 'NO' ,
  `companyId` INT NULL ,
  `total` DECIMAL(10,2) NULL ,
  PRIMARY KEY (`id`) ,
  INDEX `fk_wi_companyId` (`companyId` ASC) ,
  INDEX `fk_wi_vendorId` (`vendorId` ASC) ,
  CONSTRAINT `fk_wi_companyId`
    FOREIGN KEY (`companyId` )
    REFERENCES `Companies` (`id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_wi_vendorId`
    FOREIGN KEY (`vendorId` )
    REFERENCES `Vendors` (`id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `WasteInvoiceServices`
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS `WasteInvoiceServices` (
  `id` BIGINT NOT NULL AUTO_INCREMENT ,
  `serviceDate` DATE NULL ,
  `serviceTypeId` INT NULL ,
  `materialId` INT NULL ,
  `quantity` DECIMAL(10,3) NULL ,
  `unitId` INT NULL ,
  `trashFee` DECIMAL(10,2) NULL ,
  `serviceId` INT NULL ,
  `invoiceId` BIGINT NULL ,
  PRIMARY KEY (`id`) ,
  INDEX `fk_wis_invoiceId` (`invoiceId` ASC) ,
  INDEX `fk_wis_materialId` (`materialId` ASC) ,
  CONSTRAINT `fk_wis_invoiceId`
    FOREIGN KEY (`invoiceId` )
    REFERENCES `WasteInvoices` (`id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_wis_materialId`
    FOREIGN KEY (`materialId` )
    REFERENCES `Materials` (`id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `WasteInvoiceFees`
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS `WasteInvoiceFees` (
  `id` BIGINT NOT NULL AUTO_INCREMENT ,
  `invoiceId` BIGINT NULL ,
  `feeType` INT NULL ,
  `feeAmount` DECIMAL(10,2) NULL ,
  PRIMARY KEY (`id`) ,
  INDEX `fk_wf_invoiceId` (`invoiceId` ASC) ,
  CONSTRAINT `fk_wf_invoiceId`
    FOREIGN KEY (`invoiceId` )
    REFERENCES `WasteInvoices` (`id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;



SET SQL_MODE=@OLD_SQL_MODE;
SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS;
SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS;

-- -----------------------------------------------------
-- Data for table `Companies`
-- -----------------------------------------------------
START TRANSACTION;
INSERT INTO `Companies` (`id`, `name`, `addressLine`, `email`, `phone`) VALUES
	(1, 'Walgreens', '123 Walgreens St,<br/> New York, NY 90210', 'change@this.email.example.com', '800-278-6726');
COMMIT;

START TRANSACTION;

insert into CompanyUsers (username, password, firstName, lastName, title, email, phone, accessLevel, active, companyid)
	values (
	'tien', '057ba03d6c44104863dc7361fe4578965d1887360f90a0895882e58a6248fc86596dff7ca04eb17b0184fd32a7229d2c28d71de29752b6663a2653f0bf9904b3',
	'Tin', 'Yuan',
	'N/A',
	'tien@nrgmeeting.com', '555-555-5555',
	'ADMIN', 1,
	1), (
	'nikolay', '057ba03d6c44104863dc7361fe4578965d1887360f90a0895882e58a6248fc86596dff7ca04eb17b0184fd32a7229d2c28d71de29752b6663a2653f0bf9904b3',
	'Nikolay', 'Kolev',
	'N/A',
	'nikolaynkolev@gmail.com', '555-555-5555',
	'ADMIN', 1,
	1), ('vanya', '057ba03d6c44104863dc7361fe4578965d1887360f90a0895882e58a6248fc86596dff7ca04eb17b0184fd32a7229d2c28d71de29752b6663a2653f0bf9904b3',
	'Vanya', 'Dimitrova',
	'N/A',
	'vanya.dimitrova@iteco.bg', '555-555-5555',
	'ADMIN', 1,
	1), ('frank', '057ba03d6c44104863dc7361fe4578965d1887360f90a0895882e58a6248fc86596dff7ca04eb17b0184fd32a7229d2c28d71de29752b6663a2653f0bf9904b3',
	'Frank', 'Brown',
	'N/A',
	'appsontap@gmail.com', '555-555-5555',
	'ADMIN', 1,
	1);
COMMIT;


-- -----------------------------------------------------
-- Data for table `States`
-- -----------------------------------------------------
START TRANSACTION;

INSERT INTO `States` (`id`, `name`, `code`, `region`) VALUES (NULL, 'Alabama', 'AL', NULL);
INSERT INTO `States` (`id`, `name`, `code`, `region`) VALUES (NULL, 'Alaska', 'AK', NULL);
INSERT INTO `States` (`id`, `name`, `code`, `region`) VALUES (NULL, 'Arizona', 'AZ', NULL);
INSERT INTO `States` (`id`, `name`, `code`, `region`) VALUES (NULL, 'Arkansas', 'AR', NULL);
INSERT INTO `States` (`id`, `name`, `code`, `region`) VALUES (NULL, 'California', 'CA', NULL);
INSERT INTO `States` (`id`, `name`, `code`, `region`) VALUES (NULL, 'Colorado', 'CO', NULL);
INSERT INTO `States` (`id`, `name`, `code`, `region`) VALUES (NULL, 'Connecticut', 'CT', NULL);
INSERT INTO `States` (`id`, `name`, `code`, `region`) VALUES (NULL, 'Delaware', 'DW', NULL);
INSERT INTO `States` (`id`, `name`, `code`, `region`) VALUES (NULL, 'Florida', 'FL', NULL);
INSERT INTO `States` (`id`, `name`, `code`, `region`) VALUES (NULL, 'Georgia', 'GA', NULL);
INSERT INTO `States` (`id`, `name`, `code`, `region`) VALUES (NULL, 'Hawaii', 'HI', NULL);
INSERT INTO `States` (`id`, `name`, `code`, `region`) VALUES (NULL, 'Idaho', 'ID', NULL);
INSERT INTO `States` (`id`, `name`, `code`, `region`) VALUES (NULL, 'Illinois', 'IL', NULL);
INSERT INTO `States` (`id`, `name`, `code`, `region`) VALUES (NULL, 'Indiana', 'IN', NULL);
INSERT INTO `States` (`id`, `name`, `code`, `region`) VALUES (NULL, 'Iowa', 'IA', NULL);
INSERT INTO `States` (`id`, `name`, `code`, `region`) VALUES (NULL, 'Kansas', 'KS', NULL);
INSERT INTO `States` (`id`, `name`, `code`, `region`) VALUES (NULL, 'Kentucky', 'KY', NULL);
INSERT INTO `States` (`id`, `name`, `code`, `region`) VALUES (NULL, 'Louisiana', 'LA', NULL);
INSERT INTO `States` (`id`, `name`, `code`, `region`) VALUES (NULL, 'Maine', 'ME', NULL);
INSERT INTO `States` (`id`, `name`, `code`, `region`) VALUES (NULL, 'Maryland', 'MD', '');
INSERT INTO `States` (`id`, `name`, `code`, `region`) VALUES (NULL, 'Massachusetts', 'MA', NULL);
INSERT INTO `States` (`id`, `name`, `code`, `region`) VALUES (NULL, 'Michigan', 'MI', NULL);
INSERT INTO `States` (`id`, `name`, `code`, `region`) VALUES (NULL, 'Minnesota', 'MN', NULL);
INSERT INTO `States` (`id`, `name`, `code`, `region`) VALUES (NULL, 'Mississippi', 'MS', NULL);
INSERT INTO `States` (`id`, `name`, `code`, `region`) VALUES (NULL, 'Missouri', 'MO', NULL);
INSERT INTO `States` (`id`, `name`, `code`, `region`) VALUES (NULL, 'Montana', 'MT', NULL);
INSERT INTO `States` (`id`, `name`, `code`, `region`) VALUES (NULL, 'Nebraska', 'NE', NULL);
INSERT INTO `States` (`id`, `name`, `code`, `region`) VALUES (NULL, 'Nevada', 'NV', NULL);
INSERT INTO `States` (`id`, `name`, `code`, `region`) VALUES (NULL, 'New Hampshire', 'NH', '');
INSERT INTO `States` (`id`, `name`, `code`, `region`) VALUES (NULL, 'New Jersey', 'NJ', NULL);
INSERT INTO `States` (`id`, `name`, `code`, `region`) VALUES (NULL, 'New Mexico', 'NM', NULL);
INSERT INTO `States` (`id`, `name`, `code`, `region`) VALUES (NULL, 'New York', 'NY', NULL);
INSERT INTO `States` (`id`, `name`, `code`, `region`) VALUES (NULL, 'North Carolina', 'NC', NULL);
INSERT INTO `States` (`id`, `name`, `code`, `region`) VALUES (NULL, 'North Dakota', 'ND', NULL);
INSERT INTO `States` (`id`, `name`, `code`, `region`) VALUES (NULL, 'Ohio', 'OH', NULL);
INSERT INTO `States` (`id`, `name`, `code`, `region`) VALUES (NULL, 'Oklahoma', 'OK', NULL);
INSERT INTO `States` (`id`, `name`, `code`, `region`) VALUES (NULL, 'Oregon', 'OR', NULL);
INSERT INTO `States` (`id`, `name`, `code`, `region`) VALUES (NULL, 'Pennsylvania', 'PA', NULL);
INSERT INTO `States` (`id`, `name`, `code`, `region`) VALUES (NULL, 'Rhode Island', 'RI', NULL);
INSERT INTO `States` (`id`, `name`, `code`, `region`) VALUES (NULL, 'South Carolina', 'SC', NULL);
INSERT INTO `States` (`id`, `name`, `code`, `region`) VALUES (NULL, 'South Dakota', 'SD', NULL);
INSERT INTO `States` (`id`, `name`, `code`, `region`) VALUES (NULL, 'Tennessee', 'TN', NULL);
INSERT INTO `States` (`id`, `name`, `code`, `region`) VALUES (NULL, 'Texas', 'TX', NULL);
INSERT INTO `States` (`id`, `name`, `code`, `region`) VALUES (NULL, 'Utah', 'UT', NULL);
INSERT INTO `States` (`id`, `name`, `code`, `region`) VALUES (NULL, 'Vermont', 'VT', NULL);
INSERT INTO `States` (`id`, `name`, `code`, `region`) VALUES (NULL, 'Virginia', 'VA', NULL);
INSERT INTO `States` (`id`, `name`, `code`, `region`) VALUES (NULL, 'Washington', 'WA', NULL);
INSERT INTO `States` (`id`, `name`, `code`, `region`) VALUES (NULL, 'West Virginia', 'WV', NULL);
INSERT INTO `States` (`id`, `name`, `code`, `region`) VALUES (NULL, 'Wisconsin', 'VI', NULL);
INSERT INTO `States` (`id`, `name`, `code`, `region`) VALUES (NULL, 'Wyoming', 'WY', NULL);

COMMIT;


START TRANSACTION;
INSERT INTO `DistributionCenterServiceDurations` (`id`, `name`, `companyId`) VALUES
	(1, 'Temporary', 1),
	(2, 'Permanent', 1),
	(3, 'Other', 1);
COMMIT;

START TRANSACTION;
INSERT INTO `DistributionCenterServicePurposes` (`id`, `name`, `companyId`) VALUES
	(1, 'Normal Service', 1),
	(2, 'Pickup', 1),
	(3, 'Haul', 1),
	(4, 'Other', 1);
COMMIT;

START TRANSACTION;
INSERT INTO `Materials` (`id`, `name`, `companyId`) VALUES
	(1, 'Cardboard', 1),
	(2, 'Glass', 1),
	(3, 'Aluminum', 1),
	(4, 'Metal', 1),
	(5, 'Plastic', 1),
	(6, 'Paper', 1),
	(7, 'Wood', 1),
	(8, 'Film', 1),
	(9, 'Commingle', 1),
	(10, 'Single Stream', 1);
COMMIT;

START TRANSACTION;
INSERT INTO `SupportRequestContainers` (`id`, `name`, `companyId`) VALUES
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
	(21, 'Trash	Hand', 1),
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
COMMIT;

START TRANSACTION;
INSERT INTO `SupportRequestServiceTypes` (`id`, `name`, `companyId`) VALUES
	(1, 'Pickup', 1),
	(2, 'Haul', 1),
	(3, 'Relamping', 1),
	(4, 'Setup New Service', 1),
	(5, 'Other', 1);
COMMIT;

START TRANSACTION;
INSERT INTO `VendorServiceDurations` (`id`, `name`, `companyId`) VALUES
	(1, 'Temporary', 1),
	(2, 'Permanent', 1),
	(3, 'Other', 1);
COMMIT;

START TRANSACTION;
INSERT INTO `VendorServicePurposes` (`id`, `name`, `companyId`) VALUES
	(1, 'Normal Service', 1),
	(2, 'Pickup', 1),
	(3, 'Haul', 1),
	(4, 'Other', 1);
COMMIT;
