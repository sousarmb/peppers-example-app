CREATE DATABASE `peppers`;
USE `peppers`;

CREATE TABLE `peppers` (
  `pk` int unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(64) NOT NULL,
  `email` varchar(64) NOT NULL,
  `created_on` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_on` timestamp NULL DEFAULT NULL,
  `deleted_on` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`pk`),
  UNIQUE KEY `name-email` (`name`,`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT INTO `peppers` (`name`, `email`) VALUES 
('black pepper','black.peppers@peppers.em'), 
('red pepper', 'red.pepper@peppers.em'), 
('yellow pepper','yellow.pepper@peppers.im'),
('orange pepper','orange.pepper@peppers.im');