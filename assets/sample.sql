-- phpMyAdmin SQL Dump
-- version 4.0.7
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: Oct 11, 2013 at 07:00 PM
-- Server version: 5.5.32-MariaDB-log
-- PHP Version: 5.5.4

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Database: `DyACL`
--

--
-- Dumping data for table `resources`
--

INSERT INTO `resources` (`id`, `name`) VALUES
(1, 'public'),
(2, 'secret'),
(3, 'user_can_only_view'),
(4, 'user_can_not_delete');

--
-- Dumping data for table `roles`
--

INSERT INTO `roles` (`id`, `name`, `active`) VALUES
(1, 'admin', b'1'),
(2, 'user', b'1');

--
-- Dumping data for table `roles_resources`
--

INSERT INTO `roles_resources` (`id`, `role_id`, `resource`, `action`, `privilege`) VALUES
(1, 2, 'public', 'all', 'allow'),
(2, 2, 'secret', 'all', 'deny'),
(3, 2, 'user_can_only_read', 'read', 'allow'),
(4, 2, 'user_can_not_delete', 'read', 'allow'),
(5, 2, 'user_can_not_delete', 'create', 'allow'),
(7, 1, 'secret', 'all', 'allow'),
(6, 2, 'user_can_not_delete', 'update', 'allow');

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `username`, `password`, `email`, `active`) VALUES
(1, 'user', 'e10adc3949ba59abbe56e057f20f883e', 'test@example.com', 1),
(2, 'admin', 'e10adc3949ba59abbe56e057f20f883e', 'test2@example.com', 1);

--
-- Dumping data for table `users_roles`
--

INSERT INTO `users_roles` (`user_id`, `role_id`) VALUES
(1, 2),
(2, 1),
(2, 2);

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
