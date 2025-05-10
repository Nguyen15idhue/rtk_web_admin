-- phpMyAdmin SQL Dump
-- version 5.2.2
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: May 10, 2025 at 08:03 AM
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
-- Database: `sa2`
--

-- --------------------------------------------------------

--
-- Table structure for table `account_groups`
--

CREATE TABLE `account_groups` (
  `registration_id` int NOT NULL,
  `survey_account_id` varchar(64) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

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
(142, 90, 'login', 'user', '90', NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/136.0.0.0 Safari/537.36', '2025-05-07 22:49:05'),
(143, 95, 'verification_email_sent', 'user', '95', NULL, '{\"email\":\"amx24933@toaik.com\",\"verification_token\":\"239623dc58...\",\"timestamp\":\"2025-05-07 22:56:11\"}', '::1', NULL, '2025-05-07 22:56:11'),
(144, 95, 'login', 'user', '95', NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/136.0.0.0 Safari/537.36', '2025-05-07 22:57:06'),
(145, 95, 'purchase', 'registration', '148', NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/136.0.0.0 Safari/537.36', '2025-05-07 22:57:43'),
(146, 95, 'login', 'user', '95', NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/136.0.0.0 Safari/537.36', '2025-05-07 23:15:45'),
(147, 95, 'renewal_request', 'registration', '149', NULL, '{\"registration_id\":\"149\",\"selected_accounts\":[\"2154\",\"2155\",\"2156\"],\"total_price\":810000,\"package\":\"G\\u00f3i 3 Th\\u00e1ng\"}', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/136.0.0.0 Safari/537.36', '2025-05-07 23:16:20'),
(148, 90, 'login', 'user', '90', NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/136.0.0.0 Safari/537.36', '2025-05-08 21:28:37'),
(149, 90, 'renewal_request', 'registration', '152', NULL, '{\"registration_id\":\"152\",\"selected_accounts\":[\"2170\",\"2171\"],\"total_price\":540000,\"package\":\"G\\u00f3i 3 Th\\u00e1ng\"}', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/136.0.0.0 Safari/537.36', '2025-05-08 21:47:21'),
(150, 90, 'renewal_request', 'registration', '155', NULL, '{\"registration_id\":\"155\",\"selected_accounts\":[\"2173\",\"2174\"],\"total_price\":540000,\"package\":\"G\\u00f3i 3 Th\\u00e1ng\"}', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/136.0.0.0 Safari/537.36', '2025-05-08 22:27:07'),
(151, 90, 'renewal_request', 'registration', '156', NULL, '{\"registration_id\":\"156\",\"selected_accounts\":[\"2176\",\"2177\"],\"total_price\":540000,\"package\":\"G\\u00f3i 3 Th\\u00e1ng\"}', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/136.0.0.0 Safari/537.36', '2025-05-08 22:37:24'),
(152, 90, 'login', 'user', '90', NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/136.0.0.0 Safari/537.36', '2025-05-09 07:54:44'),
(153, 90, 'renewal_request', 'registration', '157', NULL, '{\"registration_id\":\"157\",\"selected_accounts\":[\"2189\",\"2190\"],\"total_price\":540000,\"package\":\"G\\u00f3i 3 Th\\u00e1ng\"}', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/136.0.0.0 Safari/537.36', '2025-05-09 07:54:50'),
(154, 90, 'login', 'user', '90', NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/136.0.0.0 Safari/537.36', '2025-05-09 10:41:10'),
(155, 90, 'login', 'user', '90', NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/136.0.0.0 Safari/537.36', '2025-05-09 16:31:51'),
(156, 90, 'update_invoice_info', 'user', '90', NULL, NULL, NULL, NULL, '2025-05-09 16:32:16'),
(157, 90, 'renewal_request', 'registration', '158', NULL, '{\"registration_id\":\"158\",\"selected_accounts\":[\"2193\",\"2194\"],\"total_price\":200000,\"package\":\"G\\u00f3i 1 Th\\u00e1ng\"}', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/136.0.0.0 Safari/537.36', '2025-05-09 17:01:28'),
(158, 90, 'login', 'user', '90', NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/136.0.0.0 Safari/537.36', '2025-05-09 21:58:24'),
(159, 90, 'update_invoice_info', 'user', '90', NULL, NULL, NULL, NULL, '2025-05-09 21:59:39'),
(160, 90, 'login', 'user', '90', NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/136.0.0.0 Safari/537.36', '2025-05-10 07:16:54');

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
(2, 'Hello', 'admin', '$2y$10$pkuCs/ggxoVEA/gxeGwPeOAAsxtNTIUSceJoUtJ84twv0UQpYBDqW', 'customercare', '2025-04-27 14:31:55', '2025-05-09 16:11:48'),
(6, 'Nguyễn Văn A', 'ad', '$2y$10$bmmrydfRBxWzKlORURhna.wwLglluuigMkdylqIawKPXONK38usFC', 'admin', '2025-05-07 23:02:34', '2025-05-09 16:09:39');

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

--
-- Dumping data for table `guide`
--

INSERT INTO `guide` (`id`, `title`, `slug`, `content`, `author_id`, `topic`, `status`, `thumbnail`, `image`, `view_count`, `created_at`, `updated_at`, `published_at`) VALUES
(14, 'Testing', 'testing', '<p><em>Đ&acirc;y l&agrave;&nbsp;</em></p>', 6, '', 'published', '', '', 0, '2025-05-08 10:28:52', '2025-05-09 22:46:58', NULL),
(15, 'Hôm này ăn gì', 'hom-nay-an-gi', '<p>Ăn g&igrave;</p>\n<p><img src=\"https://static.vecteezy.com/system/resources/previews/025/894/618/non_2x/cute-word-hello-cartoon-style-illustration-vector.jpg\" alt=\"Cute word \'Hello\' Cartoon style, Vector illustration. 25894618 Vector Art  at Vecteezy\" /></p>', 6, '', 'published', '', NULL, 0, '2025-05-09 10:32:50', '2025-05-09 17:43:42', NULL),
(16, 'Hướng dẫn', 'huong-dan', '', 6, '', 'published', NULL, NULL, 0, '2025-05-09 11:52:31', '2025-05-09 22:46:57', NULL),
(17, 'Chỉ 1 chút', 'chi-1-chut', '', 6, '', 'published', 'logo.jpg', NULL, 0, '2025-05-09 16:12:16', '2025-05-10 07:27:44', NULL);

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
(8, 144, 'approved', 'test.pdf', NULL, '2025-05-10 07:20:52');

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
  `name` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Tên người quản lý',
  `phone` varchar(15) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'SĐT người quản lý',
  `address` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Địa chỉ người quản lý'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `manager`
--

INSERT INTO `manager` (`id`, `name`, `phone`, `address`) VALUES
(1, 'Nguyễn Văn A', '0912345678', 'Hà Nội'),
(2, 'Trần Thị B', '0987654321', 'Hải Phòng'),
(3, 'A', '1', '2');

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
-- Table structure for table `payment_methods`
--

CREATE TABLE `payment_methods` (
  `id` int NOT NULL,
  `user_id` int NOT NULL,
  `bank_name` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `account_number` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `account_holder` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `is_default` tinyint(1) DEFAULT '0',
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

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
(134, 92, 1, 63, NULL, 1, '2025-05-07 07:05:10', '2025-06-14 00:29:51', 100000.00, 0, 0.00, 100000.00, 'pending', '2025-05-07 07:05:10', '2025-05-07 07:05:10', NULL, NULL),
(136, 92, 4, 63, NULL, 3, '2025-05-07 07:09:12', '2026-05-14 00:29:51', 2700000.00, 0, 0.00, 2700000.00, 'pending', '2025-05-07 07:09:12', '2025-05-07 07:09:12', NULL, NULL),
(137, 92, 2, 63, NULL, 1, '2025-05-07 07:15:10', '2025-08-14 00:29:51', 270000.00, 0, 0.00, 270000.00, 'pending', '2025-05-07 07:15:10', '2025-05-07 07:15:10', NULL, NULL),
(138, 92, 1, 63, NULL, 3, '2025-05-14 00:29:51', '2025-06-14 00:29:51', 100000.00, 0, 0.00, 300000.00, 'pending', '2025-05-07 07:25:29', '2025-05-07 07:25:29', NULL, NULL),
(139, 92, 1, 63, NULL, 7, '2025-05-14 00:29:51', '2025-06-14 00:29:51', 100000.00, 0, 0.00, 700000.00, 'pending', '2025-05-07 07:26:49', '2025-05-07 07:26:49', NULL, NULL),
(140, 92, 2, 63, NULL, 2, '2025-05-14 00:29:51', '2025-08-14 00:29:51', 270000.00, 0, 0.00, 540000.00, 'pending', '2025-05-07 21:55:52', '2025-05-07 21:55:52', NULL, NULL),
(141, 92, 3, 63, NULL, 3, '2025-05-14 00:29:51', '2025-11-14 00:29:51', 500000.00, 0, 0.00, 1500000.00, 'pending', '2025-05-07 21:57:40', '2025-05-07 21:57:40', NULL, NULL),
(142, 92, 1, 62, NULL, 5, '2025-05-07 21:59:27', '2025-06-07 21:59:27', 100000.00, 0, 0.00, 500000.00, 'pending', '2025-05-07 21:59:27', '2025-05-07 21:59:27', NULL, NULL),
(143, 92, 1, 63, NULL, 7, '2025-05-07 22:01:05', '2025-06-07 22:01:05', 100000.00, 0, 0.00, 700000.00, 'pending', '2025-05-07 22:01:05', '2025-05-07 22:01:05', NULL, NULL),
(144, 92, 2, 63, NULL, 99, '2025-05-07 22:01:23', '2025-08-07 22:01:23', 270000.00, 0, 0.00, 26730000.00, 'pending', '2025-05-07 22:01:23', '2025-05-07 22:01:23', NULL, NULL),
(145, 92, 1, 63, NULL, 5, '2025-05-07 22:10:34', '2025-06-07 22:10:34', 100000.00, 0, 0.00, 500000.00, 'active', '2025-05-07 22:10:34', '2025-05-08 21:15:26', NULL, NULL),
(146, 92, 2, 63, NULL, 4, '2025-05-14 00:29:51', '2025-08-14 00:29:51', 270000.00, 0, 0.00, 1080000.00, 'pending', '2025-05-07 22:11:05', '2025-05-07 22:11:05', NULL, NULL),
(147, 92, 3, 63, NULL, 8, '2025-05-14 00:29:51', '2025-05-14 00:29:51', 500000.00, 0, 0.00, 4000000.00, 'rejected', '2025-05-07 22:13:30', '2025-05-08 13:09:25', NULL, 'c'),
(148, 90, 3, 12, NULL, 3, '2025-05-09 00:00:00', '2026-02-09 23:59:59', 270000.00, 0, 0.00, 810000.00, 'active', '2025-05-07 22:57:43', '2025-05-09 17:00:26', NULL, NULL);

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
('58', 'G216', 'Trạm a Hưng gửi', '67', 20.96750510, 106.71247261, 0, NULL),
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

-- --------------------------------------------------------

--
-- Table structure for table `transaction_history`
--

CREATE TABLE `transaction_history` (
  `id` int NOT NULL,
  `registration_id` int DEFAULT NULL,
  `user_id` int NOT NULL,
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

INSERT INTO `transaction_history` (`id`, `registration_id`, `user_id`, `transaction_type`, `amount`, `status`, `payment_method`, `payment_image`, `export_invoice`, `invoice_info`, `payment_confirmed`, `payment_confirmed_at`, `payment_reference`, `created_at`, `updated_at`) VALUES
(139, 143, 92, 'purchase', 700000.00, 'pending', NULL, NULL, 0, NULL, 0, NULL, NULL, '2025-05-07 22:01:05', '2025-05-07 22:01:05'),
(140, 144, 92, 'purchase', 26730000.00, 'pending', NULL, NULL, 0, NULL, 0, NULL, NULL, '2025-05-07 22:01:23', '2025-05-07 22:01:23'),
(141, 145, 92, 'purchase', 500000.00, 'completed', NULL, 'reg_145_1746630640.png', 0, NULL, 0, NULL, NULL, '2025-05-07 22:10:34', '2025-05-08 21:15:26'),
(142, 146, 92, 'renewal', 1080000.00, 'pending', NULL, 'reg_146_1746630676.png', 0, NULL, 0, NULL, NULL, '2025-05-07 22:11:05', '2025-05-07 22:11:16'),
(143, 147, 92, 'renewal', 4000000.00, 'failed', NULL, NULL, 0, NULL, 0, NULL, NULL, '2025-05-07 22:13:30', '2025-05-08 22:23:29'),
(144, 148, 90, 'purchase', 810000.00, 'completed', NULL, NULL, 0, NULL, 0, NULL, NULL, '2025-05-07 22:57:43', '2025-05-10 07:20:43');

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
  `is_collaborator` tinyint(1) NOT NULL DEFAULT '0',
  `is_company` tinyint(1) DEFAULT NULL,
  `company_name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `tax_code` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
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

INSERT INTO `user` (`id`, `username`, `email`, `password`, `phone`, `is_collaborator`, `is_company`, `company_name`, `tax_code`, `tax_registered`, `created_at`, `updated_at`, `deleted_at`, `status`, `email_verified`, `email_verify_token`) VALUES
(88, 'Long2004', 'tranhailong2408@gmail.com', '$2y$10$Ykj5ewmTa9z.EWdiVDPN6OKJ0lCXZU7L7ndZ7DDNd0LCXrHVraTOq', '0999999445', 0, 1, 'as', '123', NULL, '2025-04-27 14:55:01', '2025-05-04 09:08:21', NULL, 1, 1, NULL),
(89, 'Long2005', 'tranhailong2410@gmail.com', '$2y$10$4/NI4svx6977OTC0q13r5eRE5Uovn7nmR47Z.j3V9yy19n9XacQBa', '0900000005', 0, 1, 'ad', '123', NULL, '2025-04-29 15:00:08', '2025-05-04 09:08:23', NULL, 1, 1, NULL),
(90, 'Long2002', 'tranhailong2407@gmail.com', '$2y$10$Qt93QW.LtJzj/Vze0box4OmShRAfRxKCJdUtpYIJ5Ccu6H3nSy9Qe', '0999999443', 0, 1, 'as', '1233333333', NULL, '2025-05-04 09:08:01', '2025-05-09 21:59:39', NULL, 1, 1, NULL),
(91, 'nguyendozxc15@gmail.com', 'nguyendozxc15@gmail.com', '$2y$10$y8rSLvI2J48XZjTCb9IIgOmEf5Tz42r0OVLOrrlovJHt8JjVrNRvq', '0981190564', 0, 0, NULL, NULL, NULL, '2025-05-05 11:09:06', '2025-05-05 11:09:26', NULL, 1, 1, NULL),
(92, 'dovannguyen2005bv@gmail.com', 'dovannguyen2005bv@gmail.com', '$2y$10$BUQc5aTNhk0h1mBfQhrlG.1kkVfb8t.9Hj6lnHZYr43CEQUqBlLpS', '0981190562', 0, 0, 'Công ty cổ phần công nghệ Otek', '2222333332', NULL, '2025-05-05 14:33:57', '2025-05-09 16:58:15', NULL, 1, 1, NULL),
(95, 'nguyendozx', 'amx24933@toaik.com', '$2y$10$gZNVcTlouUcZAiuCaoC7zexP1vskUVWbTKJ0ByhlkMIHNq/xDgnM6', '0900000000', 0, 0, NULL, NULL, NULL, '2025-05-07 22:56:07', '2025-05-09 16:58:15', NULL, 1, 1, NULL),
(97, 'Long20022', '123456@gmail.com', '$2y$10$UKP6KiaINmzEjrz1Fm4p/eWN2Rypyw/IZ86SD9XMPEzuCeTwNGj/O', '123', 0, 0, NULL, NULL, NULL, '2025-05-09 17:39:16', NULL, NULL, 1, 0, NULL);

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
('02c870us909cbd9omhlt3sdmra', 6, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/136.0.0.0 Safari/537.36', '2025-05-10 12:22:38', '2025-05-10 15:02:34', 1),
('2ib14ov858oe5nhgja07ag345g', 6, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/136.0.0.0 Safari/537.36', '2025-05-08 20:47:34', '2025-05-08 21:27:43', 1),
('47d1g4tekg5grmcjjt1u7binrl', 6, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/136.0.0.0 Safari/537.36', '2025-05-07 23:19:38', '2025-05-07 23:19:52', 1),
('4hk4qnqkq5bb1nb863ou8kcdjg', 6, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/136.0.0.0 Safari/537.36', '2025-05-08 07:12:20', '2025-05-08 08:07:57', 1),
('71pk9t6rjget543bjcb418jck9', 6, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/136.0.0.0 Safari/537.36', '2025-05-09 12:43:20', '2025-05-09 16:09:41', 0),
('7jmdli56kpdq6ope7s1jugel04', 6, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/136.0.0.0 Safari/537.36', '2025-05-09 16:09:44', '2025-05-09 16:52:05', 1),
('85su7909vg2rusp5vrlm0jf6i3', 6, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/136.0.0.0 Safari/537.36', '2025-05-08 10:17:41', '2025-05-08 11:00:42', 1),
('a1d4o4oj2gn9m1icddhporcgms', 6, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/136.0.0.0 Safari/537.36', '2025-05-09 19:31:47', '2025-05-09 20:15:22', 1),
('a9k386umc9v86u54o1id47866i', 6, '::1', 'Mozilla/5.0 (Linux; Android 6.0; Nexus 5 Build/MRA58N) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/136.0.0.0 Mobile Safari/537.36', '2025-05-09 20:15:33', '2025-05-09 21:09:51', 1),
('an3p5hnp9din4m4h4s7ijk0uim', 6, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/136.0.0.0 Safari/537.36', '2025-05-09 08:53:58', '2025-05-09 10:40:37', 1),
('aq9lirkrg22cjjpjieq8fv2ll0', 6, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/136.0.0.0 Safari/537.36', '2025-05-09 11:17:55', '2025-05-09 11:57:28', 1),
('b4cfhiiucuq64ev1kat37uk5b8', 6, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/136.0.0.0 Safari/537.36', '2025-05-09 10:41:30', '2025-05-09 11:58:40', 1),
('c246em0818gnbp4skka181oihu', 6, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/136.0.0.0 Safari/537.36', '2025-05-08 09:32:38', '2025-05-08 09:53:16', 1),
('dao4tbas89tuit8dvg7a96fmqm', 6, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/136.0.0.0 Safari/537.36', '2025-05-10 10:17:45', '2025-05-10 11:36:35', 1),
('dj9nrpdri6tjq045rjikes4tdt', 6, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/136.0.0.0 Safari/537.36', '2025-05-09 11:58:43', '2025-05-09 12:09:08', 1),
('egsb3tmsv2sf8k70vecp24fd1n', 6, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/136.0.0.0 Safari/537.36', '2025-05-10 07:17:57', '2025-05-10 07:46:33', 0),
('f8d51fhc2rg1tuvoivj044je5h', 6, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/136.0.0.0 Safari/537.36', '2025-05-08 09:53:32', '2025-05-08 10:15:34', 1),
('fefb0j27j41ukbaopmbruro2mk', 6, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/136.0.0.0 Safari/537.36', '2025-05-10 10:17:00', '2025-05-10 10:17:09', 1),
('ghdrtfm1magchilqhgpjfehgiu', 6, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/136.0.0.0 Safari/537.36', '2025-05-09 16:45:28', '2025-05-09 17:43:54', 1),
('hb5dslfp7lg1s8ag8oun3e7o5s', 6, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/136.0.0.0 Safari/537.36', '2025-05-10 07:46:39', '2025-05-10 08:10:22', 1),
('jlokr1p8p844i558dfdp858ivo', 6, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/136.0.0.0 Safari/537.36', '2025-05-09 07:42:19', '2025-05-09 08:02:35', 1),
('o0bk0eb6psaj7hk7k6dipklbii', 6, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/136.0.0.0 Safari/537.36', '2025-05-08 23:17:11', '2025-05-08 23:32:44', 1),
('odnd711tl5e90u4ubdkjoartc4', 6, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/136.0.0.0 Safari/537.36', '2025-05-08 10:17:18', '2025-05-08 10:17:27', 1),
('piu4f3866265j5v4h4nkm1444l', 6, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/136.0.0.0 Safari/537.36', '2025-05-08 13:02:22', '2025-05-08 14:33:15', 1),
('r4m2vkfvb017qhff8aam1ik2g0', 6, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/136.0.0.0 Safari/537.36', '2025-05-07 23:12:28', '2025-05-07 23:21:07', 1),
('sf1s10ebbek9069vp7khd81nin', 6, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/136.0.0.0 Safari/537.36', '2025-05-10 11:36:43', '2025-05-10 11:37:54', 1),
('sivhg91ft4et9autnvkpaml8sv', 6, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/136.0.0.0 Safari/537.36', '2025-05-09 22:21:25', '2025-05-09 23:30:14', 1),
('sk0qh0251u9ua08p64pkjikgkm', 6, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/136.0.0.0 Safari/537.36', '2025-05-08 11:00:50', '2025-05-08 11:00:52', 1),
('srm913h5er4kd8246d530mrahf', 6, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/136.0.0.0 Safari/537.36', '2025-05-10 07:39:36', '2025-05-10 07:39:53', 1),
('ta46om7b0els57u1v74bsbtr6e', 6, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/136.0.0.0 Safari/537.36', '2025-05-08 20:58:23', '2025-05-08 23:17:06', 1);

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
(7, 95, 1, 0, 'light', '2025-05-07 22:56:07', NULL),
(8, 97, 1, 0, 'light', '2025-05-09 17:39:16', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `withdrawal`
--

CREATE TABLE `withdrawal` (
  `id` int NOT NULL,
  `collaborator_id` int NOT NULL,
  `amount` decimal(15,2) NOT NULL,
  `bank_info` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `status` enum('pending','completed','rejected') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT 'pending',
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  `processed_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

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
-- Indexes for table `payment_methods`
--
ALTER TABLE `payment_methods`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_payment_methods_user_id` (`user_id`);

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
  ADD KEY `idx_transaction_history_user_id` (`user_id`);

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
-- Indexes for table `withdrawal`
--
ALTER TABLE `withdrawal`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_withdrawal_collaborator_id` (`collaborator_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `activity_logs`
--
ALTER TABLE `activity_logs`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=161;

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
-- AUTO_INCREMENT for table `error_logs`
--
ALTER TABLE `error_logs`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `guide`
--
ALTER TABLE `guide`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=18;

--
-- AUTO_INCREMENT for table `invoice`
--
ALTER TABLE `invoice`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `location`
--
ALTER TABLE `location`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=64;

--
-- AUTO_INCREMENT for table `manager`
--
ALTER TABLE `manager`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `package`
--
ALTER TABLE `package`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `payment_methods`
--
ALTER TABLE `payment_methods`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `registration`
--
ALTER TABLE `registration`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=165;

--
-- AUTO_INCREMENT for table `transaction_history`
--
ALTER TABLE `transaction_history`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=151;

--
-- AUTO_INCREMENT for table `user`
--
ALTER TABLE `user`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=98;

--
-- AUTO_INCREMENT for table `user_settings`
--
ALTER TABLE `user_settings`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `withdrawal`
--
ALTER TABLE `withdrawal`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

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
-- Constraints for table `payment_methods`
--
ALTER TABLE `payment_methods`
  ADD CONSTRAINT `fk_payment_methods_user` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

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
-- Constraints for table `survey_account`
--
ALTER TABLE `survey_account`
  ADD CONSTRAINT `fk_survey_account_registration` FOREIGN KEY (`registration_id`) REFERENCES `registration` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `transaction_history`
--
ALTER TABLE `transaction_history`
  ADD CONSTRAINT `fk_transaction_history_registration` FOREIGN KEY (`registration_id`) REFERENCES `registration` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_transaction_history_user` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`) ON DELETE RESTRICT ON UPDATE CASCADE;

--
-- Constraints for table `user_settings`
--
ALTER TABLE `user_settings`
  ADD CONSTRAINT `fk_user_settings_user` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `withdrawal`
--
ALTER TABLE `withdrawal`
  ADD CONSTRAINT `fk_withdrawal_collaborator` FOREIGN KEY (`collaborator_id`) REFERENCES `collaborator` (`id`) ON DELETE RESTRICT ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
