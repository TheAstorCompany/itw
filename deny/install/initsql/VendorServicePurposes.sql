-- phpMyAdmin SQL Dump
-- version 3.2.3
-- http://www.phpmyadmin.net
--
-- Host: 10.10.10.1
-- Generation Time: May 08, 2012 at 02:36 PM
-- Server version: 5.1.61
-- PHP Version: 5.3.5-1ubuntu7.8

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Database: `astor`
--

--
-- Dumping data for table `VendorServicePurposes`
--

INSERT INTO `VendorServicePurposes` (`id`, `name`, `companyId`) VALUES
(1, 'Normal Service', 1),
(2, 'Pickup', 1),
(3, 'Haul', 1),
(4, 'Other', 1);
