SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0;
SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0;
SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='TRADITIONAL';

CREATE SCHEMA IF NOT EXISTS `astor` DEFAULT CHARACTER SET latin1 COLLATE latin1_swedish_ci ;
USE `astor` ;

-- -----------------------------------------------------
-- Table `astor`.`Companies`
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS `astor`.`Companies` (
  `id` INT NOT NULL AUTO_INCREMENT ,
  `name` VARCHAR(128) NOT NULL ,
  `addressLine` VARCHAR(255) NULL ,
  `email` VARCHAR(128) NOT NULL ,
  `phone` VARCHAR(45) NOT NULL ,
  PRIMARY KEY (`id`) )
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `astor`.`SupportRequestServiceTypes`
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS `astor`.`SupportRequestServiceTypes` (
  `id` INT NOT NULL AUTO_INCREMENT ,
  `name` VARCHAR(128) NULL ,
  `companyId` INT NOT NULL ,
  PRIMARY KEY (`id`) ,
  INDEX `fk_srst_companyId` (`companyId` ASC) ,
  CONSTRAINT `fk_srst_companyId`
    FOREIGN KEY (`companyId` )
    REFERENCES `astor`.`Companies` (`id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `astor`.`SupportRequestContainers`
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS `astor`.`SupportRequestContainers` (
  `id` INT NOT NULL AUTO_INCREMENT ,
  `name` VARCHAR(128) NOT NULL ,
  `companyId` INT NOT NULL ,
  PRIMARY KEY (`id`) ,
  INDEX `fk_src_companyId` (`companyId` ASC) ,
  CONSTRAINT `fk_src_companyId`
    FOREIGN KEY (`companyId` )
    REFERENCES `astor`.`Companies` (`id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `astor`.`CompanyUsers`
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS `astor`.`CompanyUsers` (
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
    REFERENCES `astor`.`Companies` (`id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `astor`.`SupportRequests`
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS `astor`.`SupportRequests` (
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
    REFERENCES `astor`.`SupportRequestServiceTypes` (`id` )
    ON DELETE SET NULL
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_sr_containers`
    FOREIGN KEY (`containerId` )
    REFERENCES `astor`.`SupportRequestContainers` (`id` )
    ON DELETE SET NULL
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_sr_companyId`
    FOREIGN KEY (`companyId` )
    REFERENCES `astor`.`Companies` (`id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_sr_createdByUserId`
    FOREIGN KEY (`createdBy` )
    REFERENCES `astor`.`CompanyUsers` (`id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_sr_updatedByUserId`
    FOREIGN KEY (`updatedBy` )
    REFERENCES `astor`.`CompanyUsers` (`id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `astor`.`Regions`
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS `astor`.`Regions` (
  `id` INT NOT NULL AUTO_INCREMENT ,
  `name` VARCHAR(45) NULL ,
  `companyId` INT NULL ,
  PRIMARY KEY (`id`) ,
  INDEX `fk_r_companyId` (`companyId` ASC) ,
  CONSTRAINT `fk_r_companyId`
    FOREIGN KEY (`companyId` )
    REFERENCES `astor`.`Companies` (`id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `astor`.`States`
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS `astor`.`States` (
  `id` INT NOT NULL AUTO_INCREMENT ,
  `name` VARCHAR(45) NULL ,
  `code` VARCHAR(2) NULL ,
  `region` VARCHAR(45) NULL ,
  PRIMARY KEY (`id`) )
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `astor`.`Vendors`
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS `astor`.`Vendors` (
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
    REFERENCES `astor`.`Companies` (`id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_vc_regionId`
    FOREIGN KEY (`regionId` )
    REFERENCES `astor`.`Regions` (`id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_vc_stateId`
    FOREIGN KEY (`stateId` )
    REFERENCES `astor`.`States` (`id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `astor`.`RecyclingInvoices`
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS `astor`.`RecyclingInvoices` (
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
    REFERENCES `astor`.`Vendors` (`id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_ri_companyId`
    FOREIGN KEY (`companyId` )
    REFERENCES `astor`.`Companies` (`id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `astor`.`Materials`
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS `astor`.`Materials` (
  `id` INT NOT NULL AUTO_INCREMENT ,
  `name` VARCHAR(45) NULL ,
  `companyId` INT NULL ,
  PRIMARY KEY (`id`) ,
  INDEX `fk_m_companyId` (`companyId` ASC) ,
  CONSTRAINT `fk_m_companyId`
    FOREIGN KEY (`companyId` )
    REFERENCES `astor`.`Companies` (`id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `astor`.`RecyclingInvoicesMaterials`
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS `astor`.`RecyclingInvoicesMaterials` (
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
    REFERENCES `astor`.`RecyclingInvoices` (`id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_rim_materialId`
    FOREIGN KEY (`materialId` )
    REFERENCES `astor`.`Materials` (`id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `astor`.`VendorContacts`
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS `astor`.`VendorContacts` (
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
    REFERENCES `astor`.`Vendors` (`id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `astor`.`VendorServiceDurations`
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS `astor`.`VendorServiceDurations` (
  `id` INT NOT NULL AUTO_INCREMENT ,
  `name` VARCHAR(128) NULL ,
  `companyId` INT NULL ,
  PRIMARY KEY (`id`) ,
  INDEX `fk_vsd_companyId` (`companyId` ASC) ,
  CONSTRAINT `fk_vsd_companyId`
    FOREIGN KEY (`companyId` )
    REFERENCES `astor`.`Companies` (`id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `astor`.`VendorServicePurposes`
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS `astor`.`VendorServicePurposes` (
  `id` INT NOT NULL AUTO_INCREMENT ,
  `name` VARCHAR(128) NULL ,
  `companyId` INT NULL ,
  PRIMARY KEY (`id`) ,
  INDEX `fk_vsp_companyId` (`companyId` ASC) ,
  CONSTRAINT `fk_vsp_companyId`
    FOREIGN KEY (`companyId` )
    REFERENCES `astor`.`Companies` (`id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `astor`.`VendorServices`
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS `astor`.`VendorServices` (
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
    REFERENCES `astor`.`VendorServiceDurations` (`id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_vs_purposeId`
    FOREIGN KEY (`purposeId` )
    REFERENCES `astor`.`VendorServicePurposes` (`id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_vs_vendorId`
    FOREIGN KEY (`vendorId` )
    REFERENCES `astor`.`Vendors` (`id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `astor`.`Stores`
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS `astor`.`Stores` (
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
    REFERENCES `astor`.`Companies` (`id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_s_stateId`
    FOREIGN KEY (`stateId` )
    REFERENCES `astor`.`States` (`id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `astor`.`StoreContacts`
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS `astor`.`StoreContacts` (
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
    REFERENCES `astor`.`Stores` (`id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `astor`.`StoreServiceDurations`
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS `astor`.`StoreServiceDurations` (
  `id` INT NOT NULL AUTO_INCREMENT ,
  `name` VARCHAR(128) NOT NULL ,
  `companyId` INT NULL ,
  PRIMARY KEY (`id`) ,
  INDEX `fk_ssd_comapnyId` (`companyId` ASC) ,
  CONSTRAINT `fk_ssd_comapnyId`
    FOREIGN KEY (`companyId` )
    REFERENCES `astor`.`Companies` (`id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `astor`.`StoreServicePurposes`
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS `astor`.`StoreServicePurposes` (
  `id` INT NOT NULL AUTO_INCREMENT ,
  `name` VARCHAR(128) NOT NULL ,
  `companyId` INT NULL ,
  PRIMARY KEY (`id`) ,
  INDEX `fk_ssp_companyId` (`companyId` ASC) ,
  CONSTRAINT `fk_ssp_companyId`
    FOREIGN KEY (`companyId` )
    REFERENCES `astor`.`Companies` (`id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `astor`.`StoreServices`
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS `astor`.`StoreServices` (
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
    REFERENCES `astor`.`Stores` (`id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_ss_durationId`
    FOREIGN KEY (`durationId` )
    REFERENCES `astor`.`StoreServiceDurations` (`id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_ss_purposeId`
    FOREIGN KEY (`purposeId` )
    REFERENCES `astor`.`StoreServicePurposes` (`id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `astor`.`DistributionCenters`
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS `astor`.`DistributionCenters` (
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
    REFERENCES `astor`.`Companies` (`id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_dc_stateId`
    FOREIGN KEY (`stateId` )
    REFERENCES `astor`.`States` (`id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `astor`.`DistributionCenterContacts`
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS `astor`.`DistributionCenterContacts` (
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
    REFERENCES `astor`.`DistributionCenters` (`id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `astor`.`DistributionCenterServiceDurations`
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS `astor`.`DistributionCenterServiceDurations` (
  `id` INT NOT NULL AUTO_INCREMENT ,
  `name` VARCHAR(128) NULL ,
  `companyId` INT NULL ,
  PRIMARY KEY (`id`) ,
  INDEX `fk_dcsd_companyId` (`companyId` ASC) ,
  CONSTRAINT `fk_dcsd_companyId`
    FOREIGN KEY (`companyId` )
    REFERENCES `astor`.`Companies` (`id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `astor`.`DistributionCenterServicePurposes`
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS `astor`.`DistributionCenterServicePurposes` (
  `id` INT NOT NULL AUTO_INCREMENT ,
  `name` VARCHAR(128) NULL ,
  `companyId` INT NULL ,
  PRIMARY KEY (`id`) ,
  INDEX `fk_dcsp_companyId` (`companyId` ASC) ,
  CONSTRAINT `fk_dcsp_companyId`
    FOREIGN KEY (`companyId` )
    REFERENCES `astor`.`Companies` (`id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `astor`.`DistributionCenterServices`
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS `astor`.`DistributionCenterServices` (
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
    REFERENCES `astor`.`DistributionCenters` (`id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_dcs_durationId`
    FOREIGN KEY (`durationId` )
    REFERENCES `astor`.`DistributionCenterServiceDurations` (`id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_dcs_purposeId`
    FOREIGN KEY (`purposeId` )
    REFERENCES `astor`.`DistributionCenterServicePurposes` (`id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `astor`.`MarketRate`
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS `astor`.`MarketRate` (
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
    REFERENCES `astor`.`Regions` (`id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_mr_materialId`
    FOREIGN KEY (`materialId` )
    REFERENCES `astor`.`Materials` (`id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_mr_companyId`
    FOREIGN KEY (`companyId` )
    REFERENCES `astor`.`Companies` (`id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `astor`.`RecyclingInvoicesFees`
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS `astor`.`RecyclingInvoicesFees` (
  `id` BIGINT NOT NULL AUTO_INCREMENT ,
  `invoiceId` BIGINT NULL ,
  `feeType` INT NULL ,
  `feeAmount` DECIMAL(10,2) NULL ,
  PRIMARY KEY (`id`) ,
  INDEX `fk_rif_invoiceId` (`invoiceId` ASC) ,
  CONSTRAINT `fk_rif_invoiceId`
    FOREIGN KEY (`invoiceId` )
    REFERENCES `astor`.`RecyclingInvoices` (`id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `astor`.`RecyclingInvoicesInfo`
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS `astor`.`RecyclingInvoicesInfo` (
  `id` BIGINT NOT NULL AUTO_INCREMENT ,
  `invoiceDate` DATE NULL ,
  `invoiceNumber` VARCHAR(45) NULL ,
  `pricePerTon` DECIMAL(10,3) NULL ,
  `invoiceId` BIGINT NULL ,
  PRIMARY KEY (`id`) ,
  INDEX `fk_rii_invoiceId` (`invoiceId` ASC) ,
  CONSTRAINT `fk_rii_invoiceId`
    FOREIGN KEY (`invoiceId` )
    REFERENCES `astor`.`RecyclingInvoices` (`id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `astor`.`ci_sessions`
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS `astor`.`ci_sessions` (
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
-- Table `astor`.`RecyclingCharges`
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS `astor`.`RecyclingCharges` (
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
    REFERENCES `astor`.`Vendors` (`id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_rc_companyId`
    FOREIGN KEY (`companyId` )
    REFERENCES `astor`.`Companies` (`id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `astor`.`RecyclingChargesFees`
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS `astor`.`RecyclingChargesFees` (
  `id` BIGINT NOT NULL AUTO_INCREMENT ,
  `feeType` INT NULL ,
  `fee` DECIMAL(10,2) NULL ,
  `recyclingChargeId` BIGINT NULL ,
  PRIMARY KEY (`id`) ,
  INDEX `fk_rcf_recyclingChargeId` (`recyclingChargeId` ASC) ,
  CONSTRAINT `fk_rcf_recyclingChargeId`
    FOREIGN KEY (`recyclingChargeId` )
    REFERENCES `astor`.`RecyclingCharges` (`id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `astor`.`RecyclingChargeItems`
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS `astor`.`RecyclingChargeItems` (
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
    REFERENCES `astor`.`RecyclingCharges` (`id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_rci_materialId`
    FOREIGN KEY (`materialId` )
    REFERENCES `astor`.`Materials` (`id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `astor`.`WasteInvoices`
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS `astor`.`WasteInvoices` (
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
    REFERENCES `astor`.`Companies` (`id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_wi_vendorId`
    FOREIGN KEY (`vendorId` )
    REFERENCES `astor`.`Vendors` (`id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `astor`.`WasteInvoiceServices`
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS `astor`.`WasteInvoiceServices` (
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
    REFERENCES `astor`.`WasteInvoices` (`id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_wis_materialId`
    FOREIGN KEY (`materialId` )
    REFERENCES `astor`.`Materials` (`id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `astor`.`WasteInvoiceFees`
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS `astor`.`WasteInvoiceFees` (
  `id` BIGINT NOT NULL AUTO_INCREMENT ,
  `invoiceId` BIGINT NULL ,
  `feeType` INT NULL ,
  `feeAmount` DECIMAL(10,2) NULL ,
  PRIMARY KEY (`id`) ,
  INDEX `fk_wf_invoiceId` (`invoiceId` ASC) ,
  CONSTRAINT `fk_wf_invoiceId`
    FOREIGN KEY (`invoiceId` )
    REFERENCES `astor`.`WasteInvoices` (`id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;



SET SQL_MODE=@OLD_SQL_MODE;
SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS;
SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS;

-- -----------------------------------------------------
-- Data for table `astor`.`Companies`
-- -----------------------------------------------------
START TRANSACTION;
USE `astor`;
INSERT INTO `astor`.`Companies` (`id`, `name`, `addressLine`, `email`, `phone`) VALUES (1, 'Walgreens', '123 Walgreens St, New York, NY 90210', NULL, NULL);

COMMIT;

-- -----------------------------------------------------
-- Data for table `astor`.`States`
-- -----------------------------------------------------
START TRANSACTION;
USE `astor`;
INSERT INTO `astor`.`States` (`id`, `name`, `code`, `region`) VALUES (NULL, 'Alabama', 'AL', NULL);
INSERT INTO `astor`.`States` (`id`, `name`, `code`, `region`) VALUES (NULL, 'Alaska', 'AK', NULL);
INSERT INTO `astor`.`States` (`id`, `name`, `code`, `region`) VALUES (NULL, 'Arizona', 'AZ', NULL);
INSERT INTO `astor`.`States` (`id`, `name`, `code`, `region`) VALUES (NULL, 'Arkansas', 'AR', NULL);
INSERT INTO `astor`.`States` (`id`, `name`, `code`, `region`) VALUES (NULL, 'California', 'CA', NULL);
INSERT INTO `astor`.`States` (`id`, `name`, `code`, `region`) VALUES (NULL, 'Colorado', 'CO', NULL);
INSERT INTO `astor`.`States` (`id`, `name`, `code`, `region`) VALUES (NULL, 'Connecticut', 'CT', NULL);
INSERT INTO `astor`.`States` (`id`, `name`, `code`, `region`) VALUES (NULL, 'Delaware', 'DW', NULL);
INSERT INTO `astor`.`States` (`id`, `name`, `code`, `region`) VALUES (NULL, 'Florida', 'FL', NULL);
INSERT INTO `astor`.`States` (`id`, `name`, `code`, `region`) VALUES (NULL, 'Georgia', 'GA', NULL);
INSERT INTO `astor`.`States` (`id`, `name`, `code`, `region`) VALUES (NULL, 'Hawaii', 'HI', NULL);
INSERT INTO `astor`.`States` (`id`, `name`, `code`, `region`) VALUES (NULL, 'Idaho', 'ID', NULL);
INSERT INTO `astor`.`States` (`id`, `name`, `code`, `region`) VALUES (NULL, 'Illinois', 'IL', NULL);
INSERT INTO `astor`.`States` (`id`, `name`, `code`, `region`) VALUES (NULL, 'Indiana', 'IN', NULL);
INSERT INTO `astor`.`States` (`id`, `name`, `code`, `region`) VALUES (NULL, 'Iowa', 'IA', NULL);
INSERT INTO `astor`.`States` (`id`, `name`, `code`, `region`) VALUES (NULL, 'Kansas', 'KS', NULL);
INSERT INTO `astor`.`States` (`id`, `name`, `code`, `region`) VALUES (NULL, 'Kentucky', 'KY', NULL);
INSERT INTO `astor`.`States` (`id`, `name`, `code`, `region`) VALUES (NULL, 'Louisiana', 'LA', NULL);
INSERT INTO `astor`.`States` (`id`, `name`, `code`, `region`) VALUES (NULL, 'Maine', 'ME', NULL);
INSERT INTO `astor`.`States` (`id`, `name`, `code`, `region`) VALUES (NULL, 'Maryland', 'MD', '');
INSERT INTO `astor`.`States` (`id`, `name`, `code`, `region`) VALUES (NULL, 'Massachusetts', 'MA', NULL);
INSERT INTO `astor`.`States` (`id`, `name`, `code`, `region`) VALUES (NULL, 'Michigan', 'MI', NULL);
INSERT INTO `astor`.`States` (`id`, `name`, `code`, `region`) VALUES (NULL, 'Minnesota', 'MN', NULL);
INSERT INTO `astor`.`States` (`id`, `name`, `code`, `region`) VALUES (NULL, 'Mississippi', 'MS', NULL);
INSERT INTO `astor`.`States` (`id`, `name`, `code`, `region`) VALUES (NULL, 'Missouri', 'MO', NULL);
INSERT INTO `astor`.`States` (`id`, `name`, `code`, `region`) VALUES (NULL, 'Montana', 'MT', NULL);
INSERT INTO `astor`.`States` (`id`, `name`, `code`, `region`) VALUES (NULL, 'Nebraska', 'NE', NULL);
INSERT INTO `astor`.`States` (`id`, `name`, `code`, `region`) VALUES (NULL, 'Nevada', 'NV', NULL);
INSERT INTO `astor`.`States` (`id`, `name`, `code`, `region`) VALUES (NULL, 'New Hampshire', 'NH', '');
INSERT INTO `astor`.`States` (`id`, `name`, `code`, `region`) VALUES (NULL, 'New Jersey', 'NJ', NULL);
INSERT INTO `astor`.`States` (`id`, `name`, `code`, `region`) VALUES (NULL, 'New Mexico', 'NM', NULL);
INSERT INTO `astor`.`States` (`id`, `name`, `code`, `region`) VALUES (NULL, 'New York', 'NY', NULL);
INSERT INTO `astor`.`States` (`id`, `name`, `code`, `region`) VALUES (NULL, 'North Carolina', 'NC', NULL);
INSERT INTO `astor`.`States` (`id`, `name`, `code`, `region`) VALUES (NULL, 'North Dakota', 'ND', NULL);
INSERT INTO `astor`.`States` (`id`, `name`, `code`, `region`) VALUES (NULL, 'Ohio', 'OH', NULL);
INSERT INTO `astor`.`States` (`id`, `name`, `code`, `region`) VALUES (NULL, 'Oklahoma', 'OK', NULL);
INSERT INTO `astor`.`States` (`id`, `name`, `code`, `region`) VALUES (NULL, 'Oregon', 'OR', NULL);
INSERT INTO `astor`.`States` (`id`, `name`, `code`, `region`) VALUES (NULL, 'Pennsylvania', 'PA', NULL);
INSERT INTO `astor`.`States` (`id`, `name`, `code`, `region`) VALUES (NULL, 'Rhode Island', 'RI', NULL);
INSERT INTO `astor`.`States` (`id`, `name`, `code`, `region`) VALUES (NULL, 'South Carolina', 'SC', NULL);
INSERT INTO `astor`.`States` (`id`, `name`, `code`, `region`) VALUES (NULL, 'South Dakota', 'SD', NULL);
INSERT INTO `astor`.`States` (`id`, `name`, `code`, `region`) VALUES (NULL, 'Tennessee', 'TN', NULL);
INSERT INTO `astor`.`States` (`id`, `name`, `code`, `region`) VALUES (NULL, 'Texas', 'TX', NULL);
INSERT INTO `astor`.`States` (`id`, `name`, `code`, `region`) VALUES (NULL, 'Utah', 'UT', NULL);
INSERT INTO `astor`.`States` (`id`, `name`, `code`, `region`) VALUES (NULL, 'Vermont', 'VT', NULL);
INSERT INTO `astor`.`States` (`id`, `name`, `code`, `region`) VALUES (NULL, 'Virginia', 'VA', NULL);
INSERT INTO `astor`.`States` (`id`, `name`, `code`, `region`) VALUES (NULL, 'Washington', 'WA', NULL);
INSERT INTO `astor`.`States` (`id`, `name`, `code`, `region`) VALUES (NULL, 'West Virginia', 'WV', NULL);
INSERT INTO `astor`.`States` (`id`, `name`, `code`, `region`) VALUES (NULL, 'Wisconsin', 'VI', NULL);
INSERT INTO `astor`.`States` (`id`, `name`, `code`, `region`) VALUES (NULL, 'Wyoming', 'WY', NULL);

COMMIT;
