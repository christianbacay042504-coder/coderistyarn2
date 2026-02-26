-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Feb 26, 2026 at 05:44 PM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `sjdm_tours`
--

-- --------------------------------------------------------

--
-- Table structure for table `admin_activity`
--

CREATE TABLE `admin_activity` (
  `id` int(11) NOT NULL,
  `admin_id` int(11) NOT NULL,
  `action` varchar(100) NOT NULL,
  `module` varchar(50) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `user_agent` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `admin_activity`
--

INSERT INTO `admin_activity` (`id`, `admin_id`, `action`, `module`, `description`, `ip_address`, `user_agent`, `created_at`) VALUES
(1, 1, 'LOGIN', 'auth', 'Super admin logged in', '192.168.1.1', NULL, '2026-02-05 00:00:00'),
(2, 1, 'ACCESS', 'dashboard', 'Admin accessed dashboard', '192.168.1.1', NULL, '2026-02-05 00:05:00'),
(7, 1, 'LOGOUT', 'auth', 'Super admin logged out', '192.168.1.1', NULL, '2026-02-05 09:00:00'),
(13, 1, 'LOGOUT', 'auth', 'Super admin logged out', '192.168.1.1', NULL, '2026-02-05 10:00:00'),
(15, 1, 'ACCESS', 'dashboard', 'Admin accessed dashboard', '::1', NULL, '2026-02-06 08:24:49'),
(16, 1, 'ACCESS', 'dashboard', 'Admin accessed dashboard', '::1', NULL, '2026-02-06 09:38:48'),
(17, 1, 'ACCESS', 'dashboard', 'Admin accessed dashboard', '::1', NULL, '2026-02-06 12:00:24'),
(18, 1, 'ACCESS', 'dashboard', 'Admin accessed dashboard', '::1', NULL, '2026-02-06 12:00:42'),
(19, 1, 'ACCESS', 'dashboard', 'Admin accessed dashboard', '::1', NULL, '2026-02-06 12:00:47'),
(20, 1, 'ACCESS', 'dashboard', 'Admin accessed dashboard', '::1', NULL, '2026-02-06 12:04:31'),
(21, 1, 'ACCESS', 'dashboard', 'Admin accessed dashboard', '::1', NULL, '2026-02-06 12:05:49'),
(22, 1, 'ACCESS', 'dashboard', 'Admin accessed dashboard', '::1', NULL, '2026-02-06 12:05:58'),
(23, 1, 'ACCESS', 'dashboard', 'Admin accessed dashboard', '::1', NULL, '2026-02-06 12:05:58'),
(24, 1, 'ACCESS', 'dashboard', 'Admin accessed dashboard', '::1', NULL, '2026-02-06 12:05:59'),
(25, 1, 'ACCESS', 'dashboard', 'Admin accessed dashboard', '::1', NULL, '2026-02-08 21:28:28'),
(26, 1, 'ACCESS', 'dashboard', 'Admin accessed dashboard', '::1', NULL, '2026-02-08 21:36:11'),
(27, 1, 'ACCESS', 'dashboard', 'Admin accessed dashboard', '::1', NULL, '2026-02-08 22:39:58'),
(28, 1, 'ACCESS', 'dashboard', 'Admin accessed dashboard', '::1', NULL, '2026-02-08 22:48:46'),
(29, 1, 'ACCESS', 'dashboard', 'Admin accessed dashboard', '::1', NULL, '2026-02-08 22:49:17'),
(30, 1, 'ACCESS', 'dashboard', 'Admin accessed dashboard', '::1', NULL, '2026-02-08 22:50:01'),
(31, 1, 'ACCESS', 'dashboard', 'Admin accessed dashboard', '::1', NULL, '2026-02-08 22:50:07'),
(32, 1, 'ACCESS', 'dashboard', 'Admin accessed dashboard', '::1', NULL, '2026-02-08 22:51:02'),
(33, 1, 'ACCESS', 'dashboard', 'Admin accessed dashboard', '::1', NULL, '2026-02-10 07:49:39'),
(34, 1, 'ACCESS', 'dashboard', 'Admin accessed dashboard', '::1', NULL, '2026-02-10 07:52:10'),
(35, 1, 'ACCESS', 'dashboard', 'Admin accessed dashboard', '::1', NULL, '2026-02-10 08:10:02'),
(36, 1, 'ACCESS', 'dashboard', 'Admin accessed dashboard', '::1', NULL, '2026-02-10 08:12:19'),
(37, 1, 'ACCESS', 'dashboard', 'Admin accessed dashboard', '::1', NULL, '2026-02-10 08:21:11'),
(38, 1, 'ACCESS', 'dashboard', 'Admin accessed dashboard', '::1', NULL, '2026-02-10 12:47:41'),
(39, 1, 'ACCESS', 'dashboard', 'Admin accessed dashboard', '::1', NULL, '2026-02-10 22:24:20'),
(40, 1, 'ACCESS', 'dashboard', 'Admin accessed dashboard', '::1', NULL, '2026-02-10 22:25:59'),
(41, 1, 'ACCESS', 'dashboard', 'Admin accessed dashboard', '::1', NULL, '2026-02-10 22:41:32'),
(42, 1, 'ACCESS', 'dashboard', 'Admin accessed dashboard', '::1', NULL, '2026-02-10 22:43:47'),
(43, 1, 'ACCESS', 'dashboard', 'Admin accessed dashboard', '::1', NULL, '2026-02-11 00:40:19'),
(44, 1, 'ACCESS', 'dashboard', 'Admin accessed dashboard', '::1', NULL, '2026-02-11 00:48:37'),
(45, 1, 'ACCESS', 'dashboard', 'Admin accessed dashboard', '::1', NULL, '2026-02-11 00:49:44'),
(46, 1, 'ACCESS', 'dashboard', 'Admin accessed dashboard', '::1', NULL, '2026-02-11 00:55:57'),
(47, 1, 'ACCESS', 'dashboard', 'Admin accessed dashboard', '::1', NULL, '2026-02-11 01:03:37'),
(48, 1, 'ACCESS', 'dashboard', 'Admin accessed dashboard', '::1', NULL, '2026-02-11 01:04:52'),
(49, 1, 'ACCESS', 'dashboard', 'Admin accessed dashboard', '::1', NULL, '2026-02-11 01:17:13'),
(50, 1, 'ACCESS', 'dashboard', 'Admin accessed dashboard', '::1', NULL, '2026-02-11 01:21:49'),
(51, 1, 'ACCESS', 'dashboard', 'Admin accessed dashboard', '::1', NULL, '2026-02-11 01:29:44'),
(52, 1, 'ACCESS', 'dashboard', 'Admin accessed dashboard', '::1', NULL, '2026-02-11 01:40:04'),
(53, 1, 'ACCESS', 'dashboard', 'Admin accessed dashboard', '::1', NULL, '2026-02-11 01:45:11'),
(54, 1, 'ACCESS', 'dashboard', 'Admin accessed dashboard', '::1', NULL, '2026-02-11 01:45:14'),
(55, 1, 'ACCESS', 'dashboard', 'Admin accessed dashboard', '::1', NULL, '2026-02-11 01:45:33'),
(56, 1, 'ACCESS', 'dashboard', 'Admin accessed dashboard', '::1', NULL, '2026-02-11 02:01:23'),
(57, 1, 'ACCESS', 'dashboard', 'Admin accessed dashboard', '::1', NULL, '2026-02-11 02:19:59'),
(58, 1, 'ACCESS', 'dashboard', 'Admin accessed dashboard', '::1', NULL, '2026-02-11 02:39:32'),
(59, 1, 'ACCESS', 'dashboard', 'Admin accessed dashboard', '::1', NULL, '2026-02-11 02:54:12'),
(60, 1, 'ACCESS', 'dashboard', 'Admin accessed dashboard', '::1', NULL, '2026-02-11 02:55:21'),
(61, 1, 'ACCESS', 'dashboard', 'Admin accessed dashboard', '::1', NULL, '2026-02-11 03:05:28'),
(62, 1, 'ACCESS', 'dashboard', 'Admin accessed dashboard', '::1', NULL, '2026-02-11 03:06:03'),
(63, 1, 'ACCESS', 'dashboard', 'Admin accessed dashboard', '::1', NULL, '2026-02-11 03:21:12'),
(64, 1, 'ACCESS', 'dashboard', 'Admin accessed dashboard', '::1', NULL, '2026-02-15 14:28:05'),
(65, 1, 'ACCESS', 'dashboard', 'Admin accessed dashboard', '::1', NULL, '2026-02-15 15:49:53'),
(66, 1, 'ACCESS', 'dashboard', 'Admin accessed dashboard', '::1', NULL, '2026-02-15 15:50:05'),
(67, 1, 'ACCESS', 'dashboard', 'Admin accessed dashboard', '::1', NULL, '2026-02-15 15:55:33'),
(68, 1, 'ACCESS', 'dashboard', 'Admin accessed dashboard', '::1', NULL, '2026-02-15 18:54:19'),
(69, 1, 'ACCESS', 'dashboard', 'Admin accessed dashboard', '::1', NULL, '2026-02-15 23:44:24'),
(70, 1, 'ACCESS', 'dashboard', 'Admin accessed dashboard', '::1', NULL, '2026-02-15 23:44:28'),
(71, 1, 'ACCESS', 'dashboard', 'Admin accessed dashboard', '::1', NULL, '2026-02-15 23:44:34'),
(72, 1, 'ACCESS', 'dashboard', 'Admin accessed dashboard', '::1', NULL, '2026-02-15 23:45:41'),
(73, 1, 'ACCESS', 'dashboard', 'Admin accessed dashboard', '::1', NULL, '2026-02-16 04:56:31'),
(74, 1, 'ACCESS', 'dashboard', 'Admin accessed dashboard', '::1', NULL, '2026-02-16 05:08:34'),
(75, 1, 'ACCESS', 'dashboard', 'Admin accessed dashboard', '::1', NULL, '2026-02-16 05:10:02'),
(76, 1, 'ACCESS', 'dashboard', 'Admin accessed dashboard', '::1', NULL, '2026-02-16 05:10:02'),
(77, 1, 'ACCESS', 'dashboard', 'Admin accessed dashboard', '::1', NULL, '2026-02-16 05:10:02'),
(78, 1, 'ACCESS', 'dashboard', 'Admin accessed dashboard', '::1', NULL, '2026-02-16 05:10:02'),
(79, 1, 'ACCESS', 'dashboard', 'Admin accessed dashboard', '::1', NULL, '2026-02-16 05:10:03'),
(80, 1, 'ACCESS', 'dashboard', 'Admin accessed dashboard', '::1', NULL, '2026-02-16 05:10:03'),
(81, 1, 'ACCESS', 'dashboard', 'Admin accessed dashboard', '::1', NULL, '2026-02-16 05:13:52'),
(82, 1, 'ACCESS', 'dashboard', 'Admin accessed dashboard', '::1', NULL, '2026-02-16 07:03:59'),
(83, 1, 'ACCESS', 'dashboard', 'Admin accessed dashboard', '::1', NULL, '2026-02-16 07:05:22'),
(84, 1, 'ACCESS', 'dashboard', 'Admin accessed dashboard', '::1', NULL, '2026-02-16 07:44:50'),
(85, 1, 'ACCESS', 'dashboard', 'Admin accessed dashboard', '::1', NULL, '2026-02-16 08:12:35'),
(86, 1, 'ACCESS', 'dashboard', 'Admin accessed dashboard', '::1', NULL, '2026-02-16 08:12:52'),
(87, 1, 'ACCESS', 'dashboard', 'Admin accessed dashboard', '::1', NULL, '2026-02-16 08:19:54'),
(88, 1, 'ACCESS', 'dashboard', 'Admin accessed dashboard', '::1', NULL, '2026-02-16 08:21:54'),
(89, 1, 'ACCESS', 'dashboard', 'Admin accessed dashboard', '::1', NULL, '2026-02-16 08:24:56'),
(90, 1, 'ACCESS', 'dashboard', 'Admin accessed dashboard', '::1', NULL, '2026-02-19 08:58:15'),
(91, 1, 'ACCESS', 'dashboard', 'Admin accessed dashboard', '::1', NULL, '2026-02-19 09:24:29'),
(92, 1, 'ACCESS', 'dashboard', 'Admin accessed dashboard', '::1', NULL, '2026-02-19 09:31:26'),
(93, 1, 'ACCESS', 'dashboard', 'Admin accessed dashboard', '::1', NULL, '2026-02-19 09:42:38'),
(94, 1, 'ACCESS', 'dashboard', 'Admin accessed dashboard', '::1', NULL, '2026-02-19 09:46:12'),
(95, 1, 'ACCESS', 'dashboard', 'Admin accessed dashboard', '::1', NULL, '2026-02-19 09:57:34'),
(96, 1, 'ACCESS', 'dashboard', 'Admin accessed dashboard', '::1', NULL, '2026-02-19 09:57:47'),
(97, 1, 'ACCESS', 'dashboard', 'Admin accessed dashboard', '::1', NULL, '2026-02-19 10:03:38'),
(98, 1, 'ACCESS', 'dashboard', 'Admin accessed dashboard', '::1', NULL, '2026-02-19 10:05:10'),
(99, 1, 'ACCESS', 'dashboard', 'Admin accessed dashboard', '::1', NULL, '2026-02-19 10:08:07'),
(100, 1, 'ACCESS', 'dashboard', 'Admin accessed dashboard', '::1', NULL, '2026-02-19 11:54:58'),
(101, 1, 'ACCESS', 'dashboard', 'Admin accessed dashboard', '::1', NULL, '2026-02-19 11:55:20'),
(102, 1, 'ACCESS', 'dashboard', 'Admin accessed dashboard', '::1', NULL, '2026-02-19 12:33:43'),
(103, 1, 'ACCESS', 'dashboard', 'Admin accessed dashboard', '::1', NULL, '2026-02-19 13:10:43'),
(104, 1, 'ACCESS', 'dashboard', 'Admin accessed dashboard', '::1', NULL, '2026-02-19 13:14:25'),
(105, 1, 'ACCESS', 'dashboard', 'Admin accessed dashboard', '::1', NULL, '2026-02-19 13:18:20'),
(106, 1, 'ACCESS', 'dashboard', 'Admin accessed dashboard', '::1', NULL, '2026-02-19 13:30:10'),
(107, 1, 'ACCESS', 'dashboard', 'Admin accessed dashboard', '::1', NULL, '2026-02-23 19:37:02'),
(108, 1, 'ACCESS', 'dashboard', 'Admin accessed dashboard', '::1', NULL, '2026-02-23 19:37:14'),
(109, 1, 'ACCESS', 'dashboard', 'Admin accessed dashboard', '::1', NULL, '2026-02-23 19:37:22'),
(110, 1, 'ACCESS', 'dashboard', 'Admin accessed dashboard', '::1', NULL, '2026-02-23 20:17:08'),
(111, 1, 'ACCESS', 'dashboard', 'Admin accessed dashboard', '::1', NULL, '2026-02-23 20:18:36'),
(112, 1, 'ACCESS', 'dashboard', 'Admin accessed dashboard', '::1', NULL, '2026-02-23 20:21:28'),
(113, 1, 'ACCESS', 'dashboard', 'Admin accessed dashboard', '::1', NULL, '2026-02-23 20:25:08'),
(114, 1, 'ACCESS', 'dashboard', 'Admin accessed dashboard', '::1', NULL, '2026-02-23 20:26:04'),
(115, 1, 'ACCESS', 'dashboard', 'Admin accessed dashboard', '::1', NULL, '2026-02-23 20:30:35'),
(116, 1, 'ACCESS', 'dashboard', 'Admin accessed dashboard', '::1', NULL, '2026-02-23 20:30:36'),
(117, 1, 'ACCESS', 'dashboard', 'Admin accessed dashboard', '::1', NULL, '2026-02-23 20:30:36'),
(118, 1, 'ACCESS', 'dashboard', 'Admin accessed dashboard', '::1', NULL, '2026-02-23 20:30:36'),
(119, 1, 'ACCESS', 'dashboard', 'Admin accessed dashboard', '::1', NULL, '2026-02-23 20:30:36'),
(120, 1, 'ACCESS', 'dashboard', 'Admin accessed dashboard', '::1', NULL, '2026-02-23 20:30:37'),
(121, 1, 'ACCESS', 'dashboard', 'Admin accessed dashboard', '::1', NULL, '2026-02-23 20:31:05'),
(122, 1, 'ACCESS', 'dashboard', 'Admin accessed dashboard', '::1', NULL, '2026-02-23 20:39:49'),
(123, 1, 'ACCESS', 'dashboard', 'Admin accessed dashboard', '::1', NULL, '2026-02-23 20:39:59'),
(124, 1, 'ACCESS', 'dashboard', 'Admin accessed dashboard', '::1', NULL, '2026-02-23 20:40:12'),
(125, 1, 'ACCESS', 'dashboard', 'Admin accessed dashboard', '::1', NULL, '2026-02-23 20:40:36'),
(126, 1, 'ACCESS', 'dashboard', 'Admin accessed dashboard', '::1', NULL, '2026-02-23 20:40:50'),
(127, 1, 'ACCESS', 'dashboard', 'Admin accessed dashboard', '::1', NULL, '2026-02-23 20:45:37'),
(128, 1, 'ACCESS', 'dashboard', 'Admin accessed dashboard', '::1', NULL, '2026-02-23 20:45:53'),
(129, 1, 'ACCESS', 'dashboard', 'Admin accessed dashboard', '::1', NULL, '2026-02-23 20:46:05'),
(130, 1, 'ACCESS', 'dashboard', 'Admin accessed dashboard', '::1', NULL, '2026-02-23 20:52:30'),
(131, 1, 'ACCESS', 'dashboard', 'Admin accessed dashboard', '::1', NULL, '2026-02-23 20:52:46'),
(132, 1, 'ACCESS', 'dashboard', 'Admin accessed dashboard', '::1', NULL, '2026-02-23 20:53:01'),
(133, 1, 'ACCESS', 'dashboard', 'Admin accessed dashboard', '::1', NULL, '2026-02-23 20:59:37'),
(134, 1, 'ACCESS', 'dashboard', 'Admin accessed dashboard', '::1', NULL, '2026-02-23 20:59:51'),
(135, 1, 'ACCESS', 'dashboard', 'Admin accessed dashboard', '::1', NULL, '2026-02-23 21:00:13'),
(136, 1, 'ACCESS', 'dashboard', 'Admin accessed dashboard', '::1', NULL, '2026-02-23 21:01:13'),
(137, 1, 'ACCESS', 'dashboard', 'Admin accessed dashboard', '::1', NULL, '2026-02-23 21:01:24'),
(138, 1, 'ACCESS', 'dashboard', 'Admin accessed dashboard', '::1', NULL, '2026-02-23 21:02:48'),
(139, 1, 'ACCESS', 'dashboard', 'Admin accessed dashboard', '::1', NULL, '2026-02-23 21:03:12'),
(140, 1, 'ACCESS', 'dashboard', 'Admin accessed dashboard', '::1', NULL, '2026-02-23 21:04:35'),
(141, 1, 'ACCESS', 'dashboard', 'Admin accessed dashboard', '::1', NULL, '2026-02-23 21:04:46'),
(142, 1, 'ACCESS', 'dashboard', 'Admin accessed dashboard', '::1', NULL, '2026-02-23 21:08:21'),
(143, 1, 'ACCESS', 'dashboard', 'Admin accessed dashboard', '::1', NULL, '2026-02-23 21:08:30'),
(144, 1, 'ACCESS', 'dashboard', 'Admin accessed dashboard', '::1', NULL, '2026-02-23 21:13:11'),
(145, 1, 'ACCESS', 'dashboard', 'Admin accessed dashboard', '::1', NULL, '2026-02-23 21:13:28'),
(146, 1, 'ACCESS', 'dashboard', 'Admin accessed dashboard', '::1', NULL, '2026-02-23 21:18:42'),
(147, 1, 'ACCESS', 'dashboard', 'Admin accessed dashboard', '::1', NULL, '2026-02-23 21:22:01'),
(148, 1, 'ACCESS', 'dashboard', 'Admin accessed dashboard', '::1', NULL, '2026-02-23 21:22:02'),
(149, 1, 'ACCESS', 'dashboard', 'Admin accessed dashboard', '::1', NULL, '2026-02-23 21:22:02'),
(150, 1, 'ACCESS', 'dashboard', 'Admin accessed dashboard', '::1', NULL, '2026-02-23 21:22:02'),
(151, 1, 'ACCESS', 'dashboard', 'Admin accessed dashboard', '::1', NULL, '2026-02-23 21:22:13'),
(152, 1, 'ACCESS', 'dashboard', 'Admin accessed dashboard', '::1', NULL, '2026-02-23 21:32:43'),
(153, 1, 'ACCESS', 'dashboard', 'Admin accessed dashboard', '::1', NULL, '2026-02-24 01:36:57'),
(154, 1, 'ACCESS', 'dashboard', 'Admin accessed dashboard', '::1', NULL, '2026-02-24 02:41:45'),
(155, 1, 'ACCESS', 'dashboard', 'Admin accessed dashboard', '::1', NULL, '2026-02-24 04:13:47'),
(156, 1, 'ACCESS', 'dashboard', 'Admin accessed dashboard', '::1', NULL, '2026-02-25 02:22:09'),
(157, 1, 'ACCESS', 'dashboard', 'Admin accessed dashboard', '::1', NULL, '2026-02-25 02:32:14'),
(158, 1, 'ACCESS', 'dashboard', 'Admin accessed dashboard', '::1', NULL, '2026-02-25 02:32:18'),
(159, 1, 'ACCESS', 'dashboard', 'Admin accessed dashboard', '::1', NULL, '2026-02-25 02:32:22'),
(160, 1, 'ACCESS', 'dashboard', 'Admin accessed dashboard', '::1', NULL, '2026-02-25 02:34:18'),
(161, 1, 'ACCESS', 'dashboard', 'Admin accessed dashboard', '::1', NULL, '2026-02-25 02:56:50'),
(162, 1, 'ACCESS', 'dashboard', 'Admin accessed dashboard', '::1', NULL, '2026-02-25 03:01:10'),
(163, 1, 'ACCESS', 'dashboard', 'Admin accessed dashboard', '::1', NULL, '2026-02-25 12:11:18'),
(164, 1, 'ACCESS', 'dashboard', 'Admin accessed dashboard', '::1', NULL, '2026-02-25 14:26:58'),
(165, 1, 'ACCESS', 'dashboard', 'Admin accessed dashboard', '::1', NULL, '2026-02-25 14:27:09'),
(166, 1, 'ACCESS', 'dashboard', 'Admin accessed dashboard', '::1', NULL, '2026-02-25 14:28:36'),
(167, 1, 'ACCESS', 'dashboard', 'Admin accessed dashboard', '::1', NULL, '2026-02-25 14:28:37'),
(168, 1, 'ACCESS', 'dashboard', 'Admin accessed dashboard', '::1', NULL, '2026-02-25 14:29:56'),
(169, 1, 'ACCESS', 'dashboard', 'Admin accessed dashboard', '::1', NULL, '2026-02-25 14:30:27'),
(170, 1, 'ACCESS', 'dashboard', 'Admin accessed dashboard', '::1', NULL, '2026-02-25 14:31:54'),
(171, 1, 'ACCESS', 'dashboard', 'Admin accessed dashboard', '::1', NULL, '2026-02-25 14:32:00'),
(172, 1, 'ACCESS', 'dashboard', 'Admin accessed dashboard', '::1', NULL, '2026-02-25 14:32:59'),
(173, 1, 'ACCESS', 'dashboard', 'Admin accessed dashboard', '::1', NULL, '2026-02-25 14:33:08'),
(174, 1, 'ACCESS', 'dashboard', 'Admin accessed dashboard', '::1', NULL, '2026-02-25 14:37:58'),
(175, 1, 'ACCESS', 'dashboard', 'Admin accessed dashboard', '::1', NULL, '2026-02-25 14:38:03'),
(176, 1, 'ACCESS', 'dashboard', 'Admin accessed dashboard', '::1', NULL, '2026-02-25 14:39:37'),
(177, 1, 'ACCESS', 'dashboard', 'Admin accessed dashboard', '::1', NULL, '2026-02-25 14:47:09'),
(178, 1, 'ACCESS', 'dashboard', 'Admin accessed dashboard', '::1', NULL, '2026-02-25 14:47:15'),
(179, 1, 'ACCESS', 'dashboard', 'Admin accessed dashboard', '::1', NULL, '2026-02-25 14:47:30'),
(180, 1, 'ACCESS', 'dashboard', 'Admin accessed dashboard', '::1', NULL, '2026-02-25 14:52:08'),
(181, 1, 'ACCESS', 'dashboard', 'Admin accessed dashboard', '::1', NULL, '2026-02-25 14:52:24'),
(182, 1, 'ACCESS', 'dashboard', 'Admin accessed dashboard', '::1', NULL, '2026-02-25 14:52:28'),
(183, 1, 'ACCESS', 'dashboard', 'Admin accessed dashboard', '::1', NULL, '2026-02-25 14:52:52'),
(184, 1, 'ACCESS', 'dashboard', 'Admin accessed dashboard', '::1', NULL, '2026-02-25 14:53:23'),
(185, 1, 'ACCESS', 'dashboard', 'Admin accessed dashboard', '::1', NULL, '2026-02-25 15:05:55'),
(186, 1, 'ACCESS', 'dashboard', 'Admin accessed dashboard', '::1', NULL, '2026-02-25 15:06:29'),
(187, 1, 'ACCESS', 'dashboard', 'Admin accessed dashboard', '::1', NULL, '2026-02-25 15:06:41'),
(188, 1, 'ACCESS', 'dashboard', 'Admin accessed dashboard', '::1', NULL, '2026-02-25 15:06:43'),
(189, 1, 'ACCESS', 'dashboard', 'Admin accessed dashboard', '::1', NULL, '2026-02-25 15:06:43'),
(190, 1, 'ACCESS', 'dashboard', 'Admin accessed dashboard', '::1', NULL, '2026-02-25 15:19:48'),
(191, 1, 'ACCESS', 'dashboard', 'Admin accessed dashboard', '::1', NULL, '2026-02-25 15:22:53'),
(192, 1, 'ACCESS', 'dashboard', 'Admin accessed dashboard', '::1', NULL, '2026-02-25 15:25:09'),
(193, 1, 'ACCESS', 'dashboard', 'Admin accessed dashboard', '::1', NULL, '2026-02-25 15:38:56'),
(194, 1, 'ACCESS', 'dashboard', 'Admin accessed dashboard', '::1', NULL, '2026-02-25 15:39:29'),
(195, 1, 'ACCESS', 'dashboard', 'Admin accessed dashboard', '::1', NULL, '2026-02-25 15:42:52'),
(196, 1, 'ACCESS', 'dashboard', 'Admin accessed dashboard', '::1', NULL, '2026-02-25 15:42:59'),
(197, 1, 'ACCESS', 'dashboard', 'Admin accessed dashboard', '::1', NULL, '2026-02-25 15:44:35'),
(198, 1, 'ACCESS', 'dashboard', 'Admin accessed dashboard', '::1', NULL, '2026-02-25 15:44:57'),
(199, 1, 'ACCESS', 'dashboard', 'Admin accessed dashboard', '::1', NULL, '2026-02-25 15:46:50'),
(200, 1, 'ACCESS', 'dashboard', 'Admin accessed dashboard', '::1', NULL, '2026-02-25 15:52:20'),
(201, 1, 'ACCESS', 'dashboard', 'Admin accessed dashboard', '::1', NULL, '2026-02-25 15:54:57'),
(202, 1, 'ACCESS', 'dashboard', 'Admin accessed dashboard', '::1', NULL, '2026-02-25 15:55:25'),
(203, 1, 'ACCESS', 'dashboard', 'Admin accessed dashboard', '::1', NULL, '2026-02-25 15:55:28'),
(204, 1, 'ACCESS', 'dashboard', 'Admin accessed dashboard', '::1', NULL, '2026-02-25 15:59:19'),
(205, 1, 'ACCESS', 'dashboard', 'Admin accessed dashboard', '::1', NULL, '2026-02-25 16:00:54'),
(206, 1, 'ACCESS', 'dashboard', 'Admin accessed dashboard', '::1', NULL, '2026-02-25 16:09:09'),
(207, 1, 'ACCESS', 'dashboard', 'Admin accessed dashboard', '::1', NULL, '2026-02-25 16:09:22'),
(208, 1, 'ACCESS', 'dashboard', 'Admin accessed dashboard', '::1', NULL, '2026-02-25 17:44:25'),
(209, 1, 'ACCESS', 'dashboard', 'Admin accessed dashboard', '::1', NULL, '2026-02-25 18:08:11'),
(210, 1, 'ACCESS', 'dashboard', 'Admin accessed dashboard', '::1', NULL, '2026-02-25 18:11:09'),
(211, 1, 'ACCESS', 'dashboard', 'Admin accessed dashboard', '::1', NULL, '2026-02-25 18:11:10'),
(212, 1, 'ACCESS', 'dashboard', 'Admin accessed dashboard', '::1', NULL, '2026-02-26 11:01:29'),
(213, 1, 'ACCESS', 'dashboard', 'Admin accessed dashboard', '::1', NULL, '2026-02-26 11:01:35'),
(214, 1, 'ACCESS', 'dashboard', 'Admin accessed dashboard', '::1', NULL, '2026-02-26 11:01:42'),
(215, 1, 'ACCESS', 'dashboard', 'Admin accessed dashboard', '::1', NULL, '2026-02-26 15:22:33'),
(216, 1, 'ACCESS', 'dashboard', 'Admin accessed dashboard', '::1', NULL, '2026-02-26 15:22:53'),
(217, 1, 'ACCESS', 'dashboard', 'Admin accessed dashboard', '::1', NULL, '2026-02-26 15:23:24'),
(218, 1, 'ACCESS', 'dashboard', 'Admin accessed dashboard', '::1', NULL, '2026-02-26 15:27:54');

-- --------------------------------------------------------

--
-- Table structure for table `admin_dashboards`
--

CREATE TABLE `admin_dashboards` (
  `id` int(11) NOT NULL,
  `admin_id` int(11) NOT NULL,
  `dashboard_name` varchar(100) NOT NULL,
  `dashboard_layout` text DEFAULT NULL,
  `widgets_config` text DEFAULT NULL,
  `theme_settings` text DEFAULT NULL,
  `is_default` tinyint(1) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `admin_dashboards`
--

INSERT INTO `admin_dashboards` (`id`, `admin_id`, `dashboard_name`, `dashboard_layout`, `widgets_config`, `theme_settings`, `is_default`, `created_at`, `updated_at`) VALUES
(1, 1, 'Main Dashboard', '{\"layout\": \"grid\", \"columns\": 3}', '{\"widgets\": [\"users\", \"bookings\", \"revenue\", \"guides\", \"destinations\", \"hotels\"]}', NULL, 1, '2026-02-06 08:07:12', '2026-02-06 08:07:12'),
(2, 1, 'Analytics Dashboard', '{\"layout\": \"charts\", \"columns\": 2}', '{\"widgets\": [\"user_growth\", \"booking_trends\", \"revenue_chart\", \"popular_destinations\"]}', NULL, 0, '2026-02-06 08:07:12', '2026-02-06 08:07:12'),
(3, 1, 'Quick Overview', '{\"layout\": \"compact\", \"columns\": 4}', '{\"widgets\": [\"total_users\", \"today_bookings\", \"pending_tasks\", \"system_status\"]}', NULL, 0, '2026-02-06 08:07:12', '2026-02-06 08:07:12');

-- --------------------------------------------------------

--
-- Table structure for table `admin_dashboard_settings`
--

CREATE TABLE `admin_dashboard_settings` (
  `id` int(11) NOT NULL,
  `setting_key` varchar(100) NOT NULL,
  `setting_value` text DEFAULT NULL,
  `setting_type` enum('text','number','boolean') DEFAULT 'text',
  `description` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `admin_dashboard_settings`
--

INSERT INTO `admin_dashboard_settings` (`id`, `setting_key`, `setting_value`, `setting_type`, `description`, `created_at`, `updated_at`) VALUES
(1, 'page_title', 'Dashboard Overview', 'text', 'Main dashboard page title', '2026-02-06 08:07:12', '2026-02-06 08:07:12'),
(2, 'page_subtitle', 'System statistics and analytics', 'text', 'Dashboard page subtitle', '2026-02-06 08:07:12', '2026-02-06 08:07:12'),
(3, 'admin_logo_text', 'SJDM ADMIN', 'text', 'Admin panel logo text', '2026-02-06 08:07:12', '2026-02-06 08:07:12'),
(4, 'show_user_badges', '1', 'boolean', 'Show user count badges in sidebar', '2026-02-06 08:07:12', '2026-02-06 08:07:12'),
(5, 'refresh_interval', '30', 'number', 'Dashboard auto-refresh interval in seconds', '2026-02-06 08:07:12', '2026-02-06 08:07:12');

-- --------------------------------------------------------

--
-- Table structure for table `admin_menu_items`
--

CREATE TABLE `admin_menu_items` (
  `id` int(11) NOT NULL,
  `menu_name` varchar(100) NOT NULL,
  `menu_icon` varchar(50) DEFAULT NULL,
  `menu_url` varchar(200) NOT NULL,
  `display_order` int(11) DEFAULT 0,
  `is_active` tinyint(1) DEFAULT 1,
  `parent_id` int(11) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `admin_menu_items`
--

INSERT INTO `admin_menu_items` (`id`, `menu_name`, `menu_icon`, `menu_url`, `display_order`, `is_active`, `parent_id`, `created_at`, `updated_at`) VALUES
(1, 'Dashboard', 'dashboard', 'dashboard.php', 1, 1, 0, '2026-02-06 08:07:12', '2026-02-06 08:07:12'),
(2, 'User Management', 'people', 'user-management.php', 2, 1, 0, '2026-02-06 08:07:12', '2026-02-06 08:07:12'),
(3, 'Tour Guides', 'tour', 'tour-guides.php', 3, 1, 0, '2026-02-06 08:07:12', '2026-02-06 08:07:12'),
(4, 'Destinations', 'place', 'destinations.php', 4, 1, 0, '2026-02-06 08:07:12', '2026-02-06 08:07:12'),
(5, 'Hotels', 'hotel', 'hotels.php', 5, 1, 0, '2026-02-06 08:07:12', '2026-02-06 08:07:12'),
(6, 'Bookings', 'event', 'bookings.php', 6, 1, 0, '2026-02-06 08:07:12', '2026-02-06 08:07:12'),
(7, 'Analytics', 'analytics', 'analytics.php', 7, 1, 0, '2026-02-06 08:07:12', '2026-02-06 08:07:12'),
(8, 'Reports', 'description', 'reports.php', 8, 1, 0, '2026-02-06 08:07:12', '2026-02-06 08:07:12'),
(9, 'Settings', 'settings', 'settings.php', 9, 1, 0, '2026-02-06 08:07:12', '2026-02-06 08:07:12');

-- --------------------------------------------------------

--
-- Table structure for table `admin_permissions_table`
--

CREATE TABLE `admin_permissions_table` (
  `id` int(11) NOT NULL,
  `admin_id` int(11) NOT NULL,
  `module` varchar(50) NOT NULL,
  `permission_type` enum('read','write','delete','admin') NOT NULL,
  `granted_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `granted_by` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `admin_permissions_table`
--

INSERT INTO `admin_permissions_table` (`id`, `admin_id`, `module`, `permission_type`, `granted_at`, `granted_by`) VALUES
(1, 1, 'dashboard', 'admin', '2026-02-06 08:07:12', NULL),
(2, 1, 'users', 'admin', '2026-02-06 08:07:12', NULL),
(3, 1, 'bookings', 'admin', '2026-02-06 08:07:12', NULL),
(4, 1, 'guides', 'admin', '2026-02-06 08:07:12', NULL),
(5, 1, 'destinations', 'admin', '2026-02-06 08:07:12', NULL),
(6, 1, 'hotels', 'admin', '2026-02-06 08:07:12', NULL),
(7, 1, 'analytics', 'admin', '2026-02-06 08:07:12', NULL),
(8, 1, 'reports', 'admin', '2026-02-06 08:07:12', NULL),
(9, 1, 'settings', 'admin', '2026-02-06 08:07:12', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `admin_preferences`
--

CREATE TABLE `admin_preferences` (
  `id` int(11) NOT NULL,
  `admin_id` int(11) NOT NULL,
  `setting_key` varchar(100) NOT NULL,
  `setting_value` text DEFAULT NULL,
  `setting_type` enum('text','number','boolean','json') DEFAULT 'text',
  `category` varchar(50) DEFAULT 'general',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `admin_preferences`
--

INSERT INTO `admin_preferences` (`id`, `admin_id`, `setting_key`, `setting_value`, `setting_type`, `category`, `created_at`, `updated_at`) VALUES
(1, 1, 'theme', 'light', 'text', 'appearance', '2026-02-06 08:07:12', '2026-02-06 08:07:12'),
(2, 1, 'language', 'en', 'text', 'appearance', '2026-02-06 08:07:12', '2026-02-06 08:07:12'),
(3, 1, 'notifications_enabled', '1', 'boolean', 'notifications', '2026-02-06 08:07:12', '2026-02-06 08:07:12'),
(4, 1, 'auto_refresh_interval', '30', 'number', 'performance', '2026-02-06 08:07:12', '2026-02-06 08:07:12'),
(5, 1, 'items_per_page', '15', 'number', 'performance', '2026-02-06 08:07:12', '2026-02-06 08:07:12'),
(6, 1, 'default_dashboard', 'Main Dashboard', 'text', 'general', '2026-02-06 08:07:12', '2026-02-06 08:07:12'),
(7, 1, 'show_tooltips', '1', 'boolean', 'ui', '2026-02-06 08:07:12', '2026-02-06 08:07:12'),
(8, 1, 'compact_mode', '0', 'boolean', 'ui', '2026-02-06 08:07:12', '2026-02-06 08:07:12'),
(9, 1, 'admin_mark_display', 'badge', 'text', 'appearance', '2026-02-06 08:07:12', '2026-02-06 08:07:12'),
(10, 1, 'last_login_notification', '1', 'boolean', 'security', '2026-02-06 08:07:12', '2026-02-06 08:07:12');

-- --------------------------------------------------------

--
-- Table structure for table `admin_users`
--

CREATE TABLE `admin_users` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `admin_mark` varchar(10) DEFAULT 'A',
  `role_title` varchar(100) DEFAULT 'Administrator',
  `permissions` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `admin_users`
--

INSERT INTO `admin_users` (`id`, `user_id`, `admin_mark`, `role_title`, `permissions`, `created_at`, `updated_at`) VALUES
(1, 1, 'ADMIN', 'Super Administrator', 'all', '2026-02-06 08:07:12', '2026-02-06 08:07:12');

-- --------------------------------------------------------

--
-- Table structure for table `analytics_settings`
--

CREATE TABLE `analytics_settings` (
  `id` int(11) NOT NULL,
  `setting_key` varchar(50) NOT NULL,
  `setting_value` text NOT NULL,
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `analytics_settings`
--

INSERT INTO `analytics_settings` (`id`, `setting_key`, `setting_value`, `updated_at`) VALUES
(1, 'module_title', 'Analytics Dashboard', '2026-02-06 08:07:12'),
(2, 'module_subtitle', 'System analytics and insights', '2026-02-06 08:07:12'),
(3, 'default_analytics_limit', '15', '2026-02-06 08:07:12'),
(4, 'admin_mark_label', 'AN', '2026-02-06 08:07:12');

-- --------------------------------------------------------

--
-- Table structure for table `bookings`
--

CREATE TABLE `bookings` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `guide_id` int(11) DEFAULT NULL,
  `tour_name` varchar(200) NOT NULL,
  `destination` varchar(200) DEFAULT NULL,
  `booking_date` date NOT NULL,
  `check_in_date` date DEFAULT NULL,
  `check_out_date` date DEFAULT NULL,
  `number_of_people` int(11) NOT NULL,
  `contact_number` varchar(50) DEFAULT NULL,
  `email` varchar(255) DEFAULT NULL,
  `special_requests` text DEFAULT NULL,
  `rejection_notes` text DEFAULT NULL,
  `total_amount` decimal(10,2) NOT NULL,
  `payment_method` enum('pay_later','gcash','bank_transfer') DEFAULT 'pay_later',
  `status` enum('pending','confirmed','cancelled','completed') DEFAULT 'pending',
  `booking_reference` varchar(50) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `bookings`
--

INSERT INTO `bookings` (`id`, `user_id`, `guide_id`, `tour_name`, `destination`, `booking_date`, `check_in_date`, `check_out_date`, `number_of_people`, `contact_number`, `email`, `special_requests`, `rejection_notes`, `total_amount`, `payment_method`, `status`, `booking_reference`, `created_at`, `updated_at`) VALUES
(48, 40, 27, 'Tungtong Falls', 'Tungtong Falls', '2026-02-27', NULL, NULL, 7, '09705667137', 'jeanmarcaguilar829@gmail.com', 'Special', NULL, 3300.00, 'pay_later', 'confirmed', 'SJDM-72119657', '2026-02-26 15:27:37', '2026-02-26 16:15:00'),
(49, 40, 27, 'The Rising Heart', 'The Rising Heart', '2026-02-28', NULL, NULL, 1, '24234234234242', 'jeanmarcaguilar829@gmail.com', 'special', NULL, 2900.00, 'pay_later', 'cancelled', 'SJDM-72120813', '2026-02-26 15:46:53', '2026-02-26 15:51:17');

-- --------------------------------------------------------

--
-- Table structure for table `booking_settings`
--

CREATE TABLE `booking_settings` (
  `id` int(11) NOT NULL,
  `setting_key` varchar(50) NOT NULL,
  `setting_value` text NOT NULL,
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `booking_settings`
--

INSERT INTO `booking_settings` (`id`, `setting_key`, `setting_value`, `updated_at`) VALUES
(1, 'module_title', 'Bookings Management', '2026-02-06 08:07:12'),
(2, 'module_subtitle', 'Manage tour bookings and reservations', '2026-02-06 08:07:12'),
(3, 'default_booking_limit', '15', '2026-02-06 08:07:12'),
(4, 'admin_mark_label', 'BK', '2026-02-06 08:07:12');

-- --------------------------------------------------------

--
-- Table structure for table `destination_settings`
--

CREATE TABLE `destination_settings` (
  `id` int(11) NOT NULL,
  `setting_key` varchar(50) NOT NULL,
  `setting_value` text NOT NULL,
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `destination_settings`
--

INSERT INTO `destination_settings` (`id`, `setting_key`, `setting_value`, `updated_at`) VALUES
(1, 'module_title', 'Destinations Management', '2026-02-06 08:07:12'),
(2, 'module_subtitle', 'Manage tourist destinations', '2026-02-06 08:07:12'),
(3, 'default_destination_limit', '15', '2026-02-06 08:07:12'),
(4, 'admin_mark_label', 'DS', '2026-02-06 08:07:12');

-- --------------------------------------------------------

--
-- Table structure for table `guide_destinations`
--

CREATE TABLE `guide_destinations` (
  `id` int(11) NOT NULL,
  `guide_id` int(11) NOT NULL,
  `destination_id` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `guide_reviews`
--

CREATE TABLE `guide_reviews` (
  `id` int(11) NOT NULL,
  `booking_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `guide_id` int(11) NOT NULL,
  `rating` int(11) NOT NULL CHECK (`rating` >= 1 and `rating` <= 5),
  `review_text` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `guide_reviews`
--

INSERT INTO `guide_reviews` (`id`, `booking_id`, `user_id`, `guide_id`, `rating`, `review_text`, `created_at`, `updated_at`) VALUES
(1, 48, 40, 27, 5, '0', '2026-02-26 16:20:04', '2026-02-26 16:20:04');

-- --------------------------------------------------------

--
-- Table structure for table `homepage_content`
--

CREATE TABLE `homepage_content` (
  `id` int(11) NOT NULL,
  `content_type` varchar(50) NOT NULL COMMENT 'hero, features, about, etc.',
  `content_key` varchar(100) NOT NULL COMMENT 'title, subtitle, description, image, etc.',
  `content_value` text NOT NULL,
  `display_order` int(11) NOT NULL DEFAULT 0,
  `status` enum('active','inactive') NOT NULL DEFAULT 'active',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `homepage_content`
--

INSERT INTO `homepage_content` (`id`, `content_type`, `content_key`, `content_value`, `display_order`, `status`, `created_at`, `updated_at`) VALUES
(1, 'hero', 'title', 'Welcome to SJDM Tours', 1, 'active', '2026-02-06 08:07:13', '2026-02-06 08:07:13'),
(2, 'hero', 'subtitle', 'Discover the Beauty of San Jose del Monte', 2, 'active', '2026-02-06 08:07:13', '2026-02-06 08:07:13'),
(3, 'hero', 'description', 'Experience the best tourist spots and activities in San Jose del Monte with our expert tour guides.', 3, 'active', '2026-02-06 08:07:13', '2026-02-06 08:07:13'),
(4, 'hero', 'background_image', '/images/hero-bg.jpg', 4, 'active', '2026-02-06 08:07:13', '2026-02-06 08:07:13'),
(5, 'features', 'title1', 'Expert Tour Guides', 1, 'active', '2026-02-06 08:07:13', '2026-02-06 08:07:13'),
(6, 'features', 'description1', 'Professional and knowledgeable local guides to make your tour memorable.', 2, 'active', '2026-02-06 08:07:13', '2026-02-06 08:07:13'),
(7, 'features', 'icon1', 'people', 3, 'active', '2026-02-06 08:07:13', '2026-02-06 08:07:13'),
(8, 'features', 'title2', 'Best Destinations', 4, 'active', '2026-02-06 08:07:13', '2026-02-06 08:07:13'),
(9, 'features', 'description2', 'Carefully selected tourist spots showcasing the best of SJDM.', 5, 'active', '2026-02-06 08:07:13', '2026-02-06 08:07:13'),
(10, 'features', 'icon2', 'place', 6, 'active', '2026-02-06 08:07:13', '2026-02-06 08:07:13'),
(11, 'features', 'title3', 'Easy Booking', 7, 'active', '2026-02-06 08:07:13', '2026-02-06 08:07:13'),
(12, 'features', 'description3', 'Simple and secure online booking system for your convenience.', 8, 'active', '2026-02-06 08:07:13', '2026-02-06 08:07:13'),
(13, 'features', 'icon3', 'event', 9, 'active', '2026-02-06 08:07:13', '2026-02-06 08:07:13'),
(14, 'about', 'title', 'About SJDM Tours', 1, 'active', '2026-02-06 08:07:13', '2026-02-06 08:07:13'),
(15, 'about', 'description', 'We are your premier tour service provider in San Jose del Monte, offering unforgettable experiences and expert guidance to the most beautiful destinations in the city.', 2, 'active', '2026-02-06 08:07:13', '2026-02-06 08:07:13'),
(16, 'about', 'mission', 'To provide exceptional tour experiences that showcase the natural beauty and cultural heritage of San Jose del Monte.', 3, 'active', '2026-02-06 08:07:13', '2026-02-06 08:07:13'),
(17, 'about', 'vision', 'To become the leading tour service provider in the region, known for quality, reliability, and customer satisfaction.', 4, 'active', '2026-02-06 08:07:13', '2026-02-06 08:07:13');

-- --------------------------------------------------------

--
-- Table structure for table `hotels`
--

CREATE TABLE `hotels` (
  `id` int(11) NOT NULL,
  `name` varchar(200) NOT NULL,
  `description` text DEFAULT NULL,
  `category` enum('luxury','mid-range','budget','event') NOT NULL,
  `location` varchar(200) DEFAULT NULL,
  `address` text DEFAULT NULL,
  `contact_info` varchar(200) DEFAULT NULL,
  `website` varchar(200) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `price_range` varchar(100) DEFAULT NULL,
  `rating` decimal(3,2) DEFAULT 0.00,
  `review_count` int(11) DEFAULT 0,
  `amenities` text DEFAULT NULL,
  `services` text DEFAULT NULL,
  `image_url` varchar(500) DEFAULT NULL,
  `latitude` decimal(10,8) DEFAULT NULL,
  `longitude` decimal(11,8) DEFAULT NULL,
  `status` enum('active','inactive') DEFAULT 'active',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `hotels`
--

INSERT INTO `hotels` (`id`, `name`, `description`, `category`, `location`, `address`, `contact_info`, `website`, `email`, `phone`, `price_range`, `rating`, `review_count`, `amenities`, `services`, `image_url`, `latitude`, `longitude`, `status`, `created_at`, `updated_at`) VALUES
(1, 'Mountain View Hotel', 'Modern hotel with stunning mountain views and excellent amenities. Perfect for both business and leisure travelers.', '', 'San Jose del Monte, Bulacan', NULL, NULL, NULL, NULL, NULL, '2000-3500', 4.20, 0, NULL, NULL, '/images/hotel-mountain-view.jpg', NULL, NULL, 'active', '2026-01-05 00:00:00', '2026-01-05 00:00:00'),
(2, 'SJDM Grand Hotel', 'Luxurious hotel in the heart of the city. Offers world-class service and facilities.', 'luxury', 'San Jose del Monte, Bulacan', NULL, NULL, NULL, NULL, NULL, '4000-6000', 4.70, 0, NULL, NULL, '/images/hotel-grand.jpg', NULL, NULL, 'active', '2026-01-06 01:00:00', '2026-01-06 01:00:00'),
(3, 'Budget Stay Inn', 'Affordable accommodation with clean rooms and friendly service. Great value for money.', 'budget', 'San Jose del Monte, Bulacan', NULL, NULL, NULL, NULL, NULL, '800-1500', 3.80, 0, NULL, NULL, '/images/hotel-budget.jpg', NULL, NULL, 'active', '2026-01-07 02:00:00', '2026-01-07 02:00:00'),
(4, 'Nature Resort Hotel', 'Eco-friendly resort surrounded by lush greenery. Offers spa services and nature activities.', '', 'San Jose del Monte, Bulacan', NULL, NULL, NULL, NULL, NULL, '2500-4000', 4.50, 0, NULL, NULL, '/images/hotel-nature.jpg', NULL, NULL, 'active', '2026-01-08 03:00:00', '2026-01-08 03:00:00'),
(5, 'City Center Hotel', 'Conveniently located hotel near major attractions and business district.', '', 'San Jose del Monte, Bulacan', NULL, NULL, NULL, NULL, NULL, '1800-3000', 4.10, 0, NULL, NULL, '/images/hotel-city-center.jpg', NULL, NULL, 'active', '2026-01-09 04:00:00', '2026-01-09 04:00:00');

-- --------------------------------------------------------

--
-- Table structure for table `hotel_settings`
--

CREATE TABLE `hotel_settings` (
  `id` int(11) NOT NULL,
  `setting_key` varchar(50) NOT NULL,
  `setting_value` text NOT NULL,
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `hotel_settings`
--

INSERT INTO `hotel_settings` (`id`, `setting_key`, `setting_value`, `updated_at`) VALUES
(1, 'module_title', 'Hotels Management', '2026-02-06 08:07:12'),
(2, 'module_subtitle', 'Manage hotel accommodations', '2026-02-06 08:07:12'),
(3, 'default_hotel_limit', '15', '2026-02-06 08:07:12'),
(4, 'admin_mark_label', 'HT', '2026-02-06 08:07:12');

-- --------------------------------------------------------

--
-- Table structure for table `local_culture`
--

CREATE TABLE `local_culture` (
  `id` int(11) NOT NULL,
  `icon` varchar(50) DEFAULT NULL,
  `title` varchar(200) NOT NULL,
  `description` text DEFAULT NULL,
  `display_order` int(11) DEFAULT 0,
  `is_active` enum('yes','no') DEFAULT 'yes',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `local_culture`
--

INSERT INTO `local_culture` (`id`, `icon`, `title`, `description`, `display_order`, `is_active`, `created_at`, `updated_at`) VALUES
(1, '🎭', 'Traditional Festivals', 'Kakaning Bulacan Festival - Annual celebration of local delicacies\nPanagbenga-inspired floral parades\nReligious processions during Holy Week\nChristmas traditions and Simbang Gabi', 1, 'yes', '2026-01-01 00:00:00', '2026-01-01 00:00:00'),
(2, '🍽️', 'Local Cuisine', 'Bulacan specialties like Lechon Kawali and Chicharon\nNative rice cakes (kakanin) such as Puto and Kutsinta\nFresh water fish dishes from local rivers\nTraditional Filipino breakfast combinations', 2, 'yes', '2026-01-01 00:00:00', '2026-01-01 00:00:00'),
(3, '🎨', 'Arts and Crafts', 'Traditional weaving and basket making\nLocal pottery and ceramic arts\nFolk painting and sculpture\nIndigenous musical instruments', 3, 'yes', '2026-01-01 00:00:00', '2026-01-01 00:00:00'),
(4, '🏛️', 'Historical Heritage', 'Spanish colonial architecture\nHistorical churches and ancestral houses\nLocal museums and cultural centers\nRevolutionary war memorials', 4, 'yes', '2026-01-01 00:00:00', '2026-01-01 00:00:00'),
(5, '🎵', 'Music and Dance', 'Traditional folk dances\nLocal musical performances\nCommunity choirs and bands\nModern contemporary arts scene', 5, 'yes', '2026-01-01 00:00:00', '2026-01-01 00:00:00'),
(6, '👥', 'Community Life', 'Bayanihan spirit and neighborly cooperation\nLocal market traditions and trade\nFamily gatherings and celebrations\nCommunity sports and recreation', 6, 'yes', '2026-01-01 00:00:00', '2026-01-01 00:00:00');

-- --------------------------------------------------------

--
-- Table structure for table `login_activity`
--

CREATE TABLE `login_activity` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `login_time` timestamp NOT NULL DEFAULT current_timestamp(),
  `ip_address` varchar(45) DEFAULT NULL,
  `user_agent` varchar(255) DEFAULT NULL,
  `status` enum('success','failed') DEFAULT 'success'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `login_activity`
--

INSERT INTO `login_activity` (`id`, `user_id`, `login_time`, `ip_address`, `user_agent`, `status`) VALUES
(3, 1, '2026-02-05 03:15:00', '192.168.1.102', 'Mozilla/5.0 (iPhone; CPU iPhone OS 14_7_1 like Mac OS X) AppleWebKit/605.1.15', 'success'),
(6, 1, '2026-02-04 00:00:00', '192.168.1.102', 'Mozilla/5.0 (iPhone; CPU iPhone OS 14_7_1 like Mac OS X) AppleWebKit/605.1.15', 'success'),
(29, 1, '2026-02-08 21:28:27', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', 'success'),
(31, 1, '2026-02-08 21:36:09', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', 'success'),
(33, 1, '2026-02-08 22:39:57', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', 'success'),
(35, 1, '2026-02-08 22:48:45', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', 'success'),
(37, 1, '2026-02-08 22:50:00', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', 'success'),
(39, 1, '2026-02-08 22:51:01', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', 'success'),
(99, 1, '2026-02-10 07:49:38', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', 'success'),
(101, 1, '2026-02-10 08:21:10', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', 'success'),
(102, 1, '2026-02-10 12:47:35', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', 'failed'),
(103, 1, '2026-02-10 12:47:40', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', 'success'),
(109, 1, '2026-02-10 22:24:19', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', 'success'),
(110, 1, '2026-02-10 22:41:31', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', 'success'),
(114, 1, '2026-02-11 00:40:18', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', 'success'),
(115, 1, '2026-02-11 00:49:43', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', 'success'),
(116, 1, '2026-02-11 00:55:56', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', 'success'),
(118, 1, '2026-02-11 01:03:36', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', 'success'),
(119, 1, '2026-02-11 01:04:50', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', 'success'),
(120, 1, '2026-02-11 01:17:12', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', 'success'),
(121, 1, '2026-02-11 01:21:45', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', 'failed'),
(122, 1, '2026-02-11 01:21:48', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', 'success'),
(123, 1, '2026-02-11 01:29:43', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', 'success'),
(124, 1, '2026-02-11 01:40:02', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', 'success'),
(125, 1, '2026-02-11 01:45:10', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', 'success'),
(126, 1, '2026-02-11 01:45:32', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', 'success'),
(133, 1, '2026-02-11 02:01:22', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', 'success'),
(134, 1, '2026-02-11 02:19:58', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', 'success'),
(137, 1, '2026-02-11 02:39:31', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', 'success'),
(139, 1, '2026-02-11 02:54:11', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', 'success'),
(141, 1, '2026-02-11 02:55:20', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', 'success'),
(143, 1, '2026-02-11 03:05:27', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', 'success'),
(144, 1, '2026-02-11 03:21:11', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', 'success'),
(161, 1, '2026-02-15 14:27:45', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36 Edg/144.0.0.0', 'success'),
(165, 1, '2026-02-15 15:55:20', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36 Edg/144.0.0.0', 'success'),
(167, 1, '2026-02-15 18:54:01', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36 Edg/144.0.0.0', 'success'),
(175, 1, '2026-02-15 23:44:07', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', 'success'),
(176, 1, '2026-02-16 05:13:39', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', 'success'),
(177, 1, '2026-02-19 08:45:56', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', 'success'),
(178, 1, '2026-02-19 08:57:48', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', 'success'),
(179, 1, '2026-02-19 09:14:14', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', 'failed'),
(180, 1, '2026-02-19 09:14:19', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', 'success'),
(181, 1, '2026-02-19 09:24:15', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', 'success'),
(184, 1, '2026-02-19 09:28:37', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', 'success'),
(185, 1, '2026-02-19 09:30:59', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', 'success'),
(186, 1, '2026-02-19 09:42:20', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', 'success'),
(189, 1, '2026-02-19 09:45:57', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', 'success'),
(190, 1, '2026-02-19 13:17:35', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', 'success'),
(191, 40, '2026-02-19 13:24:56', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', 'success'),
(192, 40, '2026-02-19 13:25:33', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', 'success'),
(193, 1, '2026-02-19 13:29:54', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', 'success'),
(195, 40, '2026-02-23 17:35:02', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', 'success'),
(196, 40, '2026-02-23 18:44:38', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', 'success'),
(197, 1, '2026-02-23 19:36:57', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', 'success'),
(198, 1, '2026-02-23 19:37:09', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', 'success'),
(199, 1, '2026-02-23 19:37:18', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', 'success'),
(200, 1, '2026-02-23 20:17:04', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', 'success'),
(201, 1, '2026-02-23 20:18:32', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', 'success'),
(203, 1, '2026-02-23 20:21:24', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', 'success'),
(205, 1, '2026-02-23 20:25:04', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', 'success'),
(206, 40, '2026-02-23 20:25:32', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', 'success'),
(207, 1, '2026-02-23 20:26:00', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', 'success'),
(208, 1, '2026-02-23 20:27:28', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', 'success'),
(209, 1, '2026-02-23 20:30:42', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', 'success'),
(210, 1, '2026-02-23 20:31:10', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', 'success'),
(211, 1, '2026-02-23 20:39:45', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', 'success'),
(212, 1, '2026-02-23 20:39:55', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', 'success'),
(213, 1, '2026-02-23 20:40:08', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', 'success'),
(214, 1, '2026-02-23 20:40:32', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', 'success'),
(215, 1, '2026-02-23 20:40:46', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', 'success'),
(216, 1, '2026-02-23 20:45:33', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', 'success'),
(217, 1, '2026-02-23 20:45:49', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', 'success'),
(218, 1, '2026-02-23 20:46:01', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', 'success'),
(219, 1, '2026-02-23 20:52:41', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', 'success'),
(220, 1, '2026-02-23 20:52:56', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', 'success'),
(222, 1, '2026-02-23 20:59:48', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', 'success'),
(223, 1, '2026-02-23 21:00:09', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', 'success'),
(224, 1, '2026-02-23 21:01:20', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', 'success'),
(225, 1, '2026-02-23 21:03:08', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', 'success'),
(226, 1, '2026-02-23 21:04:41', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', 'success'),
(227, 1, '2026-02-23 21:08:26', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', 'success'),
(228, 1, '2026-02-23 21:13:08', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', 'success'),
(229, 1, '2026-02-23 21:13:24', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', 'success'),
(230, 1, '2026-02-23 21:18:32', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', 'failed'),
(231, 1, '2026-02-23 21:18:37', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', 'success'),
(232, 1, '2026-02-23 21:22:08', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', 'success'),
(233, 1, '2026-02-23 21:32:16', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', 'success'),
(234, 40, '2026-02-23 21:40:54', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', 'success'),
(235, 1, '2026-02-24 01:35:55', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', 'success'),
(236, 1, '2026-02-24 02:41:18', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', 'success'),
(243, 40, '2026-02-24 02:48:40', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36 Edg/145.0.0.0', 'success'),
(244, 1, '2026-02-24 04:13:26', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', 'success'),
(245, 40, '2026-02-24 12:28:34', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', 'success'),
(246, 40, '2026-02-24 19:14:41', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', 'success'),
(247, 40, '2026-02-25 01:53:11', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', 'success'),
(248, 1, '2026-02-25 02:21:42', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', 'success'),
(249, 1, '2026-02-25 15:39:10', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', 'success'),
(250, 1, '2026-02-25 15:44:44', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', 'success'),
(251, 1, '2026-02-25 15:59:04', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', 'success'),
(252, 44, '2026-02-25 16:01:15', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', 'success'),
(253, 44, '2026-02-25 16:06:17', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', ''),
(254, 40, '2026-02-25 16:06:27', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', 'success'),
(255, 1, '2026-02-25 16:08:55', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', 'success'),
(256, 40, '2026-02-25 16:09:36', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', 'success'),
(257, 44, '2026-02-25 16:22:37', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', 'success'),
(258, 44, '2026-02-25 16:23:31', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', 'success'),
(259, 1, '2026-02-25 17:44:13', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', 'success'),
(260, 40, '2026-02-25 17:46:31', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36 Edg/145.0.0.0', 'success'),
(261, 1, '2026-02-26 10:59:07', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', 'success'),
(262, 1, '2026-02-26 11:01:08', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', 'success'),
(263, 40, '2026-02-26 11:02:01', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', 'success'),
(264, 44, '2026-02-26 11:03:21', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', 'success'),
(265, 1, '2026-02-26 15:22:07', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', 'success'),
(266, 1, '2026-02-26 15:23:09', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', 'success'),
(267, 40, '2026-02-26 15:23:41', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', 'success'),
(268, 44, '2026-02-26 15:28:57', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36 Edg/145.0.0.0', 'success'),
(269, 44, '2026-02-26 15:38:50', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36 Edg/145.0.0.0', 'success'),
(270, 44, '2026-02-26 16:44:16', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36 Edg/145.0.0.0', '');

-- --------------------------------------------------------

--
-- Table structure for table `otp_codes`
--

CREATE TABLE `otp_codes` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `email` varchar(255) NOT NULL,
  `otp_code` varchar(6) NOT NULL,
  `expires_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `is_used` tinyint(1) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `used_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `otp_codes`
--

INSERT INTO `otp_codes` (`id`, `user_id`, `email`, `otp_code`, `expires_at`, `is_used`, `created_at`, `used_at`) VALUES
(19, 1, 'Jeanmarcaguilar829@gmail.com', '153762', '2026-02-15 14:28:04', 1, '2026-02-15 14:27:45', '2026-02-15 14:28:04'),
(22, 1, 'jeanmarcaguilar829@gmail.com', '292283', '2026-02-15 15:55:32', 1, '2026-02-15 15:55:20', '2026-02-15 15:55:32'),
(24, 1, 'jeanmarcaguilar829@gmail.com', '860688', '2026-02-15 18:54:18', 1, '2026-02-15 18:54:01', '2026-02-15 18:54:18'),
(31, 1, 'jeanmarcaguilar829@gmail.com', '375726', '2026-02-15 23:44:22', 1, '2026-02-15 23:44:07', '2026-02-15 23:44:22'),
(32, 1, 'jeanmarcaguilar829@gmail.com', '949908', '2026-02-16 05:13:51', 1, '2026-02-16 05:13:39', '2026-02-16 05:13:51'),
(33, 1, 'jeanmarcaguilar829@gmail.com', '833601', '2026-02-19 08:55:56', 0, '2026-02-19 08:45:56', NULL),
(34, 1, 'christianbacay042504@gmail.com', '224839', '2026-02-19 08:58:13', 1, '2026-02-19 08:57:48', '2026-02-19 08:58:13'),
(36, 1, 'christianbacay042504@gmail.com', '738633', '2026-02-19 09:24:28', 1, '2026-02-19 09:24:15', '2026-02-19 09:24:28'),
(39, 1, 'christianbacay042504@gmail.com', '188381', '2026-02-19 09:31:24', 1, '2026-02-19 09:30:59', '2026-02-19 09:31:24'),
(40, 1, 'christianbacay042504@gmail.com', '259786', '2026-02-19 09:42:37', 1, '2026-02-19 09:42:20', '2026-02-19 09:42:37'),
(42, 1, 'christianbacay042504@gmail.com', '783608', '2026-02-19 09:46:11', 1, '2026-02-19 09:45:57', '2026-02-19 09:46:11'),
(43, 1, 'christianbacay042504@gmail.com', '979097', '2026-02-19 13:18:19', 1, '2026-02-19 13:17:35', '2026-02-19 13:18:19'),
(44, 40, 'jeanmarcaguilar829@gmail.com', '004834', '2026-02-19 13:25:10', 1, '2026-02-19 13:24:56', '2026-02-19 13:25:10'),
(45, 40, 'jeanmarcaguilar829@gmail.com', '266418', '2026-02-19 13:25:56', 1, '2026-02-19 13:25:33', '2026-02-19 13:25:56'),
(46, 1, 'christianbacay042504@gmail.com', '438837', '2026-02-19 13:30:09', 1, '2026-02-19 13:29:54', '2026-02-19 13:30:09'),
(49, 40, 'jeanmarcaguilar829@gmail.com', '874474', '2026-02-23 17:46:38', 0, '2026-02-23 17:36:38', NULL),
(50, 40, 'jeanmarcaguilar829@gmail.com', '849374', '2026-02-23 18:45:10', 1, '2026-02-23 18:44:38', '2026-02-23 18:45:10'),
(53, 1, 'senchi2528@gmail.com', '599796', '2026-02-23 19:47:18', 0, '2026-02-23 19:37:18', NULL),
(60, 40, 'jeanmarcaguilar829@gmail.com', '693336', '2026-02-23 20:35:32', 0, '2026-02-23 20:25:32', NULL),
(85, 1, 'senchi2528@gmail.com', '739183', '2026-02-23 21:32:08', 0, '2026-02-23 21:22:08', NULL),
(86, 1, 'senchi2528@gmail.com', '266722', '2026-02-23 21:32:42', 1, '2026-02-23 21:32:16', '2026-02-23 21:32:42'),
(87, 40, 'jeanmarcaguilar829@gmail.com', '368445', '2026-02-23 21:41:14', 1, '2026-02-23 21:40:54', '2026-02-23 21:41:14'),
(88, 1, 'senchi2528@gmail.com', '507978', '2026-02-24 01:36:55', 1, '2026-02-24 01:35:55', '2026-02-24 01:36:55'),
(89, 1, 'senchi2528@gmail.com', '288055', '2026-02-24 02:41:44', 1, '2026-02-24 02:41:18', '2026-02-24 02:41:44'),
(92, 40, 'jeanmarcaguilar829@gmail.com', '373989', '2026-02-24 02:48:55', 1, '2026-02-24 02:48:40', '2026-02-24 02:48:55'),
(93, 1, 'senchi2528@gmail.com', '846920', '2026-02-24 04:13:46', 1, '2026-02-24 04:13:26', '2026-02-24 04:13:46'),
(94, 40, 'jeanmarcaguilar829@gmail.com', '952396', '2026-02-24 12:28:52', 1, '2026-02-24 12:28:34', '2026-02-24 12:28:52'),
(95, 40, 'jeanmarcaguilar829@gmail.com', '184215', '2026-02-24 19:15:00', 1, '2026-02-24 19:14:41', '2026-02-24 19:15:00'),
(96, 40, 'jeanmarcaguilar829@gmail.com', '921880', '2026-02-25 01:53:23', 1, '2026-02-25 01:53:11', '2026-02-25 01:53:23'),
(97, 1, 'senchi2528@gmail.com', '945789', '2026-02-25 02:22:08', 1, '2026-02-25 02:21:42', '2026-02-25 02:22:08'),
(98, 1, 'senchi2528@gmail.com', '541164', '2026-02-25 15:39:27', 1, '2026-02-25 15:39:10', '2026-02-25 15:39:27'),
(99, 1, 'senchi2528@gmail.com', '806480', '2026-02-25 15:44:56', 1, '2026-02-25 15:44:44', '2026-02-25 15:44:56'),
(100, 1, 'senchi2528@gmail.com', '456693', '2026-02-25 15:59:18', 1, '2026-02-25 15:59:04', '2026-02-25 15:59:18'),
(101, 44, 'christianbacay042504@gmail.com', '798813', '2026-02-25 16:01:36', 1, '2026-02-25 16:01:15', '2026-02-25 16:01:36'),
(102, 40, 'jeanmarcaguilar829@gmail.com', '659688', '2026-02-25 16:07:11', 1, '2026-02-25 16:06:27', '2026-02-25 16:07:11'),
(103, 1, 'senchi2528@gmail.com', '615455', '2026-02-25 16:09:08', 1, '2026-02-25 16:08:55', '2026-02-25 16:09:08'),
(104, 40, 'jeanmarcaguilar829@gmail.com', '100305', '2026-02-25 16:09:58', 1, '2026-02-25 16:09:36', '2026-02-25 16:09:58'),
(106, 44, 'christianbacay042504@gmail.com', '106219', '2026-02-25 16:23:53', 1, '2026-02-25 16:23:31', '2026-02-25 16:23:53'),
(107, 1, 'senchi2528@gmail.com', '484212', '2026-02-25 17:44:24', 1, '2026-02-25 17:44:13', '2026-02-25 17:44:24'),
(108, 40, 'jeanmarcaguilar829@gmail.com', '628804', '2026-02-25 17:47:16', 1, '2026-02-25 17:46:31', '2026-02-25 17:47:16'),
(110, 1, 'senchi2528@gmail.com', '575950', '2026-02-26 11:01:28', 1, '2026-02-26 11:01:08', '2026-02-26 11:01:28'),
(111, 40, 'jeanmarcaguilar829@gmail.com', '514402', '2026-02-26 11:02:22', 1, '2026-02-26 11:02:01', '2026-02-26 11:02:22'),
(112, 44, 'christianbacay042504@gmail.com', '542725', '2026-02-26 11:04:38', 1, '2026-02-26 11:03:21', '2026-02-26 11:04:38'),
(113, 1, 'senchi2528@gmail.com', '404123', '2026-02-26 15:22:32', 1, '2026-02-26 15:22:07', '2026-02-26 15:22:32'),
(114, 1, 'senchi2528@gmail.com', '351351', '2026-02-26 15:23:23', 1, '2026-02-26 15:23:09', '2026-02-26 15:23:23'),
(115, 40, 'jeanmarcaguilar829@gmail.com', '613588', '2026-02-26 15:23:53', 1, '2026-02-26 15:23:41', '2026-02-26 15:23:53'),
(117, 44, 'christianbacay042504@gmail.com', '998517', '2026-02-26 15:39:30', 1, '2026-02-26 15:38:50', '2026-02-26 15:39:30');

-- --------------------------------------------------------

--
-- Table structure for table `registration_tour_guide`
--

CREATE TABLE `registration_tour_guide` (
  `id` int(11) NOT NULL,
  `application_date` timestamp NOT NULL DEFAULT current_timestamp(),
  `last_name` varchar(100) NOT NULL,
  `first_name` varchar(100) NOT NULL,
  `middle_initial` varchar(5) DEFAULT NULL,
  `preferred_name` varchar(100) DEFAULT NULL,
  `date_of_birth` date NOT NULL,
  `gender` enum('male','female') NOT NULL,
  `home_address` text NOT NULL,
  `primary_phone` varchar(20) NOT NULL,
  `secondary_phone` varchar(20) DEFAULT NULL,
  `email` varchar(255) NOT NULL,
  `emergency_contact_name` varchar(100) NOT NULL,
  `emergency_contact_relationship` varchar(50) NOT NULL,
  `emergency_contact_phone` varchar(20) NOT NULL,
  `dot_accreditation` varchar(100) NOT NULL,
  `accreditation_expiry` date NOT NULL,
  `specialization` enum('mountain','waterfall','cultural','adventure','photography') NOT NULL,
  `years_experience` int(3) NOT NULL DEFAULT 0,
  `first_aid_certified` enum('yes','no') NOT NULL,
  `first_aid_expiry` date DEFAULT NULL,
  `base_location` varchar(255) NOT NULL,
  `employment_type` enum('full-time','part-time','weekends') NOT NULL,
  `has_vehicle` enum('yes','no') NOT NULL,
  `resume_file` varchar(255) DEFAULT NULL,
  `dot_id_file` varchar(255) DEFAULT NULL,
  `government_id_file` varchar(255) DEFAULT NULL,
  `nbi_clearance_file` varchar(255) DEFAULT NULL,
  `first_aid_certificate_file` varchar(255) DEFAULT NULL,
  `id_photo_file` varchar(255) DEFAULT NULL,
  `status` enum('pending','under_review','approved','rejected') NOT NULL DEFAULT 'pending',
  `admin_notes` text DEFAULT NULL,
  `review_date` datetime DEFAULT NULL,
  `reviewed_by` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `registration_tour_guide`
--

INSERT INTO `registration_tour_guide` (`id`, `application_date`, `last_name`, `first_name`, `middle_initial`, `preferred_name`, `date_of_birth`, `gender`, `home_address`, `primary_phone`, `secondary_phone`, `email`, `emergency_contact_name`, `emergency_contact_relationship`, `emergency_contact_phone`, `dot_accreditation`, `accreditation_expiry`, `specialization`, `years_experience`, `first_aid_certified`, `first_aid_expiry`, `base_location`, `employment_type`, `has_vehicle`, `resume_file`, `dot_id_file`, `government_id_file`, `nbi_clearance_file`, `first_aid_certificate_file`, `id_photo_file`, `status`, `admin_notes`, `review_date`, `reviewed_by`) VALUES
(9, '2026-02-25 15:58:52', 'Bacay', 'Christian', 'J', 'Ian', '2026-04-25', 'male', 'Holy Spirit', '09705667137', '09636219345', 'christianbacay042504@gmail.com', 'Jean Marc Aguilar', 'Brother', '09636219345', '1231231313123', '2026-02-26', 'mountain', 5, 'yes', '2028-07-29', 'Sauyo', 'part-time', 'yes', 'tour_guide_documents/699f1c3c814f9_resume.docx', 'tour_guide_documents/699f1c3c821bb_dotId.png', 'tour_guide_documents/699f1c3c82e9c_governmentId.png', 'tour_guide_documents/699f1c3c83d8f_nbiClearance.png', 'tour_guide_documents/699f1c3c84b3d_firstAidCertificate.png', 'tour_guide_documents/699f1c3c8555c_idPhoto.png', 'approved', NULL, '2026-02-25 23:59:56', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `report_settings`
--

CREATE TABLE `report_settings` (
  `id` int(11) NOT NULL,
  `setting_key` varchar(50) NOT NULL,
  `setting_value` text NOT NULL,
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `report_settings`
--

INSERT INTO `report_settings` (`id`, `setting_key`, `setting_value`, `updated_at`) VALUES
(1, 'module_title', 'Reports Management', '2026-02-06 08:07:12'),
(2, 'module_subtitle', 'Generate and manage reports', '2026-02-06 08:07:12'),
(3, 'default_report_limit', '15', '2026-02-06 08:07:12'),
(4, 'admin_mark_label', 'RP', '2026-02-06 08:07:12');

-- --------------------------------------------------------

--
-- Table structure for table `restaurants`
--

CREATE TABLE `restaurants` (
  `id` int(11) NOT NULL,
  `name` varchar(200) NOT NULL,
  `description` text DEFAULT NULL,
  `category` enum('casual_dining','local_eatery','fast_food','fine_dining','cafe','bar') NOT NULL,
  `location` varchar(200) DEFAULT NULL,
  `address` text DEFAULT NULL,
  `contact_info` varchar(200) DEFAULT NULL,
  `website` varchar(200) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `price_range` varchar(100) DEFAULT NULL,
  `rating` decimal(3,2) DEFAULT 0.00,
  `review_count` int(11) DEFAULT 0,
  `cuisine_type` varchar(100) DEFAULT NULL,
  `opening_hours` varchar(200) DEFAULT NULL,
  `features` text DEFAULT NULL,
  `services` text DEFAULT NULL,
  `image_url` varchar(500) DEFAULT NULL,
  `latitude` decimal(10,8) DEFAULT NULL,
  `longitude` decimal(11,8) DEFAULT NULL,
  `status` enum('active','inactive') DEFAULT 'active',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `restaurants`
--

INSERT INTO `restaurants` (`id`, `name`, `description`, `category`, `location`, `address`, `contact_info`, `website`, `email`, `phone`, `price_range`, `rating`, `review_count`, `cuisine_type`, `opening_hours`, `features`, `services`, `image_url`, `latitude`, `longitude`, `status`, `created_at`, `updated_at`) VALUES
(1, 'Escobar\'s', 'Popular local restaurant serving authentic Filipino cuisine and comfort food in a cozy atmosphere.', 'casual_dining', 'San Jose del Monte, Bulacan', NULL, NULL, NULL, NULL, NULL, '200-500', 4.30, 0, NULL, NULL, NULL, NULL, '/images/restaurant-escobars.jpg', NULL, NULL, 'active', '2026-01-01 00:00:00', '2026-01-01 00:00:00'),
(2, 'Roadside Dampa', 'Casual roadside eatery offering fresh seafood and grilled specialties at affordable prices.', 'local_eatery', 'San Jose del Monte, Bulacan', NULL, NULL, NULL, NULL, NULL, '150-400', 4.10, 0, NULL, NULL, NULL, NULL, '/images/restaurant-dampa.jpg', NULL, NULL, 'active', '2026-01-02 01:00:00', '2026-01-02 01:00:00'),
(3, 'Max\'s SM SJDM', 'Well-known restaurant chain serving classic Filipino dishes and fried chicken in SM City San Jose del Monte.', 'fast_food', 'San Jose del Monte, Bulacan', NULL, NULL, NULL, NULL, NULL, '300-600', 4.50, 0, NULL, NULL, NULL, NULL, '/images/restaurant-maxs.jpg', NULL, NULL, 'active', '2026-01-03 02:00:00', '2026-01-03 02:00:00'),
(4, 'Local Carinderia', 'Traditional Filipino eatery offering home-cooked meals at budget-friendly prices.', 'local_eatery', 'San Jose del Monte, Bulacan', NULL, NULL, NULL, NULL, NULL, '100-250', 4.00, 0, NULL, NULL, NULL, NULL, '/images/restaurant-carinderia.jpg', NULL, NULL, 'active', '2026-01-04 03:00:00', '2026-01-04 03:00:00'),
(5, 'Cafe de Monte', 'Cozy coffee shop serving freshly brewed coffee, pastries, and light meals.', 'cafe', 'San Jose del Monte, Bulacan', NULL, NULL, NULL, NULL, NULL, '150-300', 4.20, 0, NULL, NULL, NULL, NULL, '/images/restaurant-cafe.jpg', NULL, NULL, 'active', '2026-01-05 04:00:00', '2026-01-05 04:00:00');

-- --------------------------------------------------------

--
-- Table structure for table `saved_tours`
--

CREATE TABLE `saved_tours` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `tour_name` varchar(200) NOT NULL,
  `tour_description` text DEFAULT NULL,
  `saved_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `status_logs`
--

CREATE TABLE `status_logs` (
  `id` int(11) NOT NULL,
  `table_name` varchar(50) NOT NULL,
  `record_id` int(11) NOT NULL,
  `old_status` varchar(50) DEFAULT NULL,
  `new_status` varchar(50) NOT NULL,
  `changed_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Logs status changes for audit trail purposes';

-- --------------------------------------------------------

--
-- Table structure for table `tourist_spots`
--

CREATE TABLE `tourist_spots` (
  `id` int(11) NOT NULL,
  `name` varchar(200) NOT NULL,
  `description` text DEFAULT NULL,
  `category` enum('nature','historical','religious','farm','park','urban') NOT NULL,
  `location` varchar(200) DEFAULT NULL,
  `address` text DEFAULT NULL,
  `operating_hours` varchar(100) DEFAULT NULL,
  `entrance_fee` varchar(100) DEFAULT NULL,
  `difficulty_level` enum('easy','moderate','difficult') DEFAULT 'moderate',
  `duration` varchar(100) DEFAULT NULL,
  `best_time_to_visit` varchar(100) DEFAULT NULL,
  `activities` text DEFAULT NULL,
  `amenities` text DEFAULT NULL,
  `contact_info` varchar(200) DEFAULT NULL,
  `website` varchar(200) DEFAULT NULL,
  `image_url` varchar(500) DEFAULT NULL,
  `latitude` decimal(10,8) DEFAULT NULL,
  `longitude` decimal(11,8) DEFAULT NULL,
  `rating` decimal(3,2) DEFAULT 0.00,
  `review_count` int(11) DEFAULT 0,
  `status` enum('active','inactive') DEFAULT 'active',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `tourist_spots`
--

INSERT INTO `tourist_spots` (`id`, `name`, `description`, `category`, `location`, `address`, `operating_hours`, `entrance_fee`, `difficulty_level`, `duration`, `best_time_to_visit`, `activities`, `amenities`, `contact_info`, `website`, `image_url`, `latitude`, `longitude`, `rating`, `review_count`, `status`, `created_at`, `updated_at`) VALUES
(12, 'Abes Farm', 'Abes Farm is a premier agri-tourism destination offering visitors an authentic \n                        Filipino farming experience. Established over two decades ago, this family-owned \n                        farm provides educational and recreational activities that showcase traditional \n                        agricultural practices in a modern, sustainable setting.', 'farm', 'San Jose del Monte, Bulacan', 'San Jose del Monte, Bulacan', '8:00 AM - 5:00 PM', 'Free', 'difficult', '', 'All year round', '', 'Shed', '', '', 'https://tse2.mm.bing.net/th/id/OIP.6PNZ3NMvsNBmKlxJeHTXEQHaDj?pid=Api&P=0&h=220', NULL, NULL, 0.00, 0, 'active', '2026-02-06 09:40:50', '2026-02-10 08:03:23'),
(13, 'Burong Falls', 'Burong Falls is one of San Jose del Monte\'s most spectacular natural attractions, featuring \n                        impressive multi-tiered cascades surrounded by pristine rainforest. This destination is part \n                        of a trio falls tour (Burong, Otso-Otso, and Kaytitinga Falls) with a mandatory tour guide \n                        fee of PHP 350 per group (max 5 pax), arranged through the tourism office.', 'nature', 'San Jose del Monte, Bulacan', 'San Jose del Monte, Bulacan', '6:00 AM - 6:00 PM', '₱350', 'difficult', '', 'Dry season', 'Hiking, Trekking', '', '', '', 'https://tse3.mm.bing.net/th/id/OIP.cLp6dchz3-ZHjFh6duCy9wHaEc?pid=Api&P=0&h=220', NULL, NULL, 0.00, 0, 'active', '2026-02-06 09:40:50', '2026-02-08 21:37:25'),
(14, 'City Oval & People\'s Park', 'Spanning over 10 hectares in the heart of San Jose del Monte, the City Oval and People\'s Park \n                        complex serves as the city\'s premier sports, recreation, and community event facility. This \n                        multi-purpose complex combines world-class athletic facilities with beautiful green spaces.', 'park', 'San Jose del Monte, Bulacan', 'San Jose del Monte, Bulacan', '5:00 AM - 10:00 PM', 'Free', 'moderate', '', 'All year round', '', 'Parking, Shed', '', '', 'https://tse1.mm.bing.net/th/id/OIP.1dnzg82f-NJm7SRdZ6HNBAHaFJ?pid=Api&P=0&h=220', NULL, NULL, 0.00, 0, 'active', '2026-02-06 09:40:50', '2026-02-08 21:37:55'),
(15, 'Kaytitinga Falls', 'Kaytitinga Falls is one of the most picturesque waterfalls in San Jose del Monte, \n                        featuring three stunning tiers of cascading water surrounded by lush tropical forest. \n                        This destination is part of a trio falls tour (Burong, Otso-Otso, and Kaytitinga Falls) with a mandatory tour guide \n                        fee of PHP 350 per group (max 5 pax), arranged through the tourism office.', 'nature', 'San Jose del Monte, Bulacan', 'San Jose del Monte, Bulacan', '7:00 AM - 10:00 AM', '₱350', 'difficult', '', 'Morning', 'Hiking, Swimming', '', '', '', 'https://tse4.mm.bing.net/th/id/OIP.EWW5-Bjp7Hci8KrQ7pZbrQHaEo?pid=Api&P=0&h=220', NULL, NULL, 0.00, 0, 'active', '2026-02-06 09:40:50', '2026-02-08 21:38:16'),
(16, 'Mt. Balagbag', 'Mt. Balagbag, standing at 777 meters above sea level, is one of the most popular hiking \n                        destinations near Metro Manila. Known as the \"Mt. Pulag of Bulacan,\" it offers stunning \n                        360-degree panoramic views of Metro Manila, Laguna de Bay, and the surrounding mountain ranges.', 'nature', 'San Jose del Monte, Bulacan', 'San Jose del Monte, Bulacan', '6:00 AM - 6:00 PM', '₱25', 'easy', '', 'Morning', 'Hiking, Camping', 'Shed', '', '', 'https://tse3.mm.bing.net/th/id/OIP.S_I-lXG6p3QDfiO8yUoxTQHaFj?pid=Api&P=0&h=220', NULL, NULL, 0.00, 0, 'active', '2026-02-06 09:40:50', '2026-02-08 21:38:52'),
(17, 'Otso-Otso Falls', 'Named after the Filipino word \"otso\" meaning eight, Otso-Otso Falls is a magnificent \n                        series of eight interconnected waterfalls located in the pristine wilderness of San Jose del Monte. \n                        This destination is part of a trio falls tour (Burong, Otso-Otso, and Kaytitinga Falls) with a mandatory tour guide \n                        fee of PHP 350 per group (max 5 pax), arranged through the tourism office.', 'nature', 'San Jose del Monte, Bulacan', 'San Jose del Monte, Bulacan', '7:00 AM - 4:00 PM', '₱350', 'easy', '', 'Dry season', 'Hiking, Swimming', '', '', '', 'https://tse4.mm.bing.net/th/id/OIP.SsASPok_BbP2wo1flyJvzgHaDj?pid=Api&P=0&h=220', NULL, NULL, 0.00, 0, 'active', '2026-02-06 09:40:50', '2026-02-08 21:39:13'),
(18, 'Our Lady of Lourdes Grotto', 'The Our Lady of Lourdes Grotto is a renowned spiritual sanctuary in San Jose del Monte, \n                        established in 1958 as a replica of the famous Lourdes Grotto in France. This peaceful \n                        religious site attracts thousands of pilgrims and visitors seeking spiritual renewal, \n                        healing, and divine intervention.', 'religious', 'San Jose del Monte, Bulacan', 'San Jose del Monte, Bulacan', '5:00 AM - 8:00 PM', 'Free', 'moderate', '', 'All year round', 'Photography', 'Shed', '', '', 'https://tse2.mm.bing.net/th/id/OIP.3k2Pj5tL7rhVT-sDN2XSzwHaEK?pid=Api&P=0&h=220', NULL, NULL, 0.00, 0, 'active', '2026-02-06 09:40:50', '2026-02-08 22:41:27'),
(20, 'Paradise Hill Farm', 'Paradise Hill Farm is a 25-hectare integrated sustainable farm that combines modern agricultural \n                        practices with eco-tourism experiences. Established in 2010, this farm utilizes organic farming \n                        methods, renewable energy, and water conservation techniques to create a model for sustainable \n                        agriculture in San Jose del Monte.', 'farm', 'San Jose del Monte, Bulacan', 'San Jose del Monte, Bulacan', '8:00 AM - 5:00 PM', '₱200', 'moderate', '', 'All year round', 'Photography', 'Shed, Restaurant', '', '', 'https://tse3.mm.bing.net/th/id/OIP.K8xFcpCM-Frmj4yyT7jkmQHaE8?pid=Api&P=0&h=220', NULL, NULL, 0.00, 0, 'active', '2026-02-06 09:40:50', '2026-02-08 21:40:02'),
(21, 'The Rising Heart Monument', 'Standing majestically at the entrance of San Jose del Monte City, The Rising Heart Monument \n                        is a 15-meter tall steel sculpture that has become the city\'s most recognizable landmark. \n                        Completed in 2018 to celebrate the city\'s conversion from municipality to component city, \n                        this monument symbolizes the city\'s rising prosperity, love for community, and resilient spirit.', 'park', 'San Jose del Monte, Bulacan', 'San Jose del Monte, Bulacan', '5:30 AM - 6:30 AM', 'Free', 'moderate', '', 'All year round', 'Photography', 'Parking, Restroom, Shed', '', '', 'https://tse2.mm.bing.net/th/id/OIP.NoRJkeG3JRq1XuSygZRRNwHaEc?pid=Api&P=0&h=220', NULL, NULL, 0.00, 0, 'active', '2026-02-06 09:40:50', '2026-02-08 22:49:11'),
(22, 'Tungtong Falls', 'Tungtong Falls is a spectacular 25-meter waterfall located within a dramatic canyon formation \n                        in San Jose del Monte. Named after the local term \"tungtong\" meaning \"to climb or ascend,\" \n                        this waterfall features a unique rock amphitheater that creates perfect acoustics and \n                        breathtaking visual effects when sunlight hits the cascading water.', 'nature', 'San Jose del Monte, Bulacan', 'San Jose del Monte, Bulacan', '7:00 AM - 5:00 PM', '₱1', 'difficult', '', 'All year round', 'Hiking, Swimming', 'Parking', '', '', 'https://tse3.mm.bing.net/th/id/OIP.PGdvW97mFRf7-GQD84CukQHaEc?pid=Api&P=0&h=220', NULL, NULL, 0.00, 0, 'active', '2026-02-06 09:40:50', '2026-02-08 21:40:45'),
(23, 'Padre Pio Shrine', 'The Padre Pio Shrine in San Jose del Monte is a major Catholic pilgrimage site dedicated to Saint Padre Pio of Pietrelcina, known for his stigmata and miraculous healings. Established in 1998, this spiritual complex has become one of the most visited religious sites in Bulacan, attracting devotees seeking spiritual guidance, healing, and miracles. The shrine features a magnificent 800-seat main chapel with Italian-inspired architecture, a relic museum housing authentic relics of Saint Padre Pio, a serene prayer garden with life-size statues and Stations of the Cross, a healing water fountain, 12 confessionals, a retreat center, and comprehensive facilities for pilgrims.', 'religious', 'San Jose del Monte, Bulacan', 'San Jose del Monte, Bulacan', '6:00 AM - 9:00 PM', 'Free (Donations Welcome)', 'moderate', '2-3 hours', 'All year round', 'Prayer, Meditation, Religious Services, Photography, Spiritual Retreat', 'Main Chapel, Relic Museum, Prayer Garden, Healing Water Fountain, Confession Complex, Retreat Center, Bookstore & Gift Shop, Pilgrim Rest Area, Parking', '', '', 'https://images.unsplash.com/photo-1542766788-a2f588f447ee?q=80&w=2067&auto=format&fit=crop', NULL, NULL, 0.00, 0, 'active', '2026-02-25 01:34:58', '2026-02-25 01:34:58');

-- --------------------------------------------------------

--
-- Table structure for table `tour_guides`
--

CREATE TABLE `tour_guides` (
  `id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `name` varchar(100) NOT NULL,
  `specialty` varchar(200) DEFAULT NULL,
  `category` enum('mountain','city','farm','waterfall','historical','general','cultural','adventure','photography') NOT NULL DEFAULT 'general',
  `description` text DEFAULT NULL,
  `bio` text DEFAULT NULL,
  `areas_of_expertise` text DEFAULT NULL,
  `rating` decimal(3,2) DEFAULT 0.00,
  `review_count` int(11) DEFAULT 0,
  `price_range` varchar(100) DEFAULT NULL,
  `price_min` decimal(10,2) DEFAULT NULL,
  `price_max` decimal(10,2) DEFAULT NULL,
  `languages` varchar(200) DEFAULT NULL,
  `contact_number` varchar(20) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `schedules` text DEFAULT NULL,
  `experience_years` int(11) DEFAULT NULL,
  `group_size` varchar(50) DEFAULT NULL,
  `verified` tinyint(1) DEFAULT 0,
  `total_tours` int(11) DEFAULT 0,
  `photo_url` varchar(500) DEFAULT NULL,
  `status` enum('active','inactive') DEFAULT 'active',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `resume` varchar(500) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `tour_guides`
--

INSERT INTO `tour_guides` (`id`, `user_id`, `name`, `specialty`, `category`, `description`, `bio`, `areas_of_expertise`, `rating`, `review_count`, `price_range`, `price_min`, `price_max`, `languages`, `contact_number`, `email`, `schedules`, `experience_years`, `group_size`, `verified`, `total_tours`, `photo_url`, `status`, `created_at`, `updated_at`, `resume`) VALUES
(27, 44, 'Christian Bacay', 'waterfall', 'mountain', NULL, 'ILOVEAJ', NULL, 0.00, 0, NULL, NULL, NULL, 'English, Filipino', '09123456789', 'christianbacay042504@gmail.com', NULL, 5, NULL, 1, 0, NULL, 'active', '2026-02-25 15:59:56', '2026-02-25 18:10:54', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `tour_guide_availability`
--

CREATE TABLE `tour_guide_availability` (
  `id` int(11) NOT NULL,
  `tour_guide_id` int(11) NOT NULL,
  `available_date` date NOT NULL,
  `start_time` time NOT NULL,
  `end_time` time NOT NULL,
  `status` enum('available','booked','unavailable') DEFAULT 'available',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tour_guide_availability`
--

INSERT INTO `tour_guide_availability` (`id`, `tour_guide_id`, `available_date`, `start_time`, `end_time`, `status`, `created_at`) VALUES
(4, 27, '2026-02-27', '07:36:00', '08:38:00', 'available', '2026-02-25 18:38:11'),
(5, 27, '2026-02-28', '14:43:00', '17:43:00', 'available', '2026-02-26 15:40:55');

-- --------------------------------------------------------

--
-- Table structure for table `tour_guide_languages`
--

CREATE TABLE `tour_guide_languages` (
  `id` int(11) NOT NULL,
  `registration_id` int(11) NOT NULL,
  `language` varchar(50) NOT NULL,
  `proficiency` enum('native','fluent','conversational') NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `tour_guide_languages`
--

INSERT INTO `tour_guide_languages` (`id`, `registration_id`, `language`, `proficiency`) VALUES
(14, 9, 'filipino', 'fluent'),
(15, 9, 'english', 'fluent');

-- --------------------------------------------------------

--
-- Table structure for table `tour_guide_profiles`
--

CREATE TABLE `tour_guide_profiles` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `license_number` varchar(50) DEFAULT NULL,
  `specialization` text DEFAULT NULL,
  `experience_years` int(11) DEFAULT 0,
  `languages` text DEFAULT NULL,
  `hourly_rate` decimal(10,2) DEFAULT 0.00,
  `availability_status` enum('available','busy','offline') DEFAULT 'available',
  `rating` decimal(3,2) DEFAULT 0.00,
  `total_tours` int(11) DEFAULT 0,
  `bio` text DEFAULT NULL,
  `contact_number` varchar(20) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `tour_guide_reviews`
--

CREATE TABLE `tour_guide_reviews` (
  `id` int(11) NOT NULL,
  `tour_guide_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `booking_id` int(11) DEFAULT NULL,
  `rating` int(11) NOT NULL CHECK (`rating` >= 1 and `rating` <= 5),
  `review` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `tour_guide_settings`
--

CREATE TABLE `tour_guide_settings` (
  `id` int(11) NOT NULL,
  `setting_key` varchar(50) NOT NULL,
  `setting_value` text NOT NULL,
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `tour_guide_settings`
--

INSERT INTO `tour_guide_settings` (`id`, `setting_key`, `setting_value`, `updated_at`) VALUES
(1, 'module_title', 'Tour Guides Management', '2026-02-06 08:07:12'),
(2, 'module_subtitle', 'Manage and monitor tour guides', '2026-02-06 08:07:12'),
(3, 'default_guide_limit', '15', '2026-02-06 08:07:12'),
(4, 'admin_mark_label', 'TG', '2026-02-06 08:07:12');

-- --------------------------------------------------------

--
-- Table structure for table `travel_tips`
--

CREATE TABLE `travel_tips` (
  `id` int(11) NOT NULL,
  `icon` varchar(50) DEFAULT NULL,
  `title` varchar(200) NOT NULL,
  `category` varchar(50) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `display_order` int(11) DEFAULT 0,
  `is_active` enum('yes','no') DEFAULT 'yes',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `travel_tips`
--

INSERT INTO `travel_tips` (`id`, `icon`, `title`, `category`, `description`, `display_order`, `is_active`, `created_at`, `updated_at`) VALUES
(1, 'directions_bus', 'Transportation Tips', 'transport', 'Book transportation in advance during peak seasons\nUse reputable transport services for safety\nKeep emergency contact numbers handy\nDownload offline maps for navigation', 1, 'yes', '2026-01-01 00:00:00', '2026-01-01 00:00:00'),
(2, 'hotel', 'Accommodation Advice', 'accommodation', 'Read recent reviews before booking\nCheck location proximity to attractions\nConfirm cancellation policies\nBring personal toiletries for comfort', 2, 'yes', '2026-01-01 00:00:00', '2026-01-01 00:00:00'),
(3, 'restaurant', 'Food & Dining', 'food', 'Try local delicacies for authentic experience\nDrink bottled water for safety\nCheck food hygiene standards\nBring snacks for long tours', 3, 'yes', '2026-01-01 00:00:00', '2026-01-01 00:00:00'),
(4, 'health_and_safety', 'Health & Safety', 'safety', 'Pack basic medications and first aid\nGet travel insurance for emergencies\nKeep copies of important documents\nKnow local emergency numbers', 4, 'yes', '2026-01-01 00:00:00', '2026-01-01 00:00:00'),
(5, 'payments', 'Money & Payments', 'finance', 'Bring enough cash for small purchases\nInform bank about travel plans\nUse secure ATMs for withdrawals\nKeep money in different locations', 5, 'yes', '2026-01-01 00:00:00', '2026-01-01 00:00:00'),
(6, 'camera_alt', 'Photography Tips', 'photography', 'Respect photography restrictions\nBring extra batteries and memory cards\nAsk permission before photographing people\nCapture sunrise/sunset for best shots', 6, 'yes', '2026-01-01 00:00:00', '2026-01-01 00:00:00'),
(7, 'phone_iphone', 'Communication', 'communication', 'Buy local SIM cards for data\nDownload useful travel apps\nLearn basic local phrases\nKeep phone charged with power bank', 7, 'yes', '2026-01-01 00:00:00', '2026-01-01 00:00:00'),
(8, 'shopping_bag', 'Shopping Guide', 'shopping', 'Bargain respectfully at local markets\nBuy authentic local products\nCheck customs regulations for souvenirs\nSupport local artisans and businesses', 8, 'yes', '2026-01-01 00:00:00', '2026-01-01 00:00:00');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `first_name` varchar(50) NOT NULL,
  `last_name` varchar(50) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `user_type` enum('user','admin','tour_guide') DEFAULT 'user',
  `status` enum('active','inactive','suspended') DEFAULT 'active',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `last_login` timestamp NULL DEFAULT NULL,
  `preferences_set` tinyint(1) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `first_name`, `last_name`, `email`, `password`, `user_type`, `status`, `created_at`, `updated_at`, `last_login`, `preferences_set`) VALUES
(1, 'Admin', 'SJDM', 'senchi2528@gmail.com', '$2y$10$6qLFC1Jeajfeb2yMht1BWOodAuVhSWGNBsh7lF6DVMn0Md.deNNkq', 'admin', 'active', '2026-01-30 07:02:22', '2026-02-26 15:23:09', '2026-02-26 15:23:09', 0),
(40, 'Jean Marc', 'Aguilar', 'jeanmarcaguilar829@gmail.com', '$2y$10$HW.T2T7lI879YrHtzXZU2uPgvNMrrapQOTEliUZn.LC9Pjl/1MHpK', 'user', 'active', '2026-02-19 13:22:43', '2026-02-26 15:23:41', '2026-02-26 15:23:41', 1),
(44, 'Christian', 'Bacay', 'christianbacay042504@gmail.com', '$2y$10$YgfuPd7kTP10ToBqpJJRoOHJmuk2K71mJ1mpCHxkwq2DZz6OBUuCW', 'tour_guide', 'active', '2026-02-25 15:59:56', '2026-02-26 15:38:50', '2026-02-26 15:38:50', 0);

-- --------------------------------------------------------

--
-- Table structure for table `user_favorites`
--

CREATE TABLE `user_favorites` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `item_id` int(11) NOT NULL,
  `item_type` enum('tourist_spot','tour_guide','hotel') NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `user_management_settings`
--

CREATE TABLE `user_management_settings` (
  `id` int(11) NOT NULL,
  `setting_key` varchar(50) NOT NULL,
  `setting_value` text NOT NULL,
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `user_management_settings`
--

INSERT INTO `user_management_settings` (`id`, `setting_key`, `setting_value`, `updated_at`) VALUES
(1, 'module_title', 'User Management', '2026-02-06 08:07:12'),
(2, 'module_subtitle', 'Manage, monitor and authorize system users', '2026-02-06 08:07:12'),
(3, 'default_user_limit', '15', '2026-02-06 08:07:12'),
(4, 'admin_mark_label', 'A', '2026-02-06 08:07:12');

-- --------------------------------------------------------

--
-- Table structure for table `user_preferences`
--

CREATE TABLE `user_preferences` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `category` varchar(100) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `user_preferences`
--

INSERT INTO `user_preferences` (`id`, `user_id`, `category`, `created_at`) VALUES
(22, 40, 'nature', '2026-02-19 13:22:50'),
(23, 40, 'farm', '2026-02-19 13:22:50'),
(24, 40, 'park', '2026-02-19 13:22:50'),
(25, 40, 'adventure', '2026-02-19 13:22:50'),
(26, 40, 'cultural', '2026-02-19 13:22:50'),
(27, 40, 'religious', '2026-02-19 13:22:50'),
(28, 40, 'entertainment', '2026-02-19 13:22:50'),
(29, 40, 'food', '2026-02-19 13:22:50');

-- --------------------------------------------------------

--
-- Table structure for table `user_settings`
--

CREATE TABLE `user_settings` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `notifications` tinyint(1) DEFAULT 1,
  `email_updates` tinyint(1) DEFAULT 1,
  `share_history` tinyint(1) DEFAULT 0,
  `public_profile` tinyint(1) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `user_settings`
--

INSERT INTO `user_settings` (`id`, `user_id`, `notifications`, `email_updates`, `share_history`, `public_profile`, `created_at`, `updated_at`) VALUES
(3, 1, 0, 1, 0, 1, '2026-02-03 04:00:00', '2026-02-03 04:00:00');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `admin_activity`
--
ALTER TABLE `admin_activity`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_admin_id` (`admin_id`),
  ADD KEY `idx_action` (`action`),
  ADD KEY `idx_created_at` (`created_at`);

--
-- Indexes for table `admin_dashboards`
--
ALTER TABLE `admin_dashboards`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_admin_id` (`admin_id`);

--
-- Indexes for table `admin_dashboard_settings`
--
ALTER TABLE `admin_dashboard_settings`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `setting_key` (`setting_key`);

--
-- Indexes for table `admin_menu_items`
--
ALTER TABLE `admin_menu_items`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_display_order` (`display_order`),
  ADD KEY `idx_is_active` (`is_active`);

--
-- Indexes for table `admin_permissions_table`
--
ALTER TABLE `admin_permissions_table`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_admin_id` (`admin_id`),
  ADD KEY `idx_module` (`module`);

--
-- Indexes for table `admin_preferences`
--
ALTER TABLE `admin_preferences`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_admin_setting` (`admin_id`,`setting_key`),
  ADD KEY `idx_admin_id` (`admin_id`),
  ADD KEY `idx_category` (`category`);

--
-- Indexes for table `admin_users`
--
ALTER TABLE `admin_users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `user_id` (`user_id`);

--
-- Indexes for table `analytics_settings`
--
ALTER TABLE `analytics_settings`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `idx_key` (`setting_key`);

--
-- Indexes for table `bookings`
--
ALTER TABLE `bookings`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_user_id` (`user_id`),
  ADD KEY `idx_guide_id` (`guide_id`),
  ADD KEY `idx_booking_date` (`booking_date`),
  ADD KEY `idx_status` (`status`),
  ADD KEY `idx_booking_reference` (`booking_reference`);

--
-- Indexes for table `booking_settings`
--
ALTER TABLE `booking_settings`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `idx_key` (`setting_key`);

--
-- Indexes for table `destination_settings`
--
ALTER TABLE `destination_settings`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `idx_key` (`setting_key`);

--
-- Indexes for table `guide_destinations`
--
ALTER TABLE `guide_destinations`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_guide_destination` (`guide_id`,`destination_id`),
  ADD KEY `idx_guide_id` (`guide_id`),
  ADD KEY `idx_destination_id` (`destination_id`);

--
-- Indexes for table `guide_reviews`
--
ALTER TABLE `guide_reviews`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_booking_review` (`booking_id`,`user_id`),
  ADD KEY `idx_guide_reviews_guide_id` (`guide_id`),
  ADD KEY `idx_guide_reviews_user_id` (`user_id`),
  ADD KEY `idx_guide_reviews_created_at` (`created_at`);

--
-- Indexes for table `homepage_content`
--
ALTER TABLE `homepage_content`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_content_type` (`content_type`),
  ADD KEY `idx_status` (`status`),
  ADD KEY `idx_display_order` (`display_order`);

--
-- Indexes for table `hotels`
--
ALTER TABLE `hotels`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_category` (`category`),
  ADD KEY `idx_status` (`status`),
  ADD KEY `idx_rating` (`rating`);

--
-- Indexes for table `hotel_settings`
--
ALTER TABLE `hotel_settings`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `idx_key` (`setting_key`);

--
-- Indexes for table `local_culture`
--
ALTER TABLE `local_culture`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_display_order` (`display_order`),
  ADD KEY `idx_is_active` (`is_active`);

--
-- Indexes for table `login_activity`
--
ALTER TABLE `login_activity`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_user_id` (`user_id`);

--
-- Indexes for table `otp_codes`
--
ALTER TABLE `otp_codes`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_user_email` (`user_id`,`email`),
  ADD KEY `idx_otp_code` (`otp_code`),
  ADD KEY `idx_expires_at` (`expires_at`);

--
-- Indexes for table `registration_tour_guide`
--
ALTER TABLE `registration_tour_guide`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`),
  ADD UNIQUE KEY `dot_accreditation` (`dot_accreditation`),
  ADD KEY `status` (`status`),
  ADD KEY `application_date` (`application_date`),
  ADD KEY `specialization` (`specialization`);

--
-- Indexes for table `report_settings`
--
ALTER TABLE `report_settings`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `idx_key` (`setting_key`);

--
-- Indexes for table `restaurants`
--
ALTER TABLE `restaurants`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_category` (`category`),
  ADD KEY `idx_status` (`status`),
  ADD KEY `idx_rating` (`rating`);

--
-- Indexes for table `saved_tours`
--
ALTER TABLE `saved_tours`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_user_id` (`user_id`);

--
-- Indexes for table `status_logs`
--
ALTER TABLE `status_logs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `table_record` (`table_name`,`record_id`),
  ADD KEY `changed_at` (`changed_at`);

--
-- Indexes for table `tourist_spots`
--
ALTER TABLE `tourist_spots`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_category` (`category`),
  ADD KEY `idx_status` (`status`),
  ADD KEY `idx_rating` (`rating`);

--
-- Indexes for table `tour_guides`
--
ALTER TABLE `tour_guides`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_category` (`category`),
  ADD KEY `idx_status` (`status`),
  ADD KEY `idx_rating` (`rating`),
  ADD KEY `idx_verified` (`verified`),
  ADD KEY `fk_tour_guides_user_id` (`user_id`);

--
-- Indexes for table `tour_guide_availability`
--
ALTER TABLE `tour_guide_availability`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_tour_guide_availability_guide_id` (`tour_guide_id`),
  ADD KEY `idx_tour_guide_availability_date` (`available_date`);

--
-- Indexes for table `tour_guide_languages`
--
ALTER TABLE `tour_guide_languages`
  ADD PRIMARY KEY (`id`),
  ADD KEY `registration_id` (`registration_id`);

--
-- Indexes for table `tour_guide_profiles`
--
ALTER TABLE `tour_guide_profiles`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_tour_guide_profiles_user_id` (`user_id`);

--
-- Indexes for table `tour_guide_reviews`
--
ALTER TABLE `tour_guide_reviews`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_tour_guide_reviews_guide_id` (`tour_guide_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `tour_guide_settings`
--
ALTER TABLE `tour_guide_settings`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `idx_key` (`setting_key`);

--
-- Indexes for table `travel_tips`
--
ALTER TABLE `travel_tips`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_category` (`category`),
  ADD KEY `idx_display_order` (`display_order`),
  ADD KEY `idx_is_active` (`is_active`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Indexes for table `user_favorites`
--
ALTER TABLE `user_favorites`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_user_id` (`user_id`),
  ADD KEY `idx_item` (`item_id`,`item_type`);

--
-- Indexes for table `user_management_settings`
--
ALTER TABLE `user_management_settings`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `idx_key` (`setting_key`);

--
-- Indexes for table `user_preferences`
--
ALTER TABLE `user_preferences`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_user_category` (`user_id`,`category`);

--
-- Indexes for table `user_settings`
--
ALTER TABLE `user_settings`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_user_settings` (`user_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `admin_activity`
--
ALTER TABLE `admin_activity`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=219;

--
-- AUTO_INCREMENT for table `admin_dashboards`
--
ALTER TABLE `admin_dashboards`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `admin_dashboard_settings`
--
ALTER TABLE `admin_dashboard_settings`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `admin_menu_items`
--
ALTER TABLE `admin_menu_items`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `admin_permissions_table`
--
ALTER TABLE `admin_permissions_table`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `admin_preferences`
--
ALTER TABLE `admin_preferences`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `admin_users`
--
ALTER TABLE `admin_users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `analytics_settings`
--
ALTER TABLE `analytics_settings`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `bookings`
--
ALTER TABLE `bookings`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=50;

--
-- AUTO_INCREMENT for table `booking_settings`
--
ALTER TABLE `booking_settings`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `destination_settings`
--
ALTER TABLE `destination_settings`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `guide_destinations`
--
ALTER TABLE `guide_destinations`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=22;

--
-- AUTO_INCREMENT for table `guide_reviews`
--
ALTER TABLE `guide_reviews`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `homepage_content`
--
ALTER TABLE `homepage_content`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=18;

--
-- AUTO_INCREMENT for table `hotels`
--
ALTER TABLE `hotels`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `hotel_settings`
--
ALTER TABLE `hotel_settings`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `local_culture`
--
ALTER TABLE `local_culture`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `login_activity`
--
ALTER TABLE `login_activity`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=271;

--
-- AUTO_INCREMENT for table `otp_codes`
--
ALTER TABLE `otp_codes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=118;

--
-- AUTO_INCREMENT for table `registration_tour_guide`
--
ALTER TABLE `registration_tour_guide`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `report_settings`
--
ALTER TABLE `report_settings`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `restaurants`
--
ALTER TABLE `restaurants`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `saved_tours`
--
ALTER TABLE `saved_tours`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `status_logs`
--
ALTER TABLE `status_logs`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `tourist_spots`
--
ALTER TABLE `tourist_spots`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=24;

--
-- AUTO_INCREMENT for table `tour_guides`
--
ALTER TABLE `tour_guides`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=28;

--
-- AUTO_INCREMENT for table `tour_guide_availability`
--
ALTER TABLE `tour_guide_availability`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `tour_guide_languages`
--
ALTER TABLE `tour_guide_languages`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT for table `tour_guide_profiles`
--
ALTER TABLE `tour_guide_profiles`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `tour_guide_reviews`
--
ALTER TABLE `tour_guide_reviews`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `tour_guide_settings`
--
ALTER TABLE `tour_guide_settings`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `travel_tips`
--
ALTER TABLE `travel_tips`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=45;

--
-- AUTO_INCREMENT for table `user_favorites`
--
ALTER TABLE `user_favorites`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `user_management_settings`
--
ALTER TABLE `user_management_settings`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `user_preferences`
--
ALTER TABLE `user_preferences`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=30;

--
-- AUTO_INCREMENT for table `user_settings`
--
ALTER TABLE `user_settings`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `admin_activity`
--
ALTER TABLE `admin_activity`
  ADD CONSTRAINT `admin_activity_ibfk_1` FOREIGN KEY (`admin_id`) REFERENCES `admin_users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `admin_dashboards`
--
ALTER TABLE `admin_dashboards`
  ADD CONSTRAINT `admin_dashboards_ibfk_1` FOREIGN KEY (`admin_id`) REFERENCES `admin_users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `admin_permissions_table`
--
ALTER TABLE `admin_permissions_table`
  ADD CONSTRAINT `admin_permissions_table_ibfk_1` FOREIGN KEY (`admin_id`) REFERENCES `admin_users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `admin_preferences`
--
ALTER TABLE `admin_preferences`
  ADD CONSTRAINT `admin_preferences_ibfk_1` FOREIGN KEY (`admin_id`) REFERENCES `admin_users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `admin_users`
--
ALTER TABLE `admin_users`
  ADD CONSTRAINT `admin_users_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `bookings`
--
ALTER TABLE `bookings`
  ADD CONSTRAINT `bookings_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_bookings_guide_id` FOREIGN KEY (`guide_id`) REFERENCES `tour_guides` (`id`) ON DELETE SET NULL ON UPDATE CASCADE;

--
-- Constraints for table `guide_destinations`
--
ALTER TABLE `guide_destinations`
  ADD CONSTRAINT `fk_guide_destinations_destination` FOREIGN KEY (`destination_id`) REFERENCES `tourist_spots` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_guide_destinations_guide` FOREIGN KEY (`guide_id`) REFERENCES `tour_guides` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `guide_reviews`
--
ALTER TABLE `guide_reviews`
  ADD CONSTRAINT `guide_reviews_ibfk_1` FOREIGN KEY (`booking_id`) REFERENCES `bookings` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `guide_reviews_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `guide_reviews_ibfk_3` FOREIGN KEY (`guide_id`) REFERENCES `tour_guides` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `login_activity`
--
ALTER TABLE `login_activity`
  ADD CONSTRAINT `login_activity_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `otp_codes`
--
ALTER TABLE `otp_codes`
  ADD CONSTRAINT `otp_codes_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `saved_tours`
--
ALTER TABLE `saved_tours`
  ADD CONSTRAINT `saved_tours_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `tour_guides`
--
ALTER TABLE `tour_guides`
  ADD CONSTRAINT `fk_tour_guides_user_id` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `tour_guide_availability`
--
ALTER TABLE `tour_guide_availability`
  ADD CONSTRAINT `tour_guide_availability_ibfk_1` FOREIGN KEY (`tour_guide_id`) REFERENCES `tour_guides` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `tour_guide_languages`
--
ALTER TABLE `tour_guide_languages`
  ADD CONSTRAINT `tour_guide_languages_ibfk_1` FOREIGN KEY (`registration_id`) REFERENCES `registration_tour_guide` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `tour_guide_profiles`
--
ALTER TABLE `tour_guide_profiles`
  ADD CONSTRAINT `tour_guide_profiles_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `tour_guide_reviews`
--
ALTER TABLE `tour_guide_reviews`
  ADD CONSTRAINT `tour_guide_reviews_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `tour_guide_reviews_ibfk_3` FOREIGN KEY (`tour_guide_id`) REFERENCES `tour_guides` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `user_favorites`
--
ALTER TABLE `user_favorites`
  ADD CONSTRAINT `fk_user_favorites_item` FOREIGN KEY (`item_id`) REFERENCES `tourist_spots` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_user_favorites_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `user_preferences`
--
ALTER TABLE `user_preferences`
  ADD CONSTRAINT `user_preferences_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `user_settings`
--
ALTER TABLE `user_settings`
  ADD CONSTRAINT `user_settings_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
