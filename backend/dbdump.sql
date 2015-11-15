-- phpMyAdmin SQL Dump
-- version 4.0.10.10
-- http://www.phpmyadmin.net
--
-- Host: 127.8.179.2:3306
-- Generation Time: Jun 28, 2015 at 08:18 AM
-- Server version: 5.5.41
-- PHP Version: 5.3.3

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Database: `doctr`
--

-- --------------------------------------------------------

--
-- Table structure for table `allergies`
--

CREATE TABLE IF NOT EXISTS `allergies` (
  `aid` int(11) NOT NULL AUTO_INCREMENT,
  `uid` bigint(50) NOT NULL,
  `allergen` text NOT NULL,
  `reaction` text NOT NULL,
  `severity` text NOT NULL,
  `comment` text NOT NULL,
  `actions` text NOT NULL,
  `lastupdated` date NOT NULL,
  PRIMARY KEY (`aid`),
  KEY `allergies_uid` (`uid`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=5 ;

--
-- Dumping data for table `allergies`
--

INSERT INTO `allergies` (`aid`, `uid`, `allergen`, `reaction`, `severity`, `comment`, `actions`, `lastupdated`) VALUES
(1, 2, 'allergen1', 'react1', 'sev1', 'co1', 'act1', '2015-06-06'),
(2, 3, 'allergen1', 'react123', 'severity', 'commm222', 'act3333', '2015-06-07'),
(4, 6, 'alll', 'reeee', 'seee', '', 'actttt', '0000-00-00');

-- --------------------------------------------------------

--
-- Table structure for table `doctors`
--

CREATE TABLE IF NOT EXISTS `doctors` (
  `did` int(11) NOT NULL,
  `uid` bigint(50) NOT NULL,
  PRIMARY KEY (`did`),
  KEY `doctors_uid` (`uid`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `doctors`
--

INSERT INTO `doctors` (`did`, `uid`) VALUES
(1, 2),
(7, 5),
(8, 6);

-- --------------------------------------------------------

--
-- Table structure for table `pharmacists`
--

CREATE TABLE IF NOT EXISTS `pharmacists` (
  `phid` int(11) NOT NULL,
  `uid` bigint(50) NOT NULL,
  KEY `uid` (`uid`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `pharmacists`
--

INSERT INTO `pharmacists` (`phid`, `uid`) VALUES
(1, 4),
(2, 7);

-- --------------------------------------------------------

--
-- Table structure for table `records`
--

CREATE TABLE IF NOT EXISTS `records` (
  `rid` int(11) NOT NULL AUTO_INCREMENT,
  `uid` bigint(50) NOT NULL,
  `did` int(11) NOT NULL,
  `diagnosis` text NOT NULL,
  `date` date NOT NULL,
  `medicine` text NOT NULL,
  PRIMARY KEY (`rid`),
  KEY `records_uid` (`uid`) USING BTREE,
  KEY `records_did` (`did`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=8 ;

--
-- Dumping data for table `records`
--

INSERT INTO `records` (`rid`, `uid`, `did`, `diagnosis`, `date`, `medicine`) VALUES
(1, 2, 1, 'dia1', '2015-06-05', 'med1 med3'),
(2, 3, 1, 'dia2', '2015-06-07', 'med2 med3'),
(3, 3, 1, 'dia2', '2015-06-07', 'med2 med4'),
(7, 3, 1, 'dsvfsd', '2015-06-20', 'med1');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE IF NOT EXISTS `users` (
  `uid` bigint(50) NOT NULL AUTO_INCREMENT,
  `email` varchar(256) NOT NULL,
  `password` varchar(256) NOT NULL,
  `name` varchar(50) NOT NULL,
  `gender` varchar(10) NOT NULL,
  `dob` date NOT NULL,
  PRIMARY KEY (`uid`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=11 ;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`uid`, `email`, `password`, `name`, `gender`, `dob`) VALUES
(1, 'jay@gmail.com', '5e884898da28047151d0e56f8dc6292773603d0d6aabbdd62a11ef721d1542d8', 'jay', 'M', '2015-06-09'),
(2, 'megh@gmail.com', '5e884898da28047151d0e56f8dc6292773603d0d6aabbdd62a11ef721d1542d8', 'megh', 'M', '1997-10-25'),
(3, 'meet@gmail.com', '5e884898da28047151d0e56f8dc6292773603d0d6aabbdd62a11ef721d1542d8', 'meet', 'M', '1993-03-07'),
(4, 'sid@gmail.com', '5e884898da28047151d0e56f8dc6292773603d0d6aabbdd62a11ef721d1542d8', 'sid', 'M', '1997-05-10'),
(5, 'meet2@gmail.com', '5e884898da28047151d0e56f8dc6292773603d0d6aabbdd62a11ef721d1542d8', 'me', 'M', '1993-02-01'),
(6, 'jj@gmail.com', '5e884898da28047151d0e56f8dc6292773603d0d6aabbdd62a11ef721d1542d8', 'mejj', 'M', '1993-02-01'),
(7, 'pharmacist@gmail.com', '5e884898da28047151d0e56f8dc6292773603d0d6aabbdd62a11ef721d1542d8', 'fdgfgffg', 'F', '1997-10-25');

-- --------------------------------------------------------

--
-- Table structure for table `vaccines`
--

CREATE TABLE IF NOT EXISTS `vaccines` (
  `vid` int(11) NOT NULL AUTO_INCREMENT,
  `uid` bigint(50) NOT NULL,
  `vaccine` text NOT NULL,
  `date` date NOT NULL,
  `place` text NOT NULL,
  PRIMARY KEY (`vid`),
  KEY `vaccines_uid` (`uid`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=36 ;

--
-- Dumping data for table `vaccines`
--

INSERT INTO `vaccines` (`vid`, `uid`, `vaccine`, `date`, `place`) VALUES
(2, 3, 'vac3', '2015-06-06', 'ahm'),
(27, 2, 'vac1', '2015-06-06', 'vad'),
(33, 3, 'vaccci', '2015-06-10', 'vadodara'),
(34, 6, 'vacccccccccc', '1993-10-08', 'vadd'),
(35, 3, 'adssda', '2015-06-20', 'asdsad');

-- --------------------------------------------------------

--
-- Table structure for table `vitals`
--

CREATE TABLE IF NOT EXISTS `vitals` (
  `uid` bigint(50) NOT NULL,
  `height` float NOT NULL,
  `weight` float NOT NULL,
  `bmi` float NOT NULL,
  `pulse` float NOT NULL,
  `bp` float NOT NULL,
  PRIMARY KEY (`uid`),
  KEY `vitals_uid` (`uid`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `vitals`
--

INSERT INTO `vitals` (`uid`, `height`, `weight`, `bmi`, `pulse`, `bp`) VALUES
(2, 5.3, 55, 75, 70, 70),
(3, 5.3, 55, 70, 70, 70);

--
-- Constraints for dumped tables
--

--
-- Constraints for table `allergies`
--
ALTER TABLE `allergies`
  ADD CONSTRAINT `allergies_ibfk_1` FOREIGN KEY (`uid`) REFERENCES `users` (`uid`) ON UPDATE CASCADE;

--
-- Constraints for table `doctors`
--
ALTER TABLE `doctors`
  ADD CONSTRAINT `doctors_ibfk_1` FOREIGN KEY (`uid`) REFERENCES `users` (`uid`) ON UPDATE CASCADE;

--
-- Constraints for table `pharmacists`
--
ALTER TABLE `pharmacists`
  ADD CONSTRAINT `pharmacists_ibfk_1` FOREIGN KEY (`uid`) REFERENCES `users` (`uid`) ON UPDATE CASCADE;

--
-- Constraints for table `records`
--
ALTER TABLE `records`
  ADD CONSTRAINT `records_ibfk_1` FOREIGN KEY (`uid`) REFERENCES `users` (`uid`) ON UPDATE CASCADE,
  ADD CONSTRAINT `records_ibfk_2` FOREIGN KEY (`did`) REFERENCES `doctors` (`did`) ON UPDATE CASCADE;

--
-- Constraints for table `vaccines`
--
ALTER TABLE `vaccines`
  ADD CONSTRAINT `vaccines_ibfk_1` FOREIGN KEY (`uid`) REFERENCES `users` (`uid`) ON UPDATE CASCADE;

--
-- Constraints for table `vitals`
--
ALTER TABLE `vitals`
  ADD CONSTRAINT `vitals_ibfk_1` FOREIGN KEY (`uid`) REFERENCES `users` (`uid`) ON UPDATE CASCADE;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
