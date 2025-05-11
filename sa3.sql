-- phpMyAdmin SQL Dump
-- version 5.2.2
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: May 11, 2025 at 01:18 PM
-- Server version: 8.4.3
-- PHP Version: 8.3.16

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `sa3`
--

-- --------------------------------------------------------

--
-- Table structure for table `account_groups`
--

CREATE TABLE `account_groups` (
  `registration_id` int NOT NULL,
  `survey_account_id` varchar(64) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `account_groups`
--

INSERT INTO `account_groups` (`registration_id`, `survey_account_id`) VALUES
(147, 'RTK_112_1746552592'),
(148, 'RTK_148_1746632871'),
(149, 'RTK_148_1746632871'),
(150, 'RTK_148_1746632871'),
(153, 'RTK_153_1746719607'),
(193, 'RTK_153_1746719607'),
(154, 'RTK_154_1746719614'),
(185, 'RTK_154_1746719614'),
(193, 'RTK_154_1746719614'),
(155, 'RTK_155_1746719623'),
(193, 'RTK_155_1746719623'),
(156, 'RTK_156_1746719629'),
(193, 'RTK_156_1746719629'),
(157, 'RTK_157_1746719635'),
(193, 'RTK_157_1746719635'),
(158, 'RTK_158_1746719641'),
(193, 'RTK_158_1746719641'),
(159, 'RTK_159_1746719650'),
(169, 'RTK_159_1746719650'),
(177, 'RTK_159_1746719650'),
(182, 'RTK_159_1746719650'),
(193, 'RTK_159_1746719650'),
(160, 'RTK_160_1746719657'),
(169, 'RTK_160_1746719657'),
(177, 'RTK_160_1746719657'),
(182, 'RTK_160_1746719657'),
(188, 'RTK_160_1746719657'),
(193, 'RTK_160_1746719657'),
(161, 'RTK_161_1746719668'),
(169, 'RTK_161_1746719668'),
(177, 'RTK_161_1746719668'),
(180, 'RTK_161_1746719668'),
(182, 'RTK_161_1746719668'),
(188, 'RTK_161_1746719668'),
(193, 'RTK_161_1746719668'),
(198, 'RTK_161_1746719668'),
(200, 'RTK_161_1746719668'),
(205, 'RTK_161_1746719668'),
(162, 'RTK_162_1746719675'),
(169, 'RTK_162_1746719675'),
(177, 'RTK_162_1746719675'),
(180, 'RTK_162_1746719675'),
(188, 'RTK_162_1746719675'),
(194, 'RTK_162_1746719675'),
(198, 'RTK_162_1746719675'),
(202, 'RTK_162_1746719675'),
(205, 'RTK_162_1746719675'),
(147, 'RTK_61_1746444219'),
(147, 'RTK_64_1746457824'),
(147, 'RTK_68_1746510776'),
(147, 'RTK_69_1746511192'),
(147, 'RTK_70_1746511206'),
(147, 'RTK_78_1746549444'),
(147, 'RTK_93_1746551383');

-- --------------------------------------------------------

--
-- Table structure for table `activity_logs`
--

CREATE TABLE `activity_logs` (
  `id` int NOT NULL,
  `user_id` int DEFAULT NULL,
  `action` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `entity_type` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `entity_id` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `old_values` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin,
  `new_values` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin,
  `ip_address` varchar(45) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `user_agent` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `activity_logs`
--

INSERT INTO `activity_logs` (`id`, `user_id`, `action`, `entity_type`, `entity_id`, `old_values`, `new_values`, `ip_address`, `user_agent`, `created_at`) VALUES
(55, 91, 'login', 'user', '91', NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/122.0.0.0 Safari/537.36', '2025-05-05 17:49:27'),
(56, 91, 'purchase', 'registration', '46', NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/122.0.0.0 Safari/537.36', '2025-05-05 17:49:42'),
(57, 91, 'trial_activation', 'registration', '46', NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/122.0.0.0 Safari/537.36', '2025-05-05 17:49:43'),
(58, 91, 'purchase', 'registration', '47', NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/122.0.0.0 Safari/537.36', '2025-05-05 17:49:58'),
(59, 91, 'purchase', 'registration', '48', NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/122.0.0.0 Safari/537.36', '2025-05-05 17:50:28'),
(60, 91, 'purchase', 'registration', '49', NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/122.0.0.0 Safari/537.36', '2025-05-05 17:51:20'),
(61, 91, 'purchase', 'registration', '50', NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/122.0.0.0 Safari/537.36', '2025-05-05 17:51:47'),
(62, 91, 'purchase', 'registration', '51', NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/122.0.0.0 Safari/537.36', '2025-05-05 17:52:06'),
(63, 91, 'purchase', 'registration', '52', NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/122.0.0.0 Safari/537.36', '2025-05-05 17:53:27'),
(64, 91, 'trial_activation', 'registration', '52', NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/122.0.0.0 Safari/537.36', '2025-05-05 17:53:28'),
(65, 91, 'purchase', 'registration', '53', NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/122.0.0.0 Safari/537.36', '2025-05-05 17:57:37'),
(66, 91, 'purchase', 'registration', '54', NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/122.0.0.0 Safari/537.36', '2025-05-05 17:58:41'),
(67, 91, 'trial_activation', 'registration', '54', NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/122.0.0.0 Safari/537.36', '2025-05-05 17:58:42'),
(68, 91, 'purchase', 'registration', '55', NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/122.0.0.0 Safari/537.36', '2025-05-05 18:03:46'),
(69, 91, 'trial_activation', 'registration', '55', NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/122.0.0.0 Safari/537.36', '2025-05-05 18:03:47'),
(70, 91, 'purchase', 'registration', '56', NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/122.0.0.0 Safari/537.36', '2025-05-05 18:03:56'),
(71, 91, 'purchase', 'registration', '57', NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/122.0.0.0 Safari/537.36', '2025-05-05 18:10:28'),
(72, 91, 'purchase', 'registration', '58', NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/122.0.0.0 Safari/537.36', '2025-05-05 18:15:28'),
(73, 92, 'login', 'user', '92', NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/122.0.0.0 Safari/537.36', '2025-05-05 18:18:15'),
(74, 92, 'purchase', 'registration', '59', NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/122.0.0.0 Safari/537.36', '2025-05-05 18:18:30'),
(75, 92, 'login', 'user', '92', NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/122.0.0.0 Safari/537.36', '2025-05-05 18:21:39'),
(76, 92, 'purchase', 'registration', '60', NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/122.0.0.0 Safari/537.36', '2025-05-05 18:23:31'),
(77, 92, 'purchase', 'registration', '61', NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/122.0.0.0 Safari/537.36', '2025-05-05 18:23:38'),
(78, 92, 'trial_activation', 'registration', '61', NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/122.0.0.0 Safari/537.36', '2025-05-05 18:23:39'),
(79, 92, 'login', 'user', '92', NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/122.0.0.0 Safari/537.36', '2025-05-05 18:30:01'),
(80, 92, 'login', 'user', '92', NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/122.0.0.0 Safari/537.36', '2025-05-05 21:32:38'),
(81, 92, 'purchase', 'registration', '62', NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/122.0.0.0 Safari/537.36', '2025-05-05 21:38:36'),
(82, 92, 'purchase', 'registration', '63', NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/122.0.0.0 Safari/537.36', '2025-05-05 22:07:56'),
(83, 92, 'purchase', 'registration', '64', NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/122.0.0.0 Safari/537.36', '2025-05-05 22:10:23'),
(84, 92, 'trial_activation', 'registration', '64', NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/122.0.0.0 Safari/537.36', '2025-05-05 22:10:24'),
(85, 92, 'login', 'user', '92', NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/122.0.0.0 Safari/537.36', '2025-05-06 12:18:29'),
(86, 92, 'renewal_request', 'registration', '65', NULL, '{\"package_id\":1,\"accounts\":[\"RTK_64_1746457824\"],\"start_time\":\"2025-05-06 12:48:42\",\"end_time\":\"2025-06-05 12:48:42\",\"total_price\":100000}', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/122.0.0.0 Safari/537.36', '2025-05-06 12:48:42'),
(87, 92, 'renewal_request', 'registration', '66', NULL, '{\"package_id\":4,\"accounts\":[\"RTK_64_1746457824\"],\"start_time\":\"2025-05-06 12:48:58\",\"end_time\":\"2026-05-06 12:48:58\",\"total_price\":900000}', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/122.0.0.0 Safari/537.36', '2025-05-06 12:48:58'),
(88, 92, 'renewal_request', 'registration', '67', NULL, '{\"package_id\":2,\"accounts\":[\"RTK_64_1746457824\"],\"start_time\":\"2025-05-06 12:49:08\",\"end_time\":\"2025-08-04 12:49:08\",\"total_price\":270000}', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/122.0.0.0 Safari/537.36', '2025-05-06 12:49:08'),
(89, 92, 'purchase', 'registration', '68', NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/122.0.0.0 Safari/537.36', '2025-05-06 12:52:55'),
(90, 92, 'trial_activation', 'registration', '68', NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/122.0.0.0 Safari/537.36', '2025-05-06 12:52:56'),
(91, 92, 'purchase', 'registration', '69', NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/122.0.0.0 Safari/537.36', '2025-05-06 12:59:51'),
(92, 92, 'trial_activation', 'registration', '69', NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/122.0.0.0 Safari/537.36', '2025-05-06 12:59:52'),
(93, 92, 'purchase', 'registration', '70', NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/122.0.0.0 Safari/537.36', '2025-05-06 13:00:05'),
(94, 92, 'trial_activation', 'registration', '70', NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/122.0.0.0 Safari/537.36', '2025-05-06 13:00:06'),
(95, 92, 'renewal_request', 'registration', '71', NULL, '{\"package_id\":2,\"accounts\":[\"RTK_69_1746511192\",\"RTK_68_1746510776\"],\"start_time\":\"2025-05-06 13:00:26\",\"end_time\":\"2025-08-04 13:00:26\",\"total_price\":270000}', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/122.0.0.0 Safari/537.36', '2025-05-06 13:00:26'),
(96, 92, 'purchase', 'registration', '72', NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/122.0.0.0 Safari/537.36', '2025-05-06 13:04:38'),
(97, 92, 'login', 'user', '92', NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/122.0.0.0 Safari/537.36', '2025-05-06 21:31:08'),
(98, 92, 'purchase', 'registration', '73', NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/122.0.0.0 Safari/537.36', '2025-05-06 22:18:48'),
(99, 92, 'purchase', 'registration', '74', NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/122.0.0.0 Safari/537.36', '2025-05-06 23:36:08'),
(100, 92, 'purchase', 'registration', '75', NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/122.0.0.0 Safari/537.36', '2025-05-06 23:36:18'),
(101, 92, 'purchase', 'registration', '77', NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/122.0.0.0 Safari/537.36', '2025-05-06 23:36:47'),
(102, 92, 'purchase', 'registration', '78', NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/122.0.0.0 Safari/537.36', '2025-05-06 23:37:23'),
(103, 92, 'trial_activation', 'registration', '78', NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/122.0.0.0 Safari/537.36', '2025-05-06 23:37:24'),
(104, 92, 'purchase', 'registration', '86', NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/122.0.0.0 Safari/537.36', '2025-05-06 23:57:03'),
(105, 92, 'purchase', 'registration', '93', NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/122.0.0.0 Safari/537.36', '2025-05-07 00:09:42'),
(106, 92, 'trial_activation', 'registration', '93', NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/122.0.0.0 Safari/537.36', '2025-05-07 00:09:43'),
(107, 92, 'purchase', 'registration', '96', NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/122.0.0.0 Safari/537.36', '2025-05-07 00:13:19'),
(108, 92, 'purchase', 'registration', '98', NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/122.0.0.0 Safari/537.36', '2025-05-07 00:13:49'),
(109, 92, 'purchase', 'registration', '102', NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/122.0.0.0 Safari/537.36', '2025-05-07 00:20:17'),
(110, 92, 'renewal_request', 'registration', '106', NULL, '{\"registration_ids\":[\"106\"],\"selected_accounts\":[\"RTK_93_1746551383\"],\"total_price\":270000}', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/122.0.0.0 Safari/537.36', '2025-05-07 00:22:19'),
(111, 92, 'renewal_completed', 'registration', '106', NULL, '{\"accounts\":[\"RTK_93_1746551383\"],\"new_end_time\":\"2025-08-14 00:09:42\"}', '::1', NULL, '2025-05-07 00:22:25'),
(112, 92, 'renewal_request', 'registration', '109', NULL, '{\"registration_ids\":[\"107\",\"108\",\"109\"],\"selected_accounts\":[\"RTK_70_1746511206\",\"RTK_78_1746549444\",\"RTK_93_1746551383\"],\"total_price\":810000}', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/122.0.0.0 Safari/537.36', '2025-05-07 00:22:56'),
(113, 92, 'purchase', 'registration', '110', NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/122.0.0.0 Safari/537.36', '2025-05-07 00:28:33'),
(114, 92, 'purchase', 'registration', '111', NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/122.0.0.0 Safari/537.36', '2025-05-07 00:28:56'),
(115, 92, 'purchase', 'registration', '112', NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/122.0.0.0 Safari/537.36', '2025-05-07 00:29:51'),
(116, 92, 'trial_activation', 'registration', '112', NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/122.0.0.0 Safari/537.36', '2025-05-07 00:29:52'),
(117, 92, 'renewal_request', 'registration', '116', NULL, '{\"registration_ids\":[\"113\",\"114\",\"115\",\"116\"],\"selected_accounts\":[\"RTK_112_1746552592\",\"RTK_70_1746511206\",\"RTK_78_1746549444\",\"RTK_93_1746551383\"],\"total_price\":1140000}', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/122.0.0.0 Safari/537.36', '2025-05-07 00:30:11'),
(118, 92, 'login', 'user', '92', NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/122.0.0.0 Safari/537.36', '2025-05-07 06:44:27'),
(119, 92, 'renewal_request', 'registration', '118', NULL, '{\"registration_ids\":[\"117\",\"118\"],\"selected_accounts\":[\"RTK_112_1746552592\",\"RTK_93_1746551383\"],\"total_price\":540000}', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/122.0.0.0 Safari/537.36', '2025-05-07 06:44:37'),
(120, 92, 'renewal_request', 'registration', '125', NULL, '{\"registration_ids\":[\"119\",\"120\",\"121\",\"122\",\"123\",\"124\",\"125\"],\"selected_accounts\":[\"RTK_112_1746552592\",\"RTK_64_1746457824\",\"RTK_68_1746510776\",\"RTK_69_1746511192\",\"RTK_70_1746511206\",\"RTK_78_1746549444\",\"RTK_93_1746551383\"],\"total_price\":5100000}', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/122.0.0.0 Safari/537.36', '2025-05-07 06:45:31'),
(121, 92, 'renewal_request', 'registration', '126', NULL, '{\"registration_ids\":[\"126\"],\"selected_accounts\":[\"RTK_112_1746552592\"],\"total_price\":100000}', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/122.0.0.0 Safari/537.36', '2025-05-07 06:47:49'),
(122, 92, 'renewal_request', 'registration', '129', NULL, '{\"registration_ids\":[\"127\",\"128\",\"129\"],\"selected_accounts\":[\"RTK_112_1746552592\",\"RTK_78_1746549444\",\"RTK_93_1746551383\"],\"total_price\":1270000}', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/122.0.0.0 Safari/537.36', '2025-05-07 06:48:27'),
(123, 92, 'purchase', 'registration', '130', NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/122.0.0.0 Safari/537.36', '2025-05-07 06:55:23'),
(124, 92, 'purchase', 'registration', '131', NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/122.0.0.0 Safari/537.36', '2025-05-07 06:55:38'),
(125, 92, 'purchase', 'registration', '132', NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/122.0.0.0 Safari/537.36', '2025-05-07 06:56:10'),
(126, 92, 'renewal_request', 'registration', '133', NULL, '{\"registration_ids\":[\"133\"],\"selected_accounts\":[\"RTK_112_1746552592\"],\"total_price\":270000}', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/122.0.0.0 Safari/537.36', '2025-05-07 06:56:26'),
(127, 92, 'renewal_request', 'registration', '134', NULL, '{\"accounts\":{\"RTK_112_1746552592\":{\"account_id\":\"RTK_112_1746552592\",\"username\":\"TRIAL_YBI007\",\"num_account\":1,\"package_name\":\"G\\u00f3i 1 Th\\u00e1ng\",\"province\":\"Y\\u00ean B\\u00e1i\",\"start_time\":\"2025-05-14 00:29:51\",\"end_time\":\"2025-06-14 00:29:51\",\"location_id\":63}},\"package\":{\"id\":1,\"name\":\"G\\u00f3i 1 Th\\u00e1ng\",\"price\":\"100000.00\",\"duration_text\":\"\\/ 1 th\\u00e1ng\"},\"total_accounts\":1,\"total_price\":100000,\"timestamp\":1746576310}', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/122.0.0.0 Safari/537.36', '2025-05-07 07:05:10'),
(128, 92, 'purchase', 'registration', '135', NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/122.0.0.0 Safari/537.36', '2025-05-07 07:09:02'),
(129, 92, 'renewal_request', 'registration', '136', NULL, '{\"accounts\":{\"RTK_112_1746552592\":{\"account_id\":\"RTK_112_1746552592\",\"username\":\"TRIAL_YBI007\",\"num_account\":1,\"package_name\":\"G\\u00f3i 1 N\\u0103m\",\"province\":\"Y\\u00ean B\\u00e1i\",\"start_time\":\"2025-05-14 00:29:51\",\"end_time\":\"2026-05-14 00:29:51\",\"location_id\":63},\"RTK_78_1746549444\":{\"account_id\":\"RTK_78_1746549444\",\"username\":\"TRIAL_YBI005\",\"num_account\":1,\"package_name\":\"G\\u00f3i 1 N\\u0103m\",\"province\":\"Y\\u00ean B\\u00e1i\",\"start_time\":\"2025-05-13 23:37:23\",\"end_time\":\"2026-05-13 23:37:23\",\"location_id\":63},\"RTK_93_1746551383\":{\"account_id\":\"RTK_93_1746551383\",\"username\":\"TRIAL_YBI006\",\"num_account\":1,\"package_name\":\"G\\u00f3i 1 N\\u0103m\",\"province\":\"Y\\u00ean B\\u00e1i\",\"start_time\":\"2025-08-14 00:09:42\",\"end_time\":\"2026-08-14 00:09:42\",\"location_id\":63}},\"package\":{\"id\":4,\"name\":\"G\\u00f3i 1 N\\u0103m\",\"price\":\"900000.00\",\"duration_text\":\"\\/ 1 n\\u0103m\"},\"total_accounts\":3,\"total_price\":2700000,\"timestamp\":1746576552}', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/122.0.0.0 Safari/537.36', '2025-05-07 07:09:12'),
(130, 92, 'renewal_request', 'registration', '137', NULL, '{\"accounts\":{\"RTK_112_1746552592\":{\"account_id\":\"RTK_112_1746552592\",\"username\":\"TRIAL_YBI007\",\"num_account\":1,\"package_name\":\"G\\u00f3i 3 Th\\u00e1ng\",\"province\":\"Y\\u00ean B\\u00e1i\",\"start_time\":\"2025-05-14 00:29:51\",\"end_time\":\"2025-08-14 00:29:51\",\"location_id\":63}},\"package\":{\"id\":2,\"name\":\"G\\u00f3i 3 Th\\u00e1ng\",\"price\":\"270000.00\",\"duration_text\":\"\\/ 3 th\\u00e1ng\"},\"total_accounts\":1,\"total_price\":270000,\"timestamp\":1746576910}', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/122.0.0.0 Safari/537.36', '2025-05-07 07:15:10'),
(131, 92, 'renewal_request', 'registration', '138', NULL, '{\"registration_id\":\"138\",\"selected_accounts\":[\"RTK_112_1746552592\",\"RTK_78_1746549444\",\"RTK_93_1746551383\"],\"total_price\":300000,\"package\":\"G\\u00f3i 1 Th\\u00e1ng\"}', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/122.0.0.0 Safari/537.36', '2025-05-07 07:25:29'),
(132, 92, 'renewal_request', 'registration', '139', NULL, '{\"registration_id\":\"139\",\"selected_accounts\":[\"RTK_112_1746552592\",\"RTK_64_1746457824\",\"RTK_68_1746510776\",\"RTK_69_1746511192\",\"RTK_70_1746511206\",\"RTK_78_1746549444\",\"RTK_93_1746551383\"],\"total_price\":700000,\"package\":\"G\\u00f3i 1 Th\\u00e1ng\"}', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/122.0.0.0 Safari/537.36', '2025-05-07 07:26:49'),
(133, 92, 'login', 'user', '92', NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/122.0.0.0 Safari/537.36', '2025-05-07 21:55:33'),
(134, 92, 'renewal_request', 'registration', '140', NULL, '{\"registration_id\":\"140\",\"selected_accounts\":[\"RTK_112_1746552592\",\"RTK_93_1746551383\"],\"total_price\":540000,\"package\":\"G\\u00f3i 3 Th\\u00e1ng\"}', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/122.0.0.0 Safari/537.36', '2025-05-07 21:55:52'),
(135, 92, 'renewal_request', 'registration', '141', NULL, '{\"registration_id\":\"141\",\"selected_accounts\":[\"RTK_112_1746552592\",\"RTK_78_1746549444\",\"RTK_93_1746551383\"],\"total_price\":1500000,\"package\":\"G\\u00f3i 6 Th\\u00e1ng\"}', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/122.0.0.0 Safari/537.36', '2025-05-07 21:57:40'),
(136, 92, 'purchase', 'registration', '142', NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/122.0.0.0 Safari/537.36', '2025-05-07 21:59:27'),
(137, 92, 'purchase', 'registration', '143', NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/122.0.0.0 Safari/537.36', '2025-05-07 22:01:05'),
(138, 92, 'purchase', 'registration', '144', NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/122.0.0.0 Safari/537.36', '2025-05-07 22:01:23'),
(139, 92, 'purchase', 'registration', '145', NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/122.0.0.0 Safari/537.36', '2025-05-07 22:10:34'),
(140, 92, 'renewal_request', 'registration', '146', NULL, '{\"registration_id\":\"146\",\"selected_accounts\":[\"RTK_112_1746552592\",\"RTK_61_1746444219\",\"RTK_70_1746511206\",\"RTK_93_1746551383\"],\"total_price\":1080000,\"package\":\"G\\u00f3i 3 Th\\u00e1ng\"}', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/122.0.0.0 Safari/537.36', '2025-05-07 22:11:05'),
(141, 92, 'renewal_request', 'registration', '147', NULL, '{\"registration_id\":\"147\",\"selected_accounts\":[\"RTK_112_1746552592\",\"RTK_61_1746444219\",\"RTK_64_1746457824\",\"RTK_68_1746510776\",\"RTK_69_1746511192\",\"RTK_70_1746511206\",\"RTK_78_1746549444\",\"RTK_93_1746551383\"],\"total_price\":4000000,\"package\":\"G\\u00f3i 6 Th\\u00e1ng\"}', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/122.0.0.0 Safari/537.36', '2025-05-07 22:13:30'),
(142, 93, 'verification_email_sent', 'user', '93', NULL, '{\"email\":\"nguyendozxc20@gmail.com\",\"verification_token\":\"edd3ad19ea...\",\"timestamp\":\"2025-05-07 22:39:50\"}', '::1', NULL, '2025-05-07 22:39:50'),
(143, 93, 'email_verified', 'user', '93', NULL, '{\"status\":\"verified\",\"email\":\"nguyendozxc20@gmail.com\",\"timestamp\":\"2025-05-07 22:47:18\"}', '::1', NULL, '2025-05-07 22:47:18'),
(144, 93, 'login', 'user', '93', NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/122.0.0.0 Safari/537.36', '2025-05-07 22:47:29'),
(145, 93, 'purchase', 'registration', '148', NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/122.0.0.0 Safari/537.36', '2025-05-07 22:47:50'),
(146, 93, 'trial_activation', 'registration', '148', NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/122.0.0.0 Safari/537.36', '2025-05-07 22:47:51'),
(147, 93, 'renewal_request', 'registration', '149', NULL, '{\"registration_id\":\"149\",\"selected_accounts\":[\"RTK_148_1746632871\"],\"total_price\":270000,\"package\":\"G\\u00f3i 3 Th\\u00e1ng\"}', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/122.0.0.0 Safari/537.36', '2025-05-07 22:47:58'),
(148, 93, 'renewal_request', 'registration', '150', NULL, '{\"registration_id\":\"150\",\"selected_accounts\":[\"RTK_148_1746632871\"],\"total_price\":100000,\"package\":\"G\\u00f3i 1 Th\\u00e1ng\"}', '::1', 'Mozilla/5.0 (Linux; Android 6.0; Nexus 5 Build/MRA58N) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/122.0.0.0 Mobile Safari/537.36', '2025-05-07 23:51:38'),
(149, 93, 'login', 'user', '93', NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/122.0.0.0 Safari/537.36', '2025-05-08 08:41:34'),
(150, 93, 'login', 'user', '93', NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/122.0.0.0 Safari/537.36', '2025-05-08 21:46:03'),
(151, 93, 'purchase', 'registration', '151', NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/122.0.0.0 Safari/537.36', '2025-05-08 21:46:10'),
(152, 93, 'purchase', 'registration', '152', NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/122.0.0.0 Safari/537.36', '2025-05-08 21:50:28'),
(153, 93, 'purchase', 'registration', '153', NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/122.0.0.0 Safari/537.36', '2025-05-08 22:53:25'),
(154, 93, 'trial_activation', 'registration', '153', NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/122.0.0.0 Safari/537.36', '2025-05-08 22:53:28'),
(155, 93, 'purchase', 'registration', '154', NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/122.0.0.0 Safari/537.36', '2025-05-08 22:53:33'),
(156, 93, 'trial_activation', 'registration', '154', NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/122.0.0.0 Safari/537.36', '2025-05-08 22:53:34'),
(157, 93, 'purchase', 'registration', '155', NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/122.0.0.0 Safari/537.36', '2025-05-08 22:53:42'),
(158, 93, 'trial_activation', 'registration', '155', NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/122.0.0.0 Safari/537.36', '2025-05-08 22:53:43'),
(159, 93, 'purchase', 'registration', '156', NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/122.0.0.0 Safari/537.36', '2025-05-08 22:53:48'),
(160, 93, 'trial_activation', 'registration', '156', NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/122.0.0.0 Safari/537.36', '2025-05-08 22:53:49'),
(161, 93, 'purchase', 'registration', '157', NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/122.0.0.0 Safari/537.36', '2025-05-08 22:53:54'),
(162, 93, 'trial_activation', 'registration', '157', NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/122.0.0.0 Safari/537.36', '2025-05-08 22:53:55'),
(163, 93, 'purchase', 'registration', '158', NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/122.0.0.0 Safari/537.36', '2025-05-08 22:54:00'),
(164, 93, 'trial_activation', 'registration', '158', NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/122.0.0.0 Safari/537.36', '2025-05-08 22:54:01'),
(165, 93, 'purchase', 'registration', '159', NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/122.0.0.0 Safari/537.36', '2025-05-08 22:54:09'),
(166, 93, 'trial_activation', 'registration', '159', NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/122.0.0.0 Safari/537.36', '2025-05-08 22:54:10'),
(167, 93, 'purchase', 'registration', '160', NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/122.0.0.0 Safari/537.36', '2025-05-08 22:54:16'),
(168, 93, 'trial_activation', 'registration', '160', NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/122.0.0.0 Safari/537.36', '2025-05-08 22:54:17'),
(169, 93, 'purchase', 'registration', '161', NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/122.0.0.0 Safari/537.36', '2025-05-08 22:54:27'),
(170, 93, 'trial_activation', 'registration', '161', NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/122.0.0.0 Safari/537.36', '2025-05-08 22:54:28'),
(171, 93, 'purchase', 'registration', '162', NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/122.0.0.0 Safari/537.36', '2025-05-08 22:54:34'),
(172, 93, 'trial_activation', 'registration', '162', NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/122.0.0.0 Safari/537.36', '2025-05-08 22:54:35'),
(173, 93, 'login', 'user', '93', NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/122.0.0.0 Safari/537.36', '2025-05-09 09:06:11'),
(174, 93, 'login', 'user', '93', NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/122.0.0.0 Safari/537.36', '2025-05-10 08:40:33'),
(175, 93, 'purchase', 'registration', '163', NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/122.0.0.0 Safari/537.36', '2025-05-10 09:17:57'),
(176, 93, 'purchase', 'registration', '164', NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/122.0.0.0 Safari/537.36', '2025-05-10 09:31:12'),
(177, 93, 'purchase', 'registration', '165', NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/122.0.0.0 Safari/537.36', '2025-05-10 09:31:36'),
(178, 93, 'purchase', 'registration', '166', NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/122.0.0.0 Safari/537.36', '2025-05-10 09:35:06'),
(179, 93, 'purchase', 'registration', '167', NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/122.0.0.0 Safari/537.36', '2025-05-10 09:46:37'),
(180, 93, 'purchase', 'registration', '168', NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/122.0.0.0 Safari/537.36', '2025-05-10 09:54:37'),
(181, 93, 'renewal_request', 'registration', '169', NULL, '{\"registration_id\":\"169\",\"selected_accounts\":[\"RTK_159_1746719650\",\"RTK_160_1746719657\",\"RTK_161_1746719668\",\"RTK_162_1746719675\"],\"total_price\":400000,\"package\":\"G\\u00f3i 1 Th\\u00e1ng\"}', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/122.0.0.0 Safari/537.36', '2025-05-10 10:09:17'),
(182, 93, 'purchase', 'registration', '170', NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/122.0.0.0 Safari/537.36', '2025-05-10 10:09:37'),
(183, 93, 'purchase', 'registration', '171', NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/122.0.0.0 Safari/537.36', '2025-05-10 10:21:12'),
(184, 93, 'purchase', 'registration', '172', NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/122.0.0.0 Safari/537.36', '2025-05-10 10:21:48'),
(185, 93, 'purchase', 'registration', '173', NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/122.0.0.0 Safari/537.36', '2025-05-10 10:21:57'),
(186, 93, 'purchase', 'registration', '174', NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/122.0.0.0 Safari/537.36', '2025-05-10 10:25:39'),
(187, 93, 'purchase', 'registration', '175', NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/122.0.0.0 Safari/537.36', '2025-05-10 10:26:37'),
(188, 93, 'purchase', 'registration', '176', NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/122.0.0.0 Safari/537.36', '2025-05-10 10:35:44'),
(189, 93, 'renewal_request', 'registration', '177', NULL, '{\"registration_id\":\"177\",\"selected_accounts\":[\"RTK_159_1746719650\",\"RTK_160_1746719657\",\"RTK_161_1746719668\",\"RTK_162_1746719675\"],\"total_price\":1080000,\"package\":\"G\\u00f3i 3 Th\\u00e1ng\"}', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/122.0.0.0 Safari/537.36', '2025-05-10 10:37:31'),
(190, 93, 'purchase', 'registration', '178', NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/122.0.0.0 Safari/537.36', '2025-05-10 10:37:44'),
(191, 93, 'purchase', 'registration', '179', NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/122.0.0.0 Safari/537.36', '2025-05-10 10:57:11'),
(192, 93, 'renewal_request', 'registration', '180', NULL, '{\"registration_id\":\"180\",\"selected_accounts\":[\"RTK_161_1746719668\",\"RTK_162_1746719675\"],\"total_price\":540000,\"package\":\"G\\u00f3i 3 Th\\u00e1ng\"}', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/122.0.0.0 Safari/537.36', '2025-05-10 10:58:34'),
(193, 93, 'purchase', 'registration', '181', NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/122.0.0.0 Safari/537.36', '2025-05-10 11:01:06'),
(194, 93, 'renewal_request', 'registration', '182', NULL, '{\"registration_id\":\"182\",\"selected_accounts\":[\"RTK_159_1746719650\",\"RTK_160_1746719657\",\"RTK_161_1746719668\"],\"total_price\":810000,\"package\":\"G\\u00f3i 3 Th\\u00e1ng\"}', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/122.0.0.0 Safari/537.36', '2025-05-10 11:04:09'),
(195, 93, 'purchase', 'registration', '183', NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/122.0.0.0 Safari/537.36', '2025-05-10 11:14:22'),
(196, 93, 'purchase', 'registration', '184', NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/122.0.0.0 Safari/537.36', '2025-05-10 11:18:25'),
(197, 93, 'renewal_request', 'registration', '185', NULL, '{\"registration_id\":\"185\",\"selected_accounts\":[\"RTK_154_1746719614\"],\"total_price\":270000,\"package\":\"G\\u00f3i 3 Th\\u00e1ng\"}', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/122.0.0.0 Safari/537.36', '2025-05-10 11:23:04'),
(198, 93, 'purchase', 'registration', '186', NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/122.0.0.0 Safari/537.36', '2025-05-10 11:27:27'),
(199, 93, 'purchase', 'registration', '187', NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/122.0.0.0 Safari/537.36', '2025-05-10 11:29:02'),
(200, 93, 'renewal_request', 'registration', '188', NULL, '{\"registration_id\":\"188\",\"selected_accounts\":[\"RTK_160_1746719657\",\"RTK_161_1746719668\",\"RTK_162_1746719675\"],\"total_price\":810000,\"package\":\"G\\u00f3i 3 Th\\u00e1ng\"}', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/122.0.0.0 Safari/537.36', '2025-05-10 11:29:24'),
(201, 93, 'purchase', 'registration', '189', NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/122.0.0.0 Safari/537.36', '2025-05-10 11:36:35'),
(202, 93, 'purchase', 'registration', '190', NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/122.0.0.0 Safari/537.36', '2025-05-10 11:37:07'),
(203, 93, 'purchase', 'registration', '191', NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/122.0.0.0 Safari/537.36', '2025-05-10 11:39:56'),
(204, 93, 'purchase', 'registration', '192', NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/122.0.0.0 Safari/537.36', '2025-05-10 11:40:15'),
(205, 93, 'renewal_request', 'registration', '193', NULL, '{\"registration_id\":\"193\",\"selected_accounts\":[\"RTK_153_1746719607\",\"RTK_154_1746719614\",\"RTK_155_1746719623\",\"RTK_156_1746719629\",\"RTK_157_1746719635\",\"RTK_158_1746719641\",\"RTK_159_1746719650\",\"RTK_160_1746719657\",\"RTK_161_1746719668\"],\"total_price\":2430000,\"package\":\"G\\u00f3i 3 Th\\u00e1ng\"}', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/122.0.0.0 Safari/537.36', '2025-05-10 11:42:23'),
(206, 93, 'login', 'user', '93', NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/122.0.0.0 Safari/537.36', '2025-05-10 20:10:26'),
(207, 93, 'renewal_request', 'registration', '194', NULL, '{\"registration_id\":\"194\",\"selected_accounts\":[\"RTK_162_1746719675\"],\"total_price\":270000,\"package\":\"G\\u00f3i 3 Th\\u00e1ng\"}', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/122.0.0.0 Safari/537.36', '2025-05-10 20:10:34'),
(208, 93, 'purchase', 'registration', '195', NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/122.0.0.0 Safari/537.36', '2025-05-10 20:18:42'),
(209, 93, 'purchase', 'registration', '196', NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/122.0.0.0 Safari/537.36', '2025-05-10 20:27:52'),
(210, 93, 'purchase', 'registration', '197', NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/122.0.0.0 Safari/537.36', '2025-05-10 20:28:04'),
(211, 93, 'renewal_request', 'registration', '198', NULL, '{\"registration_id\":\"198\",\"selected_accounts\":[\"RTK_161_1746719668\",\"RTK_162_1746719675\"],\"total_price\":540000,\"package\":\"G\\u00f3i 3 Th\\u00e1ng\"}', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/122.0.0.0 Safari/537.36', '2025-05-10 20:28:15'),
(212, 93, 'purchase', 'registration', '199', NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/122.0.0.0 Safari/537.36', '2025-05-10 20:34:31'),
(213, 93, 'renewal_request', 'registration', '200', NULL, '{\"registration_id\":\"200\",\"selected_accounts\":[\"RTK_161_1746719668\"],\"total_price\":500000,\"package\":\"G\\u00f3i 6 Th\\u00e1ng\"}', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/122.0.0.0 Safari/537.36', '2025-05-10 20:34:50'),
(214, 93, 'purchase', 'registration', '201', NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/122.0.0.0 Safari/537.36', '2025-05-10 20:38:56'),
(215, 93, 'renewal_request', 'registration', '202', NULL, '{\"registration_id\":\"202\",\"selected_accounts\":[\"RTK_162_1746719675\"],\"total_price\":500000,\"package\":\"G\\u00f3i 6 Th\\u00e1ng\"}', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/122.0.0.0 Safari/537.36', '2025-05-10 20:40:03'),
(216, 93, 'purchase', 'registration', '203', NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/122.0.0.0 Safari/537.36', '2025-05-10 20:42:02'),
(217, 93, 'purchase', 'registration', '204', NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/122.0.0.0 Safari/537.36', '2025-05-10 20:46:57'),
(218, 94, 'verification_email_sent', 'user', '94', NULL, '{\"email\":\"onf52053@toaik.com\",\"verification_token\":\"bcf3432eb3...\",\"timestamp\":\"2025-05-10 21:01:02\"}', '::1', NULL, '2025-05-10 21:01:02'),
(219, 93, 'login', 'user', '93', NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/122.0.0.0 Safari/537.36', '2025-05-10 21:03:01'),
(220, 93, 'update_invoice_info', 'user', '93', NULL, NULL, NULL, NULL, '2025-05-10 21:14:34'),
(221, 93, 'renewal_request', 'registration', '205', NULL, '{\"registration_id\":\"205\",\"selected_accounts\":[\"RTK_161_1746719668\",\"RTK_162_1746719675\"],\"total_price\":540000,\"package\":\"G\\u00f3i 3 Th\\u00e1ng\"}', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/122.0.0.0 Safari/537.36', '2025-05-10 21:18:24'),
(222, 93, 'purchase', 'registration', '206', NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/122.0.0.0 Safari/537.36', '2025-05-10 21:26:49'),
(223, 93, 'create_support_request', 'support_requests', '1', NULL, NULL, NULL, NULL, '2025-05-10 23:28:10'),
(224, 93, 'login', 'user', '93', NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/122.0.0.0 Safari/537.36', '2025-05-11 10:14:43'),
(225, 93, 'login', 'user', '93', NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/122.0.0.0 Safari/537.36', '2025-05-11 10:50:24'),
(226, 95, 'verification_email_sent', 'user', '95', NULL, '{\"email\":\"kei65757@toaik.com\",\"verification_token\":\"364d9abff0...\",\"timestamp\":\"2025-05-11 10:51:24\"}', '::1', NULL, '2025-05-11 10:51:24'),
(227, 95, 'login', 'user', '95', NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/122.0.0.0 Safari/537.36', '2025-05-11 10:54:26'),
(228, 95, 'purchase', 'registration', '207', NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/122.0.0.0 Safari/537.36', '2025-05-11 10:54:38'),
(229, 95, 'purchase', 'registration', '208', NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/122.0.0.0 Safari/537.36', '2025-05-11 11:01:45'),
(230, 95, 'purchase', 'registration', '209', NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/122.0.0.0 Safari/537.36', '2025-05-11 11:04:58'),
(231, 95, 'purchase', 'registration', '210', NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/122.0.0.0 Safari/537.36', '2025-05-11 11:51:32'),
(232, 95, 'purchase', 'registration', '211', NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/122.0.0.0 Safari/537.36', '2025-05-11 11:57:10'),
(233, 95, 'purchase', 'registration', '212', NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/122.0.0.0 Safari/537.36', '2025-05-11 12:12:14'),
(234, 95, 'purchase', 'registration', '213', NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/122.0.0.0 Safari/537.36', '2025-05-11 12:25:42'),
(235, 96, 'verification_email_sent', 'user', '96', NULL, '{\"email\":\"addd@gmail.com\",\"verification_token\":\"95a07bd345...\",\"timestamp\":\"2025-05-11 14:44:32\"}', '::1', NULL, '2025-05-11 14:44:32'),
(236, 93, 'login', 'user', '93', NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/122.0.0.0 Safari/537.36', '2025-05-11 14:46:24'),
(237, 93, 'purchase', 'registration', '214', NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/122.0.0.0 Safari/537.36', '2025-05-11 14:58:24'),
(238, 95, 'login', 'user', '95', NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/122.0.0.0 Safari/537.36', '2025-05-11 18:03:47'),
(239, 95, 'purchase', 'registration', '215', NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/122.0.0.0 Safari/537.36', '2025-05-11 18:04:41'),
(240, 93, 'create_support_request', 'support_requests', '2', NULL, NULL, NULL, NULL, '2025-05-11 18:07:40'),
(241, 95, 'purchase', 'registration', '216', NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/122.0.0.0 Safari/537.36', '2025-05-11 18:08:43');

-- --------------------------------------------------------

--
-- Table structure for table `admin`
--

CREATE TABLE `admin` (
  `id` int NOT NULL,
  `name` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `admin_username` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `admin_password` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `role` enum('admin','customercare') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `admin`
--

INSERT INTO `admin` (`id`, `name`, `admin_username`, `admin_password`, `role`, `created_at`, `updated_at`) VALUES
(6, 'Nguyễn Văn A', 'ad', '$2y$10$rCSNWkHiNhtUcBL/.pFSMezmm0DKtzFivmm4AcmrTs5uvaNCXbqv2', 'admin', '2025-05-11 18:17:41', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `collaborator`
--

CREATE TABLE `collaborator` (
  `id` int NOT NULL,
  `user_id` int NOT NULL,
  `referral_code` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `status` enum('pending','approved','rejected') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT 'pending',
  `balance` decimal(15,2) DEFAULT '0.00',
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `company_info`
--

CREATE TABLE `company_info` (
  `id` int NOT NULL,
  `name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `address` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `phone` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `website` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `tax_code` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `description` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `working_hours` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `company_info`
--

INSERT INTO `company_info` (`id`, `name`, `address`, `phone`, `email`, `website`, `tax_code`, `description`, `working_hours`, `created_at`, `updated_at`) VALUES
(1, 'Công ty Cổ phần Công nghệ RTK', 'Tòa nhà Otek, 17 Duy Tân, Cầu Giấy, Hà Nội', '0981190564', 'support@rtktech.vn', 'https://rtktech.vn', '0109281282', 'Công ty chuyên cung cấp giải pháp đo đạc với công nghệ RTK hiện đại, chất lượng cao, đáng tin cậy.', 'Thứ 2 - Thứ 6: 8:00 - 17:30, Thứ 7: 8:00 - 12:00', '2025-05-10 23:16:25', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `error_logs`
--

CREATE TABLE `error_logs` (
  `id` int NOT NULL,
  `error_type` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `error_message` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `stack_trace` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `user_id` int DEFAULT NULL,
  `ip_address` varchar(45) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `guide`
--

CREATE TABLE `guide` (
  `id` int NOT NULL,
  `title` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `slug` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `content` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `author_id` int NOT NULL,
  `topic` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `status` enum('draft','published','archived') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'draft',
  `thumbnail` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `image` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `view_count` int NOT NULL DEFAULT '0',
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  `published_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `invoice`
--

CREATE TABLE `invoice` (
  `id` int NOT NULL,
  `transaction_history_id` int NOT NULL,
  `status` enum('pending','approved','rejected') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'pending',
  `invoice_file` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `rejected_reason` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `invoice`
--

INSERT INTO `invoice` (`id`, `transaction_history_id`, `status`, `invoice_file`, `rejected_reason`, `created_at`) VALUES
(4, 60, 'pending', NULL, NULL, '2025-05-05 18:23:47'),
(5, 59, 'pending', NULL, NULL, '2025-05-05 18:32:12'),
(6, 86, 'pending', NULL, NULL, '2025-05-07 00:04:26'),
(7, 158, 'pending', NULL, NULL, '2025-05-10 21:14:49');

-- --------------------------------------------------------

--
-- Table structure for table `location`
--

CREATE TABLE `location` (
  `id` int NOT NULL,
  `province` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `province_code` varchar(10) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `status` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `location`
--

INSERT INTO `location` (`id`, `province`, `province_code`, `status`, `created_at`) VALUES
(1, 'An Giang', 'AGG', 1, '2025-04-19 12:15:05'),
(2, 'Bà Rịa - Vũng Tàu', 'BVT', 1, '2025-04-19 12:15:05'),
(3, 'Bắc Giang', 'BGG', 1, '2025-04-19 12:15:05'),
(4, 'Bắc Kạn', 'BKN', 1, '2025-04-19 12:15:05'),
(5, 'Bạc Liêu', 'BLU', 1, '2025-04-19 12:15:05'),
(6, 'Bắc Ninh', 'BNH', 1, '2025-04-19 12:15:05'),
(7, 'Bến Tre', 'BTE', 1, '2025-04-19 12:15:05'),
(8, 'Bình Định', 'BDH', 1, '2025-04-19 12:15:05'),
(9, 'Bình Dương', 'BDG', 1, '2025-04-19 12:15:05'),
(10, 'Bình Phước', 'BPC', 1, '2025-04-19 12:15:05'),
(11, 'Bình Thuận', 'BTN', 1, '2025-04-19 12:15:05'),
(12, 'Cà Mau', 'CMA', 1, '2025-04-19 12:15:05'),
(13, 'Cần Thơ', 'CTO', 1, '2025-04-19 12:15:05'),
(14, 'Cao Bằng', 'CBG', 1, '2025-04-19 12:15:05'),
(15, 'Đà Nẵng', 'DNG', 1, '2025-04-19 12:15:05'),
(16, 'Đắk Lắk', 'DLK', 1, '2025-04-19 12:15:05'),
(17, 'Đắk Nông', 'DNG', 1, '2025-04-19 12:15:05'),
(18, 'Điện Biên', 'DBN', 1, '2025-04-19 12:15:05'),
(19, 'Đồng Nai', 'DNI', 1, '2025-04-19 12:15:05'),
(20, 'Đồng Tháp', 'DTP', 1, '2025-04-19 12:15:05'),
(21, 'Gia Lai', 'GLI', 1, '2025-04-19 12:15:05'),
(22, 'Hà Giang', 'HGG', 1, '2025-04-19 12:15:05'),
(23, 'Hà Nam', 'HNM', 1, '2025-04-19 12:15:05'),
(24, 'Hà Nội', 'HNI', 1, '2025-04-19 12:15:05'),
(25, 'Hà Tĩnh', 'HTH', 1, '2025-04-19 12:15:05'),
(26, 'Hải Dương', 'HDG', 1, '2025-04-19 12:15:05'),
(27, 'Hải Phòng', 'HPG', 1, '2025-04-19 12:15:05'),
(28, 'Hậu Giang', 'HGG', 1, '2025-04-19 12:15:05'),
(29, 'Hòa Bình', 'HBI', 1, '2025-04-19 12:15:05'),
(30, 'Hưng Yên', 'HYE', 1, '2025-04-19 12:15:05'),
(31, 'Khánh Hòa', 'KHA', 1, '2025-04-19 12:15:05'),
(32, 'Kiên Giang', 'KGG', 1, '2025-04-19 12:15:05'),
(33, 'Kon Tum', 'KTM', 1, '2025-04-19 12:15:05'),
(34, 'Lai Châu', 'LCH', 1, '2025-04-19 12:15:05'),
(35, 'Lâm Đồng', 'LDM', 1, '2025-04-19 12:15:05'),
(36, 'Lạng Sơn', 'LSN', 1, '2025-04-19 12:15:05'),
(37, 'Lào Cai', 'LCI', 1, '2025-04-19 12:15:05'),
(38, 'Long An', 'LAN', 1, '2025-04-19 12:15:05'),
(39, 'Nam Định', 'NDI', 1, '2025-04-19 12:15:05'),
(40, 'Nghệ An', 'NAN', 1, '2025-04-19 12:15:05'),
(41, 'Ninh Bình', 'NBI', 1, '2025-04-19 12:15:05'),
(42, 'Ninh Thuận', 'NNT', 1, '2025-04-19 12:15:05'),
(43, 'Phú Thọ', 'PTO', 1, '2025-04-19 12:15:05'),
(44, 'Phú Yên', 'PYE', 1, '2025-04-19 12:15:05'),
(45, 'Quảng Bình', 'QBN', 1, '2025-04-19 12:15:05'),
(46, 'Quảng Nam', 'QNM', 1, '2025-04-19 12:15:05'),
(47, 'Quảng Ngãi', 'QNG', 1, '2025-04-19 12:15:05'),
(48, 'Quảng Ninh', 'QNI', 1, '2025-04-19 12:15:05'),
(49, 'Quảng Trị', 'QTR', 1, '2025-04-19 12:15:05'),
(50, 'Sóc Trăng', 'STR', 1, '2025-04-19 12:15:05'),
(51, 'Sơn La', 'SLA', 1, '2025-04-19 12:15:05'),
(52, 'Tây Ninh', 'TNN', 1, '2025-04-19 12:15:05'),
(53, 'Thái Bình', 'TBI', 1, '2025-04-19 12:15:05'),
(54, 'Thái Nguyên', 'TNN', 1, '2025-04-19 12:15:05'),
(55, 'Thanh Hóa', 'THA', 1, '2025-04-19 12:15:05'),
(56, 'Thừa Thiên Huế', 'TTH', 1, '2025-04-19 12:15:05'),
(57, 'Tiền Giang', 'TYG', 1, '2025-04-19 12:15:05'),
(58, 'TP Hồ Chí Minh', 'HCM', 1, '2025-04-19 12:15:05'),
(59, 'Trà Vinh', 'TVI', 1, '2025-04-19 12:15:05'),
(60, 'Tuyên Quang', 'TQU', 1, '2025-04-19 12:15:05'),
(61, 'Vĩnh Long', 'VLO', 1, '2025-04-19 12:15:05'),
(62, 'Vĩnh Phúc', 'VPH', 1, '2025-04-19 12:15:05'),
(63, 'Yên Bái', 'YBI', 1, '2025-04-19 12:15:05');

-- --------------------------------------------------------

--
-- Table structure for table `manager`
--

CREATE TABLE `manager` (
  `id` int NOT NULL,
  `name` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Tên người quản lý',
  `phone` varchar(15) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'SĐT người quản lý',
  `address` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Địa chỉ người quản lý'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `manager`
--

INSERT INTO `manager` (`id`, `name`, `phone`, `address`) VALUES
(2, 'Trần Thị B', '0987654321', 'Hải Phòng'),
(5, 'Nguyễn Văn A', '1', '2');

-- --------------------------------------------------------

--
-- Table structure for table `mount_point`
--

CREATE TABLE `mount_point` (
  `id` varchar(64) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `location_id` int NOT NULL,
  `ip` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `port` int NOT NULL,
  `mountpoint` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `mount_point`
--

INSERT INTO `mount_point` (`id`, `location_id`, `ip`, `port`, `mountpoint`) VALUES
('18', 27, '203.171.25.138', 1509, 'HaiPhong'),
('19', 30, '203.171.25.138', 1509, 'HungYen'),
('41', 51, '203.171.25.138', 1509, 'SonLa'),
('44', 63, '203.171.25.138', 1509, 'YBI_TPYenBai'),
('45', 63, '203.171.25.138', 1509, 'YBI_NghiaLo'),
('46', 63, '203.171.25.138', 1509, 'YBI_LucYen'),
('47', 63, '203.171.25.138', 1509, 'YBI_TramTau'),
('48', 63, '203.171.25.138', 1509, 'YBI_VanYen'),
('49', 63, '203.171.25.138', 1509, 'YBI_MuCangChai'),
('50', 51, '203.171.25.138', 1509, 'SLA_TPSonLa'),
('51', 48, '203.171.25.138', 1509, 'QuangNinh'),
('54', 54, '203.171.25.138', 1509, 'TNN_TPThaiNguyen'),
('55', 54, '203.171.25.138', 1509, 'TNN_VoNhai'),
('56', 54, '203.171.25.138', 1509, 'TNN_DaiTu'),
('57', 54, '203.171.25.138', 1509, 'TNN_PhoYen'),
('58', 54, '203.171.25.138', 1509, 'TNN_DinhHoa'),
('59', 54, '203.171.25.138', 1509, 'ThaiNguyen'),
('60', 63, '203.171.25.138', 1509, 'YenBai'),
('61', 27, '203.171.25.138', 1509, 'HPG_VinhBao'),
('63', 24, '203.171.25.138', 1509, 'HaNoi'),
('64', 63, '203.171.25.138', 1509, 'YBI_XuanAI'),
('65', 51, '203.171.25.138', 1509, 'SLA_MocChau'),
('66', 27, '203.171.25.138', 1509, 'VpHaiPhong'),
('67', 27, '203.171.25.138', 1509, 'HPG_LeChan'),
('68', 16, '203.171.25.138', 1509, 'P5_HaNoi'),
('69', 24, '203.171.25.138', 1509, 'HNI_CauGiay'),
('70', 48, '203.171.25.138', 1509, 'QNH_HaLong'),
('71', 30, '203.171.25.138', 1509, 'HYN_AnThi');

-- --------------------------------------------------------

--
-- Table structure for table `package`
--

CREATE TABLE `package` (
  `id` int NOT NULL,
  `package_id` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `name` varchar(150) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `price` decimal(15,2) NOT NULL,
  `duration_text` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `features_json` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `is_recommended` tinyint(1) NOT NULL DEFAULT '0',
  `button_text` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'Chọn Gói',
  `savings_text` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `display_order` int NOT NULL DEFAULT '0',
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `package`
--

INSERT INTO `package` (`id`, `package_id`, `name`, `price`, `duration_text`, `features_json`, `is_recommended`, `button_text`, `savings_text`, `is_active`, `display_order`, `created_at`, `updated_at`) VALUES
(1, 'monthly', 'Gói 1 Tháng', 100000.00, '/ 1 tháng', '[{\"icon\":\"fa-check\",\"text\":\"Truy cập đầy đủ tính năng\",\"available\":true},{\"icon\":\"fa-check\",\"text\":\"Hỗ trợ cơ bản\",\"available\":true},{\"icon\":\"fa-check\",\"text\":\"10 lượt đo đạc / ngày\",\"available\":true}]', 0, 'Chọn Gói', NULL, 1, 10, '2025-04-19 12:14:35', '2025-04-19 15:39:26'),
(2, 'quarterly', 'Gói 3 Tháng', 270000.00, '/ 3 tháng', '[{\"icon\":\"fa-check\",\"text\":\"Truy cập đầy đủ tính năng\",\"available\":true},{\"icon\":\"fa-check\",\"text\":\"Hỗ trợ cơ bản\",\"available\":true},{\"icon\":\"fa-check\",\"text\":\"15 lượt đo đạc / ngày\",\"available\":true},{\"icon\":\"fa-check\",\"text\":\"Ưu tiên hỗ trợ thấp\",\"available\":true}]', 0, 'Chọn Gói', NULL, 1, 20, '2025-04-19 12:14:35', NULL),
(3, 'biannual', 'Gói 6 Tháng', 500000.00, '/ 6 tháng', '[{\"icon\":\"fa-check\",\"text\":\"Truy cập đầy đủ tính năng\",\"available\":true},{\"icon\":\"fa-check\",\"text\":\"Hỗ trợ tiêu chuẩn\",\"available\":true},{\"icon\":\"fa-check\",\"text\":\"25 lượt đo đạc / ngày\",\"available\":true},{\"icon\":\"fa-check\",\"text\":\"Ưu tiên hỗ trợ trung bình\",\"available\":true}]', 0, 'Chọn Gói', NULL, 1, 30, '2025-04-19 12:14:35', NULL),
(4, 'annual', 'Gói 1 Năm', 900000.00, '/ 1 năm', '[{\"icon\":\"fa-check\",\"text\":\"Truy cập đầy đủ tính năng\",\"available\":true},{\"icon\":\"fa-check\",\"text\":\"Hỗ trợ ưu tiên\",\"available\":true},{\"icon\":\"fa-check\",\"text\":\"50 lượt đo đạc / ngày\",\"available\":true},{\"icon\":\"fa-check\",\"text\":\"Ưu tiên hỗ trợ cao\",\"available\":true},{\"icon\":\"fa-check\",\"text\":\"Truy cập sớm tính năng mới\",\"available\":true}]', 1, 'Chọn Gói', NULL, 1, 40, '2025-04-19 12:14:35', '2025-04-19 15:56:38'),
(5, 'lifetime', 'Gói Vĩnh Viễn', 5000000.00, '/ trọn đời', '[{\"icon\":\"fa-check\",\"text\":\"Truy cập đầy đủ tính năng\",\"available\":true},{\"icon\":\"fa-check\",\"text\":\"Hỗ trợ VIP trọn đời\",\"available\":true},{\"icon\":\"fa-check\",\"text\":\"Không giới hạn lượt đo đạc\",\"available\":true},{\"icon\":\"fa-check\",\"text\":\"Ưu tiên hỗ trợ cao nhất\",\"available\":true},{\"icon\":\"fa-check\",\"text\":\"Mọi cập nhật trong tương lai\",\"available\":true}]', 0, 'Liên hệ mua', NULL, 1, 50, '2025-04-19 12:14:35', NULL),
(7, 'trial_7d', 'Gói Dùng Thử 7 Ngày', 0.00, '/ 7 ngày', '[{\"icon\":\"fa-check\",\"text\":\"Truy cập tính năng cơ bản\",\"available\":true},{\"icon\":\"fa-check\",\"text\":\"Hỗ trợ cộng đồng\",\"available\":true},{\"icon\":\"fa-check\",\"text\":\"5 lượt đo đạc / ngày\",\"available\":true}]', 0, 'Dùng Thử Miễn Phí', NULL, 1, 5, '2025-04-20 21:25:57', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `referral`
--

CREATE TABLE `referral` (
  `id` int NOT NULL,
  `user_id` int NOT NULL,
  `referral_code` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `referral`
--

INSERT INTO `referral` (`id`, `user_id`, `referral_code`, `created_at`, `updated_at`) VALUES
(1, 93, 'APGWBIJ2', '2025-05-11 03:36:59', '2025-05-11 03:36:59'),
(2, 95, '7WCUHN71', '2025-05-11 06:51:25', '2025-05-11 06:51:25');

-- --------------------------------------------------------

--
-- Table structure for table `referral_commission`
--

CREATE TABLE `referral_commission` (
  `id` int NOT NULL,
  `referrer_id` int NOT NULL,
  `referred_user_id` int NOT NULL,
  `transaction_id` int NOT NULL,
  `commission_amount` decimal(10,2) NOT NULL,
  `status` enum('pending','approved','paid','cancelled') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'pending',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `referral_commission`
--

INSERT INTO `referral_commission` (`id`, `referrer_id`, `referred_user_id`, `transaction_id`, `commission_amount`, `status`, `created_at`, `updated_at`) VALUES
(1, 93, 95, 203, 450000.00, 'approved', '2025-05-11 04:49:40', '2025-05-11 04:49:40'),
(2, 93, 95, 204, 25000.00, 'approved', '2025-05-11 04:49:40', '2025-05-11 04:49:40'),
(3, 93, 95, 205, 2775000.00, 'approved', '2025-05-11 04:49:40', '2025-05-11 04:49:40'),
(4, 93, 95, 207, 2220000.00, 'approved', '2025-05-11 05:14:52', '2025-05-11 05:14:52'),
(5, 93, 95, 208, 275000.00, 'approved', '2025-05-11 05:14:52', '2025-05-11 05:14:52'),
(6, 93, 95, 209, 170000.00, 'approved', '2025-05-11 05:26:20', '2025-05-11 05:26:20'),
(7, 93, 95, 211, 481000.00, 'approved', '2025-05-11 11:05:59', '2025-05-11 11:05:59'),
(8, 93, 95, 212, 4500000.00, 'approved', '2025-05-11 11:09:35', '2025-05-11 11:09:35');

-- --------------------------------------------------------

--
-- Table structure for table `referred_user`
--

CREATE TABLE `referred_user` (
  `id` int NOT NULL,
  `referrer_id` int NOT NULL,
  `referred_user_id` int NOT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `referred_user`
--

INSERT INTO `referred_user` (`id`, `referrer_id`, `referred_user_id`, `created_at`) VALUES
(1, 93, 95, '2025-05-11 03:51:24');

-- --------------------------------------------------------

--
-- Table structure for table `registration`
--

CREATE TABLE `registration` (
  `id` int NOT NULL,
  `user_id` int DEFAULT NULL,
  `package_id` int DEFAULT NULL,
  `location_id` int DEFAULT NULL,
  `collaborator_id` int DEFAULT NULL,
  `num_account` int DEFAULT NULL,
  `start_time` datetime DEFAULT NULL,
  `end_time` datetime DEFAULT NULL,
  `base_price` decimal(15,2) DEFAULT NULL,
  `vat_percent` float NOT NULL DEFAULT '0',
  `vat_amount` decimal(15,2) NOT NULL DEFAULT '0.00',
  `total_price` decimal(15,2) NOT NULL,
  `status` enum('pending','active','rejected') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'pending',
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  `deleted_at` datetime DEFAULT NULL,
  `rejection_reason` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `registration`
--

INSERT INTO `registration` (`id`, `user_id`, `package_id`, `location_id`, `collaborator_id`, `num_account`, `start_time`, `end_time`, `base_price`, `vat_percent`, `vat_amount`, `total_price`, `status`, `created_at`, `updated_at`, `deleted_at`, `rejection_reason`) VALUES
(55, 91, 7, 63, NULL, 1, '2025-05-05 18:03:46', '2025-05-12 18:03:46', 0.00, 0, 0.00, 0.00, 'active', '2025-05-05 18:03:46', '2025-05-05 18:03:47', NULL, NULL),
(56, 91, 7, 63, NULL, 1, '2025-05-05 18:03:56', '2025-05-12 18:03:56', 0.00, 0, 0.00, 0.00, 'pending', '2025-05-05 18:03:56', '2025-05-05 18:03:56', NULL, NULL),
(57, 91, 1, 63, NULL, 5, '2025-05-05 18:10:28', '2025-06-05 18:10:28', 100000.00, 0, 0.00, 500000.00, 'pending', '2025-05-05 18:10:28', '2025-05-05 18:10:28', NULL, NULL),
(58, 91, 1, 63, NULL, 5, '2025-05-05 18:15:28', '2025-06-05 18:15:28', 100000.00, 0, 0.00, 500000.00, 'pending', '2025-05-05 18:15:28', '2025-05-05 18:15:28', NULL, NULL),
(59, 92, 1, 63, NULL, 7, '2025-05-05 18:18:30', '2025-06-05 18:18:30', 100000.00, 0, 0.00, 700000.00, 'pending', '2025-05-05 18:18:30', '2025-05-05 18:18:30', NULL, NULL),
(60, 92, 7, 63, NULL, 1, '2025-05-05 18:23:31', '2025-05-12 18:23:31', 0.00, 0, 0.00, 0.00, 'pending', '2025-05-05 18:23:31', '2025-05-05 18:23:31', NULL, NULL),
(61, 92, 7, 54, NULL, 1, '2025-05-05 18:23:38', '2025-05-12 18:23:38', 0.00, 0, 0.00, 0.00, 'active', '2025-05-05 18:23:38', '2025-05-05 18:23:39', NULL, NULL),
(62, 92, 1, 63, NULL, 5, '2025-05-05 21:38:36', '2025-06-05 21:38:36', 100000.00, 0, 0.00, 500000.00, 'pending', '2025-05-05 21:38:36', '2025-05-05 22:06:23', NULL, 'test'),
(63, 92, 7, 63, NULL, 1, '2025-05-05 22:07:56', '2025-05-12 22:07:56', 0.00, 0, 0.00, 0.00, 'pending', '2025-05-05 22:07:56', '2025-05-05 22:07:56', NULL, NULL),
(64, 92, 7, 63, NULL, 1, '2025-05-05 22:10:23', '2025-05-12 22:10:23', 0.00, 0, 0.00, 0.00, 'active', '2025-05-05 22:10:23', '2025-05-05 22:10:24', NULL, NULL),
(65, 92, 1, 63, NULL, 1, '2025-05-06 12:48:42', '2025-06-05 12:48:42', 100000.00, 0, 0.00, 100000.00, 'pending', '2025-05-06 12:48:42', NULL, NULL, NULL),
(66, 92, 4, 63, NULL, 1, '2025-05-06 12:48:58', '2026-05-06 12:48:58', 900000.00, 0, 0.00, 900000.00, 'pending', '2025-05-06 12:48:58', NULL, NULL, NULL),
(67, 92, 2, 63, NULL, 1, '2025-05-06 12:49:08', '2025-08-04 12:49:08', 270000.00, 0, 0.00, 270000.00, 'pending', '2025-05-06 12:49:08', NULL, NULL, NULL),
(68, 92, 7, 63, NULL, 1, '2025-05-06 12:52:55', '2025-05-13 12:52:55', 0.00, 0, 0.00, 0.00, 'active', '2025-05-06 12:52:55', '2025-05-06 12:52:56', NULL, NULL),
(69, 92, 7, 63, NULL, 1, '2025-05-06 12:59:51', '2025-05-13 12:59:51', 0.00, 0, 0.00, 0.00, 'active', '2025-05-06 12:59:51', '2025-05-06 12:59:52', NULL, NULL),
(70, 92, 7, 54, NULL, 1, '2025-05-06 13:00:05', '2025-05-13 13:00:05', 0.00, 0, 0.00, 0.00, 'active', '2025-05-06 13:00:05', '2025-05-06 13:00:06', NULL, NULL),
(71, 92, 2, 63, NULL, 2, '2025-05-06 13:00:26', '2025-08-04 13:00:26', 270000.00, 0, 0.00, 270000.00, 'pending', '2025-05-06 13:00:26', NULL, NULL, NULL),
(72, 92, 1, 63, NULL, 5, '2025-05-06 13:04:38', '2025-06-06 13:04:38', 100000.00, 0, 0.00, 500000.00, 'pending', '2025-05-06 13:04:38', '2025-05-06 13:04:38', NULL, NULL),
(73, 92, 1, 63, NULL, 5, '2025-05-06 22:18:48', '2025-06-06 22:18:48', 100000.00, 0, 0.00, 500000.00, 'pending', '2025-05-06 22:18:48', '2025-05-06 22:18:48', NULL, NULL),
(74, 92, 7, 63, NULL, 1, '2025-05-06 23:36:08', '2025-05-13 23:36:08', 0.00, 0, 0.00, 0.00, 'pending', '2025-05-06 23:36:08', '2025-05-06 23:36:08', NULL, NULL),
(75, 92, 1, 63, NULL, 5, '2025-05-06 23:36:18', '2025-06-06 23:36:18', 100000.00, 0, 0.00, 500000.00, 'pending', '2025-05-06 23:36:18', '2025-05-06 23:36:18', NULL, NULL),
(77, 92, 7, 63, NULL, 1, '2025-05-06 23:36:47', '2025-05-13 23:36:47', 0.00, 0, 0.00, 0.00, 'pending', '2025-05-06 23:36:47', '2025-05-06 23:36:47', NULL, NULL),
(78, 92, 7, 63, NULL, 1, '2025-05-06 23:37:23', '2025-05-13 23:37:23', 0.00, 0, 0.00, 0.00, 'active', '2025-05-06 23:37:23', '2025-05-06 23:37:24', NULL, NULL),
(79, 92, 3, 63, NULL, 1, '2025-05-13 23:37:23', '2025-11-13 23:37:23', 500000.00, 0, 0.00, 500000.00, 'pending', '2025-05-06 23:52:18', '2025-05-06 23:52:18', NULL, NULL),
(80, 92, 1, 63, NULL, 1, '2025-05-13 12:52:55', '2025-06-13 12:52:55', 100000.00, 0, 0.00, 100000.00, 'pending', '2025-05-06 23:54:18', '2025-05-06 23:54:18', NULL, NULL),
(81, 92, 2, 63, NULL, 1, '2025-05-13 12:59:51', '2025-08-13 12:59:51', 270000.00, 0, 0.00, 270000.00, 'pending', '2025-05-06 23:54:18', '2025-05-06 23:54:18', NULL, NULL),
(82, 92, 3, 54, NULL, 1, '2025-05-13 13:00:05', '2025-11-13 13:00:05', 500000.00, 0, 0.00, 500000.00, 'pending', '2025-05-06 23:54:18', '2025-05-06 23:54:18', NULL, NULL),
(83, 92, 4, 63, NULL, 1, '2025-05-13 23:37:23', '2026-05-13 23:37:23', 900000.00, 0, 0.00, 900000.00, 'pending', '2025-05-06 23:54:18', '2025-05-06 23:54:18', NULL, NULL),
(84, 92, 1, 54, NULL, 1, '2025-05-13 13:00:05', '2025-06-13 13:00:05', 100000.00, 0, 0.00, 100000.00, 'pending', '2025-05-06 23:55:55', '2025-05-06 23:55:55', NULL, NULL),
(85, 92, 1, 63, NULL, 1, '2025-05-13 23:37:23', '2025-06-13 23:37:23', 100000.00, 0, 0.00, 100000.00, 'pending', '2025-05-06 23:55:55', '2025-05-06 23:55:55', NULL, NULL),
(86, 92, 1, 63, NULL, 5, '2025-05-06 23:57:03', '2025-06-06 23:57:03', 100000.00, 0, 0.00, 500000.00, 'pending', '2025-05-06 23:57:03', '2025-05-06 23:57:03', NULL, NULL),
(87, 92, 1, 54, NULL, 1, '2025-05-13 13:00:05', '2025-06-13 13:00:05', 100000.00, 0, 0.00, 100000.00, 'pending', '2025-05-06 23:57:21', '2025-05-06 23:57:21', NULL, NULL),
(88, 92, 1, 63, NULL, 1, '2025-05-13 23:37:23', '2025-06-13 23:37:23', 100000.00, 0, 0.00, 100000.00, 'pending', '2025-05-06 23:57:21', '2025-05-06 23:57:21', NULL, NULL),
(90, 92, 2, 63, NULL, 1, '2025-05-13 23:37:23', '2025-08-13 23:37:23', 270000.00, 0, 0.00, 270000.00, 'pending', '2025-05-07 00:09:00', '2025-05-07 00:09:00', NULL, NULL),
(91, 92, 3, 54, NULL, 1, '2025-05-13 13:00:05', '2025-11-13 13:00:05', 500000.00, 0, 0.00, 500000.00, 'pending', '2025-05-07 00:09:09', '2025-05-07 00:09:09', NULL, NULL),
(92, 92, 2, 63, NULL, 1, '2025-05-13 23:37:23', '2025-08-13 23:37:23', 270000.00, 0, 0.00, 270000.00, 'pending', '2025-05-07 00:09:09', '2025-05-07 00:09:09', NULL, NULL),
(93, 92, 7, 63, NULL, 1, '2025-05-07 00:09:42', '2025-05-14 00:09:42', 0.00, 0, 0.00, 0.00, 'active', '2025-05-07 00:09:42', '2025-05-07 00:09:43', NULL, NULL),
(94, 92, 2, 63, NULL, 1, '2025-05-13 23:37:23', '2025-08-13 23:37:23', 270000.00, 0, 0.00, 270000.00, 'pending', '2025-05-07 00:09:56', '2025-05-07 00:09:56', NULL, NULL),
(95, 92, 2, 63, NULL, 1, '2025-05-14 00:09:42', '2025-08-14 00:09:42', 270000.00, 0, 0.00, 270000.00, 'pending', '2025-05-07 00:09:56', '2025-05-07 00:09:56', NULL, NULL),
(96, 92, 1, 63, NULL, 5, '2025-05-07 00:13:19', '2025-06-07 00:13:19', 100000.00, 0, 0.00, 500000.00, 'pending', '2025-05-07 00:13:19', '2025-05-07 00:13:19', NULL, NULL),
(98, 92, 1, 63, NULL, 2, '2025-05-07 00:13:49', '2025-06-07 00:13:49', 100000.00, 0, 0.00, 200000.00, 'pending', '2025-05-07 00:13:49', '2025-05-07 00:13:49', NULL, NULL),
(99, 92, 2, 54, NULL, 1, '2025-05-13 13:00:05', '2025-08-13 13:00:05', 270000.00, 0, 0.00, 270000.00, 'pending', '2025-05-07 00:14:05', '2025-05-07 00:14:05', NULL, NULL),
(100, 92, 3, 63, NULL, 1, '2025-05-13 23:37:23', '2025-11-13 23:37:23', 500000.00, 0, 0.00, 500000.00, 'pending', '2025-05-07 00:14:05', '2025-05-07 00:14:05', NULL, NULL),
(101, 92, 2, 63, NULL, 1, '2025-05-14 00:09:42', '2025-08-14 00:09:42', 270000.00, 0, 0.00, 270000.00, 'pending', '2025-05-07 00:14:05', '2025-05-07 00:14:05', NULL, NULL),
(102, 92, 1, 63, NULL, 3, '2025-05-07 00:20:17', '2025-06-07 00:20:17', 100000.00, 0, 0.00, 300000.00, 'pending', '2025-05-07 00:20:17', '2025-05-07 00:20:17', NULL, NULL),
(103, 92, 2, 54, NULL, 1, '2025-05-13 13:00:05', '2025-08-13 13:00:05', 270000.00, 0, 0.00, 270000.00, 'pending', '2025-05-07 00:20:37', '2025-05-07 00:20:37', NULL, NULL),
(104, 92, 3, 63, NULL, 1, '2025-05-13 23:37:23', '2025-11-13 23:37:23', 500000.00, 0, 0.00, 500000.00, 'pending', '2025-05-07 00:20:37', '2025-05-07 00:20:37', NULL, NULL),
(105, 92, 2, 63, NULL, 1, '2025-05-14 00:09:42', '2025-08-14 00:09:42', 270000.00, 0, 0.00, 270000.00, 'pending', '2025-05-07 00:20:37', '2025-05-07 00:20:37', NULL, NULL),
(106, 92, 2, 63, NULL, 1, '2025-05-14 00:09:42', '2025-08-14 00:09:42', 270000.00, 0, 0.00, 270000.00, 'pending', '2025-05-07 00:22:19', '2025-05-07 00:22:19', NULL, NULL),
(107, 92, 2, 54, NULL, 1, '2025-05-13 13:00:05', '2025-08-13 13:00:05', 270000.00, 0, 0.00, 270000.00, 'pending', '2025-05-07 00:22:56', '2025-05-07 00:22:56', NULL, NULL),
(108, 92, 2, 63, NULL, 1, '2025-05-13 23:37:23', '2025-08-13 23:37:23', 270000.00, 0, 0.00, 270000.00, 'pending', '2025-05-07 00:22:56', '2025-05-07 00:22:56', NULL, NULL),
(109, 92, 2, 63, NULL, 1, '2025-08-14 00:09:42', '2025-11-14 00:09:42', 270000.00, 0, 0.00, 270000.00, 'pending', '2025-05-07 00:22:56', '2025-05-07 00:22:56', NULL, NULL),
(110, 92, 7, 63, NULL, 1, '2025-05-07 00:28:33', '2025-05-14 00:28:33', 0.00, 0, 0.00, 0.00, 'pending', '2025-05-07 00:28:33', '2025-05-07 00:28:33', NULL, NULL),
(111, 92, 7, 62, NULL, 1, '2025-05-07 00:28:56', '2025-05-14 00:28:56', 0.00, 0, 0.00, 0.00, 'pending', '2025-05-07 00:28:56', '2025-05-07 00:28:56', NULL, NULL),
(112, 92, 7, 63, NULL, 1, '2025-05-07 00:29:51', '2025-05-14 00:29:51', 0.00, 0, 0.00, 0.00, 'active', '2025-05-07 00:29:51', '2025-05-07 00:29:52', NULL, NULL),
(113, 92, 1, 63, NULL, 1, '2025-05-14 00:29:51', '2025-06-14 00:29:51', 100000.00, 0, 0.00, 100000.00, 'pending', '2025-05-07 00:30:11', '2025-05-07 00:30:11', NULL, NULL),
(114, 92, 2, 54, NULL, 1, '2025-05-13 13:00:05', '2025-08-13 13:00:05', 270000.00, 0, 0.00, 270000.00, 'pending', '2025-05-07 00:30:11', '2025-05-07 00:30:11', NULL, NULL),
(115, 92, 3, 63, NULL, 1, '2025-05-13 23:37:23', '2025-11-13 23:37:23', 500000.00, 0, 0.00, 500000.00, 'pending', '2025-05-07 00:30:11', '2025-05-07 00:30:11', NULL, NULL),
(116, 92, 2, 63, NULL, 1, '2025-08-14 00:09:42', '2025-11-14 00:09:42', 270000.00, 0, 0.00, 270000.00, 'pending', '2025-05-07 00:30:11', '2025-05-07 00:30:11', NULL, NULL),
(117, 92, 2, 63, NULL, 1, '2025-05-14 00:29:51', '2025-08-14 00:29:51', 270000.00, 0, 0.00, 270000.00, 'pending', '2025-05-07 06:44:37', '2025-05-07 06:44:37', NULL, NULL),
(118, 92, 2, 63, NULL, 1, '2025-08-14 00:09:42', '2025-11-14 00:09:42', 270000.00, 0, 0.00, 270000.00, 'pending', '2025-05-07 06:44:37', '2025-05-07 06:44:37', NULL, NULL),
(119, 92, 1, 63, NULL, 1, '2025-05-14 00:29:51', '2025-06-14 00:29:51', 100000.00, 0, 0.00, 100000.00, 'pending', '2025-05-07 06:45:31', '2025-05-07 06:45:31', NULL, NULL),
(120, 92, 4, 63, NULL, 1, '2025-05-07 06:45:31', '2026-05-07 06:45:31', 900000.00, 0, 0.00, 900000.00, 'pending', '2025-05-07 06:45:31', '2025-05-07 06:45:31', NULL, NULL),
(121, 92, 4, 63, NULL, 1, '2025-05-13 12:52:55', '2026-05-13 12:52:55', 900000.00, 0, 0.00, 900000.00, 'pending', '2025-05-07 06:45:31', '2025-05-07 06:45:31', NULL, NULL),
(122, 92, 4, 63, NULL, 1, '2025-05-13 12:59:51', '2026-05-13 12:59:51', 900000.00, 0, 0.00, 900000.00, 'pending', '2025-05-07 06:45:31', '2025-05-07 06:45:31', NULL, NULL),
(123, 92, 4, 54, NULL, 1, '2025-05-13 13:00:05', '2026-05-13 13:00:05', 900000.00, 0, 0.00, 900000.00, 'pending', '2025-05-07 06:45:31', '2025-05-07 06:45:31', NULL, NULL),
(124, 92, 3, 63, NULL, 1, '2025-05-13 23:37:23', '2025-11-13 23:37:23', 500000.00, 0, 0.00, 500000.00, 'pending', '2025-05-07 06:45:31', '2025-05-07 06:45:31', NULL, NULL),
(125, 92, 4, 63, NULL, 1, '2025-08-14 00:09:42', '2026-08-14 00:09:42', 900000.00, 0, 0.00, 900000.00, 'pending', '2025-05-07 06:45:31', '2025-05-07 06:45:31', NULL, NULL),
(126, 92, 1, 63, NULL, 1, '2025-05-14 00:29:51', '2025-06-14 00:29:51', 100000.00, 0, 0.00, 100000.00, 'pending', '2025-05-07 06:47:49', '2025-05-07 06:47:49', NULL, NULL),
(127, 92, 3, 63, NULL, 1, '2025-05-14 00:29:51', '2025-11-14 00:29:51', 500000.00, 0, 0.00, 500000.00, 'pending', '2025-05-07 06:48:27', '2025-05-07 06:48:27', NULL, NULL),
(128, 92, 3, 63, NULL, 1, '2025-05-13 23:37:23', '2025-11-13 23:37:23', 500000.00, 0, 0.00, 500000.00, 'pending', '2025-05-07 06:48:27', '2025-05-07 06:48:27', NULL, NULL),
(129, 92, 2, 63, NULL, 1, '2025-08-14 00:09:42', '2025-11-14 00:09:42', 270000.00, 0, 0.00, 270000.00, 'pending', '2025-05-07 06:48:27', '2025-05-07 06:48:27', NULL, NULL),
(130, 92, 1, 63, NULL, 5, '2025-05-07 06:55:23', '2025-06-07 06:55:23', 100000.00, 0, 0.00, 500000.00, 'pending', '2025-05-07 06:55:23', '2025-05-07 06:55:23', NULL, NULL),
(131, 92, 1, 63, NULL, 5, '2025-05-07 06:55:38', '2025-06-07 06:55:38', 100000.00, 0, 0.00, 500000.00, 'pending', '2025-05-07 06:55:38', '2025-05-07 06:55:38', NULL, NULL),
(132, 92, 1, 21, NULL, 6, '2025-05-07 06:56:10', '2025-06-07 06:56:10', 100000.00, 0, 0.00, 600000.00, 'pending', '2025-05-07 06:56:10', '2025-05-07 06:56:10', NULL, NULL),
(133, 92, 2, 63, NULL, 1, '2025-05-14 00:29:51', '2025-08-14 00:29:51', 270000.00, 0, 0.00, 270000.00, 'pending', '2025-05-07 06:56:26', '2025-05-07 06:56:26', NULL, NULL),
(134, 92, 1, 63, NULL, 1, '2025-05-07 07:05:10', '2025-06-14 00:29:51', 100000.00, 0, 0.00, 100000.00, 'pending', '2025-05-07 07:05:10', '2025-05-07 07:05:10', NULL, NULL),
(135, 92, 1, 63, NULL, 5, '2025-05-07 07:09:02', '2025-06-07 07:09:02', 100000.00, 0, 0.00, 500000.00, 'pending', '2025-05-07 07:09:02', '2025-05-07 07:09:02', NULL, NULL),
(136, 92, 4, 63, NULL, 3, '2025-05-07 07:09:12', '2026-05-14 00:29:51', 2700000.00, 0, 0.00, 2700000.00, 'pending', '2025-05-07 07:09:12', '2025-05-07 07:09:12', NULL, NULL),
(137, 92, 2, 63, NULL, 1, '2025-05-07 07:15:10', '2025-08-14 00:29:51', 270000.00, 0, 0.00, 270000.00, 'pending', '2025-05-07 07:15:10', '2025-05-07 07:15:10', NULL, NULL),
(138, 92, 1, 63, NULL, 3, '2025-05-14 00:29:51', '2025-06-14 00:29:51', 100000.00, 0, 0.00, 300000.00, 'pending', '2025-05-07 07:25:29', '2025-05-07 07:25:29', NULL, NULL),
(139, 92, 1, 63, NULL, 7, '2025-05-14 00:29:51', '2025-06-14 00:29:51', 100000.00, 0, 0.00, 700000.00, 'pending', '2025-05-07 07:26:49', '2025-05-07 07:26:49', NULL, NULL),
(140, 92, 2, 63, NULL, 2, '2025-05-14 00:29:51', '2025-08-14 00:29:51', 270000.00, 0, 0.00, 540000.00, 'pending', '2025-05-07 21:55:52', '2025-05-07 21:55:52', NULL, NULL),
(141, 92, 3, 63, NULL, 3, '2025-05-14 00:29:51', '2025-11-14 00:29:51', 500000.00, 0, 0.00, 1500000.00, 'pending', '2025-05-07 21:57:40', '2025-05-07 21:57:40', NULL, NULL),
(142, 92, 1, 62, NULL, 5, '2025-05-07 21:59:27', '2025-06-07 21:59:27', 100000.00, 0, 0.00, 500000.00, 'pending', '2025-05-07 21:59:27', '2025-05-07 21:59:27', NULL, NULL),
(143, 92, 1, 63, NULL, 7, '2025-05-07 22:01:05', '2025-06-07 22:01:05', 100000.00, 0, 0.00, 700000.00, 'pending', '2025-05-07 22:01:05', '2025-05-07 22:01:05', NULL, NULL),
(144, 92, 2, 63, NULL, 99, '2025-05-07 22:01:23', '2025-08-07 22:01:23', 270000.00, 0, 0.00, 26730000.00, 'pending', '2025-05-07 22:01:23', '2025-05-07 22:01:23', NULL, NULL),
(145, 92, 1, 63, NULL, 5, '2025-05-07 22:10:34', '2025-06-07 22:10:34', 100000.00, 0, 0.00, 500000.00, 'pending', '2025-05-07 22:10:34', '2025-05-07 22:10:34', NULL, NULL),
(146, 92, 2, 63, NULL, 4, '2025-05-14 00:29:51', '2025-08-14 00:29:51', 270000.00, 0, 0.00, 1080000.00, 'pending', '2025-05-07 22:11:05', '2025-05-07 22:11:05', NULL, NULL),
(147, 92, 3, 63, NULL, 8, '2025-05-14 00:29:51', '2025-11-14 00:29:51', 500000.00, 0, 0.00, 4000000.00, 'pending', '2025-05-07 22:13:30', '2025-05-07 22:13:30', NULL, NULL),
(148, 93, 7, 63, NULL, 1, '2025-05-07 22:47:50', '2025-05-14 22:47:50', 0.00, 0, 0.00, 0.00, 'active', '2025-05-07 22:47:50', '2025-05-07 22:47:51', NULL, NULL),
(149, 93, 2, 63, NULL, 1, '2025-05-14 22:47:50', '2025-08-14 22:47:50', 270000.00, 0, 0.00, 270000.00, 'pending', '2025-05-07 22:47:58', '2025-05-07 22:47:58', NULL, NULL),
(150, 93, 1, 63, NULL, 1, '2025-05-14 22:47:50', '2025-06-14 22:47:50', 100000.00, 0, 0.00, 100000.00, 'pending', '2025-05-07 23:51:38', '2025-05-07 23:51:38', NULL, NULL),
(151, 93, 1, 63, NULL, 2, '2025-05-08 21:46:10', '2025-06-08 21:46:10', 100000.00, 0, 0.00, 200000.00, 'pending', '2025-05-08 21:46:10', '2025-05-08 21:46:10', NULL, NULL),
(152, 93, 1, 63, NULL, 2, '2025-05-08 21:50:28', '2025-06-08 21:50:28', 100000.00, 0, 0.00, 200000.00, 'pending', '2025-05-08 21:50:28', '2025-05-08 21:50:28', NULL, NULL),
(153, 93, 7, 63, NULL, 1, '2025-05-08 22:53:25', '2025-05-15 22:53:25', 0.00, 0, 0.00, 0.00, 'active', '2025-05-08 22:53:25', '2025-05-08 22:53:28', NULL, NULL),
(154, 93, 7, 63, NULL, 1, '2025-05-08 22:53:33', '2025-05-15 22:53:33', 0.00, 0, 0.00, 0.00, 'active', '2025-05-08 22:53:33', '2025-05-08 22:53:34', NULL, NULL),
(155, 93, 7, 63, NULL, 1, '2025-05-08 22:53:42', '2025-05-15 22:53:42', 0.00, 0, 0.00, 0.00, 'active', '2025-05-08 22:53:42', '2025-05-08 22:53:43', NULL, NULL),
(156, 93, 7, 63, NULL, 1, '2025-05-08 22:53:48', '2025-05-15 22:53:48', 0.00, 0, 0.00, 0.00, 'active', '2025-05-08 22:53:48', '2025-05-08 22:53:49', NULL, NULL),
(157, 93, 7, 63, NULL, 1, '2025-05-08 22:53:54', '2025-05-15 22:53:54', 0.00, 0, 0.00, 0.00, 'active', '2025-05-08 22:53:54', '2025-05-08 22:53:55', NULL, NULL),
(158, 93, 7, 63, NULL, 1, '2025-05-08 22:54:00', '2025-05-15 22:54:00', 0.00, 0, 0.00, 0.00, 'active', '2025-05-08 22:54:00', '2025-05-08 22:54:01', NULL, NULL),
(159, 93, 7, 63, NULL, 1, '2025-05-08 22:54:09', '2025-05-15 22:54:09', 0.00, 0, 0.00, 0.00, 'active', '2025-05-08 22:54:09', '2025-05-08 22:54:10', NULL, NULL),
(160, 93, 7, 63, NULL, 1, '2025-05-08 22:54:16', '2025-05-15 22:54:16', 0.00, 0, 0.00, 0.00, 'active', '2025-05-08 22:54:16', '2025-05-08 22:54:17', NULL, NULL),
(161, 93, 7, 63, NULL, 1, '2025-05-08 22:54:27', '2025-05-15 22:54:27', 0.00, 0, 0.00, 0.00, 'active', '2025-05-08 22:54:27', '2025-05-08 22:54:28', NULL, NULL),
(162, 93, 7, 63, NULL, 1, '2025-05-08 22:54:34', '2025-05-15 22:54:34', 0.00, 0, 0.00, 0.00, 'active', '2025-05-08 22:54:34', '2025-05-08 22:54:35', NULL, NULL),
(163, 93, 1, 63, NULL, 5, '2025-05-10 09:17:57', '2025-06-10 09:17:57', 100000.00, 0, 0.00, 500000.00, 'pending', '2025-05-10 09:17:57', '2025-05-10 09:17:57', NULL, NULL),
(164, 93, 1, 63, NULL, 5, '2025-05-10 09:31:12', '2025-06-10 09:31:12', 100000.00, 0, 0.00, 500000.00, 'pending', '2025-05-10 09:31:12', '2025-05-10 09:31:12', NULL, NULL),
(165, 93, 1, 16, NULL, 5, '2025-05-10 09:31:36', '2025-06-10 09:31:36', 100000.00, 0, 0.00, 500000.00, 'pending', '2025-05-10 09:31:36', '2025-05-10 09:31:36', NULL, NULL),
(166, 93, 1, 63, NULL, 10, '2025-05-10 09:35:06', '2025-06-10 09:35:06', 100000.00, 0, 0.00, 1000000.00, 'pending', '2025-05-10 09:35:06', '2025-05-10 09:35:06', NULL, NULL),
(167, 93, 1, 63, NULL, 5, '2025-05-10 09:46:37', '2025-06-10 09:46:37', 100000.00, 0, 0.00, 500000.00, 'pending', '2025-05-10 09:46:37', '2025-05-10 09:46:37', NULL, NULL),
(168, 93, 1, 63, NULL, 5, '2025-05-10 09:54:37', '2025-06-10 09:54:37', 100000.00, 0, 0.00, 500000.00, 'pending', '2025-05-10 09:54:37', '2025-05-10 09:54:37', NULL, NULL),
(169, 93, 1, 63, NULL, 4, '2025-05-15 22:54:09', '2025-06-15 22:54:09', 100000.00, 0, 0.00, 400000.00, 'pending', '2025-05-10 10:09:17', '2025-05-10 10:09:17', NULL, NULL),
(170, 93, 4, 63, NULL, 6, '2025-05-10 10:09:37', '2026-05-10 10:09:37', 900000.00, 0, 0.00, 5400000.00, 'pending', '2025-05-10 10:09:37', '2025-05-10 10:09:37', NULL, NULL),
(171, 93, 1, 63, NULL, 16, '2025-05-10 10:21:12', '2025-06-10 10:21:12', 100000.00, 0, 0.00, 1600000.00, 'pending', '2025-05-10 10:21:12', '2025-05-10 10:21:12', NULL, NULL),
(172, 93, 1, 63, NULL, 16, '2025-05-10 10:21:48', '2025-06-10 10:21:48', 100000.00, 0, 0.00, 1600000.00, 'pending', '2025-05-10 10:21:48', '2025-05-10 10:21:48', NULL, NULL),
(173, 93, 2, 63, NULL, 5, '2025-05-10 10:21:57', '2025-08-10 10:21:57', 270000.00, 0, 0.00, 1350000.00, 'pending', '2025-05-10 10:21:57', '2025-05-10 10:21:57', NULL, NULL),
(174, 93, 2, 63, NULL, 10, '2025-05-10 10:25:39', '2025-08-10 10:25:39', 270000.00, 0, 0.00, 2700000.00, 'pending', '2025-05-10 10:25:39', '2025-05-10 10:25:39', NULL, NULL),
(175, 93, 2, 61, NULL, 7, '2025-05-10 10:26:37', '2025-08-10 10:26:37', 270000.00, 0, 0.00, 1890000.00, 'pending', '2025-05-10 10:26:37', '2025-05-10 10:26:37', NULL, NULL),
(176, 93, 2, 63, NULL, 9, '2025-05-10 10:35:44', '2025-08-10 10:35:44', 270000.00, 0, 0.00, 2430000.00, 'pending', '2025-05-10 10:35:44', '2025-05-10 10:35:44', NULL, NULL),
(177, 93, 2, 63, NULL, 4, '2025-05-15 22:54:09', '2025-08-15 22:54:09', 270000.00, 0, 0.00, 1080000.00, 'pending', '2025-05-10 10:37:31', '2025-05-10 10:37:31', NULL, NULL),
(178, 93, 4, 63, NULL, 5, '2025-05-10 10:37:44', '2026-05-10 10:37:44', 900000.00, 0, 0.00, 4500000.00, 'pending', '2025-05-10 10:37:44', '2025-05-10 10:37:44', NULL, NULL),
(179, 93, 2, 63, NULL, 5, '2025-05-10 10:57:11', '2025-08-10 10:57:11', 270000.00, 0, 0.00, 1350000.00, 'pending', '2025-05-10 10:57:11', '2025-05-10 10:57:11', NULL, NULL),
(180, 93, 2, 63, NULL, 2, '2025-05-15 22:54:27', '2025-08-15 22:54:27', 270000.00, 0, 0.00, 540000.00, 'pending', '2025-05-10 10:58:34', '2025-05-10 10:58:34', NULL, NULL),
(181, 93, 1, 17, NULL, 5, '2025-05-10 11:01:06', '2025-06-10 11:01:06', 100000.00, 0, 0.00, 500000.00, 'pending', '2025-05-10 11:01:06', '2025-05-10 11:01:06', NULL, NULL),
(182, 93, 2, 63, NULL, 3, '2025-05-15 22:54:09', '2025-08-15 22:54:09', 270000.00, 0, 0.00, 810000.00, 'pending', '2025-05-10 11:04:09', '2025-05-10 11:04:09', NULL, NULL),
(183, 93, 1, 63, NULL, 50, '2025-05-10 11:14:22', '2025-06-10 11:14:22', 100000.00, 0, 0.00, 5000000.00, 'pending', '2025-05-10 11:14:22', '2025-05-10 11:14:22', NULL, NULL),
(184, 93, 1, 63, NULL, 5, '2025-05-10 11:18:25', '2025-06-10 11:18:25', 100000.00, 0, 0.00, 500000.00, 'pending', '2025-05-10 11:18:25', '2025-05-10 11:18:25', NULL, NULL),
(185, 93, 2, 63, NULL, 1, '2025-05-15 22:53:33', '2025-08-15 22:53:33', 270000.00, 0, 0.00, 270000.00, 'pending', '2025-05-10 11:23:04', '2025-05-10 11:23:04', NULL, NULL),
(186, 93, 4, 63, NULL, 66, '2025-05-10 11:27:27', '2026-05-10 11:27:27', 900000.00, 0, 0.00, 59400000.00, 'pending', '2025-05-10 11:27:27', '2025-05-10 11:27:27', NULL, NULL),
(187, 93, 1, 63, NULL, 5, '2025-05-10 11:29:02', '2025-06-10 11:29:02', 100000.00, 0, 0.00, 500000.00, 'pending', '2025-05-10 11:29:02', '2025-05-10 11:29:02', NULL, NULL),
(188, 93, 2, 63, NULL, 3, '2025-05-15 22:54:16', '2025-08-15 22:54:16', 270000.00, 0, 0.00, 810000.00, 'pending', '2025-05-10 11:29:24', '2025-05-10 11:29:24', NULL, NULL),
(189, 93, 7, 63, NULL, 1, '2025-05-10 11:36:35', '2025-05-17 11:36:35', 0.00, 0, 0.00, 0.00, 'pending', '2025-05-10 11:36:35', '2025-05-10 11:36:35', NULL, NULL),
(190, 93, 2, 63, NULL, 1, '2025-05-10 11:37:07', '2025-08-10 11:37:07', 270000.00, 0, 0.00, 270000.00, 'pending', '2025-05-10 11:37:07', '2025-05-10 11:37:07', NULL, NULL),
(191, 93, 1, 63, NULL, 5, '2025-05-10 11:39:56', '2025-06-10 11:39:56', 100000.00, 0, 0.00, 500000.00, 'pending', '2025-05-10 11:39:56', '2025-05-10 11:39:56', NULL, NULL),
(192, 93, 1, 63, NULL, 5, '2025-05-10 11:40:15', '2025-06-10 11:40:15', 100000.00, 0, 0.00, 500000.00, 'pending', '2025-05-10 11:40:15', '2025-05-10 11:40:15', NULL, NULL),
(193, 93, 2, 63, NULL, 9, '2025-05-15 22:53:25', '2025-08-15 22:53:25', 270000.00, 0, 0.00, 2430000.00, 'pending', '2025-05-10 11:42:23', '2025-05-10 11:42:23', NULL, NULL),
(194, 93, 2, 63, NULL, 1, '2025-05-15 22:54:34', '2025-08-15 22:54:34', 270000.00, 0, 0.00, 270000.00, 'pending', '2025-05-10 20:10:34', '2025-05-10 20:10:34', NULL, NULL),
(195, 93, 1, 63, NULL, 5, '2025-05-10 20:18:42', '2025-06-10 20:18:42', 100000.00, 0, 0.00, 500000.00, 'pending', '2025-05-10 20:18:42', '2025-05-10 20:18:42', NULL, NULL),
(196, 93, 1, 63, NULL, 5, '2025-05-10 20:27:52', '2025-06-10 20:27:52', 100000.00, 0, 0.00, 500000.00, 'pending', '2025-05-10 20:27:52', '2025-05-10 20:27:52', NULL, NULL),
(197, 93, 1, 63, NULL, 5, '2025-05-10 20:28:04', '2025-06-10 20:28:04', 100000.00, 0, 0.00, 500000.00, 'pending', '2025-05-10 20:28:04', '2025-05-10 20:28:04', NULL, NULL),
(198, 93, 2, 63, NULL, 2, '2025-05-15 22:54:27', '2025-08-15 22:54:27', 270000.00, 0, 0.00, 540000.00, 'pending', '2025-05-10 20:28:15', '2025-05-10 20:28:15', NULL, NULL),
(199, 93, 2, 63, NULL, 5, '2025-05-10 20:34:31', '2025-08-10 20:34:31', 270000.00, 0, 0.00, 1350000.00, 'pending', '2025-05-10 20:34:31', '2025-05-10 20:34:31', NULL, NULL),
(200, 93, 3, 63, NULL, 1, '2025-05-15 22:54:27', '2025-11-15 22:54:27', 500000.00, 0, 0.00, 500000.00, 'pending', '2025-05-10 20:34:50', '2025-05-10 20:34:50', NULL, NULL),
(201, 93, 1, 63, NULL, 5, '2025-05-10 20:38:56', '2025-06-10 20:38:56', 100000.00, 0, 0.00, 500000.00, 'pending', '2025-05-10 20:38:56', '2025-05-10 20:38:56', NULL, NULL),
(202, 93, 3, 63, NULL, 1, '2025-05-15 22:54:34', '2025-11-15 22:54:34', 500000.00, 0, 0.00, 500000.00, 'pending', '2025-05-10 20:40:03', '2025-05-10 20:40:03', NULL, NULL),
(203, 93, 1, 63, NULL, 5, '2025-05-10 20:42:02', '2025-06-10 20:42:02', 100000.00, 0, 0.00, 500000.00, 'pending', '2025-05-10 20:42:02', '2025-05-10 20:42:02', NULL, NULL),
(204, 93, 1, 63, NULL, 5, '2025-05-10 20:46:57', '2025-06-10 20:46:57', 100000.00, 0, 0.00, 500000.00, 'pending', '2025-05-10 20:46:57', '2025-05-10 20:46:57', NULL, NULL),
(205, 93, 2, 63, NULL, 2, '2025-05-15 22:54:27', '2025-08-15 22:54:27', 270000.00, 0, 0.00, 540000.00, 'pending', '2025-05-10 21:18:24', '2025-05-10 21:18:24', NULL, NULL),
(206, 93, 1, 63, NULL, 15, '2025-05-10 21:26:49', '2025-06-10 21:26:49', 100000.00, 0, 0.00, 1500000.00, 'pending', '2025-05-10 21:26:49', '2025-05-10 21:26:49', NULL, NULL),
(207, 95, 4, 63, NULL, 10, '2025-05-11 10:54:38', '2026-05-11 10:54:38', 900000.00, 0, 0.00, 9000000.00, 'pending', '2025-05-11 10:54:38', '2025-05-11 10:54:38', NULL, NULL),
(208, 95, 1, 63, NULL, 5, '2025-05-11 11:01:45', '2025-06-11 11:01:45', 100000.00, 0, 0.00, 500000.00, 'pending', '2025-05-11 11:01:45', '2025-05-11 11:01:45', NULL, NULL),
(209, 95, 1, 63, NULL, 555, '2025-05-11 11:04:58', '2025-06-11 11:04:58', 100000.00, 0, 0.00, 55500000.00, 'pending', '2025-05-11 11:04:58', '2025-05-11 11:04:58', NULL, NULL),
(210, 95, 4, 63, NULL, 55, '2025-05-11 11:51:32', '2026-05-11 11:51:32', 900000.00, 0, 0.00, 49500000.00, 'pending', '2025-05-11 11:51:32', '2025-05-11 11:51:32', NULL, NULL),
(211, 95, 1, 63, NULL, 444, '2025-05-11 11:57:10', '2025-06-11 11:57:10', 100000.00, 0, 0.00, 44400000.00, 'pending', '2025-05-11 11:57:10', '2025-05-11 11:57:10', NULL, NULL),
(212, 95, 1, 18, NULL, 55, '2025-05-11 12:12:14', '2025-06-11 12:12:14', 100000.00, 0, 0.00, 5500000.00, 'pending', '2025-05-11 12:12:14', '2025-05-11 12:12:14', NULL, NULL),
(213, 95, 1, 51, NULL, 34, '2025-05-11 12:25:42', '2025-06-11 12:25:42', 100000.00, 0, 0.00, 3400000.00, 'pending', '2025-05-11 12:25:42', '2025-05-11 12:25:42', NULL, NULL),
(214, 93, 3, 63, NULL, 6, '2025-05-11 14:58:24', '2025-11-11 14:58:24', 500000.00, 0, 0.00, 3000000.00, 'pending', '2025-05-11 14:58:24', '2025-05-11 14:58:24', NULL, NULL),
(215, 95, 4, 63, NULL, 10, '2025-05-11 18:04:41', '2026-05-11 18:04:41', 900000.00, 0, 0.00, 9000000.00, 'pending', '2025-05-11 18:04:41', '2025-05-11 18:04:41', NULL, NULL),
(216, 95, 4, 63, NULL, 100, '2025-05-11 18:08:43', '2026-05-11 18:08:43', 900000.00, 0, 0.00, 90000000.00, 'pending', '2025-05-11 18:08:43', '2025-05-11 18:08:43', NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `role_permissions`
--

CREATE TABLE `role_permissions` (
  `role` enum('admin','customercare') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `permission` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `allowed` tinyint(1) NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `role_permissions`
--

INSERT INTO `role_permissions` (`role`, `permission`, `allowed`) VALUES
('admin', 'account_management', 1),
('admin', 'admin_user_create', 1),
('admin', 'dashboard', 1),
('admin', 'guide_management', 1),
('admin', 'invoice_management', 1),
('admin', 'invoice_review', 1),
('admin', 'permission_edit', 1),
('admin', 'permission_management', 1),
('admin', 'referral_management', 1),
('admin', 'reports', 1),
('admin', 'revenue_management', 1),
('admin', 'settings', 1),
('admin', 'station_management', 1),
('admin', 'user_create', 1),
('admin', 'user_management', 1),
('customercare', 'dashboard', 1),
('customercare', 'invoice_management', 0),
('customercare', 'user_management', 1);

-- --------------------------------------------------------

--
-- Table structure for table `station`
--

CREATE TABLE `station` (
  `id` varchar(64) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `station_name` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `identificationName` varchar(254) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `mountpoint_id` varchar(64) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `lat` decimal(10,8) NOT NULL,
  `long` decimal(11,8) NOT NULL,
  `status` tinyint(1) NOT NULL DEFAULT '1' COMMENT 'Trạng thái hoạt động của trạm (1: active, 0: inactive)',
  `manager_id` int DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `station`
--

INSERT INTO `station` (`id`, `station_name`, `identificationName`, `mountpoint_id`, `lat`, `long`, `status`, `manager_id`) VALUES
('25', 'HPG1', 'VP Hải Phòng - 136 Dương Đình Nghệ', '67', 20.83045360, 106.67064669, 1, NULL),
('26', 'HPG2', 'Nhà a Cương - Vĩnh Bảo|Hưng - 0379527233', '61', 20.71929979, 106.46123062, 1, NULL),
('28', 'QNH5', 'Nhà cậu a Dũng - Hạ Long|Toản - 0934496886', '70', 20.94945693, 107.10729783, 1, NULL),
('30', 'HYN1', 'Nhà a Hữu - bạn a Dũng - Ân Thi|A Hữu - 0979514287', '71', 20.82821906, 106.08995397, 1, NULL),
('34', 'HNI0', 'VP Hà Nội - 216 Trung Kính', '69', 21.02222257, 105.79144295, 1, NULL),
('37', 'SLA2', 'Nhà a Trung - Mộc Châu - Sơn La|A Trung - 0976698698', '65', 20.84684019, 104.63939672, 1, NULL),
('38', 'YBI4', 'Vp ban quản lý Nghĩa Lộ|Anh Hiếu - 0865251185', '45', 21.59752950, 104.51231468, 1, NULL),
('41', 'YBI6', 'Nhà con gái a Tuấn - Trạm Tấu|A Tuấn - 0859596050', '47', 21.46597839, 104.38110734, 1, NULL),
('42', 'YBI5', 'VP ban quan lý dự án Lục Yên|Hải - 0378639689', '46', 22.11158723, 104.76653605, 1, NULL),
('43', 'YBI8', 'Nhà anh Tuân - Mù Cang Chải|A Tuân - 0987019029', '49', 21.84980281, 104.08574760, 1, NULL),
('44', 'YBI1', 'Nhà a Việt Béo - TP Yên bái|A Việt - 0947366066', '44', 21.71137621, 104.90685342, 1, NULL),
('45', 'YBI7', 'Nhà anh Chiến - Văn Yên|A Chiến - 0977886300', '48', 21.96978018, 104.56571434, 1, NULL),
('49', 'TNN1', 'Nhà A Hưng râu - TP Thái Nguyên|A Hưng - 0982892196', '54', 21.57363716, 105.84031625, 1, NULL),
('50', 'SL12', 'Nhà anh Tân - Tp Son La| Anh Tân - 0335027798', '50', 21.32205139, 103.91523536, 1, NULL),
('53', 'TNN6', 'Nhà anh Phú - Định hoá| a Phú - 0867666929', '58', 21.91116659, 105.64929213, 1, NULL),
('54', 'TNN4', 'vpdkdd Đại Từ|Đỗ Đình Long - 0355055740', '56', 21.63442798, 105.63582762, 1, NULL),
('55', 'TNN3', 'Nhà anh Thoi - Võ Nhai|Anh Việt - 0353177492', '55', 21.75411886, 106.07746349, 1, NULL),
('56', 'TNN5', 'Nhà anh Long - Phổ Yên|Anh Long - 0986650808', '57', 21.41624565, 105.86203136, 1, NULL),
('57', 'YBI2', 'Nhà anh Tuấn - Văn Yên|Anh Tuấn - 0963844634', '64', 21.85003200, 104.70568793, 1, NULL),
('58', 'G216', 'Trạm a Hưng gửi', NULL, 20.96750510, 106.71247261, 0, NULL),
('59', 'P501', 'tram Ha Noi', '69', 21.02222257, 105.79144295, 1, NULL),
('61', 'PYN5', 'Tuy Hòa| Anh Thịnh - 0856036778', NULL, 13.10045080, 109.31209672, 1, NULL),
('62', 'PYN4', 'Tuy Hòa ex| Anh Thịnh - 0856036778', NULL, 13.09002297, 109.28998055, 1, NULL),
('63', 'PYN3', 'Tuy An| Anh Thịnh - 0856036778', NULL, 13.33943150, 109.20787570, 1, NULL),
('64', 'PYN2', 'Tây Hòa| Anh Thịnh - 0856036778', NULL, 12.98001507, 109.22999187, 0, NULL),
('65', 'PYN1', 'Sơn Hòa| Anh Thịnh - 0856036778', NULL, 13.04000376, 108.98001038, 1, NULL),
('66', 'HPG3', 'TRạm test móc dữ liệu từ sv WIndow', NULL, 20.81446808, 106.68709058, 0, NULL),
('67', 'HNI1', 'Test móc dữ liệu ra ', NULL, 21.02239446, 105.81017867, 1, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `support_requests`
--

CREATE TABLE `support_requests` (
  `id` int NOT NULL,
  `user_id` int NOT NULL,
  `subject` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `message` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `category` enum('technical','billing','account','other') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'other',
  `status` enum('pending','in_progress','resolved','closed') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'pending',
  `admin_response` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `support_requests`
--

INSERT INTO `support_requests` (`id`, `user_id`, `subject`, `message`, `category`, `status`, `admin_response`, `created_at`, `updated_at`) VALUES
(1, 93, 'Không thể kết nối tới vps', 'ff', 'technical', 'pending', NULL, '2025-05-10 23:28:10', NULL),
(2, 93, 'test', 'aa', 'billing', 'resolved', 'test2', '2025-05-11 18:07:40', '2025-05-11 18:08:13');

-- --------------------------------------------------------

--
-- Table structure for table `survey_account`
--

CREATE TABLE `survey_account` (
  `id` varchar(64) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `registration_id` int NOT NULL,
  `start_time` datetime DEFAULT NULL,
  `end_time` datetime DEFAULT NULL,
  `username_acc` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `password_acc` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `concurrent_user` int DEFAULT '1',
  `enabled` tinyint(1) DEFAULT '1',
  `caster` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `user_type` int DEFAULT NULL,
  `regionIds` int DEFAULT NULL,
  `customerBizType` int DEFAULT '1',
  `area` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  `deleted_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `survey_account`
--

INSERT INTO `survey_account` (`id`, `registration_id`, `start_time`, `end_time`, `username_acc`, `password_acc`, `concurrent_user`, `enabled`, `caster`, `user_type`, `regionIds`, `customerBizType`, `area`, `created_at`, `updated_at`, `deleted_at`) VALUES
('RTK_112_1746552592', 112, '2025-05-07 00:29:51', '2025-05-14 00:29:51', 'TRIAL_YBI007', '0981190562', 1, 1, NULL, NULL, NULL, 1, NULL, '2025-05-07 00:29:52', NULL, NULL),
('RTK_148_1746632871', 148, '2025-05-07 22:47:50', '2025-05-14 22:47:50', 'TRIAL_YBI008', '0981190522', 1, 1, NULL, NULL, NULL, 1, NULL, '2025-05-07 22:47:51', NULL, NULL),
('RTK_153_1746719607', 153, '2025-05-08 22:53:25', '2025-05-15 22:53:25', 'TRIAL_YBI009', '0981190522', 1, 1, NULL, NULL, NULL, 1, NULL, '2025-05-08 22:53:27', NULL, NULL),
('RTK_154_1746719614', 154, '2025-05-08 22:53:33', '2025-05-15 22:53:33', 'TRIAL_YBI010', '0981190522', 1, 1, NULL, NULL, NULL, 1, NULL, '2025-05-08 22:53:34', NULL, NULL),
('RTK_155_1746719623', 155, '2025-05-08 22:53:42', '2025-05-15 22:53:42', 'TRIAL_YBI011', '0981190522', 1, 1, NULL, NULL, NULL, 1, NULL, '2025-05-08 22:53:43', NULL, NULL),
('RTK_156_1746719629', 156, '2025-05-08 22:53:48', '2025-05-15 22:53:48', 'TRIAL_YBI012', '0981190522', 1, 1, NULL, NULL, NULL, 1, NULL, '2025-05-08 22:53:49', NULL, NULL),
('RTK_157_1746719635', 157, '2025-05-08 22:53:54', '2025-05-15 22:53:54', 'TRIAL_YBI013', '0981190522', 1, 1, NULL, NULL, NULL, 1, NULL, '2025-05-08 22:53:55', NULL, NULL),
('RTK_158_1746719641', 158, '2025-05-08 22:54:00', '2025-05-15 22:54:00', 'TRIAL_YBI014', '0981190522', 1, 1, NULL, NULL, NULL, 1, NULL, '2025-05-08 22:54:01', NULL, NULL),
('RTK_159_1746719650', 159, '2025-05-08 22:54:09', '2025-05-15 22:54:09', 'TRIAL_YBI015', '0981190522', 1, 1, NULL, NULL, NULL, 1, NULL, '2025-05-08 22:54:10', NULL, NULL),
('RTK_160_1746719657', 160, '2025-05-08 22:54:16', '2025-05-15 22:54:16', 'TRIAL_YBI016', '0981190522', 1, 1, NULL, NULL, NULL, 1, NULL, '2025-05-08 22:54:17', NULL, NULL),
('RTK_161_1746719668', 161, '2025-05-08 22:54:27', '2025-05-15 22:54:27', 'TRIAL_YBI017', '0981190522', 1, 1, NULL, NULL, NULL, 1, NULL, '2025-05-08 22:54:28', NULL, NULL),
('RTK_162_1746719675', 162, '2025-05-08 22:54:34', '2025-05-15 22:54:34', 'TRIAL_YBI018', '0981190522', 1, 1, NULL, NULL, NULL, 1, NULL, '2025-05-08 22:54:35', NULL, NULL),
('RTK_55_1746443027', 55, '2025-05-05 18:03:46', '2025-05-12 18:03:46', 'TRIAL_YBI001', '0981190564', 1, 1, NULL, NULL, NULL, 1, NULL, '2025-05-05 18:03:47', NULL, NULL),
('RTK_61_1746444219', 61, '2025-05-05 18:23:38', '2025-05-12 18:23:38', 'TRIAL_TNN001', '0981190562', 1, 1, NULL, NULL, NULL, 1, NULL, '2025-05-05 18:23:39', NULL, NULL),
('RTK_64_1746457824', 64, '2025-05-05 22:10:23', '2025-05-05 22:10:23', 'TRIAL_YBI002', '0981190562', 1, 0, NULL, NULL, NULL, 1, NULL, '2025-05-05 22:10:24', '2025-05-05 22:12:19', NULL),
('RTK_68_1746510776', 68, '2025-05-06 12:52:55', '2025-05-13 12:52:55', 'TRIAL_YBI003', '0981190562', 1, 1, NULL, NULL, NULL, 1, NULL, '2025-05-06 12:52:56', NULL, NULL),
('RTK_69_1746511192', 69, '2025-05-06 12:59:51', '2025-05-13 12:59:51', 'TRIAL_YBI004', '0981190562', 1, 1, NULL, NULL, NULL, 1, NULL, '2025-05-06 12:59:52', NULL, NULL),
('RTK_70_1746511206', 70, '2025-05-06 13:00:05', '2025-05-13 13:00:05', 'TRIAL_TNN002', '0981190562', 1, 1, NULL, NULL, NULL, 1, NULL, '2025-05-06 13:00:06', NULL, NULL),
('RTK_78_1746549444', 78, '2025-05-06 23:37:23', '2025-05-13 23:37:23', 'TRIAL_YBI005', '0981190562', 1, 1, NULL, NULL, NULL, 1, NULL, '2025-05-06 23:37:24', NULL, NULL),
('RTK_93_1746551383', 93, '2025-05-07 00:09:42', '2025-08-14 00:09:42', 'TRIAL_YBI006', '0981190562', 1, 1, NULL, NULL, NULL, 1, NULL, '2025-05-07 00:09:43', '2025-05-07 00:22:25', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `transaction_history`
--

CREATE TABLE `transaction_history` (
  `id` int NOT NULL,
  `registration_id` int DEFAULT NULL,
  `user_id` int NOT NULL,
  `voucher_id` int DEFAULT NULL,
  `transaction_type` enum('purchase','renewal','refund') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `amount` decimal(15,2) NOT NULL,
  `status` enum('pending','completed','failed','refunded') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT 'pending',
  `payment_method` enum('Chuyển khoản ngân hàng') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `payment_image` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `export_invoice` tinyint(1) DEFAULT '0',
  `invoice_info` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `payment_confirmed` tinyint(1) DEFAULT '0',
  `payment_confirmed_at` datetime DEFAULT NULL,
  `payment_reference` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `transaction_history`
--

INSERT INTO `transaction_history` (`id`, `registration_id`, `user_id`, `voucher_id`, `transaction_type`, `amount`, `status`, `payment_method`, `payment_image`, `export_invoice`, `invoice_info`, `payment_confirmed`, `payment_confirmed_at`, `payment_reference`, `created_at`, `updated_at`) VALUES
(59, 60, 92, NULL, 'purchase', 0.00, 'completed', NULL, 'reg_60_1746444618.png', 0, NULL, 0, NULL, NULL, '2025-05-05 18:23:31', '2025-05-05 18:31:01'),
(60, 61, 92, NULL, 'purchase', 0.00, 'completed', NULL, 'reg_61_1746444766.jpg', 1, NULL, 0, NULL, NULL, '2025-05-05 18:23:38', '2025-05-05 18:33:50'),
(61, 62, 92, NULL, 'purchase', 500000.00, 'failed', NULL, 'reg_62_1746455922.png', 0, NULL, 0, NULL, NULL, '2025-05-05 21:38:36', '2025-05-05 22:05:03'),
(62, 63, 92, NULL, 'purchase', 0.00, 'pending', NULL, 'reg_63_1746458014.png', 0, NULL, 0, NULL, NULL, '2025-05-05 22:07:56', '2025-05-05 22:13:34'),
(63, 64, 92, NULL, 'purchase', 0.00, 'completed', NULL, NULL, 0, NULL, 1, '2025-05-05 22:10:24', NULL, '2025-05-05 22:10:23', '2025-05-05 22:10:24'),
(64, 65, 92, NULL, 'renewal', 100000.00, 'pending', NULL, NULL, 0, NULL, 0, NULL, NULL, '2025-05-06 12:48:42', NULL),
(65, 66, 92, NULL, 'renewal', 900000.00, 'pending', NULL, NULL, 0, NULL, 0, NULL, NULL, '2025-05-06 12:48:58', NULL),
(66, 67, 92, NULL, 'renewal', 270000.00, 'pending', NULL, NULL, 0, NULL, 0, NULL, NULL, '2025-05-06 12:49:08', NULL),
(67, 68, 92, NULL, 'purchase', 0.00, 'completed', NULL, NULL, 0, NULL, 1, '2025-05-06 12:52:56', NULL, '2025-05-06 12:52:55', '2025-05-06 12:52:56'),
(68, 69, 92, NULL, 'purchase', 0.00, 'completed', NULL, NULL, 0, NULL, 1, '2025-05-06 12:59:52', NULL, '2025-05-06 12:59:51', '2025-05-06 12:59:52'),
(69, 70, 92, NULL, 'purchase', 0.00, 'completed', NULL, NULL, 0, NULL, 1, '2025-05-06 13:00:06', NULL, '2025-05-06 13:00:05', '2025-05-06 13:00:06'),
(70, 71, 92, NULL, 'renewal', 270000.00, 'pending', NULL, NULL, 0, NULL, 0, NULL, NULL, '2025-05-06 13:00:26', NULL),
(71, 72, 92, NULL, 'purchase', 500000.00, 'pending', NULL, 'reg_72_1746511483.png', 0, NULL, 0, NULL, NULL, '2025-05-06 13:04:38', '2025-05-06 13:04:43'),
(72, 73, 92, NULL, 'purchase', 500000.00, 'pending', NULL, 'reg_73_1746544734.png', 0, NULL, 0, NULL, NULL, '2025-05-06 22:18:48', '2025-05-06 22:18:54'),
(73, 74, 92, NULL, 'purchase', 0.00, 'pending', NULL, NULL, 0, NULL, 0, NULL, NULL, '2025-05-06 23:36:08', '2025-05-06 23:36:08'),
(74, 75, 92, NULL, 'purchase', 500000.00, 'pending', NULL, NULL, 0, NULL, 0, NULL, NULL, '2025-05-06 23:36:18', '2025-05-06 23:36:18'),
(75, 77, 92, NULL, 'purchase', 0.00, 'pending', NULL, NULL, 0, NULL, 0, NULL, NULL, '2025-05-06 23:36:47', '2025-05-06 23:36:47'),
(76, 78, 92, NULL, 'purchase', 0.00, 'completed', NULL, NULL, 0, NULL, 1, '2025-05-06 23:37:24', NULL, '2025-05-06 23:37:23', '2025-05-06 23:37:24'),
(77, 79, 92, NULL, 'renewal', 500000.00, 'pending', NULL, 'reg_79_1746550344.png', 0, NULL, 0, NULL, NULL, '2025-05-06 23:52:18', '2025-05-06 23:52:24'),
(78, NULL, 92, NULL, 'renewal', 100000.00, 'pending', NULL, NULL, 0, NULL, 0, NULL, NULL, '2025-05-06 23:54:18', '2025-05-07 00:02:37'),
(79, 81, 92, NULL, 'renewal', 270000.00, 'pending', NULL, NULL, 0, NULL, 0, NULL, NULL, '2025-05-06 23:54:18', '2025-05-06 23:54:18'),
(80, 82, 92, NULL, 'renewal', 500000.00, 'pending', NULL, NULL, 0, NULL, 0, NULL, NULL, '2025-05-06 23:54:18', '2025-05-06 23:54:18'),
(81, 83, 92, NULL, 'renewal', 900000.00, 'pending', NULL, 'reg_83_1746550472.png', 0, NULL, 0, NULL, NULL, '2025-05-06 23:54:18', '2025-05-06 23:54:32'),
(82, 84, 92, NULL, 'renewal', 100000.00, 'pending', NULL, NULL, 0, NULL, 0, NULL, NULL, '2025-05-06 23:55:55', '2025-05-06 23:55:55'),
(83, 85, 92, NULL, 'renewal', 100000.00, 'completed', NULL, 'reg_85_1746550562.png', 0, NULL, 1, NULL, NULL, '2025-05-06 23:55:55', '2025-05-11 10:55:29'),
(84, 86, 92, NULL, 'purchase', 500000.00, 'pending', NULL, 'reg_86_1746550629.png', 0, NULL, 0, NULL, NULL, '2025-05-06 23:57:03', '2025-05-06 23:57:09'),
(85, 87, 92, NULL, 'renewal', 100000.00, 'pending', NULL, NULL, 0, NULL, 0, NULL, NULL, '2025-05-06 23:57:21', '2025-05-06 23:57:21'),
(86, NULL, 92, NULL, 'renewal', 100000.00, 'completed', NULL, 'reg_88_1746550648.png', 0, NULL, 0, NULL, NULL, '2025-05-06 23:57:21', '2025-05-07 00:04:15'),
(87, 90, 92, NULL, 'renewal', 270000.00, 'pending', NULL, NULL, 0, NULL, 0, NULL, NULL, '2025-05-07 00:09:00', '2025-05-07 00:09:00'),
(88, 91, 92, NULL, 'renewal', 500000.00, 'pending', NULL, NULL, 0, NULL, 0, NULL, NULL, '2025-05-07 00:09:09', '2025-05-07 00:09:09'),
(89, 92, 92, NULL, 'renewal', 270000.00, 'pending', NULL, NULL, 0, NULL, 0, NULL, NULL, '2025-05-07 00:09:09', '2025-05-07 00:09:09'),
(90, 93, 92, NULL, 'purchase', 0.00, 'completed', NULL, NULL, 0, NULL, 1, '2025-05-07 00:09:43', NULL, '2025-05-07 00:09:42', '2025-05-07 00:09:43'),
(91, 94, 92, NULL, 'renewal', 270000.00, 'pending', NULL, NULL, 0, NULL, 0, NULL, NULL, '2025-05-07 00:09:56', '2025-05-07 00:09:56'),
(92, 95, 92, NULL, 'renewal', 270000.00, 'pending', NULL, NULL, 0, NULL, 0, NULL, NULL, '2025-05-07 00:09:56', '2025-05-07 00:09:56'),
(93, 96, 92, NULL, 'purchase', 500000.00, 'pending', NULL, NULL, 0, NULL, 0, NULL, NULL, '2025-05-07 00:13:19', '2025-05-07 00:13:19'),
(94, 98, 92, NULL, 'purchase', 200000.00, 'pending', NULL, NULL, 0, NULL, 0, NULL, NULL, '2025-05-07 00:13:49', '2025-05-07 00:13:49'),
(95, 99, 92, NULL, 'renewal', 270000.00, 'pending', NULL, NULL, 0, NULL, 0, NULL, NULL, '2025-05-07 00:14:05', '2025-05-07 00:14:05'),
(96, 100, 92, NULL, 'renewal', 500000.00, 'pending', NULL, NULL, 0, NULL, 0, NULL, NULL, '2025-05-07 00:14:05', '2025-05-07 00:14:05'),
(97, 101, 92, NULL, 'renewal', 270000.00, 'pending', NULL, 'reg_101_1746551652.png', 0, NULL, 0, NULL, NULL, '2025-05-07 00:14:05', '2025-05-07 00:14:12'),
(98, 102, 92, NULL, 'purchase', 300000.00, 'pending', NULL, 'reg_102_1746552025.png', 0, NULL, 0, NULL, NULL, '2025-05-07 00:20:17', '2025-05-07 00:20:25'),
(99, 103, 92, NULL, 'renewal', 270000.00, 'pending', NULL, NULL, 0, NULL, 0, NULL, NULL, '2025-05-07 00:20:37', '2025-05-07 00:20:37'),
(100, 104, 92, NULL, 'renewal', 500000.00, 'pending', NULL, NULL, 0, NULL, 0, NULL, NULL, '2025-05-07 00:20:37', '2025-05-07 00:20:37'),
(101, 105, 92, NULL, 'renewal', 270000.00, 'pending', NULL, NULL, 0, NULL, 0, NULL, NULL, '2025-05-07 00:20:37', '2025-05-07 00:20:37'),
(102, 106, 92, NULL, 'renewal', 270000.00, 'pending', NULL, 'reg_106_1746552145.png', 0, NULL, 0, NULL, NULL, '2025-05-07 00:22:19', '2025-05-07 00:22:25'),
(103, 107, 92, NULL, 'renewal', 270000.00, 'pending', NULL, NULL, 0, NULL, 0, NULL, NULL, '2025-05-07 00:22:56', '2025-05-07 00:22:56'),
(104, 108, 92, NULL, 'renewal', 270000.00, 'pending', NULL, NULL, 0, NULL, 0, NULL, NULL, '2025-05-07 00:22:56', '2025-05-07 00:22:56'),
(105, 109, 92, NULL, 'renewal', 270000.00, 'pending', NULL, 'reg_109_1746552332.png', 0, NULL, 0, NULL, NULL, '2025-05-07 00:22:56', '2025-05-07 00:25:32'),
(106, 110, 92, NULL, 'purchase', 0.00, 'pending', NULL, NULL, 0, NULL, 0, NULL, NULL, '2025-05-07 00:28:33', '2025-05-07 00:28:33'),
(107, 111, 92, NULL, 'purchase', 0.00, 'pending', NULL, NULL, 0, NULL, 0, NULL, NULL, '2025-05-07 00:28:56', '2025-05-07 00:28:56'),
(108, 112, 92, NULL, 'purchase', 0.00, 'completed', NULL, NULL, 0, NULL, 1, '2025-05-07 00:29:52', NULL, '2025-05-07 00:29:51', '2025-05-07 00:29:52'),
(109, 113, 92, NULL, 'renewal', 100000.00, 'pending', NULL, NULL, 0, NULL, 0, NULL, NULL, '2025-05-07 00:30:11', '2025-05-07 00:30:11'),
(110, 114, 92, NULL, 'renewal', 270000.00, 'pending', NULL, NULL, 0, NULL, 0, NULL, NULL, '2025-05-07 00:30:11', '2025-05-07 00:30:11'),
(111, 115, 92, NULL, 'renewal', 500000.00, 'pending', NULL, NULL, 0, NULL, 0, NULL, NULL, '2025-05-07 00:30:11', '2025-05-07 00:30:11'),
(112, 116, 92, NULL, 'renewal', 270000.00, 'pending', NULL, NULL, 0, NULL, 0, NULL, NULL, '2025-05-07 00:30:11', '2025-05-07 00:30:11'),
(113, 117, 92, NULL, 'renewal', 270000.00, 'pending', NULL, NULL, 0, NULL, 0, NULL, NULL, '2025-05-07 06:44:37', '2025-05-07 06:44:37'),
(114, 118, 92, NULL, 'renewal', 270000.00, 'pending', NULL, NULL, 0, NULL, 0, NULL, NULL, '2025-05-07 06:44:37', '2025-05-07 06:44:37'),
(115, 119, 92, NULL, 'renewal', 100000.00, 'pending', NULL, NULL, 0, NULL, 0, NULL, NULL, '2025-05-07 06:45:31', '2025-05-07 06:45:31'),
(116, 120, 92, NULL, 'renewal', 900000.00, 'pending', NULL, NULL, 0, NULL, 0, NULL, NULL, '2025-05-07 06:45:31', '2025-05-07 06:45:31'),
(117, 121, 92, NULL, 'renewal', 900000.00, 'pending', NULL, NULL, 0, NULL, 0, NULL, NULL, '2025-05-07 06:45:31', '2025-05-07 06:45:31'),
(118, 122, 92, NULL, 'renewal', 900000.00, 'pending', NULL, NULL, 0, NULL, 0, NULL, NULL, '2025-05-07 06:45:31', '2025-05-07 06:45:31'),
(119, 123, 92, NULL, 'renewal', 900000.00, 'pending', NULL, NULL, 0, NULL, 0, NULL, NULL, '2025-05-07 06:45:31', '2025-05-07 06:45:31'),
(120, 124, 92, NULL, 'renewal', 500000.00, 'pending', NULL, NULL, 0, NULL, 0, NULL, NULL, '2025-05-07 06:45:31', '2025-05-07 06:45:31'),
(121, 125, 92, NULL, 'renewal', 900000.00, 'pending', NULL, 'reg_125_1746575220.png', 0, NULL, 0, NULL, NULL, '2025-05-07 06:45:31', '2025-05-07 06:47:00'),
(122, 126, 92, NULL, 'renewal', 100000.00, 'pending', NULL, 'reg_126_1746575274.png', 0, NULL, 0, NULL, NULL, '2025-05-07 06:47:49', '2025-05-07 06:47:54'),
(123, 127, 92, NULL, 'renewal', 500000.00, 'pending', NULL, NULL, 0, NULL, 0, NULL, NULL, '2025-05-07 06:48:27', '2025-05-07 06:48:27'),
(124, 128, 92, NULL, 'renewal', 500000.00, 'pending', NULL, NULL, 0, NULL, 0, NULL, NULL, '2025-05-07 06:48:27', '2025-05-07 06:48:27'),
(125, 129, 92, NULL, 'renewal', 270000.00, 'pending', NULL, 'reg_129_1746575312.png', 0, NULL, 0, NULL, NULL, '2025-05-07 06:48:27', '2025-05-07 06:48:32'),
(126, 130, 92, NULL, 'purchase', 500000.00, 'pending', NULL, NULL, 0, NULL, 0, NULL, NULL, '2025-05-07 06:55:23', '2025-05-07 06:55:23'),
(127, 131, 92, NULL, 'purchase', 500000.00, 'pending', NULL, NULL, 0, NULL, 0, NULL, NULL, '2025-05-07 06:55:38', '2025-05-07 06:55:38'),
(128, 132, 92, NULL, 'purchase', 600000.00, 'pending', NULL, NULL, 0, NULL, 0, NULL, NULL, '2025-05-07 06:56:10', '2025-05-07 06:56:10'),
(129, 133, 92, NULL, 'renewal', 270000.00, 'pending', NULL, NULL, 0, NULL, 0, NULL, NULL, '2025-05-07 06:56:26', '2025-05-07 06:56:26'),
(130, 134, 92, NULL, 'renewal', 100000.00, 'pending', NULL, 'reg_134_1746576324.png', 0, NULL, 0, NULL, NULL, '2025-05-07 07:05:10', '2025-05-07 07:05:24'),
(131, 135, 92, NULL, 'purchase', 500000.00, 'pending', NULL, NULL, 0, NULL, 0, NULL, NULL, '2025-05-07 07:09:02', '2025-05-07 07:09:02'),
(132, 136, 92, NULL, 'renewal', 2700000.00, 'pending', NULL, NULL, 0, NULL, 0, NULL, NULL, '2025-05-07 07:09:12', '2025-05-07 07:09:12'),
(133, 137, 92, NULL, 'renewal', 270000.00, 'pending', NULL, 'reg_137_1746576931.png', 0, NULL, 0, NULL, NULL, '2025-05-07 07:15:10', '2025-05-07 07:15:31'),
(134, 138, 92, NULL, 'renewal', 300000.00, 'pending', NULL, 'reg_138_1746577538.png', 0, NULL, 0, NULL, NULL, '2025-05-07 07:25:29', '2025-05-07 07:25:38'),
(135, 139, 92, NULL, 'renewal', 700000.00, 'pending', NULL, 'reg_139_1746577617.png', 0, NULL, 0, NULL, NULL, '2025-05-07 07:26:49', '2025-05-07 07:26:57'),
(136, 140, 92, NULL, 'renewal', 540000.00, 'pending', NULL, 'reg_140_1746629763.png', 0, NULL, 0, NULL, NULL, '2025-05-07 21:55:52', '2025-05-07 21:56:03'),
(137, 141, 92, NULL, 'renewal', 1500000.00, 'pending', NULL, 'reg_141_1746629866.png', 0, NULL, 0, NULL, NULL, '2025-05-07 21:57:40', '2025-05-07 21:57:46'),
(138, 142, 92, NULL, 'purchase', 500000.00, 'pending', NULL, 'reg_142_1746629981.png', 0, NULL, 0, NULL, NULL, '2025-05-07 21:59:27', '2025-05-07 21:59:41'),
(139, 143, 92, NULL, 'purchase', 700000.00, 'pending', NULL, NULL, 0, NULL, 0, NULL, NULL, '2025-05-07 22:01:05', '2025-05-07 22:01:05'),
(140, 144, 92, NULL, 'purchase', 26730000.00, 'pending', NULL, NULL, 0, NULL, 0, NULL, NULL, '2025-05-07 22:01:23', '2025-05-07 22:01:23'),
(141, 145, 92, NULL, 'purchase', 500000.00, 'pending', NULL, 'reg_145_1746630640.png', 0, NULL, 0, NULL, NULL, '2025-05-07 22:10:34', '2025-05-07 22:10:40'),
(142, 146, 92, NULL, 'renewal', 1080000.00, 'pending', NULL, 'reg_146_1746630676.png', 0, NULL, 0, NULL, NULL, '2025-05-07 22:11:05', '2025-05-07 22:11:16'),
(143, 147, 92, NULL, 'renewal', 4000000.00, 'pending', NULL, 'reg_147_1746630834.png', 0, NULL, 0, NULL, NULL, '2025-05-07 22:13:30', '2025-05-07 22:13:54'),
(144, 148, 93, NULL, 'purchase', 0.00, 'completed', NULL, NULL, 0, NULL, 1, '2025-05-07 22:47:51', NULL, '2025-05-07 22:47:50', '2025-05-07 22:47:51'),
(145, 149, 93, NULL, 'renewal', 270000.00, 'pending', NULL, NULL, 0, NULL, 0, NULL, NULL, '2025-05-07 22:47:58', '2025-05-07 22:47:58'),
(146, 150, 93, NULL, 'renewal', 100000.00, 'pending', NULL, NULL, 0, NULL, 0, NULL, NULL, '2025-05-07 23:51:38', '2025-05-07 23:51:38'),
(147, 151, 93, NULL, 'purchase', 200000.00, 'pending', NULL, 'reg_151_1746715588.png', 0, NULL, 0, NULL, NULL, '2025-05-08 21:46:10', '2025-05-08 21:46:28'),
(148, 152, 93, NULL, 'purchase', 200000.00, 'pending', NULL, NULL, 0, NULL, 0, NULL, NULL, '2025-05-08 21:50:28', '2025-05-08 21:50:28'),
(149, 153, 93, NULL, 'purchase', 0.00, 'completed', NULL, NULL, 0, NULL, 1, '2025-05-08 22:53:28', NULL, '2025-05-08 22:53:25', '2025-05-08 22:53:28'),
(150, 154, 93, NULL, 'purchase', 0.00, 'completed', NULL, NULL, 0, NULL, 1, '2025-05-08 22:53:34', NULL, '2025-05-08 22:53:33', '2025-05-08 22:53:34'),
(151, 155, 93, NULL, 'purchase', 0.00, 'completed', NULL, NULL, 0, NULL, 1, '2025-05-08 22:53:43', NULL, '2025-05-08 22:53:42', '2025-05-08 22:53:43'),
(152, 156, 93, NULL, 'purchase', 0.00, 'completed', NULL, NULL, 0, NULL, 1, '2025-05-08 22:53:49', NULL, '2025-05-08 22:53:48', '2025-05-08 22:53:49'),
(153, 157, 93, NULL, 'purchase', 0.00, 'completed', NULL, NULL, 0, NULL, 1, '2025-05-08 22:53:55', NULL, '2025-05-08 22:53:54', '2025-05-08 22:53:55'),
(154, 158, 93, NULL, 'purchase', 0.00, 'completed', NULL, NULL, 0, NULL, 1, '2025-05-08 22:54:01', NULL, '2025-05-08 22:54:00', '2025-05-08 22:54:01'),
(155, 159, 93, NULL, 'purchase', 0.00, 'completed', NULL, NULL, 0, NULL, 1, '2025-05-08 22:54:10', NULL, '2025-05-08 22:54:09', '2025-05-08 22:54:10'),
(156, 160, 93, NULL, 'purchase', 0.00, 'completed', NULL, NULL, 0, NULL, 1, '2025-05-08 22:54:17', NULL, '2025-05-08 22:54:16', '2025-05-08 22:54:17'),
(157, 161, 93, NULL, 'purchase', 0.00, 'completed', NULL, NULL, 0, NULL, 1, '2025-05-08 22:54:28', NULL, '2025-05-08 22:54:27', '2025-05-08 22:54:28'),
(158, 162, 93, NULL, 'purchase', 0.00, 'completed', NULL, NULL, 0, NULL, 1, '2025-05-08 22:54:35', NULL, '2025-05-08 22:54:34', '2025-05-08 22:54:35'),
(159, 163, 93, NULL, 'purchase', 500000.00, 'pending', NULL, NULL, 0, NULL, 0, NULL, NULL, '2025-05-10 09:17:57', '2025-05-10 09:17:57'),
(160, 164, 93, NULL, 'purchase', 500000.00, 'pending', NULL, NULL, 0, NULL, 0, NULL, NULL, '2025-05-10 09:31:12', '2025-05-10 09:31:12'),
(161, 165, 93, NULL, 'purchase', 500000.00, 'pending', NULL, NULL, 0, NULL, 0, NULL, NULL, '2025-05-10 09:31:36', '2025-05-10 09:31:36'),
(162, 166, 93, NULL, 'purchase', 1000000.00, 'pending', NULL, NULL, 0, NULL, 0, NULL, NULL, '2025-05-10 09:35:06', '2025-05-10 09:35:06'),
(163, 167, 93, NULL, 'purchase', 500000.00, 'pending', NULL, NULL, 0, NULL, 0, NULL, NULL, '2025-05-10 09:46:37', '2025-05-10 09:46:37'),
(164, 168, 93, NULL, 'purchase', 500000.00, 'pending', NULL, 'reg_168_1746845835.png', 0, NULL, 0, NULL, NULL, '2025-05-10 09:54:37', '2025-05-10 09:57:15'),
(165, 169, 93, NULL, 'renewal', 400000.00, 'pending', NULL, NULL, 0, NULL, 0, NULL, NULL, '2025-05-10 10:09:17', '2025-05-10 10:09:17'),
(166, 170, 93, 3, 'purchase', 5400000.00, 'pending', NULL, NULL, 0, NULL, 0, NULL, NULL, '2025-05-10 10:09:37', '2025-05-10 10:09:37'),
(167, 171, 93, 3, 'purchase', 1600000.00, 'pending', NULL, NULL, 0, NULL, 0, NULL, NULL, '2025-05-10 10:21:12', '2025-05-10 10:21:12'),
(168, 172, 93, 5, 'purchase', 1600000.00, 'pending', NULL, NULL, 0, NULL, 0, NULL, NULL, '2025-05-10 10:21:48', '2025-05-10 10:21:48'),
(169, 173, 93, 5, 'purchase', 1350000.00, 'pending', NULL, 'reg_173_1746847487.png', 0, NULL, 0, NULL, NULL, '2025-05-10 10:21:57', '2025-05-10 10:24:47'),
(170, 174, 93, 6, 'purchase', 2700000.00, 'pending', NULL, 'reg_174_1746847556.png', 0, NULL, 0, NULL, NULL, '2025-05-10 10:25:39', '2025-05-10 10:25:56'),
(171, 175, 93, 6, 'purchase', 1890000.00, 'pending', NULL, NULL, 0, NULL, 0, NULL, NULL, '2025-05-10 10:26:37', '2025-05-10 10:26:37'),
(172, 176, 93, NULL, 'purchase', 2430000.00, 'pending', NULL, 'reg_176_1746848190.png', 0, NULL, 0, NULL, NULL, '2025-05-10 10:35:44', '2025-05-10 10:36:31'),
(173, 177, 93, NULL, 'renewal', 1080000.00, 'pending', NULL, NULL, 0, NULL, 0, NULL, NULL, '2025-05-10 10:37:31', '2025-05-10 10:37:31'),
(174, 178, 93, 5, 'purchase', 4500000.00, 'pending', NULL, NULL, 0, NULL, 0, NULL, NULL, '2025-05-10 10:37:44', '2025-05-10 10:37:44'),
(175, 179, 93, 5, 'purchase', 1150000.00, 'pending', NULL, 'reg_179_1746849445.png', 0, NULL, 0, NULL, NULL, '2025-05-10 10:57:11', '2025-05-10 10:57:25'),
(176, 180, 93, NULL, 'renewal', 540000.00, 'pending', NULL, NULL, 0, NULL, 0, NULL, NULL, '2025-05-10 10:58:34', '2025-05-10 10:58:34'),
(177, 181, 93, 4, 'purchase', 450000.00, 'pending', NULL, NULL, 0, NULL, 0, NULL, NULL, '2025-05-10 11:01:06', '2025-05-10 11:02:36'),
(178, 182, 93, NULL, 'renewal', 810000.00, 'pending', NULL, NULL, 0, NULL, 0, NULL, NULL, '2025-05-10 11:04:09', '2025-05-10 11:04:09'),
(179, 183, 93, NULL, 'purchase', 5000000.00, 'pending', NULL, NULL, 0, NULL, 0, NULL, NULL, '2025-05-10 11:14:22', '2025-05-10 11:14:22'),
(180, 184, 93, 9, 'purchase', 475000.00, 'pending', NULL, NULL, 0, NULL, 0, NULL, NULL, '2025-05-10 11:18:25', '2025-05-10 11:18:46'),
(181, 185, 93, NULL, 'renewal', 270000.00, 'pending', NULL, NULL, 0, NULL, 0, NULL, NULL, '2025-05-10 11:23:04', '2025-05-10 11:23:04'),
(182, 186, 93, NULL, 'purchase', 59400000.00, 'pending', NULL, NULL, 0, NULL, 0, NULL, NULL, '2025-05-10 11:27:27', '2025-05-10 11:28:22'),
(183, 187, 93, 9, 'purchase', 475000.00, 'pending', NULL, NULL, 0, NULL, 0, NULL, NULL, '2025-05-10 11:29:02', '2025-05-10 11:29:05'),
(184, 188, 93, NULL, 'renewal', 810000.00, 'pending', NULL, NULL, 0, NULL, 0, NULL, NULL, '2025-05-10 11:29:24', '2025-05-10 11:29:24'),
(185, 189, 93, NULL, 'purchase', 0.00, 'pending', NULL, NULL, 0, NULL, 0, NULL, NULL, '2025-05-10 11:36:35', '2025-05-10 11:36:35'),
(186, 190, 93, NULL, 'purchase', 270000.00, 'pending', NULL, NULL, 0, NULL, 0, NULL, NULL, '2025-05-10 11:37:07', '2025-05-10 11:37:07'),
(187, 191, 93, 9, 'purchase', 475000.00, 'pending', NULL, NULL, 0, NULL, 0, NULL, NULL, '2025-05-10 11:39:56', '2025-05-10 11:40:02'),
(188, 192, 93, NULL, 'purchase', 500000.00, 'pending', NULL, NULL, 0, NULL, 0, NULL, NULL, '2025-05-10 11:40:15', '2025-05-10 11:40:15'),
(189, 193, 93, NULL, 'renewal', 2430000.00, 'pending', NULL, NULL, 0, NULL, 0, NULL, NULL, '2025-05-10 11:42:23', '2025-05-10 11:42:23'),
(190, 194, 93, NULL, 'renewal', 270000.00, 'pending', NULL, NULL, 0, NULL, 0, NULL, NULL, '2025-05-10 20:10:34', '2025-05-10 20:10:34'),
(191, 195, 93, NULL, 'purchase', 500000.00, 'pending', NULL, NULL, 0, NULL, 0, NULL, NULL, '2025-05-10 20:18:42', '2025-05-10 20:19:21'),
(192, 196, 93, NULL, 'purchase', 500000.00, 'pending', NULL, NULL, 0, NULL, 0, NULL, NULL, '2025-05-10 20:27:52', '2025-05-10 20:27:52'),
(193, 197, 93, NULL, 'purchase', 500000.00, 'pending', NULL, NULL, 0, NULL, 0, NULL, NULL, '2025-05-10 20:28:04', '2025-05-10 20:28:04'),
(194, 198, 93, 7, 'renewal', 510000.00, 'pending', NULL, NULL, 0, NULL, 0, NULL, NULL, '2025-05-10 20:28:15', '2025-05-10 20:28:21'),
(195, 199, 93, NULL, 'purchase', 1350000.00, 'pending', NULL, NULL, 0, NULL, 0, NULL, NULL, '2025-05-10 20:34:31', '2025-05-10 20:34:35'),
(196, 200, 93, NULL, 'renewal', 500000.00, 'pending', NULL, NULL, 0, NULL, 0, NULL, NULL, '2025-05-10 20:34:50', '2025-05-10 20:34:54'),
(197, 201, 93, 7, 'purchase', 470000.00, 'pending', NULL, NULL, 0, NULL, 0, NULL, NULL, '2025-05-10 20:38:56', '2025-05-10 20:39:02'),
(198, 202, 93, NULL, 'renewal', 500000.00, 'pending', NULL, NULL, 0, NULL, 0, NULL, NULL, '2025-05-10 20:40:03', '2025-05-10 20:40:03'),
(199, 203, 93, NULL, 'purchase', 500000.00, 'pending', NULL, NULL, 0, NULL, 0, NULL, NULL, '2025-05-10 20:42:02', '2025-05-10 20:42:02'),
(200, 204, 93, 7, 'purchase', 470000.00, 'pending', NULL, 'reg_204_1746884833.png', 0, NULL, 0, NULL, NULL, '2025-05-10 20:46:57', '2025-05-10 20:47:13'),
(201, 205, 93, NULL, 'renewal', 540000.00, 'pending', NULL, NULL, 0, NULL, 0, NULL, NULL, '2025-05-10 21:18:24', '2025-05-10 21:18:24'),
(202, 206, 93, NULL, 'purchase', 1620000.00, 'pending', NULL, NULL, 0, NULL, 0, NULL, NULL, '2025-05-10 21:26:49', '2025-05-10 21:27:35'),
(203, 207, 95, NULL, 'purchase', 9000000.00, 'completed', NULL, 'reg_207_1746935687.png', 0, NULL, 1, NULL, NULL, '2025-05-11 10:54:38', '2025-05-11 10:55:57'),
(204, 208, 95, NULL, 'purchase', 500000.00, 'completed', NULL, 'reg_208_1746936110.png', 0, NULL, 1, NULL, NULL, '2025-05-11 11:01:45', '2025-05-11 11:02:27'),
(205, 209, 95, NULL, 'purchase', 55500000.00, 'completed', NULL, 'reg_209_1746936304.png', 0, NULL, 1, NULL, NULL, '2025-05-11 11:04:58', '2025-05-11 11:05:23'),
(206, 210, 95, NULL, 'purchase', 49500000.00, 'completed', NULL, 'reg_210_1746939099.png', 0, NULL, 1, NULL, NULL, '2025-05-11 11:51:32', '2025-05-11 11:52:10'),
(207, 211, 95, NULL, 'purchase', 44400000.00, 'completed', NULL, 'reg_211_1746939436.png', 0, NULL, 1, NULL, NULL, '2025-05-11 11:57:10', '2025-05-11 11:57:49'),
(208, 212, 95, NULL, 'purchase', 5500000.00, 'completed', NULL, 'reg_212_1746940339.png', 0, NULL, 1, NULL, NULL, '2025-05-11 12:12:14', '2025-05-11 12:12:43'),
(209, 213, 95, NULL, 'purchase', 3400000.00, 'completed', NULL, 'reg_213_1746941147.png', 0, NULL, 1, NULL, NULL, '2025-05-11 12:25:42', '2025-05-11 12:26:20'),
(210, 214, 93, 3, 'purchase', 3240000.00, 'pending', NULL, NULL, 0, NULL, 0, NULL, NULL, '2025-05-11 14:58:24', '2025-05-11 14:58:56'),
(211, 215, 95, 1, 'purchase', 9620000.00, 'completed', NULL, 'reg_215_1746961529.png', 0, NULL, 1, NULL, NULL, '2025-05-11 18:04:41', '2025-05-11 18:05:59'),
(212, 216, 95, NULL, 'purchase', 90000000.00, 'completed', NULL, 'reg_216_1746961731.png', 0, NULL, 1, NULL, NULL, '2025-05-11 18:08:43', '2025-05-11 18:09:35');

--
-- Triggers `transaction_history`
--
DELIMITER $$
CREATE TRIGGER `trg_auto_approve_commission` AFTER UPDATE ON `transaction_history` FOR EACH ROW BEGIN
    -- If transaction is completed and payment is confirmed 
    IF NEW.status = 'completed' AND NEW.payment_confirmed = 1 THEN
        -- Check if a commission record already exists
        IF NOT EXISTS (SELECT 1 FROM referral_commission WHERE transaction_id = NEW.id) THEN
            -- Check if the user was referred
            IF EXISTS (SELECT 1 FROM referred_user WHERE referred_user_id = NEW.user_id) THEN
                -- Get referrer information
                SET @referrer_id = (SELECT referrer_id FROM referred_user WHERE referred_user_id = NEW.user_id);
                -- Insert new commission record (calculation will be handled by the application)
                -- This is just a backup mechanism, the main process should happen in application code
                INSERT INTO referral_commission 
                    (referrer_id, referred_user_id, transaction_id, commission_amount, status, created_at)
                VALUES 
                    (@referrer_id, NEW.user_id, NEW.id, NEW.amount * 0.05, 'approved', NOW());
            END IF;
        ELSE
            -- Ensure existing commission is approved
            UPDATE referral_commission 
            SET status = 'approved', updated_at = NOW()
            WHERE transaction_id = NEW.id AND status != 'approved';
        END IF;
    END IF;
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Table structure for table `user`
--

CREATE TABLE `user` (
  `id` int NOT NULL,
  `username` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `email` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `password` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `phone` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `is_company` tinyint(1) DEFAULT NULL,
  `company_name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `tax_code` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `company_address` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Địa chỉ công ty',
  `tax_registered` tinyint(1) DEFAULT NULL,
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  `deleted_at` datetime DEFAULT NULL,
  `status` tinyint(1) NOT NULL DEFAULT '1' COMMENT 'Trạng thái người dùng (1: active, 0: inactive)',
  `email_verified` tinyint(1) NOT NULL DEFAULT '0',
  `email_verify_token` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `user`
--

INSERT INTO `user` (`id`, `username`, `email`, `password`, `phone`, `is_company`, `company_name`, `tax_code`, `company_address`, `tax_registered`, `created_at`, `updated_at`, `deleted_at`, `status`, `email_verified`, `email_verify_token`) VALUES
(88, 'Long2004', 'tranhailong2408@gmail.com', '$2y$10$Ykj5ewmTa9z.EWdiVDPN6OKJ0lCXZU7L7ndZ7DDNd0LCXrHVraTOq', '0999999445', 1, 'as', '123', NULL, NULL, '2025-04-27 14:55:01', '2025-05-04 09:08:21', NULL, 1, 1, NULL),
(89, 'Long2005', 'tranhailong2410@gmail.com', '$2y$10$4/NI4svx6977OTC0q13r5eRE5Uovn7nmR47Z.j3V9yy19n9XacQBa', '0900000005', 1, 'ad', '123', NULL, NULL, '2025-04-29 15:00:08', '2025-05-04 09:08:23', NULL, 1, 1, NULL),
(90, 'Long2002', 'tranhailong2407@gmail.com', '$2y$10$Qt93QW.LtJzj/Vze0box4OmShRAfRxKCJdUtpYIJ5Ccu6H3nSy9Qe', '0999999443', 0, NULL, NULL, NULL, NULL, '2025-05-04 09:08:01', NULL, NULL, 1, 0, '88a57ead39eec40e1867d27c87466a29904d3b6a280db60521cac1b77001c9a0'),
(91, 'nguyendozxc15@gmail.com', 'nguyendozxc15@gmail.com', '$2y$10$y8rSLvI2J48XZjTCb9IIgOmEf5Tz42r0OVLOrrlovJHt8JjVrNRvq', '0981190564', 0, NULL, NULL, NULL, NULL, '2025-05-05 11:09:06', '2025-05-05 11:09:26', NULL, 1, 1, NULL),
(92, 'dovannguyen2005bv@gmail.com', 'dovannguyen2005bv@gmail.com', '$2y$10$BUQc5aTNhk0h1mBfQhrlG.1kkVfb8t.9Hj6lnHZYr43CEQUqBlLpS', '0981190562', 0, 'Công ty cổ phần công nghệ Otek', '2222333332', NULL, NULL, '2025-05-05 14:33:57', '2025-05-05 17:10:23', NULL, 1, 1, NULL),
(93, 'nguyendozxc20@gmail.com', 'nguyendozxc20@gmail.com', '$2y$10$1vCsFFE0crdwZYqv/K97reAlFpU1vuIOSs5/hs0lhnwypkzBj/nCm', '0981190522', 1, 'Công ty cổ phần công nghệ Otek', '2222233332', 'Ha Noi', NULL, '2025-05-07 22:39:46', '2025-05-10 21:14:34', NULL, 1, 1, NULL),
(94, 'onf52053@toaik.com', 'onf52053@toaik.com', '$2y$10$b3Cyd8bwZ5cFc690DISmd.ZrAfzkvbvTsbbAEP/Vaiy/kmPNZcv1i', '0981130564', 0, NULL, NULL, NULL, NULL, '2025-05-10 21:00:57', NULL, NULL, 1, 0, 'bcf3432eb3e495acfab64b2cc82ecf505a4f729cacc343aca5b8a03b14d94853'),
(95, 'kei65757@toaik.com', 'kei65757@toaik.com', '$2y$10$E0jVblq.5EoERWBOXd2lvObfw.r8nko6ySx8o7x/eLgzN/qmiub1W', '0281190564', 0, NULL, NULL, NULL, NULL, '2025-05-11 10:51:20', '2025-05-11 10:54:04', NULL, 1, 1, NULL),
(96, 'addd@gmail.com', 'addd@gmail.com', '$2y$10$W8VAJit.yg0ZqAIq0e5gz.yuncGYw5gGkEg8XEWb.4oFcAK01EGdC', '0982290564', 0, NULL, NULL, NULL, NULL, '2025-05-11 14:44:27', NULL, NULL, 1, 0, '95a07bd3451bba76c31096ae73277e55b6a6a660c9d46b8ab7ea10b38b06a502');

-- --------------------------------------------------------

--
-- Table structure for table `user_sessions`
--

CREATE TABLE `user_sessions` (
  `session_id` varchar(128) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `user_id` int NOT NULL,
  `ip_address` varchar(45) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `user_agent` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `last_activity` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `is_active` tinyint(1) NOT NULL DEFAULT '1'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `user_sessions`
--

INSERT INTO `user_sessions` (`session_id`, `user_id`, `ip_address`, `user_agent`, `created_at`, `last_activity`, `is_active`) VALUES
('c8bs9dptrksse3r5j685p73krm', 6, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/136.0.0.0 Safari/537.36', '2025-05-11 19:45:11', '2025-05-11 19:58:41', 1),
('iqj0f7d1j15cfu0dtecnr2p5ve', 6, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/136.0.0.0 Safari/537.36', '2025-05-11 18:44:00', '2025-05-11 19:45:07', 1),
('nnhmbedalo8snhg1uptq9k8doj', 6, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/136.0.0.0 Safari/537.36', '2025-05-11 18:21:26', '2025-05-11 18:41:50', 1);

-- --------------------------------------------------------

--
-- Table structure for table `user_settings`
--

CREATE TABLE `user_settings` (
  `id` int NOT NULL,
  `user_id` int NOT NULL,
  `notification_email` tinyint(1) DEFAULT '1',
  `notification_sms` tinyint(1) DEFAULT '0',
  `theme_preference` enum('light','dark') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT 'light',
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `user_settings`
--

INSERT INTO `user_settings` (`id`, `user_id`, `notification_email`, `notification_sms`, `theme_preference`, `created_at`, `updated_at`) VALUES
(7, 93, 1, 0, 'light', '2025-05-07 22:39:46', NULL),
(8, 94, 1, 0, 'light', '2025-05-10 21:00:57', NULL),
(9, 95, 1, 0, 'light', '2025-05-11 10:51:20', NULL),
(10, 96, 1, 0, 'light', '2025-05-11 14:44:27', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `user_voucher_usage`
--

CREATE TABLE `user_voucher_usage` (
  `id` int NOT NULL,
  `user_id` int NOT NULL,
  `voucher_id` int NOT NULL,
  `transaction_id` int DEFAULT NULL,
  `used_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `user_voucher_usage`
--

INSERT INTO `user_voucher_usage` (`id`, `user_id`, `voucher_id`, `transaction_id`, `used_at`) VALUES
(1, 93, 5, 175, '2025-05-10 10:57:20'),
(3, 93, 4, 177, '2025-05-10 11:02:36'),
(4, 93, 9, 180, '2025-05-10 11:18:46'),
(6, 93, 9, 183, '2025-05-10 11:29:05'),
(7, 93, 9, 187, '2025-05-10 11:40:02'),
(10, 93, 7, 194, '2025-05-10 20:28:21'),
(13, 93, 7, 197, '2025-05-10 20:39:02'),
(14, 93, 7, 200, '2025-05-10 20:47:02'),
(18, 93, 3, 210, '2025-05-11 14:58:56'),
(19, 95, 1, 211, '2025-05-11 18:05:02');

-- --------------------------------------------------------

--
-- Table structure for table `voucher`
--

CREATE TABLE `voucher` (
  `id` int NOT NULL,
  `code` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `voucher_type` enum('extend_duration','percentage_discount','fixed_discount') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'extend_duration: tăng tháng sử dụng, percentage_discount: giảm tiền theo phần trăm, fixed_discount: giảm tiền cố định',
  `discount_value` decimal(15,2) NOT NULL COMMENT 'số tháng tăng thêm hoặc % giảm giá hoặc số tiền giảm cố định',
  `max_discount` decimal(15,2) DEFAULT NULL COMMENT 'giới hạn số tiền giảm tối đa (chỉ áp dụng cho percentage_discount)',
  `min_order_value` decimal(15,2) DEFAULT NULL COMMENT 'giá trị đơn hàng tối thiểu để áp dụng voucher',
  `quantity` int DEFAULT NULL COMMENT 'số lượng voucher có thể sử dụng',
  `limit_usage` int DEFAULT NULL COMMENT 'số lần tối đa một người dùng có thể sử dụng voucher này (NULL = không giới hạn)',
  `used_quantity` int NOT NULL DEFAULT '0' COMMENT 'số lượng voucher đã được sử dụng',
  `start_date` datetime NOT NULL COMMENT 'ngày bắt đầu hiệu lực',
  `end_date` datetime NOT NULL COMMENT 'ngày kết thúc hiệu lực',
  `is_active` tinyint(1) NOT NULL DEFAULT '1' COMMENT 'trạng thái kích hoạt',
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `voucher`
--

INSERT INTO `voucher` (`id`, `code`, `description`, `voucher_type`, `discount_value`, `max_discount`, `min_order_value`, `quantity`, `limit_usage`, `used_quantity`, `start_date`, `end_date`, `is_active`, `created_at`, `updated_at`) VALUES
(1, 'FIXED100K', 'Giảm 100,000đ cho đơn hàng từ 500,000đ', 'fixed_discount', 100000.00, NULL, 500000.00, 100, NULL, 1, '2025-05-10 00:00:00', '2025-06-10 23:59:59', 1, '2025-05-10 09:34:47', '2025-05-11 18:05:02'),
(2, 'PERCENT15', 'Giảm 15% tối đa 200,000đ cho đơn hàng từ 1 triệu đồng', 'percentage_discount', 15.00, 200000.00, 1000000.00, 50, NULL, 0, '2025-05-10 00:00:00', '2025-07-31 23:59:59', 1, '2025-05-10 09:34:47', '2025-05-11 14:58:44'),
(3, 'EXTRAMONTH', 'Tặng thêm 1 tháng khi gia hạn gói dịch vụ', 'extend_duration', 1.00, NULL, NULL, 200, NULL, 1, '2025-05-10 00:00:00', '2025-12-31 23:59:59', 1, '2025-05-10 09:34:47', '2025-05-11 14:58:56'),
(4, 'PERCENT10', 'Giảm 10% tối đa 100.000đ cho đơn hàng từ 200.000đ', 'percentage_discount', 10.00, 100000.00, 200000.00, 120, NULL, 1, '2025-05-01 00:00:00', '2025-07-31 23:59:59', 1, '2025-05-10 10:20:40', '2025-05-10 11:02:36'),
(5, 'FIXED200K', 'Giảm 200.000đ cho đơn hàng từ 1.000.000đ', 'fixed_discount', 200000.00, NULL, 1000000.00, 30, NULL, 1, '2025-05-01 00:00:00', '2025-12-31 23:59:59', 1, '2025-05-10 10:20:40', '2025-05-10 10:57:20'),
(6, 'PERCENT20', 'Giảm 20% tối đa 300.000đ cho đơn hàng từ 800.000đ', 'percentage_discount', 20.00, 300000.00, 800000.00, 40, NULL, 0, '2025-05-01 00:00:00', '2025-08-31 23:59:59', 1, '2025-05-10 10:20:40', '2025-05-10 10:20:40'),
(7, 'FIXED30K', 'Giảm 30.000đ cho đơn hàng từ 150.000đ', 'fixed_discount', 30000.00, NULL, 150000.00, 200, NULL, 3, '2025-05-01 00:00:00', '2025-06-30 23:59:59', 1, '2025-05-10 10:20:40', '2025-05-10 21:27:35'),
(8, 'PERCENT5', 'Giảm 5% tối đa 50.000đ cho mọi đơn hàng', 'percentage_discount', 5.00, 50000.00, NULL, 300, NULL, 0, '2025-05-01 00:00:00', '2025-12-31 23:59:59', 1, '2025-05-10 10:20:40', '2025-05-10 10:20:40'),
(9, 'FIXED25K', 'Giảm 25.000đ cho đơn hàng từ 100.000đ', 'fixed_discount', 25000.00, NULL, 100000.00, 250, NULL, 3, '2025-05-01 00:00:00', '2025-07-31 23:59:59', 1, '2025-05-10 10:20:40', '2025-05-10 11:40:02'),
(10, 'PERCENT12', 'Giảm 12% tối đa 120.000đ cho đơn hàng từ 400.000đ', 'percentage_discount', 12.00, 120000.00, 400000.00, 60, NULL, 0, '2025-05-01 00:00:00', '2025-09-30 23:59:59', 1, '2025-05-10 10:20:40', '2025-05-10 10:20:40'),
(11, 'FIXED80K', 'Giảm 80.000đ cho đơn hàng từ 250.000đ', 'fixed_discount', 80000.00, NULL, 250000.00, 70, NULL, 0, '2025-05-01 00:00:00', '2025-08-31 23:59:59', 1, '2025-05-10 10:20:40', '2025-05-10 10:20:40'),
(12, 'PERCENT18', 'Giảm 18% tối đa 180.000đ cho mọi đơn hàng', 'percentage_discount', 18.00, 180000.00, NULL, 80, NULL, 0, '2025-05-01 00:00:00', '2025-07-31 23:59:59', 1, '2025-05-10 10:20:40', '2025-05-10 10:20:40'),
(13, 'WELCOME50', 'Giảm 50.000đ cho đơn hàng từ 200.000đ, mỗi tài khoản chỉ dùng 1 lần', 'fixed_discount', 50000.00, NULL, 200000.00, 100, 1, 0, '2025-05-01 00:00:00', '2025-12-31 23:59:59', 1, '2025-05-10 10:52:34', '2025-05-10 10:52:34'),
(14, 'VIP20', 'Giảm 20% tối đa 500.000đ, mỗi tài khoản dùng tối đa 3 lần', 'percentage_discount', 20.00, 500000.00, NULL, 200, 3, 0, '2025-05-01 00:00:00', '2025-12-31 23:59:59', 1, '2025-05-10 10:52:34', '2025-05-10 10:52:34');

-- --------------------------------------------------------

--
-- Table structure for table `withdrawal_request`
--

CREATE TABLE `withdrawal_request` (
  `id` int NOT NULL,
  `user_id` int NOT NULL,
  `amount` decimal(10,2) NOT NULL,
  `bank_name` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `account_number` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `account_holder` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `status` enum('pending','completed','rejected') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'pending',
  `notes` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `withdrawal_request`
--

INSERT INTO `withdrawal_request` (`id`, `user_id`, `amount`, `bank_name`, `account_number`, `account_holder`, `status`, `notes`, `created_at`, `updated_at`) VALUES
(1, 92, 100000.00, 'Test Bank', '12345', 'Test User', 'pending', 'Test', '2025-05-11 06:26:00', '2025-05-11 06:26:00'),
(3, 93, 100000.00, 'Test Bank', '12345', 'Test User', 'pending', 'Test', '2025-05-11 06:32:25', '2025-05-11 06:34:45'),
(5, 93, 400000.00, 'Mbbank', '0981190564', 'DO VAN NGUYEN', 'completed', NULL, '2025-05-11 07:01:41', '2025-05-11 07:02:58'),
(6, 93, 500000.00, 'Mbbank', '0981 1905 64', 'DO VAN NGUYEN', 'pending', NULL, '2025-05-11 07:08:21', '2025-05-11 07:08:21'),
(7, 93, 500000.00, 'Mbbank', '0981190564', 'DO VAN NGUYEN', 'rejected', NULL, '2025-05-11 07:48:57', '2025-05-11 07:49:18'),
(8, 93, 500000.00, 'Mbbank', '0981 1905 64', 'DO VAN NGUYEN', 'pending', NULL, '2025-05-11 08:16:02', '2025-05-11 08:16:02'),
(9, 93, 500000.00, 'Mbbank', '0981 1905 64', 'DO VAN NGUYEN', 'pending', NULL, '2025-05-11 09:55:43', '2025-05-11 09:55:43'),
(10, 93, 100000.00, 'Test Bank', '0981 1905 64', 'DO VAN NGUYEN', 'pending', NULL, '2025-05-11 10:39:05', '2025-05-11 10:39:05'),
(11, 93, 2000000.00, 'Mbbank', '0981 1905 64', 'DO VAN NGUYEN', 'completed', NULL, '2025-05-11 11:06:49', '2025-05-11 11:07:09');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `account_groups`
--
ALTER TABLE `account_groups`
  ADD PRIMARY KEY (`registration_id`,`survey_account_id`),
  ADD KEY `idx_account_groups_survey_account_id` (`survey_account_id`);

--
-- Indexes for table `activity_logs`
--
ALTER TABLE `activity_logs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_user_action` (`user_id`,`action`),
  ADD KEY `idx_entity` (`entity_type`,`entity_id`);

--
-- Indexes for table `admin`
--
ALTER TABLE `admin`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `admin_username` (`admin_username`);

--
-- Indexes for table `collaborator`
--
ALTER TABLE `collaborator`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `referral_code` (`referral_code`),
  ADD KEY `idx_collaborator_user_id` (`user_id`);

--
-- Indexes for table `company_info`
--
ALTER TABLE `company_info`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `error_logs`
--
ALTER TABLE `error_logs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_error_type` (`error_type`),
  ADD KEY `idx_error_logs_user_id` (`user_id`);

--
-- Indexes for table `guide`
--
ALTER TABLE `guide`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uq_slug` (`slug`),
  ADD KEY `idx_guide_author_id` (`author_id`);

--
-- Indexes for table `invoice`
--
ALTER TABLE `invoice`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_invoice_transaction_history_id` (`transaction_history_id`);

--
-- Indexes for table `location`
--
ALTER TABLE `location`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `manager`
--
ALTER TABLE `manager`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `mount_point`
--
ALTER TABLE `mount_point`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_mount_point_location_id` (`location_id`);

--
-- Indexes for table `package`
--
ALTER TABLE `package`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uq_package_id` (`package_id`),
  ADD KEY `idx_active_order` (`is_active`,`display_order`);

--
-- Indexes for table `referral`
--
ALTER TABLE `referral`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_user_id` (`user_id`),
  ADD UNIQUE KEY `unique_referral_code` (`referral_code`);

--
-- Indexes for table `referral_commission`
--
ALTER TABLE `referral_commission`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_commission_referrer` (`referrer_id`),
  ADD KEY `fk_commission_referred_user` (`referred_user_id`),
  ADD KEY `idx_transaction_id` (`transaction_id`);

--
-- Indexes for table `referred_user`
--
ALTER TABLE `referred_user`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_referred_user` (`referred_user_id`),
  ADD KEY `fk_referred_user_referrer` (`referrer_id`);

--
-- Indexes for table `registration`
--
ALTER TABLE `registration`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_registration_user_id` (`user_id`),
  ADD KEY `idx_registration_package_id` (`package_id`),
  ADD KEY `idx_registration_location_id` (`location_id`),
  ADD KEY `idx_registration_collaborator_id` (`collaborator_id`),
  ADD KEY `idx_status_date` (`created_at`),
  ADD KEY `idx_user_date` (`user_id`,`created_at`);

--
-- Indexes for table `role_permissions`
--
ALTER TABLE `role_permissions`
  ADD PRIMARY KEY (`role`,`permission`);

--
-- Indexes for table `station`
--
ALTER TABLE `station`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_station_mountpoint_id` (`mountpoint_id`),
  ADD KEY `idx_station_manager_id` (`manager_id`);

--
-- Indexes for table `support_requests`
--
ALTER TABLE `support_requests`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_support_requests_user_id` (`user_id`),
  ADD KEY `idx_support_requests_status` (`status`);

--
-- Indexes for table `survey_account`
--
ALTER TABLE `survey_account`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username_acc` (`username_acc`),
  ADD KEY `idx_survey_account_registration_id` (`registration_id`);

--
-- Indexes for table `transaction_history`
--
ALTER TABLE `transaction_history`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_transaction_history_registration_id` (`registration_id`),
  ADD KEY `idx_transaction_history_user_id` (`user_id`),
  ADD KEY `idx_transaction_voucher` (`voucher_id`);

--
-- Indexes for table `user`
--
ALTER TABLE `user`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uq_user_email` (`email`),
  ADD UNIQUE KEY `uq_user_username` (`username`);

--
-- Indexes for table `user_sessions`
--
ALTER TABLE `user_sessions`
  ADD PRIMARY KEY (`session_id`),
  ADD KEY `idx_user_sessions_user_id` (`user_id`);

--
-- Indexes for table `user_settings`
--
ALTER TABLE `user_settings`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uq_user_settings_user_id` (`user_id`);

--
-- Indexes for table `user_voucher_usage`
--
ALTER TABLE `user_voucher_usage`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_user_voucher_usage` (`user_id`,`voucher_id`),
  ADD KEY `fk_uvu_voucher_id` (`voucher_id`),
  ADD KEY `fk_uvu_transaction_id` (`transaction_id`);

--
-- Indexes for table `voucher`
--
ALTER TABLE `voucher`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uq_voucher_code` (`code`),
  ADD KEY `idx_voucher_code` (`code`),
  ADD KEY `idx_voucher_dates` (`start_date`,`end_date`,`is_active`);

--
-- Indexes for table `withdrawal_request`
--
ALTER TABLE `withdrawal_request`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_withdrawal_user` (`user_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `activity_logs`
--
ALTER TABLE `activity_logs`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=242;

--
-- AUTO_INCREMENT for table `admin`
--
ALTER TABLE `admin`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `collaborator`
--
ALTER TABLE `collaborator`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `company_info`
--
ALTER TABLE `company_info`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `error_logs`
--
ALTER TABLE `error_logs`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `guide`
--
ALTER TABLE `guide`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT for table `invoice`
--
ALTER TABLE `invoice`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `location`
--
ALTER TABLE `location`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=64;

--
-- AUTO_INCREMENT for table `manager`
--
ALTER TABLE `manager`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `package`
--
ALTER TABLE `package`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `referral`
--
ALTER TABLE `referral`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `referral_commission`
--
ALTER TABLE `referral_commission`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `referred_user`
--
ALTER TABLE `referred_user`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `registration`
--
ALTER TABLE `registration`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=217;

--
-- AUTO_INCREMENT for table `support_requests`
--
ALTER TABLE `support_requests`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `transaction_history`
--
ALTER TABLE `transaction_history`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=213;

--
-- AUTO_INCREMENT for table `user`
--
ALTER TABLE `user`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=97;

--
-- AUTO_INCREMENT for table `user_settings`
--
ALTER TABLE `user_settings`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `user_voucher_usage`
--
ALTER TABLE `user_voucher_usage`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=20;

--
-- AUTO_INCREMENT for table `voucher`
--
ALTER TABLE `voucher`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT for table `withdrawal_request`
--
ALTER TABLE `withdrawal_request`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `account_groups`
--
ALTER TABLE `account_groups`
  ADD CONSTRAINT `fk_account_groups_registration` FOREIGN KEY (`registration_id`) REFERENCES `registration` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_account_groups_survey_account` FOREIGN KEY (`survey_account_id`) REFERENCES `survey_account` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `activity_logs`
--
ALTER TABLE `activity_logs`
  ADD CONSTRAINT `fk_activity_logs_user` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`) ON DELETE SET NULL ON UPDATE CASCADE;

--
-- Constraints for table `collaborator`
--
ALTER TABLE `collaborator`
  ADD CONSTRAINT `fk_collaborator_user` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`) ON DELETE RESTRICT ON UPDATE CASCADE;

--
-- Constraints for table `error_logs`
--
ALTER TABLE `error_logs`
  ADD CONSTRAINT `fk_error_logs_user` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`) ON DELETE SET NULL ON UPDATE CASCADE;

--
-- Constraints for table `guide`
--
ALTER TABLE `guide`
  ADD CONSTRAINT `fk_guide_admin` FOREIGN KEY (`author_id`) REFERENCES `admin` (`id`) ON DELETE RESTRICT ON UPDATE CASCADE;

--
-- Constraints for table `invoice`
--
ALTER TABLE `invoice`
  ADD CONSTRAINT `fk_invoice_transaction_history` FOREIGN KEY (`transaction_history_id`) REFERENCES `transaction_history` (`id`) ON DELETE RESTRICT ON UPDATE CASCADE;

--
-- Constraints for table `mount_point`
--
ALTER TABLE `mount_point`
  ADD CONSTRAINT `fk_mount_point_location` FOREIGN KEY (`location_id`) REFERENCES `location` (`id`) ON DELETE RESTRICT ON UPDATE CASCADE;

--
-- Constraints for table `referral`
--
ALTER TABLE `referral`
  ADD CONSTRAINT `fk_referral_user` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `referral_commission`
--
ALTER TABLE `referral_commission`
  ADD CONSTRAINT `fk_commission_referred_user` FOREIGN KEY (`referred_user_id`) REFERENCES `user` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_commission_referrer` FOREIGN KEY (`referrer_id`) REFERENCES `user` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_commission_transaction` FOREIGN KEY (`transaction_id`) REFERENCES `transaction_history` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `referred_user`
--
ALTER TABLE `referred_user`
  ADD CONSTRAINT `fk_referred_user_referred` FOREIGN KEY (`referred_user_id`) REFERENCES `user` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_referred_user_referrer` FOREIGN KEY (`referrer_id`) REFERENCES `user` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `registration`
--
ALTER TABLE `registration`
  ADD CONSTRAINT `fk_registration_collaborator` FOREIGN KEY (`collaborator_id`) REFERENCES `collaborator` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_registration_location` FOREIGN KEY (`location_id`) REFERENCES `location` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_registration_package` FOREIGN KEY (`package_id`) REFERENCES `package` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_registration_user` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`) ON DELETE SET NULL ON UPDATE CASCADE;

--
-- Constraints for table `station`
--
ALTER TABLE `station`
  ADD CONSTRAINT `fk_station_manager` FOREIGN KEY (`manager_id`) REFERENCES `manager` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_station_mount_point` FOREIGN KEY (`mountpoint_id`) REFERENCES `mount_point` (`id`) ON DELETE RESTRICT ON UPDATE CASCADE;

--
-- Constraints for table `support_requests`
--
ALTER TABLE `support_requests`
  ADD CONSTRAINT `fk_support_requests_user` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `survey_account`
--
ALTER TABLE `survey_account`
  ADD CONSTRAINT `fk_survey_account_registration` FOREIGN KEY (`registration_id`) REFERENCES `registration` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `transaction_history`
--
ALTER TABLE `transaction_history`
  ADD CONSTRAINT `fk_transaction_history_registration` FOREIGN KEY (`registration_id`) REFERENCES `registration` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_transaction_history_user` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`) ON DELETE RESTRICT ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_transaction_voucher` FOREIGN KEY (`voucher_id`) REFERENCES `voucher` (`id`) ON DELETE SET NULL ON UPDATE CASCADE;

--
-- Constraints for table `user_settings`
--
ALTER TABLE `user_settings`
  ADD CONSTRAINT `fk_user_settings_user` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `user_voucher_usage`
--
ALTER TABLE `user_voucher_usage`
  ADD CONSTRAINT `fk_uvu_transaction_id` FOREIGN KEY (`transaction_id`) REFERENCES `transaction_history` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_uvu_user_id` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_uvu_voucher_id` FOREIGN KEY (`voucher_id`) REFERENCES `voucher` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `withdrawal_request`
--
ALTER TABLE `withdrawal_request`
  ADD CONSTRAINT `fk_withdrawal_user` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
