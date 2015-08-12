-- phpMyAdmin SQL Dump
-- version 4.0.10.8
-- http://www.phpmyadmin.net
--
-- Host: db467850438.db.1and1.com:3306
-- Generation Time: Aug 12, 2015 at 09:37 AM
-- Server version: 5.1.73-log
-- PHP Version: 5.5.26

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Database: `db467850438`
--

-- --------------------------------------------------------

--
-- Table structure for table `address_states`
--

CREATE TABLE IF NOT EXISTS `address_states` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `state` varchar(64) NOT NULL DEFAULT '',
  `abbreviation` char(2) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=52 ;

--
-- Dumping data for table `address_states`
--

INSERT INTO `address_states` (`id`, `state`, `abbreviation`) VALUES
(1, 'Alabama', 'AL'),
(2, 'Alaska', 'AK'),
(3, 'Arizona', 'AZ'),
(4, 'Arkansas', 'AR'),
(5, 'California', 'CA'),
(6, 'Colorado', 'CO'),
(7, 'Connecticut', 'CT'),
(8, 'Delaware', 'DE'),
(9, 'District of Columbia', 'DC'),
(10, 'Florida', 'FL'),
(11, 'Georgia', 'GA'),
(12, 'Hawaii', 'HI'),
(13, 'Idaho', 'ID'),
(14, 'Illinois', 'IL'),
(15, 'Indiana', 'IN'),
(16, 'Iowa', 'IA'),
(17, 'Kansas', 'KS'),
(18, 'Kentucky', 'KY'),
(19, 'Louisiana', 'LA'),
(20, 'Maine', 'ME'),
(21, 'Maryland', 'MD'),
(22, 'Massachusetts', 'MA'),
(23, 'Michigan', 'MI'),
(24, 'Minnesota', 'MN'),
(25, 'Mississippi', 'MS'),
(26, 'Missouri', 'MO'),
(27, 'Montana', 'MT'),
(28, 'Nebraska', 'NE'),
(29, 'Nevada', 'NV'),
(30, 'New Hampshire', 'NH'),
(31, 'New Jersey', 'NJ'),
(32, 'New Mexico', 'NM'),
(33, 'New York', 'NY'),
(34, 'North Carolina', 'NC'),
(35, 'North Dakota', 'ND'),
(36, 'Ohio', 'OH'),
(37, 'Oklahoma', 'OK'),
(38, 'Oregon', 'OR'),
(39, 'Pennsylvania', 'PA'),
(40, 'Rhode Island', 'RI'),
(41, 'South Carolina', 'SC'),
(42, 'South Dakota', 'SD'),
(43, 'Tennessee', 'TN'),
(44, 'Texas', 'TX'),
(45, 'Utah', 'UT'),
(46, 'Vermont', 'VT'),
(47, 'Virginia', 'VA'),
(48, 'Washington', 'WA'),
(49, 'West Virginia', 'WV'),
(50, 'Wisconsin', 'WI'),
(51, 'Wyoming', 'WY');

-- --------------------------------------------------------

--
-- Table structure for table `enterprise`
--

