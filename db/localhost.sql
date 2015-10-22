-- phpMyAdmin SQL Dump
-- version 4.4.14
-- http://www.phpmyadmin.net
--
-- Host: 127.0.0.1
-- Generation Time: Oct 22, 2015 at 02:08 PM
-- Server version: 5.6.26
-- PHP Version: 5.6.12

SET FOREIGN_KEY_CHECKS=0;
SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `cms`
--
DROP DATABASE IF EXISTS `cms`;
CREATE DATABASE IF NOT EXISTS `cms` DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci;
USE `cms`;

-- --------------------------------------------------------

--
-- Table structure for table `email_log`
--

DROP TABLE IF EXISTS `email_log`;
CREATE TABLE IF NOT EXISTS `email_log` (
  `id` int(11) NOT NULL,
  `sent_date_time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `receipient_id` int(11) NOT NULL
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8;

--
-- Dumping data for table `email_log`
--

INSERT INTO `email_log` (`id`, `sent_date_time`, `receipient_id`) VALUES
(1, '2015-10-09 16:51:47', 2),
(2, '2015-10-09 16:52:01', 1);

-- --------------------------------------------------------

--
-- Table structure for table `incidents`
--

DROP TABLE IF EXISTS `incidents`;
CREATE TABLE IF NOT EXISTS `incidents` (
  `id` int(11) NOT NULL,
  `name` varchar(50) NOT NULL,
  `mobile` int(8) NOT NULL,
  `latitude` double NOT NULL,
  `longitude` double NOT NULL,
  `location` varchar(255) NOT NULL,
  `assistance_type` varchar(10) NOT NULL,
  `reported_on` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `last_updated_on` timestamp NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  `last_updated_user` int(11) DEFAULT NULL,
  `operator` int(11) NOT NULL,
  `status` int(1) NOT NULL DEFAULT '1'
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8;

--
-- Dumping data for table `incidents`
--

INSERT INTO `incidents` (`id`, `name`, `mobile`, `latitude`, `longitude`, `location`, `assistance_type`, `reported_on`, `last_updated_on`, `last_updated_user`, `operator`, `status`) VALUES
(1, 'Samuel Tan', 95475260, 1.314124, 103.768047, '331 Clementi Ave 2, Singapore 120331', '1,3', '2015-10-21 15:24:08', NULL, NULL, 1, 1),
(2, 'Angela', 85600143, 1.324764, 103.899883, 'PIE, Singapore', '1,2', '2015-10-21 15:26:07', '2015-10-22 11:39:37', 2, 1, 1),
(3, 'Lee Kong Nam', 80496372, 1.387232, 103.899427, '227A Compassvale Dr, Singapore 541227', '3', '2015-10-21 15:28:28', NULL, NULL, 1, 1),
(4, 'Lam Xiu Moi', 95630042, 1.295246, 103.78693, '55 Ayer Rajah Crescent, Singapore', '1', '2015-10-21 15:28:54', NULL, NULL, 1, 1),
(5, 'Leonard Sim', 97208843, 1.337292, 103.80832, 'PIE, Singapore', '1,2,3', '2015-10-21 15:29:39', '2015-10-22 11:39:39', 3, 1, 1),
(6, 'Xiu Mei', 85472100, 1.300052, 103.859596, '3 Haji Ln, Singapore 189196', '1,3', '2015-10-21 15:30:25', NULL, NULL, 1, 1),
(7, 'Jessica Tan', 87936425, 1.378651, 103.768959, '112 Pending Rd, Singapore 670112', '1', '2015-10-21 15:31:22', NULL, NULL, 1, 1),
(8, 'Jia Xin', 90329644, 1.434939, 103.791393, '516 Woodlands Drive 14, Singapore 730516', '1,2', '2015-10-21 16:56:13', '2015-10-22 11:39:08', 2, 2, 1);

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

DROP TABLE IF EXISTS `users`;
CREATE TABLE IF NOT EXISTS `users` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `email` varchar(200) NOT NULL,
  `password` varchar(100) NOT NULL,
  `user_type` int(11) NOT NULL
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `name`, `email`, `password`, `user_type`) VALUES
(1, 'Administrator', 'admin@cms', '827ccb0eea8a706c4c34a16891f84e7b', 1),
(2, 'David Wang', 'ops@ops.com', '827ccb0eea8a706c4c34a16891f84e7b', 2),
(3, 'Jasmine Poh', 'gov@gov.sg', '827ccb0eea8a706c4c34a16891f84e7b', 3);

-- --------------------------------------------------------

--
-- Table structure for table `users_type`
--

DROP TABLE IF EXISTS `users_type`;
CREATE TABLE IF NOT EXISTS `users_type` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8;

--
-- Dumping data for table `users_type`
--

INSERT INTO `users_type` (`id`, `name`) VALUES
(1, 'Administrator'),
(2, 'Call Center Operator'),
(3, 'Government Agency');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `email_log`
--
ALTER TABLE `email_log`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `receipient_id` (`receipient_id`);

--
-- Indexes for table `incidents`
--
ALTER TABLE `incidents`
  ADD PRIMARY KEY (`id`),
  ADD KEY `operator` (`operator`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_type` (`user_type`);

--
-- Indexes for table `users_type`
--
ALTER TABLE `users_type`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `email_log`
--
ALTER TABLE `email_log`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=3;
--
-- AUTO_INCREMENT for table `incidents`
--
ALTER TABLE `incidents`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=9;
--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=4;
--
-- AUTO_INCREMENT for table `users_type`
--
ALTER TABLE `users_type`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=4;
--
-- Constraints for dumped tables
--

--
-- Constraints for table `email_log`
--
ALTER TABLE `email_log`
  ADD CONSTRAINT `email_log_ibfk_1` FOREIGN KEY (`receipient_id`) REFERENCES `users` (`id`);

--
-- Constraints for table `incidents`
--
ALTER TABLE `incidents`
  ADD CONSTRAINT `incidents_ibfk_1` FOREIGN KEY (`operator`) REFERENCES `users` (`id`);

--
-- Constraints for table `users`
--
ALTER TABLE `users`
  ADD CONSTRAINT `users_ibfk_1` FOREIGN KEY (`user_type`) REFERENCES `users_type` (`id`);
SET FOREIGN_KEY_CHECKS=1;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
