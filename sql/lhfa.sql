-- phpMyAdmin SQL Dump
-- version 4.7.0
-- https://www.phpmyadmin.net/
--
-- Host: localhost
-- Generation Time: 2017 m. Grd 12 d. 21:41
-- Server version: 10.0.33-MariaDB
-- PHP Version: 7.1.11

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `qrzlt_logs`
--

-- --------------------------------------------------------

--
-- Sukurta duomenų struktūra lentelei `lhfa`
--

CREATE TABLE `lhfa` (
  `id` int(10) NOT NULL,
  `state` varchar(3) COLLATE utf8_lithuanian_ci NOT NULL,
  `nr` int(3) NOT NULL DEFAULT '0',
  `name` varchar(255) COLLATE utf8_lithuanian_ci NOT NULL,
  `adress` varchar(255) COLLATE utf8_lithuanian_ci NOT NULL,
  `coordsN` decimal(10,7) NOT NULL,
  `coordsE` decimal(10,7) NOT NULL,
  `x` decimal(20,7) NOT NULL,
  `y` decimal(20,7) NOT NULL,
  `WWL` varchar(10) COLLATE utf8_lithuanian_ci NOT NULL,
  `WAL` varchar(10) COLLATE utf8_lithuanian_ci NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_lithuanian_ci;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `lhfa`
--
ALTER TABLE `lhfa`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `lhfa`
--
ALTER TABLE `lhfa`
  MODIFY `id` int(10) NOT NULL AUTO_INCREMENT;COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