CREATE TABLE IF NOT EXISTS `enterprise` (
  `enterprise_id` bigint(20) NOT NULL AUTO_INCREMENT,
  `enterprise_name` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `enterprise_invite_code` varchar(250) COLLATE latin1_general_ci NOT NULL,
  `enterprise_parent` int(11) NOT NULL DEFAULT '0' COMMENT '0=is parent, anything else is the parents id',
  PRIMARY KEY (`enterprise_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci AUTO_INCREMENT=4 ;

--
-- Dumping data for table `enterprise`
--

INSERT INTO `enterprise` (`enterprise_id`, `enterprise_name`, `enterprise_invite_code`, `enterprise_parent`) VALUES
(1, 'oe.mychc.org', 'A510LPZ', 0),
(2, 'ourace.com', 'Az0PlmAc', 0),
(3, 'Guest at mychc.org', 'guest', 0);

-- --------------------------------------------------------

--
-- Table structure for table `roles`
--

CREATE TABLE IF NOT EXISTS `roles` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(32) COLLATE latin1_general_ci DEFAULT NULL,
  `desciption` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci AUTO_INCREMENT=7 ;

--
-- Dumping data for table `roles`
--

INSERT INTO `roles` (`id`, `name`, `desciption`) VALUES
(1, 'guest', 'General guest roles'),
(2, 'user', 'General user role'),
(3, 'enterprise_manager', 'General Enterprise Manager role'),
(4, 'site_admin', 'General Site administrator role'),
(5, 'user_profile', 'has access to manage own user profile'),
(6, 'helloworld', 'Access to helloworld application');

-- --------------------------------------------------------

--
-- Table structure for table `roles_users`
--

CREATE TABLE IF NOT EXISTS `roles_users` (
  `ru_id` int(11) NOT NULL AUTO_INCREMENT,
  `role_id` int(11) NOT NULL,
  `user_id` bigint(20) NOT NULL,
  PRIMARY KEY (`ru_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci AUTO_INCREMENT=10 ;

--
-- Dumping data for table `roles_users`
--

INSERT INTO `roles_users` (`ru_id`, `role_id`, `user_id`) VALUES
(1, 1, 1),
(2, 2, 1),
(3, 1, 2),
(4, 3, 1),
(5, 2, 2),
(6, 4, 1),
(8, 5, 1),
(9, 6, 1);

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE IF NOT EXISTS `users` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `username` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `password` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `salt` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `name_first` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `name_last` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `email` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `enterprise_id` bigint(20) NOT NULL,
  `is_activated` int(1) DEFAULT '0',
  `activated_by_user_id` bigint(20) DEFAULT NULL,
  `approval_state` int(11) DEFAULT NULL COMMENT '0 - new, not approved\n1 - approved\n2 - denyed\n3 - suspended',
  `failed_logins` int(11) DEFAULT NULL,
  `is_locked` int(1) DEFAULT '0',
  `locked_message` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `secondary_login_action` varchar(255) COLLATE latin1_general_ci DEFAULT NULL COMMENT 'none\nemail:email@address.com\ntel:5125551212\nsms:5125551212\nxmpp:bob@xmpp.com',
  `secondary_code` varchar(45) COLLATE latin1_general_ci DEFAULT NULL COMMENT 'code that uer is required to enter for the second auth',
  `secondary_code_date` datetime DEFAULT NULL COMMENT 'when the secondary code auth was created',
  `secondary_fails` int(11) DEFAULT NULL COMMENT 'count of fails when trying secondary code.',
  `recovery_q1` varchar(45) COLLATE latin1_general_ci DEFAULT NULL COMMENT 'Question for recovery',
  `recovery_q2` varchar(45) COLLATE latin1_general_ci DEFAULT NULL,
  `recovery_q3` varchar(45) COLLATE latin1_general_ci DEFAULT NULL,
  `recover_an1_enc` varchar(45) COLLATE latin1_general_ci DEFAULT NULL COMMENT 'answer to question 1. this is lowered case and  encryted ',
  `recover_an2_enc` varchar(45) COLLATE latin1_general_ci DEFAULT NULL,
  `recover_an3_enc` varchar(45) COLLATE latin1_general_ci DEFAULT NULL,
  `last_login` int(11) DEFAULT NULL,
  `logins` int(11) DEFAULT NULL,
  `is_ldap` int(11) NOT NULL DEFAULT '0',
  `activation_guid` varchar(100) COLLATE latin1_general_ci NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci AUTO_INCREMENT=37 ;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `username`, `password`, `salt`, `name_first`, `name_last`, `email`, `enterprise_id`, `is_activated`, `activated_by_user_id`, `approval_state`, `failed_logins`, `is_locked`, `locked_message`, `secondary_login_action`, `secondary_code`, `secondary_code_date`, `secondary_fails`, `recovery_q1`, `recovery_q2`, `recovery_q3`, `recover_an1_enc`, `recover_an2_enc`, `recover_an3_enc`, `last_login`, `logins`, `is_ldap`, `activation_guid`) VALUES
(1, 'admin', '5f4dcc3b5aa765d61d8327deb882cf99', NULL, 'The', 'Admin', 'admin@somefakedomain.abc', 1, 1, 2, 1, 0, 0, 'You account was locked due to too many failed logins', NULL, NULL, NULL, NULL, 'whats you pets name', 'What city is your favorite', 'What is your favorite color', 'Sammy', 'Paris', 'blue', 1421437578, 93, 0, ''),
(2, 'basicuser', '5f4dcc3b5aa765d61d8327deb882cf99', NULL, 'Tester', 'Testing', 'basicuser@somefakedomain.abc', 1, 1, 2, 1, 0, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, '');

-- --------------------------------------------------------

--
-- Table structure for table `user_audit_login`
--

CREATE TABLE IF NOT EXISTS `user_audit_login` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `user_name` varchar(100) COLLATE latin1_general_ci NOT NULL,
  `date_time` datetime NOT NULL,
  `ip_address` varchar(100) COLLATE latin1_general_ci NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci AUTO_INCREMENT=90 ;

--
-- Dumping data for table `user_audit_login`
--

INSERT INTO `user_audit_login` (`id`, `user_id`, `user_name`, `date_time`, `ip_address`) VALUES
(89, 1, 'admin', '2015-08-12 09:35:04', '50.84.88.230');

-- --------------------------------------------------------

--
-- Table structure for table `user_audit_login_fail`
--

CREATE TABLE IF NOT EXISTS `user_audit_login_fail` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `user_name` varchar(100) COLLATE latin1_general_ci NOT NULL,
  `date_time` datetime NOT NULL,
  `ip_address` varchar(100) COLLATE latin1_general_ci NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci AUTO_INCREMENT=25 ;

-- --------------------------------------------------------

--
-- Table structure for table `user_to_enterprise`
--

CREATE TABLE IF NOT EXISTS `user_to_enterprise` (
  `ute_id` bigint(20) NOT NULL AUTO_INCREMENT,
  `user_id` bigint(20) NOT NULL,
  `ent_id` bigint(20) NOT NULL,
  `ute_type` varchar(10) COLLATE latin1_general_ci NOT NULL,
  PRIMARY KEY (`ute_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci AUTO_INCREMENT=4 ;

--
-- Dumping data for table `user_to_enterprise`
--

INSERT INTO `user_to_enterprise` (`ute_id`, `user_id`, `ent_id`, `ute_type`) VALUES
(1, 1, 1, ''),
(2, 1, 2, '');

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
