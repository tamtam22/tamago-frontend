SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

CREATE DATABASE IF NOT EXISTS `cms` DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci;
USE `cms`;

DROP TABLE IF EXISTS `email_log`;
CREATE TABLE IF NOT EXISTS `email_log` (
  `id` int(11) NOT NULL,
  `sent_date_time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `receipient_id` int(11) NOT NULL
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8;

INSERT INTO `email_log` (`id`, `sent_date_time`, `receipient_id`) VALUES
(1, '2015-10-09 16:51:47', 2),
(2, '2015-10-09 16:52:01', 1);

DROP TABLE IF EXISTS `incidents`;
CREATE TABLE IF NOT EXISTS `incidents` (
  `id` int(11) NOT NULL,
  `name` varchar(50) NOT NULL,
  `mobile` int(8) NOT NULL,
  `latitude` double NOT NULL,
  `longitude` double NOT NULL,
  `assistance_type` varchar(10) NOT NULL,
  `reported_on` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `last_updated_on` timestamp NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  `operator` int(11) NOT NULL,
  `status` int(1) NOT NULL
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;

INSERT INTO `incidents` (`id`, `name`, `mobile`, `latitude`, `longitude`, `assistance_type`, `reported_on`, `last_updated_on`, `operator`, `status`) VALUES
(1, 'Tan Ah Meng', 99991111, 1.3340635073290337, 103.83099398950196, '1', '2015-10-07 09:54:55', '2015-10-09 22:57:28', 2, 1);

DROP TABLE IF EXISTS `users`;
CREATE TABLE IF NOT EXISTS `users` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `email` varchar(200) NOT NULL,
  `password` varchar(100) NOT NULL,
  `user_type` int(11) NOT NULL
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8;

INSERT INTO `users` (`id`, `name`, `email`, `password`, `user_type`) VALUES
(1, 'Administrator', 'admin@admin.com', '827ccb0eea8a706c4c34a16891f84e7b', 1),
(2, 'Mary Chia', 'marry@ops.com', '827ccb0eea8a706c4c34a16891f84e7b', 2);

DROP TABLE IF EXISTS `users_type`;
CREATE TABLE IF NOT EXISTS `users_type` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8;

INSERT INTO `users_type` (`id`, `name`) VALUES
(1, 'Administrator'),
(2, 'Call Center Operator'),
(3, 'Government Agencies');


ALTER TABLE `email_log`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `receipient_id` (`receipient_id`);

ALTER TABLE `incidents`
  ADD PRIMARY KEY (`id`),
  ADD KEY `operator` (`operator`);

ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_type` (`user_type`);

ALTER TABLE `users_type`
  ADD PRIMARY KEY (`id`);


ALTER TABLE `email_log`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=3;
ALTER TABLE `incidents`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=2;
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=3;
ALTER TABLE `users_type`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=4;

ALTER TABLE `email_log`
  ADD CONSTRAINT `email_log_ibfk_1` FOREIGN KEY (`receipient_id`) REFERENCES `users` (`id`);

ALTER TABLE `incidents`
  ADD CONSTRAINT `incidents_ibfk_1` FOREIGN KEY (`operator`) REFERENCES `users` (`id`);

ALTER TABLE `users`
  ADD CONSTRAINT `users_ibfk_1` FOREIGN KEY (`user_type`) REFERENCES `users_type` (`id`);

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
