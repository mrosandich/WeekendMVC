-- phpMyAdmin SQL Dump
-- version 4.0.10.8
-- http://www.phpmyadmin.net
--
-- Host: db467850438.db.1and1.com:3306
-- Generation Time: Aug 05, 2015 at 01:50 PM
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
-- Table structure for table `enterprise`
--

CREATE TABLE IF NOT EXISTS `enterprise` (
  `enterprise_id` bigint(20) NOT NULL AUTO_INCREMENT,
  `enterprise_name` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `enterprise_invite_code` varchar(250) COLLATE latin1_general_ci NOT NULL,
  `enterprise_parent` int(11) NOT NULL DEFAULT '0' COMMENT '0=is parent, anything else is the parents id',
  PRIMARY KEY (`enterprise_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci AUTO_INCREMENT=3 ;

--
-- Dumping data for table `enterprise`
--

INSERT INTO `enterprise` (`enterprise_id`, `enterprise_name`, `enterprise_invite_code`, `enterprise_parent`) VALUES
(1, 'example org', 'A510LPZ', 0),
(2, 'ourace.com', 'Az0PlmAc', 0);

-- --------------------------------------------------------

--
-- Table structure for table `roles`
--

CREATE TABLE IF NOT EXISTS `roles` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(32) COLLATE latin1_general_ci DEFAULT NULL,
  `desciption` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci AUTO_INCREMENT=5 ;

--
-- Dumping data for table `roles`
--

INSERT INTO `roles` (`id`, `name`, `desciption`) VALUES
(1, 'login', 'Login privileges, granted after account confirmation'),
(2, 'admin', 'Administrative user, has access to everything.'),
(3, 'user_profile', 'Allows users to edit their profile.'),
(4, 'helloworld', 'Has acces to Helloworld');

-- --------------------------------------------------------

--
-- Table structure for table `roles_users`
--

CREATE TABLE IF NOT EXISTS `roles_users` (
  `role_id` int(11) NOT NULL,
  `user_id` bigint(20) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci;

--
-- Dumping data for table `roles_users`
--

INSERT INTO `roles_users` (`role_id`, `user_id`) VALUES
(1, 2),
(2, 2),
(1, 3),
(3, 2),
(3, 3),
(4, 2);

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
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci AUTO_INCREMENT=4 ;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `username`, `password`, `salt`, `name_first`, `name_last`, `email`, `enterprise_id`, `is_activated`, `activated_by_user_id`, `approval_state`, `failed_logins`, `is_locked`, `locked_message`, `secondary_login_action`, `secondary_code`, `secondary_code_date`, `secondary_fails`, `recovery_q1`, `recovery_q2`, `recovery_q3`, `recover_an1_enc`, `recover_an2_enc`, `recover_an3_enc`, `last_login`, `logins`, `is_ldap`) VALUES
(2, 'admin', '5f4dcc3b5aa765d61d8327deb882cf99', NULL, 'The', 'Admin', 'admin@somefakedomain.abc', 1, 1, 2, 1, 0, 0, 'You account was locked due to too many failed logins', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1421437578, 86, 0),
(3, 'basicuser', '5f4dcc3b5aa765d61d8327deb882cf99', NULL, 'Tester', 'Testing', 'basicuser@somefakedomain.abc', 1, 1, 2, 1, 0, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0);

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
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci;

--
-- Dumping data for table `user_audit_login`
--



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
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci ;

--
-- Dumping data for table `user_audit_login_fail`
--


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
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci AUTO_INCREMENT=3 ;

--
-- Dumping data for table `user_to_enterprise`
--

INSERT INTO `user_to_enterprise` (`ute_id`, `user_id`, `ent_id`, `ute_type`) VALUES
(1, 2, 1, ''),
(2, 2, 2, '');

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
