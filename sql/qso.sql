-- phpMyAdmin SQL Dump
-- version 4.7.0
-- https://www.phpmyadmin.net/
--
-- Host: localhost
-- Generation Time: 2017 m. Grd 12 d. 21:37
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
-- Sukurta duomenų struktūra lentelei `qso`
--

CREATE TABLE `qso` (
  `id` int(10) NOT NULL,
  `datetimenow` datetime NOT NULL,
  `datetime` datetime NOT NULL,
  `caller` varchar(15) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL COMMENT 'STATION_CALLSIGN',
  `caller_simple` varchar(15) COLLATE utf8_lithuanian_ci NOT NULL,
  `operator` varchar(25) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL COMMENT 'OPERATOR',
  `operator_simple` varchar(25) COLLATE utf8_lithuanian_ci NOT NULL,
  `call` varchar(15) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL,
  `call_simple` varchar(15) COLLATE utf8_lithuanian_ci NOT NULL,
  `band` varchar(5) COLLATE utf8_lithuanian_ci NOT NULL DEFAULT '0',
  `freq` varchar(10) COLLATE utf8_lithuanian_ci NOT NULL DEFAULT '0',
  `mode` varchar(10) COLLATE utf8_lithuanian_ci NOT NULL,
  `rstr` varchar(5) COLLATE utf8_lithuanian_ci NOT NULL,
  `rsts` varchar(5) COLLATE utf8_lithuanian_ci NOT NULL,
  `notes` varchar(255) COLLATE utf8_lithuanian_ci NOT NULL,
  `error` int(3) NOT NULL,
  `pass` varchar(50) COLLATE utf8_lithuanian_ci NOT NULL DEFAULT '0',
  `wwl` varchar(10) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL,
  `wal1` varchar(2) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL,
  `wal2` int(3) NOT NULL DEFAULT '0',
  `lhfa1` varchar(3) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL,
  `lhfa2` int(4) NOT NULL DEFAULT '0',
  `lyff1` varchar(5) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL,
  `lyff2` int(4) NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_lithuanian_ci;

--
-- Triggers `qso`
--
DELIMITER $$
CREATE TRIGGER `qso_trg_d` BEFORE DELETE ON `qso` FOR EACH ROW begin

INSERT INTO `qrzlt_logs`.`jrn_qso` VALUES (NULL, old.id, old.datetimenow, old.datetime, old.caller, old.operator, old.call, old.band, old.freq, old.mode, old.rstr, old.rsts, old.notes, old.error, old.pass, old.wwl, old.wal1, old.wal2, old.lhfa1, old.lhfa2, old.lyff1, old.lyff2, NOW(), 'D');

end
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `qso_trg_i` BEFORE INSERT ON `qso` FOR EACH ROW begin

set new.call_simple = get_simple_call(new.call);
set new.caller_simple = get_simple_call(new.caller);
set new.operator_simple = get_simple_call(new.operator);


INSERT INTO `jrn_qso`(`vid`, `id`, `veiksmodata`, `veiksmas`) VALUES (NULL,(
            SELECT AUTO_INCREMENT 
            FROM information_schema.TABLES 
            WHERE TABLE_SCHEMA = DATABASE() 
            AND TABLE_NAME = 'qso'
      ),now(),'I');

end
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `qso_trg_u` BEFORE UPDATE ON `qso` FOR EACH ROW begin

set new.call_simple = get_simple_call(new.call);
set new.caller_simple = get_simple_call(new.caller);
set new.operator_simple = get_simple_call(new.operator);

INSERT INTO `qrzlt_logs`.`jrn_qso` VALUES (NULL, old.id, old.datetimenow, old.datetime, old.caller, old.operator, old.call, old.band, old.freq, old.mode, old.rstr, old.rsts, old.notes, old.error, old.pass, old.wwl, old.wal1, old.wal2, old.lhfa1, old.lhfa2, old.lyff1, old.lyff2, NOW(), 'U');

end
$$
DELIMITER ;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `qso`
--
ALTER TABLE `qso`
  ADD PRIMARY KEY (`id`),
  ADD KEY `caller` (`caller`),
  ADD KEY `call` (`call`),
  ADD KEY `wwl` (`wwl`),
  ADD KEY `wal1` (`wal1`,`wal2`),
  ADD KEY `lhfa1` (`lhfa1`,`lhfa2`),
  ADD KEY `lyff1` (`lyff1`,`lyff2`),
  ADD KEY `datetime` (`datetime`),
  ADD KEY `caller_simple` (`caller_simple`),
  ADD KEY `operator_simple` (`operator_simple`),
  ADD KEY `call_simple` (`call_simple`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `qso`
--
ALTER TABLE `qso`
  MODIFY `id` int(10) NOT NULL AUTO_INCREMENT;COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
