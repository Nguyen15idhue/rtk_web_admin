-- phpMyAdmin SQL Dump
-- version 5.2.2
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: May 27, 2025 at 07:25 AM
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
  `notify_content` text COLLATE utf8mb4_unicode_ci,
  `has_read` tinyint DEFAULT '0',
  `ip_address` varchar(45) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `user_agent` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `activity_logs`
--

INSERT INTO `activity_logs` (`id`, `user_id`, `action`, `entity_type`, `entity_id`, `old_values`, `new_values`, `notify_content`, `has_read`, `ip_address`, `user_agent`, `created_at`) VALUES
(366, 90, 'create_support_request', 'support_requests', '30', NULL, NULL, 'Tạo yêu cầu hỗ trợ mới #30', 0, NULL, NULL, '2025-05-20 07:52:05'),
(367, 90, 'create_support_request', 'support_requests', '31', NULL, NULL, 'Tạo yêu cầu hỗ trợ mới #31', 0, NULL, NULL, '2025-05-20 09:08:51'),
(368, 105, 'reject_transaction', 'transaction', '217', '{\"status\":\"pending\"}', '{\"status\":\"failed\",\"reason\":\"c\",\"registration_id\":224}', 'Giao dịch #217 đã bị từ chối. Lý do: c', 0, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/136.0.0.0 Safari/537.36', '2025-05-21 09:20:38'),
(369, 105, 'invoice_reverted', 'invoice', '9', '{\"status\":\"rejected\"}', '{\"status\":\"pending\",\"customer_id\":105}', 'Hóa đơn #9 đang gặp vấn đề và cần chỉnh sửa lại.', 0, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/136.0.0.0 Safari/537.36', '2025-05-21 15:17:00'),
(370, 105, 'invoice_sent', 'invoice', '9', '{\"status\":\"pending\"}', '{\"status\":\"approved\",\"file\":\"1747815452_muclucKTMT.pdf\",\"customer_id\":105}', 'Hóa đơn #9 đã được gửi.', 0, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/136.0.0.0 Safari/537.36', '2025-05-21 15:17:32'),
(381, 105, 'invoice_reverted', 'invoice', '9', '{\"status\":\"approved\"}', '{\"status\":\"pending\",\"customer_id\":105}', 'Hóa đơn #9 đang gặp vấn đề và cần chỉnh sửa lại.', 0, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/136.0.0.0 Safari/537.36', '2025-05-21 16:59:04'),
(382, 105, 'invoice_sent', 'invoice', '9', '{\"status\":\"pending\"}', '{\"status\":\"approved\",\"file\":\"1747822305_test.pdf\",\"customer_id\":105}', 'Hóa đơn #9 đã được gửi.', 0, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/136.0.0.0 Safari/537.36', '2025-05-21 17:11:45'),
(383, 105, 'invoice_reverted', 'invoice', '9', '{\"status\":\"approved\"}', '{\"status\":\"pending\",\"customer_id\":105}', 'Hóa đơn #9 đang gặp vấn đề và cần chỉnh sửa lại.', 0, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/136.0.0.0 Safari/537.36', '2025-05-21 17:13:02'),
(384, 90, 'invoice_reverted', 'invoice', '8', '{\"status\":\"approved\"}', '{\"status\":\"pending\",\"customer_id\":90}', 'Hóa đơn #8 đang gặp vấn đề và cần chỉnh sửa lại.', 0, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/136.0.0.0 Safari/537.36', '2025-05-21 17:14:24'),
(385, 90, 'revert_transaction', 'transaction', '220', '{\"status\":\"active\"}', '{\"status\":\"pending\",\"customer_id\":90}', 'Giao dịch #220 đã được hoàn lại về trạng thái chờ xử lý.', 0, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/136.0.0.0 Safari/537.36', '2025-05-22 10:42:53'),
(391, 90, 'revert_transaction', 'transaction', '214', '{\"status\":\"active\"}', '{\"status\":\"pending\",\"customer_id\":90}', 'Giao dịch #214 đã được hoàn lại về trạng thái chờ xử lý.', 0, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/136.0.0.0 Safari/537.36', '2025-05-22 22:17:26'),
(392, 90, 'approve_transaction', 'transaction', '220', '{\"status\":\"pending\"}', '{\"status\":\"completed\",\"created_accounts\":[],\"customer_id\":90}', 'Giao dịch #220 (Tạo mới tài khoản) đã được duyệt. Số tài khoản sẽ tạo: 1', 0, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/136.0.0.0 Safari/537.36', '2025-05-22 22:17:31'),
(393, 90, 'approve_transaction', 'transaction', '214', '{\"status\":\"pending\"}', '{\"status\":\"completed\",\"created_accounts\":[],\"customer_id\":90}', 'Giao dịch #214 (Tạo mới tài khoản) đã được duyệt. Số tài khoản sẽ tạo: 1', 0, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/136.0.0.0 Safari/537.36', '2025-05-22 22:20:44'),
(394, 90, 'account_updated_by_admin', 'account', '2381', NULL, NULL, 'Quản trị viên đã cập nhật tài khoản \'YBI400\' (ID Tài khoản: 2381).', 0, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/136.0.0.0 Safari/537.36', '2025-05-22 22:31:45'),
(395, 90, 'account_updated_by_admin', 'account', '2381', NULL, NULL, 'Quản trị viên đã cập nhật tài khoản \'YBI400\' (ID Tài khoản: 2381).', 0, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/136.0.0.0 Safari/537.36', '2025-05-22 22:41:39'),
(396, 90, 'support_request_updated', 'support_request', '25', '{\"status\":\"pending\",\"admin_response\":null}', '{\"status\":\"closed\",\"admin_response\":\"Ok nh\\u00e9\"}', 'Yêu cầu hỗ trợ #25 đã được cập nhật trạng thái thành closed.', 0, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/136.0.0.0 Safari/537.36', '2025-05-22 22:54:26'),
(397, 90, 'revert_transaction', 'transaction', '214', '{\"status\":\"active\"}', '{\"status\":\"pending\",\"customer_id\":90}', 'Giao dịch #214 đã được hoàn lại về trạng thái chờ xử lý.', 0, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/136.0.0.0 Safari/537.36', '2025-05-22 23:31:54'),
(398, 90, 'revert_transaction', 'transaction', '220', '{\"status\":\"active\"}', '{\"status\":\"pending\",\"customer_id\":90}', 'Giao dịch #220 đã được hoàn lại về trạng thái chờ xử lý.', 0, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/136.0.0.0 Safari/537.36', '2025-05-23 10:10:46'),
(399, 90, 'approve_transaction', 'transaction', '220', '{\"status\":\"pending\"}', '{\"status\":\"completed\",\"created_accounts\":[],\"customer_id\":90}', 'Giao dịch #220 (Tạo mới tài khoản) đã được duyệt. Số tài khoản sẽ tạo: 1', 0, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/136.0.0.0 Safari/537.36', '2025-05-23 10:19:10'),
(400, 90, 'account_updated_by_admin', 'account', '2383', NULL, NULL, 'Quản trị viên đã cập nhật tài khoản \'CMA001\' (ID Tài khoản: 2383).', 0, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/136.0.0.0 Safari/537.36', '2025-05-23 10:19:45'),
(401, 88, 'account_updated_by_admin', 'account', '2383', NULL, NULL, 'Quản trị viên đã cập nhật tài khoản \'CMA001\' (ID Tài khoản: 2383).', 0, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/136.0.0.0 Safari/537.36', '2025-05-23 10:20:00'),
(402, 88, 'revert_transaction', 'transaction', '220', '{\"status\":\"active\"}', '{\"status\":\"pending\",\"customer_id\":88}', 'Giao dịch #220 đã được hoàn lại về trạng thái chờ xử lý.', 0, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/136.0.0.0 Safari/537.36', '2025-05-23 13:01:52'),
(403, 105, 'approve_transaction', 'transaction', '218', '{\"status\":\"pending\"}', '{\"status\":\"completed\",\"created_accounts\":[],\"customer_id\":105}', 'Giao dịch #218 (Tạo mới tài khoản) đã được duyệt. Số tài khoản sẽ tạo: 1', 0, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/136.0.0.0 Safari/537.36', '2025-05-23 13:02:09'),
(404, 105, 'approve_transaction', 'transaction', '219', '{\"status\":\"failed\"}', '{\"status\":\"completed\",\"scheduled_accounts\":[],\"renewed_accounts\":[],\"customer_id\":105}', 'Giao dịch #219 (Gia hạn) đã được duyệt. Tài khoản dự kiến: 0, Đã gia hạn: 0', 0, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/136.0.0.0 Safari/537.36', '2025-05-23 13:02:34'),
(405, 105, 'renewal_request', 'registration', '231', NULL, '{\"registration_id\":\"231\",\"selected_accounts\":[\"2385\"],\"total_price\":270000,\"package\":\"Gói 3 Tháng\",\"location\":\"Cà Mau\"}', 'Yêu cầu gia hạn gói dịch vụ cho đăng ký #231 - Gói: Gói 3 Tháng', 0, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/136.0.0.0 Safari/537.36', '2025-05-23 13:11:19'),
(406, 105, 'approve_transaction', 'transaction', '222', '{\"status\":\"pending\"}', '{\"status\":\"completed\",\"scheduled_accounts\":[\"2385\"],\"renewed_accounts\":[],\"customer_id\":105}', 'Giao dịch #222 (Gia hạn) đã được duyệt. Tài khoản dự kiến: 1, Đã gia hạn: 0', 0, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/136.0.0.0 Safari/537.36', '2025-05-23 13:11:50'),
(407, NULL, 'account_updated_by_admin', 'account', '2385', NULL, NULL, 'Quản trị viên đã cập nhật tài khoản \'CMA001\' (ID Tài khoản: 2385).', 0, '::1', 'PHP-cURL', '2025-05-23 13:11:50'),
(408, 90, 'approve_transaction', 'transaction', '221', '{\"status\":\"pending\"}', '{\"status\":\"completed\",\"created_accounts\":[],\"customer_id\":90}', 'Giao dịch #221 (Tạo mới tài khoản) đã được duyệt. Số tài khoản sẽ tạo: 1', 0, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/136.0.0.0 Safari/537.36', '2025-05-23 13:25:33'),
(409, 105, 'revert_transaction', 'transaction', '222', '{\"status\":\"active\"}', '{\"status\":\"pending\",\"customer_id\":105}', 'Giao dịch #222 đã được hoàn lại về trạng thái chờ xử lý.', 0, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/136.0.0.0 Safari/537.36', '2025-05-23 13:28:26'),
(410, 90, 'purchase', 'registration', '232', NULL, '{\"registration_id\":\"232\",\"selected_accounts\":[1],\"total_price\":\"297000\",\"package\":\"Gói 3 Tháng\",\"location\":\"Yên Bái\"}', 'Mua gói dịch vụ: Gói 3 Tháng - Số lượng: 1', 0, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/136.0.0.0 Safari/537.36', '2025-05-24 21:20:42'),
(411, 90, 'purchase', 'registration', '233', NULL, '{\"registration_id\":\"233\",\"selected_accounts\":[1],\"total_price\":\"270000\",\"package\":\"Gói 3 Tháng\",\"location\":\"Cà Mau\"}', 'Mua gói dịch vụ: Gói 3 Tháng - Số lượng: 1', 0, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/136.0.0.0 Safari/537.36', '2025-05-24 21:25:42'),
(412, 90, 'revert_transaction', 'transaction', '221', '{\"status\":\"active\"}', '{\"status\":\"pending\",\"customer_id\":90}', 'Giao dịch #221 đã được hoàn lại về trạng thái chờ xử lý.', 0, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/136.0.0.0 Safari/537.36', '2025-05-24 21:27:16'),
(413, 105, 'revert_transaction', 'transaction', '219', '{\"status\":\"active\"}', '{\"status\":\"pending\",\"customer_id\":105}', 'Giao dịch #219 đã được hoàn lại về trạng thái chờ xử lý.', 0, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/136.0.0.0 Safari/537.36', '2025-05-24 21:27:20'),
(414, 105, 'revert_transaction', 'transaction', '218', '{\"status\":\"active\"}', '{\"status\":\"pending\",\"customer_id\":105}', 'Giao dịch #218 đã được hoàn lại về trạng thái chờ xử lý.', 0, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/136.0.0.0 Safari/537.36', '2025-05-24 21:27:22'),
(415, 90, 'approve_transaction', 'transaction', '223', '{\"status\":\"pending\"}', '{\"status\":\"completed\",\"created_accounts\":[],\"customer_id\":90}', 'Giao dịch #223 (Tạo mới tài khoản) đã được duyệt. Số tài khoản sẽ tạo: 1', 0, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/136.0.0.0 Safari/537.36', '2025-05-24 21:27:58'),
(416, 90, 'request_invoice', 'invoice', '10', NULL, '{\"transaction_history_id\":223}', 'Yêu cầu xuất hóa đơn cho giao dịch #223', 0, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/136.0.0.0 Safari/537.36', '2025-05-24 21:28:06'),
(417, 90, 'approve_transaction', 'transaction', '224', '{\"status\":\"pending\"}', '{\"status\":\"completed\",\"created_accounts\":[],\"customer_id\":90}', 'Giao dịch #224 (Tạo mới tài khoản) đã được duyệt. Số tài khoản sẽ tạo: 1', 0, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/136.0.0.0 Safari/537.36', '2025-05-24 21:28:57'),
(418, 90, 'request_invoice', 'invoice', '11', NULL, '{\"transaction_history_id\":224}', 'Yêu cầu xuất hóa đơn cho giao dịch #224', 0, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/136.0.0.0 Safari/537.36', '2025-05-24 21:29:26'),
(419, 90, 'request_invoice', 'invoice', '12', NULL, '{\"transaction_history_id\":223}', 'Yêu cầu xuất hóa đơn cho giao dịch #223', 0, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/136.0.0.0 Safari/537.36', '2025-05-24 21:44:38'),
(420, 90, 'purchase', 'registration', '234', NULL, '{\"registration_id\":\"234\",\"selected_accounts\":[1],\"total_price\":\"270000\",\"package\":\"Gói 3 Tháng\",\"location\":\"Cà Mau\"}', 'Mua gói dịch vụ: Gói 3 Tháng - Số lượng: 1', 0, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/136.0.0.0 Safari/537.36', '2025-05-24 22:29:30'),
(421, 90, 'purchase', 'registration', '235', NULL, '{\"registration_id\":\"235\",\"selected_accounts\":[1],\"total_price\":\"297000\",\"package\":\"Gói 3 Tháng\",\"location\":\"Cà Mau\"}', 'Mua gói dịch vụ: Gói 3 Tháng - Số lượng: 1', 0, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/136.0.0.0 Safari/537.36', '2025-05-24 22:29:41'),
(422, 90, 'reject_transaction', 'transaction', '226', '{\"status\":\"pending\"}', '{\"status\":\"failed\",\"reason\":\"c\",\"registration_id\":235}', 'Giao dịch #226 đã bị từ chối. Lý do: c', 0, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/136.0.0.0 Safari/537.36', '2025-05-24 23:40:17'),
(423, 90, 'revert_transaction', 'transaction', '224', '{\"status\":\"active\"}', '{\"status\":\"pending\",\"customer_id\":90}', 'Giao dịch #224 đã được hoàn lại về trạng thái chờ xử lý.', 0, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/136.0.0.0 Safari/537.36', '2025-05-25 10:24:05'),
(424, 90, 'revert_transaction', 'transaction', '223', '{\"status\":\"active\"}', '{\"status\":\"pending\",\"customer_id\":90}', 'Giao dịch #223 đã được hoàn lại về trạng thái chờ xử lý.', 0, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/136.0.0.0 Safari/537.36', '2025-05-25 10:24:07'),
(425, 90, 'support_request_updated', 'support_request', '31', '{\"status\":\"pending\",\"admin_response\":null}', '{\"status\":\"resolved\",\"admin_response\":\"\"}', 'Yêu cầu hỗ trợ #31 đã được cập nhật trạng thái thành resolved.', 0, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/136.0.0.0 Safari/537.36', '2025-05-25 12:46:33'),
(426, 90, 'support_request_updated', 'support_request', '31', '{\"status\":\"resolved\",\"admin_response\":\"\"}', '{\"status\":\"in_progress\",\"admin_response\":\"\"}', 'Yêu cầu hỗ trợ #31 đã được cập nhật trạng thái thành in_progress.', 0, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/136.0.0.0 Safari/537.36', '2025-05-25 12:46:38'),
(427, 90, 'support_request_updated', 'support_request', '31', '{\"status\":\"in_progress\",\"admin_response\":\"\"}', '{\"status\":\"pending\",\"admin_response\":\"\"}', 'Yêu cầu hỗ trợ #31 đã được cập nhật trạng thái thành pending.', 0, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/136.0.0.0 Safari/537.36', '2025-05-25 12:50:49'),
(428, 90, 'support_request_updated', 'support_request', '31', '{\"status\":\"pending\",\"admin_response\":\"\"}', '{\"status\":\"pending\",\"admin_response\":\"Kh\\u00f4ng sao nh\\u00e9\"}', 'Yêu cầu hỗ trợ #31 đã được cập nhật trạng thái thành pending.', 0, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/136.0.0.0 Safari/537.36', '2025-05-25 12:50:59'),
(429, 90, 'purchase', 'registration', '236', NULL, '{\"registration_id\":\"236\",\"selected_accounts\":[1],\"total_price\":\"100000\",\"package\":\"Gói 1 Tháng\",\"location\":\"Cà Mau\"}', 'Mua gói dịch vụ: Gói 1 Tháng - Số lượng: 1', 0, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/136.0.0.0 Safari/537.36', '2025-05-25 15:49:27'),
(430, 90, 'approve_transaction', 'transaction', '227', '{\"status\":\"pending\"}', '{\"status\":\"completed\",\"created_accounts\":[],\"customer_id\":90}', 'Giao dịch #227 (Tạo mới tài khoản) đã được duyệt. Số tài khoản sẽ tạo: 1', 0, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/136.0.0.0 Safari/537.36', '2025-05-25 15:49:57'),
(431, 90, 'renewal_request', 'registration', '237', NULL, '{\"registration_id\":\"237\",\"selected_accounts\":[\"2394\"],\"total_price\":270000,\"package\":\"Gói 3 Tháng\",\"location\":\"Cà Mau\"}', 'Yêu cầu gia hạn gói dịch vụ cho đăng ký #237 - Gói: Gói 3 Tháng', 0, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/136.0.0.0 Safari/537.36', '2025-05-25 16:08:20'),
(432, 90, 'renewal_request', 'registration', '238', NULL, '{\"registration_id\":\"238\",\"selected_accounts\":[\"2394\"],\"total_price\":270000,\"package\":\"Gói 3 Tháng\",\"location\":\"Cà Mau\"}', 'Yêu cầu gia hạn gói dịch vụ cho đăng ký #238 - Gói: Gói 3 Tháng', 0, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/136.0.0.0 Safari/537.36', '2025-05-25 16:11:04'),
(433, 90, 'renewal_request', 'registration', '239', NULL, '{\"registration_id\":\"239\",\"selected_accounts\":[\"2394\"],\"total_price\":270000,\"package\":\"Gói 3 Tháng\",\"location\":\"Cà Mau\"}', 'Yêu cầu gia hạn gói dịch vụ cho đăng ký #239 - Gói: Gói 3 Tháng', 0, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/136.0.0.0 Safari/537.36', '2025-05-25 16:11:39'),
(434, 90, 'renewal_request', 'registration', '240', NULL, '{\"registration_id\":\"240\",\"selected_accounts\":[\"2394\"],\"total_price\":297000,\"package\":\"Gói 3 Tháng\",\"location\":\"Cà Mau\"}', 'Yêu cầu gia hạn gói dịch vụ cho đăng ký #240 - Gói: Gói 3 Tháng', 0, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/136.0.0.0 Safari/537.36', '2025-05-25 16:24:52'),
(435, NULL, 'account_updated_by_admin', 'account', '2394', NULL, NULL, 'Quản trị viên đã cập nhật tài khoản \'CMA001\' (ID Tài khoản: 2394).', 0, '127.0.0.1', 'PHP-cURL', '2025-05-25 16:25:06'),
(436, 90, 'approve_transaction', 'transaction', '231', '{\"status\":\"pending\"}', '{\"status\":\"completed\",\"scheduled_accounts\":[\"2394\"],\"renewed_accounts\":[\"2394\"],\"customer_id\":90}', 'Giao dịch #231 (Gia hạn) đã được duyệt. Tài khoản dự kiến: 1, Đã gia hạn: 1', 0, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/136.0.0.0 Safari/537.36', '2025-05-25 16:25:06'),
(437, 90, 'renewal_request', 'registration', '241', NULL, '{\"registration_id\":\"241\",\"selected_accounts\":[\"2394\"],\"total_price\":270000,\"package\":\"Gói 3 Tháng\",\"location\":\"Cà Mau\"}', 'Yêu cầu gia hạn gói dịch vụ cho đăng ký #241 - Gói: Gói 3 Tháng', 0, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/136.0.0.0 Safari/537.36', '2025-05-25 16:26:53'),
(438, NULL, 'account_updated_by_admin', 'account', '2394', NULL, NULL, 'Quản trị viên đã cập nhật tài khoản \'CMA001\' (ID Tài khoản: 2394).', 0, '127.0.0.1', 'PHP-cURL', '2025-05-25 16:27:12'),
(439, 90, 'approve_transaction', 'transaction', '232', '{\"status\":\"pending\"}', '{\"status\":\"completed\",\"scheduled_accounts\":[\"2394\"],\"renewed_accounts\":[\"2394\"],\"customer_id\":90}', 'Giao dịch #232 (Gia hạn) đã được duyệt. Tài khoản dự kiến: 1, Đã gia hạn: 1', 0, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/136.0.0.0 Safari/537.36', '2025-05-25 16:27:12'),
(440, 90, 'account_updated_by_admin', 'account', '2394', NULL, NULL, 'Quản trị viên đã cập nhật tài khoản \'CMA001\' (ID Tài khoản: 2394).', 0, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/136.0.0.0 Safari/537.36', '2025-05-25 16:30:01'),
(441, 90, 'revert_transaction', 'transaction', '232', '{\"status\":\"active\"}', '{\"status\":\"pending\",\"customer_id\":90}', 'Giao dịch #232 đã được hoàn lại về trạng thái chờ xử lý.', 0, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/136.0.0.0 Safari/537.36', '2025-05-25 16:36:07'),
(442, NULL, 'account_updated_by_admin', 'account', '2394', NULL, NULL, 'Quản trị viên đã cập nhật tài khoản \'CMA001\' (ID Tài khoản: 2394).', 0, '127.0.0.1', 'PHP-cURL', '2025-05-25 16:36:22'),
(443, 90, 'approve_transaction', 'transaction', '232', '{\"status\":\"pending\"}', '{\"status\":\"completed\",\"scheduled_accounts\":[\"2394\"],\"renewed_accounts\":[\"2394\"],\"customer_id\":90}', 'Giao dịch #232 (Gia hạn) đã được duyệt. Tài khoản dự kiến: 1, Đã gia hạn: 1', 0, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/136.0.0.0 Safari/537.36', '2025-05-25 16:36:22'),
(444, 90, 'revert_transaction', 'transaction', '232', '{\"status\":\"active\"}', '{\"status\":\"pending\",\"customer_id\":90}', 'Giao dịch #232 đã được hoàn lại về trạng thái chờ xử lý.', 0, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/136.0.0.0 Safari/537.36', '2025-05-25 16:45:33'),
(445, NULL, 'account_updated_by_admin', 'account', '2394', NULL, NULL, 'Quản trị viên đã cập nhật tài khoản \'CMA001\' (ID Tài khoản: 2394).', 0, '127.0.0.1', 'PHP-cURL', '2025-05-25 16:45:40'),
(446, 90, 'approve_transaction', 'transaction', '232', '{\"status\":\"pending\"}', '{\"status\":\"completed\",\"scheduled_accounts\":[\"2394\"],\"renewed_accounts\":[\"2394\"],\"customer_id\":90}', 'Giao dịch #232 (Gia hạn) đã được duyệt. Tài khoản dự kiến: 1, Đã gia hạn: 1', 0, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/136.0.0.0 Safari/537.36', '2025-05-25 16:45:40'),
(447, 90, 'reject_transaction', 'transaction', '232', '{\"status\":\"completed\"}', '{\"status\":\"failed\",\"reason\":\"c\",\"registration_id\":241}', 'Giao dịch #232 đã bị từ chối. Lý do: c', 0, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/136.0.0.0 Safari/537.36', '2025-05-25 16:45:44'),
(448, NULL, 'account_updated_by_admin', 'account', '2394', NULL, NULL, 'Quản trị viên đã cập nhật tài khoản \'CMA001\' (ID Tài khoản: 2394).', 0, '127.0.0.1', 'PHP-cURL', '2025-05-25 16:45:54'),
(449, 90, 'approve_transaction', 'transaction', '232', '{\"status\":\"failed\"}', '{\"status\":\"completed\",\"scheduled_accounts\":[\"2394\"],\"renewed_accounts\":[\"2394\"],\"customer_id\":90}', 'Giao dịch #232 (Gia hạn) đã được duyệt. Tài khoản dự kiến: 1, Đã gia hạn: 1', 0, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/136.0.0.0 Safari/537.36', '2025-05-25 16:45:54'),
(450, 90, 'revert_transaction', 'transaction', '232', '{\"status\":\"active\"}', '{\"status\":\"pending\",\"customer_id\":90}', 'Giao dịch #232 đã được hoàn lại về trạng thái chờ xử lý.', 0, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/136.0.0.0 Safari/537.36', '2025-05-25 17:05:22'),
(451, NULL, 'account_updated_by_admin', 'account', '2394', NULL, NULL, 'Quản trị viên đã cập nhật tài khoản \'CMA001\' (ID Tài khoản: 2394).', 0, '127.0.0.1', 'PHP-cURL', '2025-05-25 17:54:27'),
(452, 90, 'approve_transaction', 'transaction', '232', '{\"status\":\"pending\"}', '{\"status\":\"completed\",\"scheduled_accounts\":[\"2394\"],\"renewed_accounts\":[\"2394\"],\"customer_id\":90}', 'Giao dịch #232 (Gia hạn) đã được duyệt. Tài khoản dự kiến: 1, Đã gia hạn: 1', 0, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/136.0.0.0 Safari/537.36', '2025-05-25 17:54:27'),
(453, 90, 'revert_transaction', 'transaction', '232', '{\"status\":\"active\"}', '{\"status\":\"pending\",\"customer_id\":90}', 'Giao dịch #232 đã được hoàn lại về trạng thái chờ xử lý.', 0, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/136.0.0.0 Safari/537.36', '2025-05-25 18:01:40'),
(454, NULL, 'account_updated_by_admin', 'account', '2394', NULL, NULL, 'Quản trị viên đã cập nhật tài khoản \'CMA001\' (ID Tài khoản: 2394).', 0, '127.0.0.1', 'PHP-cURL', '2025-05-25 18:02:00'),
(455, 90, 'approve_transaction', 'transaction', '232', '{\"status\":\"pending\"}', '{\"status\":\"completed\",\"scheduled_accounts\":[\"2394\"],\"renewed_accounts\":[\"2394\"],\"customer_id\":90}', 'Giao dịch #232 (Gia hạn) đã được duyệt. Tài khoản dự kiến: 1, Đã gia hạn: 1', 0, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/136.0.0.0 Safari/537.36', '2025-05-25 18:02:00'),
(456, 90, 'revert_transaction', 'transaction', '232', '{\"status\":\"active\"}', '{\"status\":\"pending\",\"customer_id\":90}', 'Giao dịch #232 đã được hoàn lại về trạng thái chờ xử lý.', 0, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/136.0.0.0 Safari/537.36', '2025-05-25 18:07:19'),
(457, NULL, 'account_updated_by_admin', 'account', '2394', NULL, NULL, 'Quản trị viên đã cập nhật tài khoản \'CMA001\' (ID Tài khoản: 2394).', 0, '127.0.0.1', 'PHP-cURL', '2025-05-25 18:10:36'),
(458, 90, 'approve_transaction', 'transaction', '232', '{\"status\":\"pending\"}', '{\"status\":\"completed\",\"scheduled_accounts\":[\"2394\"],\"renewed_accounts\":[\"2394\"],\"customer_id\":90}', 'Giao dịch #232 (Gia hạn) đã được duyệt. Tài khoản dự kiến: 1, Đã gia hạn: 1', 0, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/136.0.0.0 Safari/537.36', '2025-05-25 18:10:36'),
(459, 90, 'revert_transaction', 'transaction', '232', '{\"status\":\"active\"}', '{\"status\":\"pending\",\"customer_id\":90}', 'Giao dịch #232 đã được hoàn lại về trạng thái chờ xử lý.', 0, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/136.0.0.0 Safari/537.36', '2025-05-25 18:11:07'),
(460, 90, 'purchase', 'registration', '242', NULL, '{\"registration_id\":\"242\",\"selected_accounts\":[2],\"total_price\":\"1000000\",\"package\":\"Gói 6 Tháng\",\"location\":\"Cà Mau\"}', 'Mua gói dịch vụ: Gói 6 Tháng - Số lượng: 2', 0, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/136.0.0.0 Safari/537.36', '2025-05-26 10:54:36'),
(461, 90, 'purchase', 'registration', '243', NULL, '{\"registration_id\":\"243\",\"selected_accounts\":[2],\"total_price\":\"1000000\",\"package\":\"Gói 6 Tháng\",\"location\":\"Cà Mau\"}', 'Mua gói dịch vụ: Gói 6 Tháng - Số lượng: 2', 0, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/136.0.0.0 Safari/537.36', '2025-05-26 11:03:26'),
(462, 90, 'approve_transaction', 'transaction', '234', '{\"status\":\"pending\"}', '{\"status\":\"completed\",\"created_accounts\":[],\"customer_id\":90}', 'Giao dịch #234 (Tạo mới tài khoản) đã được duyệt. Số tài khoản sẽ tạo: 2', 0, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/136.0.0.0 Safari/537.36', '2025-05-26 11:20:09'),
(463, 90, 'request_invoice', 'invoice', '13', NULL, '{\"transaction_history_id\":234}', 'Yêu cầu xuất hóa đơn cho giao dịch #234', 0, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/136.0.0.0 Safari/537.36', '2025-05-26 11:20:54'),
(464, 90, 'reject_invoice', 'invoice', '13', '{\"status\":\"pending\"}', '{\"status\":\"rejected\",\"reason\":\"c\",\"customer_id\":90}', 'Yêu cầu xuất hoá đơn #13 đã bị từ chối. Lý do: c', 0, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/136.0.0.0 Safari/537.36', '2025-05-26 11:21:01'),
(465, 90, 'invoice_reverted', 'invoice', '13', '{\"status\":\"rejected\"}', '{\"status\":\"pending\",\"customer_id\":90}', 'Hóa đơn #13 đang gặp vấn đề và cần chỉnh sửa lại.', 0, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/136.0.0.0 Safari/537.36', '2025-05-26 11:22:23'),
(466, 90, 'reject_invoice', 'invoice', '13', '{\"status\":\"pending\"}', '{\"status\":\"rejected\",\"reason\":\"c\",\"customer_id\":90}', 'Yêu cầu xuất hoá đơn #13 đã bị từ chối. Lý do: c', 0, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/136.0.0.0 Safari/537.36', '2025-05-26 11:22:32'),
(467, 90, 'invoice_reverted', 'invoice', '13', '{\"status\":\"rejected\"}', '{\"status\":\"pending\",\"customer_id\":90}', 'Hóa đơn #13 đang gặp vấn đề và cần chỉnh sửa lại.', 0, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/136.0.0.0 Safari/537.36', '2025-05-26 11:22:35'),
(468, 90, 'reject_invoice', 'invoice', '13', '{\"status\":\"pending\"}', '{\"status\":\"rejected\",\"reason\":\"c\",\"customer_id\":90}', 'Yêu cầu xuất hoá đơn #13 đã bị từ chối. Lý do: c', 0, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/136.0.0.0 Safari/537.36', '2025-05-26 11:23:29'),
(469, 90, 'invoice_reverted', 'invoice', '13', '{\"status\":\"rejected\"}', '{\"status\":\"pending\",\"customer_id\":90}', 'Hóa đơn #13 đang gặp vấn đề và cần chỉnh sửa lại.', 0, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/136.0.0.0 Safari/537.36', '2025-05-26 11:23:34'),
(470, 90, 'reject_invoice', 'invoice', '13', '{\"status\":\"pending\"}', '{\"status\":\"rejected\",\"reason\":\"c\",\"customer_id\":90}', 'Yêu cầu xuất hoá đơn #13 đã bị từ chối. Lý do: c', 0, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/136.0.0.0 Safari/537.36', '2025-05-26 11:24:01'),
(471, 90, 'invoice_reverted', 'invoice', '13', '{\"status\":\"rejected\"}', '{\"status\":\"pending\",\"customer_id\":90}', 'Hóa đơn #13 đang gặp vấn đề và cần chỉnh sửa lại.', 0, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/136.0.0.0 Safari/537.36', '2025-05-26 11:24:04'),
(472, 90, 'reject_invoice', 'invoice', '13', '{\"status\":\"pending\"}', '{\"status\":\"rejected\",\"reason\":\"c\",\"customer_id\":90}', 'Yêu cầu xuất hoá đơn #13 đã bị từ chối. Lý do: c', 0, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/136.0.0.0 Safari/537.36', '2025-05-26 11:24:11'),
(473, 90, 'invoice_reverted', 'invoice', '13', '{\"status\":\"rejected\"}', '{\"status\":\"pending\",\"customer_id\":90}', 'Hóa đơn #13 đang gặp vấn đề và cần chỉnh sửa lại.', 0, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/136.0.0.0 Safari/537.36', '2025-05-26 11:24:13'),
(474, 109, 'verification_email_sent', 'user', '109', NULL, '{\"email\":\"zzj07635@toaik.com\",\"verification_token\":\"02d9d0f737...\",\"timestamp\":\"2025-05-26 23:16:11\"}', 'Đã gửi email xác thực cho: zzj07635@toaik.com', 0, '::1', NULL, '2025-05-26 23:16:11'),
(475, 109, 'email_verified', 'user', '109', NULL, '{\"status\":\"verified\",\"email\":\"zzj07635@toaik.com\",\"timestamp\":\"2025-05-26 23:19:21\"}', 'Xác thực email thành công cho: zzj07635@toaik.com', 0, '::1', NULL, '2025-05-26 23:19:21'),
(476, 109, 'purchase', 'registration', '244', NULL, '{\"registration_id\":\"244\",\"selected_accounts\":[1],\"total_price\":\"270000\",\"package\":\"Gói 3 Tháng\",\"location\":\"Cà Mau\"}', 'Mua gói dịch vụ: Gói 3 Tháng - Số lượng: 1', 0, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/136.0.0.0 Safari/537.36', '2025-05-26 23:20:01'),
(477, 110, 'verification_email_sent', 'user', '110', NULL, '{\"email\":\"mfc90967@toaik.com\",\"verification_token\":\"cd5dd27364...\",\"timestamp\":\"2025-05-26 23:26:44\"}', 'Đã gửi email xác thực cho: mfc90967@toaik.com', 0, '::1', NULL, '2025-05-26 23:26:44'),
(478, 110, 'email_verified', 'user', '110', NULL, '{\"status\":\"verified\",\"email\":\"mfc90967@toaik.com\",\"timestamp\":\"2025-05-26 23:28:09\"}', 'Xác thực email thành công cho: mfc90967@toaik.com', 0, '::1', NULL, '2025-05-26 23:28:10'),
(479, 110, 'purchase', 'registration', '245', NULL, '{\"registration_id\":\"245\",\"selected_accounts\":[3],\"total_price\":\"810000\",\"package\":\"Gói 3 Tháng\",\"location\":\"Cà Mau\"}', 'Mua gói dịch vụ: Gói 3 Tháng - Số lượng: 3', 0, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/136.0.0.0 Safari/537.36', '2025-05-26 23:28:45'),
(480, 111, 'verification_email_sent', 'user', '111', NULL, '{\"email\":\"whi18324@jioso.com\",\"verification_token\":\"58b0209495...\",\"timestamp\":\"2025-05-26 23:33:21\"}', 'Đã gửi email xác thực cho: whi18324@jioso.com', 0, '::1', NULL, '2025-05-26 23:33:21'),
(481, 111, 'email_verified', 'user', '111', NULL, '{\"status\":\"verified\",\"email\":\"whi18324@jioso.com\",\"timestamp\":\"2025-05-26 23:34:32\"}', 'Xác thực email thành công cho: whi18324@jioso.com', 0, '::1', NULL, '2025-05-26 23:34:32'),
(482, 111, 'purchase', 'registration', '246', NULL, '{\"registration_id\":\"246\",\"selected_accounts\":[3],\"total_price\":\"300000\",\"package\":\"Gói 1 Tháng\",\"location\":\"Yên Bái\"}', 'Mua gói dịch vụ: Gói 1 Tháng - Số lượng: 3', 0, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/136.0.0.0 Safari/537.36', '2025-05-26 23:34:48'),
(483, 111, 'purchase', 'registration', '247', NULL, '{\"registration_id\":\"247\",\"selected_accounts\":[3],\"total_price\":\"300000\",\"package\":\"Gói 1 Tháng\",\"location\":\"Yên Bái\"}', 'Mua gói dịch vụ: Gói 1 Tháng - Số lượng: 3', 0, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/136.0.0.0 Safari/537.36', '2025-05-26 23:46:32'),
(484, 111, 'purchase', 'registration', '248', NULL, '{\"registration_id\":\"248\",\"selected_accounts\":[2],\"total_price\":\"1000000\",\"package\":\"Gói 6 Tháng\",\"location\":\"Yên Bái\"}', 'Mua gói dịch vụ: Gói 6 Tháng - Số lượng: 2', 0, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/136.0.0.0 Safari/537.36', '2025-05-26 23:46:57'),
(485, 112, 'verification_email_sent', 'user', '112', NULL, '{\"email\":\"xes60082@toaik.com\",\"verification_token\":\"1cda836dd1...\",\"timestamp\":\"2025-05-27 07:07:38\"}', 'Đã gửi email xác thực cho: xes60082@toaik.com', 0, '::1', NULL, '2025-05-27 07:07:38'),
(486, 112, 'email_verified', 'user', '112', NULL, '{\"status\":\"verified\",\"email\":\"xes60082@toaik.com\",\"timestamp\":\"2025-05-27 07:08:01\"}', 'Xác thực email thành công cho: xes60082@toaik.com', 0, '::1', NULL, '2025-05-27 07:08:01'),
(487, 112, 'purchase', 'registration', '249', NULL, '{\"registration_id\":\"249\",\"selected_accounts\":[1],\"total_price\":\"270000\",\"package\":\"Gói 3 Tháng\",\"location\":\"Yên Bái\"}', 'Mua gói dịch vụ: Gói 3 Tháng - Số lượng: 1', 0, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/136.0.0.0 Safari/537.36', '2025-05-27 07:09:11'),
(488, 112, 'purchase', 'registration', '250', NULL, '{\"registration_id\":\"250\",\"selected_accounts\":[1],\"total_price\":\"270000\",\"package\":\"Gói 3 Tháng\",\"location\":\"Cà Mau\"}', 'Mua gói dịch vụ: Gói 3 Tháng - Số lượng: 1', 0, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/136.0.0.0 Safari/537.36', '2025-05-27 07:09:24'),
(489, 113, 'verification_email_sent', 'user', '113', NULL, '{\"email\":\"lgo61401@jioso.com\",\"verification_token\":\"fc5616c548...\",\"timestamp\":\"2025-05-27 07:22:02\"}', 'Đã gửi email xác thực cho: lgo61401@jioso.com', 0, '::1', NULL, '2025-05-27 07:22:02'),
(490, 113, 'email_verified', 'user', '113', NULL, '{\"status\":\"verified\",\"email\":\"lgo61401@jioso.com\",\"timestamp\":\"2025-05-27 07:23:44\"}', 'Xác thực email thành công cho: lgo61401@jioso.com', 0, '::1', NULL, '2025-05-27 07:23:44'),
(491, 113, 'purchase', 'registration', '251', NULL, '{\"registration_id\":\"251\",\"selected_accounts\":[2],\"total_price\":\"540000\",\"package\":\"Gói 3 Tháng\",\"location\":\"Cà Mau\"}', 'Mua gói dịch vụ: Gói 3 Tháng - Số lượng: 2', 0, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/136.0.0.0 Safari/537.36', '2025-05-27 07:24:43'),
(492, 114, 'verification_email_sent', 'user', '114', NULL, '{\"email\":\"gop25047@jioso.com\",\"verification_token\":\"aec96c6692...\",\"timestamp\":\"2025-05-27 07:40:45\"}', 'Đã gửi email xác thực cho: gop25047@jioso.com', 0, '::1', NULL, '2025-05-27 07:40:45'),
(493, 114, 'email_verified', 'user', '114', NULL, '{\"status\":\"verified\",\"email\":\"gop25047@jioso.com\",\"timestamp\":\"2025-05-27 07:41:35\"}', 'Xác thực email thành công cho: gop25047@jioso.com', 0, '::1', NULL, '2025-05-27 07:41:35'),
(494, 114, 'purchase', 'registration', '252', NULL, '{\"registration_id\":\"252\",\"selected_accounts\":[1],\"total_price\":\"270000\",\"package\":\"Gói 3 Tháng\",\"location\":\"Yên Bái\"}', 'Mua gói dịch vụ: Gói 3 Tháng - Số lượng: 1', 0, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/136.0.0.0 Safari/537.36', '2025-05-27 07:41:59'),
(495, 114, 'purchase', 'registration', '253', NULL, '{\"registration_id\":\"253\",\"selected_accounts\":[1],\"total_price\":\"270000\",\"package\":\"Gói 3 Tháng\",\"location\":\"Yên Bái\"}', 'Mua gói dịch vụ: Gói 3 Tháng - Số lượng: 1', 0, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/136.0.0.0 Safari/537.36', '2025-05-27 07:53:12'),
(496, 114, 'purchase', 'registration', '254', NULL, '{\"registration_id\":\"254\",\"selected_accounts\":[1],\"total_price\":\"297000\",\"package\":\"Gói 3 Tháng\",\"location\":\"Cà Mau\"}', 'Mua gói dịch vụ: Gói 3 Tháng - Số lượng: 1', 0, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/136.0.0.0 Safari/537.36', '2025-05-27 07:53:31'),
(497, 115, 'verification_email_sent', 'user', '115', NULL, '{\"email\":\"vxn91269@jioso.com\",\"verification_token\":\"7382b09057...\",\"timestamp\":\"2025-05-27 08:01:36\"}', 'Đã gửi email xác thực cho: vxn91269@jioso.com', 0, '::1', NULL, '2025-05-27 08:01:36'),
(498, 115, 'email_verified', 'user', '115', NULL, '{\"status\":\"verified\",\"email\":\"vxn91269@jioso.com\",\"timestamp\":\"2025-05-27 08:02:02\"}', 'Xác thực email thành công cho: vxn91269@jioso.com', 0, '::1', NULL, '2025-05-27 08:02:02'),
(499, 115, 'purchase', 'registration', '255', NULL, '{\"registration_id\":\"255\",\"selected_accounts\":[1],\"total_price\":\"270000\",\"package\":\"Gói 3 Tháng\",\"location\":\"Cà Mau\"}', 'Mua gói dịch vụ: Gói 3 Tháng - Số lượng: 1', 0, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/136.0.0.0 Safari/537.36', '2025-05-27 08:02:27'),
(500, 115, 'purchase', 'registration', '256', NULL, '{\"registration_id\":\"256\",\"selected_accounts\":[1],\"total_price\":\"270000\",\"package\":\"Gói 3 Tháng\",\"location\":\"Yên Bái\"}', 'Mua gói dịch vụ: Gói 3 Tháng - Số lượng: 1', 0, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/136.0.0.0 Safari/537.36', '2025-05-27 08:04:36'),
(501, 115, 'purchase', 'registration', '257', NULL, '{\"registration_id\":\"257\",\"selected_accounts\":[1],\"total_price\":\"270000\",\"package\":\"Gói 3 Tháng\",\"location\":\"Yên Bái\"}', 'Mua gói dịch vụ: Gói 3 Tháng - Số lượng: 1', 0, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/136.0.0.0 Safari/537.36', '2025-05-27 08:13:56'),
(502, 116, 'verification_email_sent', 'user', '116', NULL, '{\"email\":\"nva79925@jioso.com\",\"verification_token\":\"16b73bac82...\",\"timestamp\":\"2025-05-27 08:14:48\"}', 'Đã gửi email xác thực cho: nva79925@jioso.com', 0, '::1', NULL, '2025-05-27 08:14:48'),
(503, 116, 'email_verified', 'user', '116', NULL, '{\"status\":\"verified\",\"email\":\"nva79925@jioso.com\",\"timestamp\":\"2025-05-27 08:16:22\"}', 'Xác thực email thành công cho: nva79925@jioso.com', 0, '::1', NULL, '2025-05-27 08:16:22'),
(504, 116, 'purchase', 'registration', '258', NULL, '{\"registration_id\":\"258\",\"selected_accounts\":[1],\"total_price\":\"270000\",\"package\":\"Gói 3 Tháng\",\"location\":\"Yên Bái\"}', 'Mua gói dịch vụ: Gói 3 Tháng - Số lượng: 1', 0, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/136.0.0.0 Safari/537.36', '2025-05-27 08:16:42'),
(505, 117, 'verification_email_sent', 'user', '117', NULL, '{\"email\":\"pyn61579@toaik.com\",\"verification_token\":\"b0a533bac7...\",\"timestamp\":\"2025-05-27 08:18:05\"}', 'Đã gửi email xác thực cho: pyn61579@toaik.com', 0, '::1', NULL, '2025-05-27 08:18:05'),
(506, 117, 'email_verified', 'user', '117', NULL, '{\"status\":\"verified\",\"email\":\"pyn61579@toaik.com\",\"timestamp\":\"2025-05-27 08:19:25\"}', 'Xác thực email thành công cho: pyn61579@toaik.com', 0, '::1', NULL, '2025-05-27 08:19:25'),
(507, 117, 'purchase', 'registration', '259', NULL, '{\"registration_id\":\"259\",\"selected_accounts\":[1],\"total_price\":\"270000\",\"package\":\"Gói 3 Tháng\",\"location\":\"Yên Bái\"}', 'Mua gói dịch vụ: Gói 3 Tháng - Số lượng: 1', 0, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/136.0.0.0 Safari/537.36', '2025-05-27 08:19:49'),
(508, 117, 'purchase', 'registration', '260', NULL, '{\"registration_id\":\"260\",\"selected_accounts\":[1],\"total_price\":\"270000\",\"package\":\"Gói 3 Tháng\",\"location\":\"Cà Mau\"}', 'Mua gói dịch vụ: Gói 3 Tháng - Số lượng: 1', 0, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/136.0.0.0 Safari/537.36', '2025-05-27 08:20:45'),
(509, 118, 'verification_email_sent', 'user', '118', NULL, '{\"email\":\"nci02654@toaik.com\",\"verification_token\":\"2961f55948...\",\"timestamp\":\"2025-05-27 08:41:12\"}', 'Đã gửi email xác thực cho: nci02654@toaik.com', 0, '::1', NULL, '2025-05-27 08:41:12'),
(510, 118, 'email_verified', 'user', '118', NULL, '{\"status\":\"verified\",\"email\":\"nci02654@toaik.com\",\"timestamp\":\"2025-05-27 08:41:29\"}', 'Xác thực email thành công cho: nci02654@toaik.com', 0, '::1', NULL, '2025-05-27 08:41:29'),
(511, 118, 'purchase', 'registration', '261', NULL, '{\"registration_id\":\"261\",\"selected_accounts\":[2],\"total_price\":\"594000\",\"package\":\"Gói 3 Tháng\",\"location\":\"Cà Mau\"}', 'Mua gói dịch vụ: Gói 3 Tháng - Số lượng: 2', 0, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/136.0.0.0 Safari/537.36', '2025-05-27 08:41:52'),
(512, 118, 'purchase', 'registration', '262', NULL, '{\"registration_id\":\"262\",\"selected_accounts\":[2],\"total_price\":\"1000000\",\"package\":\"Gói 6 Tháng\",\"location\":\"Cà Mau\"}', 'Mua gói dịch vụ: Gói 6 Tháng - Số lượng: 2', 0, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/136.0.0.0 Safari/537.36', '2025-05-27 08:47:22');

-- --------------------------------------------------------

--
-- Table structure for table `admin`
--

CREATE TABLE `admin` (
  `id` int NOT NULL,
  `name` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `admin_username` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `admin_password` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `role` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `admin`
--

INSERT INTO `admin` (`id`, `name`, `admin_username`, `admin_password`, `role`, `created_at`, `updated_at`) VALUES
(6, 'Nguyễn Văn Nam', 'ad', '$2y$10$e2k7mdTeR8KIMIge/3iCkOKNLBI3b3ENb6t4bvmtrhHhjMVLo.P06', 'admin', '2025-05-11 18:17:41', '2025-05-24 11:46:34'),
(12, 'Là ai đó', '123', '$2y$10$TeVJyx6Nbaqkw0Opz4e0pOPXI5oS5k3jJdiknG2WeZIittssP/Ty.', 'customercare', '2025-05-17 06:17:24', '2025-05-23 21:21:04');

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
-- Table structure for table `custom_roles`
--

CREATE TABLE `custom_roles` (
  `role_key` varchar(100) NOT NULL,
  `role_display_name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `custom_roles`
--

INSERT INTO `custom_roles` (`role_key`, `role_display_name`, `created_at`, `updated_at`) VALUES
('admin', 'Quản trị viên', '2025-05-17 04:41:38', '2025-05-17 04:41:38'),
('customercare', 'Chăm sóc khách hàng', '2025-05-17 04:41:38', '2025-05-17 04:41:38'),
('newbie', 'Người mới', '2025-05-24 04:49:58', '2025-05-24 04:49:58'),
('operator', 'Vận hành', '2025-05-17 04:41:38', '2025-05-17 04:41:38');

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
(14, 'Tesit', 'tesit', '', 6, '', 'draft', NULL, NULL, 0, '2025-05-12 13:49:52', '2025-05-15 20:59:17', NULL),
(15, 'Nốt lần này', 'not-lan-nay', '', 6, '', 'draft', '', NULL, 0, '2025-05-12 21:11:20', '2025-05-15 22:24:31', NULL),
(16, 'Dám khô cằn', 'dam-kho-can', '<p>Hello tất cả</p>', 6, '', 'draft', '', NULL, 0, '2025-05-15 20:59:28', '2025-05-18 07:00:47', NULL),
(17, 'Thử phát nữa', 'thu-phat-nua', '<p>X&oacute;a <strong>s&aacute;ch tất hi c</strong></p>', 6, 'Hướng dẫn', 'published', '', NULL, 0, '2025-05-18 07:01:17', '2025-05-25 12:57:02', NULL);

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
(12, 232, 'pending', NULL, NULL, '2025-05-24 21:44:38'),
(13, 234, 'pending', NULL, NULL, '2025-05-26 11:20:54');

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
(2, 'Trần Thị NAM', '0987654123', 'Hải Phòng');

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
(2, 95, '7WCUHN71', '2025-05-11 06:51:25', '2025-05-11 06:51:25'),
(3, 90, 'NICURPGO', '2025-05-12 06:58:21', '2025-05-12 06:58:21'),
(4, 100, 'PI7V2C9X', '2025-05-12 15:49:45', '2025-05-12 15:49:45'),
(5, 105, '7N6REZX3', '2025-05-16 11:16:06', '2025-05-16 11:16:06'),
(6, 112, '1B2NPN79', '2025-05-27 00:09:35', '2025-05-27 00:09:35'),
(7, 117, 'Z5E2DL2W', '2025-05-27 01:20:38', '2025-05-27 01:20:38'),
(8, 118, 'MPN70WZO', '2025-05-27 01:48:36', '2025-05-27 01:48:36');

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
(1, 93, 95, '2025-05-11 03:51:24'),
(2, 90, 100, '2025-05-12 15:46:53'),
(3, 90, 105, '2025-05-16 11:13:11'),
(4, 90, 110, '2025-05-26 16:26:44'),
(5, 90, 112, '2025-05-27 00:07:38'),
(6, 90, 113, '2025-05-27 00:22:02');

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
  `rejection_reason` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `purchase_type` enum('company','individual') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'individual' COMMENT 'Loại mua: company - cty, individual - cá nhân',
  `invoice_allowed` tinyint(1) NOT NULL DEFAULT '0' COMMENT 'Cho phép xuất hóa đơn'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `registration`
--

INSERT INTO `registration` (`id`, `user_id`, `package_id`, `location_id`, `collaborator_id`, `num_account`, `start_time`, `end_time`, `base_price`, `vat_percent`, `vat_amount`, `total_price`, `status`, `created_at`, `updated_at`, `deleted_at`, `rejection_reason`, `purchase_type`, `invoice_allowed`) VALUES
(226, 105, 2, 12, NULL, 1, '2025-08-16 20:37:55', '2025-11-16 20:37:55', 270000.00, 0, 0.00, 270000.00, 'pending', '2025-05-16 21:16:51', '2025-05-24 21:27:20', NULL, 'c', 'individual', 0),
(227, 90, 1, 12, NULL, 2, '2025-05-18 00:00:00', '2025-06-18 00:00:00', 0.00, 0, 0.00, 0.00, 'active', '2025-05-18 07:20:32', '2025-05-18 07:20:50', NULL, NULL, 'individual', 0),
(228, 90, 1, 63, NULL, 2, '2025-05-18 00:00:00', '2025-06-18 00:00:00', 0.00, 0, 0.00, 0.00, 'active', '2025-05-18 09:39:56', NULL, NULL, NULL, 'individual', 0),
(229, 88, 1, 63, NULL, 1, '2025-05-19 16:12:51', '2025-09-19 16:12:51', 100000.00, 0, 0.00, 100000.00, 'pending', '2025-05-19 16:12:51', '2025-05-23 13:01:52', NULL, NULL, 'individual', 0),
(230, 90, 2, 12, NULL, 1, '2025-05-19 22:18:09', '2025-08-19 22:18:09', 270000.00, 0, 0.00, 270000.00, 'pending', '2025-05-19 22:18:09', '2025-05-24 21:27:16', NULL, NULL, 'individual', 0),
(231, 105, 2, 12, NULL, 1, '2025-11-24 13:02:07', '2026-02-24 13:02:07', 270000.00, 0, 0.00, 270000.00, 'pending', '2025-05-23 13:11:19', '2025-05-23 13:28:26', NULL, NULL, 'individual', 0),
(242, 90, 3, 12, NULL, 2, '2025-05-26 10:54:36', '2025-11-26 10:54:36', 500000.00, 0, 0.00, 1000000.00, 'pending', '2025-05-26 10:54:36', '2025-05-26 10:54:36', NULL, NULL, 'individual', 0),
(243, 90, 3, 12, NULL, 2, '2025-05-26 11:03:26', '2025-11-26 11:03:26', 500000.00, 10, 100000.00, 1100000.00, 'active', '2025-05-26 11:03:26', '2025-05-26 11:20:43', NULL, NULL, 'company', 1),
(244, 109, 2, 12, NULL, 1, '2025-05-26 23:20:01', '2025-08-26 23:20:01', 270000.00, 0, 0.00, 270000.00, 'pending', '2025-05-26 23:20:01', '2025-05-26 23:20:01', NULL, NULL, 'individual', 0),
(245, 110, 2, 12, NULL, 3, '2025-05-26 23:28:45', '2025-08-26 23:28:45', 270000.00, 0, 0.00, 810000.00, 'pending', '2025-05-26 23:28:45', '2025-05-26 23:28:45', NULL, NULL, 'individual', 0),
(246, 111, 1, 63, NULL, 3, '2025-05-26 23:34:48', '2025-06-26 23:34:48', 100000.00, 0, 0.00, 300000.00, 'pending', '2025-05-26 23:34:48', '2025-05-26 23:34:48', NULL, NULL, 'individual', 0),
(247, 111, 1, 63, NULL, 3, '2025-05-26 23:46:32', '2025-06-26 23:46:32', 100000.00, 0, 0.00, 300000.00, 'pending', '2025-05-26 23:46:32', '2025-05-26 23:46:32', NULL, NULL, 'individual', 0),
(248, 111, 3, 63, NULL, 2, '2025-05-26 23:46:57', '2025-11-26 23:46:57', 500000.00, 0, 0.00, 1000000.00, 'pending', '2025-05-26 23:46:57', '2025-05-26 23:46:57', NULL, NULL, 'individual', 0),
(249, 112, 2, 63, NULL, 1, '2025-05-27 07:09:11', '2025-08-27 07:09:11', 270000.00, 0, 0.00, 270000.00, 'pending', '2025-05-27 07:09:11', '2025-05-27 07:09:11', NULL, NULL, 'individual', 0),
(250, 112, 2, 12, NULL, 1, '2025-05-27 07:09:24', '2025-08-27 07:09:24', 270000.00, 0, 0.00, 270000.00, 'pending', '2025-05-27 07:09:24', '2025-05-27 07:09:24', NULL, NULL, 'individual', 0),
(251, 113, 2, 12, NULL, 2, '2025-05-27 07:24:43', '2025-08-27 07:24:43', 270000.00, 0, 0.00, 540000.00, 'pending', '2025-05-27 07:24:43', '2025-05-27 07:24:43', NULL, NULL, 'individual', 0),
(252, 114, 2, 63, NULL, 1, '2025-05-27 07:41:59', '2025-08-27 07:41:59', 270000.00, 0, 0.00, 270000.00, 'pending', '2025-05-27 07:41:59', '2025-05-27 07:41:59', NULL, NULL, 'individual', 0),
(253, 114, 2, 63, NULL, 1, '2025-05-27 07:53:12', '2025-08-27 07:53:12', 270000.00, 0, 0.00, 270000.00, 'pending', '2025-05-27 07:53:12', '2025-05-27 07:53:12', NULL, NULL, 'individual', 0),
(254, 114, 2, 12, NULL, 1, '2025-05-27 07:53:31', '2025-08-27 07:53:31', 270000.00, 10, 27000.00, 297000.00, 'pending', '2025-05-27 07:53:31', '2025-05-27 07:53:31', NULL, NULL, 'company', 1),
(255, 115, 2, 12, NULL, 1, '2025-05-27 08:02:27', '2025-08-27 08:02:27', 270000.00, 0, 0.00, 270000.00, 'pending', '2025-05-27 08:02:27', '2025-05-27 08:02:27', NULL, NULL, 'individual', 0),
(256, 115, 2, 63, NULL, 1, '2025-05-27 08:04:36', '2025-08-27 08:04:36', 270000.00, 0, 0.00, 270000.00, 'pending', '2025-05-27 08:04:36', '2025-05-27 08:04:36', NULL, NULL, 'individual', 0),
(257, 115, 2, 63, NULL, 1, '2025-05-27 08:13:56', '2025-08-27 08:13:56', 270000.00, 0, 0.00, 270000.00, 'pending', '2025-05-27 08:13:56', '2025-05-27 08:13:56', NULL, NULL, 'individual', 0),
(258, 116, 2, 63, NULL, 1, '2025-05-27 08:16:42', '2025-08-27 08:16:42', 270000.00, 0, 0.00, 270000.00, 'pending', '2025-05-27 08:16:42', '2025-05-27 08:16:42', NULL, NULL, 'individual', 0),
(259, 117, 2, 63, NULL, 1, '2025-05-27 08:19:49', '2025-08-27 08:19:49', 270000.00, 0, 0.00, 270000.00, 'pending', '2025-05-27 08:19:49', '2025-05-27 08:19:49', NULL, NULL, 'individual', 0),
(260, 117, 2, 12, NULL, 1, '2025-05-27 08:20:45', '2025-08-27 08:20:45', 270000.00, 0, 0.00, 270000.00, 'pending', '2025-05-27 08:20:45', '2025-05-27 08:20:45', NULL, NULL, 'individual', 0),
(261, 118, 2, 12, NULL, 2, '2025-05-27 08:41:51', '2025-08-27 08:41:51', 270000.00, 10, 54000.00, 594000.00, 'pending', '2025-05-27 08:41:52', '2025-05-27 08:41:52', NULL, NULL, 'company', 1),
(262, 118, 3, 12, NULL, 2, '2025-05-27 08:47:22', '2025-11-27 08:47:22', 500000.00, 0, 0.00, 1000000.00, 'pending', '2025-05-27 08:47:22', '2025-05-27 08:47:22', NULL, NULL, 'individual', 0);

--
-- Triggers `registration`
--
DELIMITER $$
CREATE TRIGGER `trg_set_registration_vat` BEFORE INSERT ON `registration` FOR EACH ROW BEGIN
  IF NEW.`purchase_type` = 'company' THEN
    SET NEW.`vat_percent` = 10;
    SET NEW.`vat_amount` = ROUND((NEW.`base_price` * COALESCE(NEW.`num_account`,1)) * 0.1, 2);
    SET NEW.`invoice_allowed` = 1;
  ELSE
    SET NEW.`vat_percent` = 0;
    SET NEW.`vat_amount` = 0;
    SET NEW.`invoice_allowed` = 0;
  END IF;
  SET NEW.`total_price` = ROUND((NEW.`base_price` * COALESCE(NEW.`num_account`,1)) + NEW.`vat_amount`, 2);
END
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `trg_update_registration_vat_update` BEFORE UPDATE ON `registration` FOR EACH ROW BEGIN
  IF NEW.`purchase_type` = 'company' THEN
    SET NEW.`vat_percent` = 10;
    SET NEW.`vat_amount` = ROUND((NEW.`base_price` * COALESCE(NEW.`num_account`,1)) * 0.1, 2);
    SET NEW.`invoice_allowed` = 1;
  ELSE
    SET NEW.`vat_percent` = 0;
    SET NEW.`vat_amount` = 0;
    SET NEW.`invoice_allowed` = 0;
  END IF;
  SET NEW.`total_price` = ROUND((NEW.`base_price` * COALESCE(NEW.`num_account`,1)) + NEW.`vat_amount`, 2);
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Table structure for table `role_permissions`
--

CREATE TABLE `role_permissions` (
  `role` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `permission` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `allowed` tinyint(1) NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `role_permissions`
--

INSERT INTO `role_permissions` (`role`, `permission`, `allowed`) VALUES
('admin', 'account_management_edit', 1),
('admin', 'account_management_view', 1),
('admin', 'guide_management_edit', 1),
('admin', 'guide_management_view', 1),
('admin', 'invoice_management_edit', 1),
('admin', 'invoice_management_view', 1),
('admin', 'invoice_review_edit', 1),
('admin', 'invoice_review_view', 1),
('admin', 'permission_edit', 1),
('admin', 'permission_management_edit', 1),
('admin', 'permission_management_view', 1),
('admin', 'referral_management_edit', 1),
('admin', 'referral_management_view', 1),
('admin', 'reports_view', 1),
('admin', 'revenue_management_edit', 1),
('admin', 'revenue_management_view', 1),
('admin', 'station_management_edit', 1),
('admin', 'station_management_view', 1),
('admin', 'support_management_edit', 1),
('admin', 'support_management_view', 1),
('admin', 'user_management_edit', 1),
('admin', 'user_management_view', 1),
('admin', 'voucher_management_edit', 1),
('admin', 'voucher_management_view', 1),
('customercare', 'account_management_edit', 0),
('customercare', 'account_management_view', 0),
('customercare', 'guide_management_edit', 0),
('customercare', 'guide_management_view', 0),
('customercare', 'invoice_management_edit', 0),
('customercare', 'invoice_management_view', 0),
('customercare', 'invoice_review_edit', 0),
('customercare', 'invoice_review_view', 0),
('customercare', 'permission_edit', 0),
('customercare', 'permission_management_edit', 0),
('customercare', 'permission_management_view', 1),
('customercare', 'referral_management_edit', 0),
('customercare', 'referral_management_view', 1),
('customercare', 'reports_view', 0),
('customercare', 'revenue_management_edit', 1),
('customercare', 'revenue_management_view', 1),
('customercare', 'station_management_edit', 0),
('customercare', 'station_management_view', 1),
('customercare', 'support_management_edit', 1),
('customercare', 'support_management_view', 1),
('customercare', 'user_management_edit', 0),
('customercare', 'user_management_view', 0),
('customercare', 'voucher_management_edit', 0),
('customercare', 'voucher_management_view', 1),
('newbie', 'account_management_edit', 0),
('newbie', 'account_management_view', 1),
('newbie', 'guide_management_edit', 0),
('newbie', 'guide_management_view', 0),
('newbie', 'invoice_management_edit', 0),
('newbie', 'invoice_management_view', 0),
('newbie', 'invoice_review_edit', 0),
('newbie', 'invoice_review_view', 0),
('newbie', 'permission_management_edit', 0),
('newbie', 'permission_management_view', 1),
('newbie', 'referral_management_edit', 0),
('newbie', 'referral_management_view', 0),
('newbie', 'reports_view', 0),
('newbie', 'revenue_management_edit', 0),
('newbie', 'revenue_management_view', 0),
('newbie', 'station_management_edit', 0),
('newbie', 'station_management_view', 1),
('newbie', 'support_management_edit', 0),
('newbie', 'support_management_view', 0),
('newbie', 'user_management_edit', 0),
('newbie', 'user_management_view', 1),
('newbie', 'voucher_management_edit', 0),
('newbie', 'voucher_management_view', 1),
('operator', 'account_management_edit', 0),
('operator', 'account_management_view', 1),
('operator', 'guide_management_edit', 0),
('operator', 'guide_management_view', 1),
('operator', 'invoice_management_edit', 0),
('operator', 'invoice_management_view', 1),
('operator', 'invoice_review_edit', 0),
('operator', 'invoice_review_view', 1),
('operator', 'permission_management_edit', 0),
('operator', 'permission_management_view', 1),
('operator', 'referral_management_edit', 0),
('operator', 'referral_management_view', 1),
('operator', 'reports_view', 1),
('operator', 'revenue_management_edit', 0),
('operator', 'revenue_management_view', 1),
('operator', 'station_management_edit', 0),
('operator', 'station_management_view', 1),
('operator', 'support_management_edit', 0),
('operator', 'support_management_view', 1),
('operator', 'user_management_edit', 0),
('operator', 'user_management_view', 1),
('operator', 'voucher_management_edit', 0),
('operator', 'voucher_management_view', 1);

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
('53', 'TNN6', 'Nhà anh Phú - Định hoá| a Phú - 0867666929', '58', 21.91116659, 105.64929213, 3, NULL),
('54', 'TNN4', 'vpdkdd Đại Từ|Đỗ Đình Long - 0355055740', '56', 21.63442798, 105.63582762, 1, NULL),
('55', 'TNN3', 'Nhà anh Thoi - Võ Nhai|Anh Việt - 0353177492', '55', 21.75411886, 106.07746349, 1, NULL),
('56', 'TNN5', 'Nhà anh Long - Phổ Yên|Anh Long - 0986650808', '57', 21.41624565, 105.86203136, 3, NULL),
('57', 'YBI2', 'Nhà anh Tuấn - Văn Yên|Anh Tuấn - 0963844634', '64', 21.85003200, 104.70568793, 1, NULL),
('58', 'G216', 'Trạm a Hưng gửi', NULL, 20.96750510, 106.71247261, -1, NULL),
('59', 'P501', 'tram Ha Noi', '69', 21.02222257, 105.79144295, 1, NULL),
('61', 'PYN5', 'Tuy Hòa| Anh Thịnh - 0856036778', NULL, 13.10045080, 109.31209672, 3, NULL),
('62', 'PYN4', 'Tuy Hòa ex| Anh Thịnh - 0856036778', NULL, 13.09002297, 109.28998055, 3, NULL),
('63', 'PYN3', 'Tuy An| Anh Thịnh - 0856036778', NULL, 13.33943150, 109.20787570, 3, NULL),
('64', 'PYN2', 'Tây Hòa| Anh Thịnh - 0856036778', NULL, 12.98001507, 109.22999187, 0, NULL),
('65', 'PYN1', 'Sơn Hòa| Anh Thịnh - 0856036778', NULL, 13.04000376, 108.98001038, 3, NULL),
('66', 'HPG3', 'TRạm test móc dữ liệu từ sv WIndow', NULL, 20.81446808, 106.68709058, -1, NULL),
('67', 'HNI1', 'Test móc dữ liệu ra ', NULL, 21.02239446, 105.81017867, -1, NULL);

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
(2, 93, 'test', 'aa', 'billing', 'resolved', 'test nốt lần này', '2025-05-11 18:07:40', '2025-05-15 21:10:44'),
(3, 90, 'SOS', 'Cầu cứu', 'technical', 'in_progress', 'Hết nhé', '2025-05-16 18:00:53', '2025-05-18 06:56:36'),
(4, 90, 'SOS', 'Test', 'technical', 'resolved', 'Ok em', '2025-05-16 18:11:53', '2025-05-17 09:39:43'),
(5, 90, 'SOS', 'Test', 'technical', 'pending', NULL, '2025-05-19 13:55:28', NULL),
(6, 90, 'Đại đại đi', 'SOS', 'technical', 'pending', NULL, '2025-05-19 14:18:24', NULL),
(7, 90, 'SOS', 'Cho hỏi', 'technical', 'pending', NULL, '2025-05-19 15:10:31', NULL),
(8, 90, 'Nốt lần', 'Alo', 'technical', 'pending', NULL, '2025-05-19 15:19:24', NULL),
(9, 90, 'Nốt', 'ótOK', 'technical', 'pending', NULL, '2025-05-19 15:29:55', NULL),
(10, 90, 'SOS', '123', 'technical', 'pending', NULL, '2025-05-19 16:09:58', NULL),
(11, 90, 'nốt', 'chir daanx', 'technical', 'pending', NULL, '2025-05-19 16:50:24', NULL),
(24, 90, 'Đại đại đi', '123', 'technical', 'pending', NULL, '2025-05-20 07:21:56', NULL),
(25, 90, 'Test3', 'OK', 'technical', 'closed', 'Ok nhé', '2025-05-20 07:24:04', '2025-05-22 22:54:26'),
(26, 90, 'Test3', '123', 'technical', 'pending', NULL, '2025-05-20 07:33:45', NULL),
(27, 90, 'SOS', '123', 'technical', 'pending', NULL, '2025-05-20 07:36:40', NULL),
(28, 90, '333', 'Hello', 'technical', 'pending', NULL, '2025-05-20 07:37:37', NULL),
(29, 90, '123', 'SOS', 'technical', 'pending', NULL, '2025-05-20 07:48:21', NULL),
(30, 90, 'Test8', '123', 'technical', 'pending', NULL, '2025-05-20 07:52:05', NULL),
(31, 90, 'Test3', '123', 'technical', 'pending', 'Không sao nhé', '2025-05-20 09:08:51', '2025-05-25 12:50:59');

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
('2402', 243, '2025-05-26 11:20:09', '2025-11-26 11:20:09', 'CMA001', '0999999443', 1, 1, NULL, NULL, NULL, 1, NULL, '2025-05-26 11:20:09', NULL, NULL),
('2403', 243, '2025-05-26 11:20:09', '2025-11-26 11:20:09', 'CMA002', '0999999443', 1, 1, NULL, NULL, NULL, 1, NULL, '2025-05-26 11:20:09', NULL, NULL);

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
(223, NULL, 90, NULL, 'purchase', 297000.00, 'pending', 'Chuyển khoản ngân hàng', 'reg_232_1748096770.jpg', 0, NULL, 0, NULL, NULL, '2025-05-24 21:20:42', '2025-05-25 10:24:06'),
(224, NULL, 90, NULL, 'purchase', 270000.00, 'pending', 'Chuyển khoản ngân hàng', NULL, 0, NULL, 0, NULL, NULL, '2025-05-24 21:25:42', '2025-05-25 10:24:05'),
(225, NULL, 90, NULL, 'purchase', 270000.00, 'pending', 'Chuyển khoản ngân hàng', NULL, 0, NULL, 0, NULL, NULL, '2025-05-24 22:29:30', '2025-05-24 22:29:30'),
(226, NULL, 90, NULL, 'purchase', 297000.00, 'failed', 'Chuyển khoản ngân hàng', NULL, 0, NULL, 0, NULL, NULL, '2025-05-24 22:29:41', '2025-05-24 23:40:17'),
(227, 227, 90, NULL, 'purchase', 100000.00, 'completed', 'Chuyển khoản ngân hàng', 'reg_236_1748162983.jpg', 0, NULL, 0, NULL, NULL, '2025-05-25 15:49:27', '2025-05-26 10:48:18'),
(228, 228, 90, NULL, 'renewal', 270000.00, 'pending', 'Chuyển khoản ngân hàng', NULL, 0, NULL, 0, NULL, NULL, '2025-05-25 16:08:20', '2025-05-26 10:48:21'),
(229, NULL, 90, NULL, 'renewal', 270000.00, 'pending', 'Chuyển khoản ngân hàng', NULL, 0, NULL, 0, NULL, NULL, '2025-05-25 16:11:04', '2025-05-25 16:11:04'),
(230, NULL, 90, NULL, 'renewal', 270000.00, 'pending', 'Chuyển khoản ngân hàng', NULL, 0, NULL, 0, NULL, NULL, '2025-05-25 16:11:39', '2025-05-25 16:11:39'),
(231, NULL, 90, NULL, 'renewal', 297000.00, 'completed', 'Chuyển khoản ngân hàng', NULL, 0, NULL, 0, NULL, NULL, '2025-05-25 16:24:52', '2025-05-25 16:25:06'),
(232, NULL, 90, 4, 'renewal', 270000.00, 'pending', 'Chuyển khoản ngân hàng', NULL, 0, NULL, 0, NULL, NULL, '2025-05-25 16:26:53', '2025-05-25 20:53:31'),
(233, 242, 90, NULL, 'purchase', 1000000.00, 'pending', 'Chuyển khoản ngân hàng', NULL, 0, NULL, 0, NULL, NULL, '2025-05-26 10:54:36', '2025-05-26 10:54:36'),
(234, 243, 90, NULL, 'purchase', 1000000.00, 'completed', 'Chuyển khoản ngân hàng', NULL, 0, NULL, 0, NULL, NULL, '2025-05-26 11:03:26', '2025-05-26 11:20:09'),
(235, 244, 109, NULL, 'purchase', 270000.00, 'pending', 'Chuyển khoản ngân hàng', NULL, 0, NULL, 0, NULL, NULL, '2025-05-26 23:20:01', '2025-05-26 23:20:01'),
(236, 245, 110, NULL, 'purchase', 810000.00, 'pending', 'Chuyển khoản ngân hàng', NULL, 0, NULL, 0, NULL, NULL, '2025-05-26 23:28:45', '2025-05-26 23:28:45'),
(237, 246, 111, NULL, 'purchase', 240000.00, 'pending', 'Chuyển khoản ngân hàng', NULL, 0, NULL, 0, NULL, NULL, '2025-05-26 23:34:48', '2025-05-26 23:44:40'),
(238, 247, 111, NULL, 'purchase', 240000.00, 'pending', 'Chuyển khoản ngân hàng', NULL, 0, NULL, 0, NULL, NULL, '2025-05-26 23:46:32', '2025-05-26 23:46:41'),
(239, 248, 111, NULL, 'purchase', 240000.00, 'pending', 'Chuyển khoản ngân hàng', NULL, 0, NULL, 0, NULL, NULL, '2025-05-26 23:46:57', '2025-05-26 23:46:57'),
(240, 249, 112, NULL, 'purchase', 216000.00, 'pending', 'Chuyển khoản ngân hàng', NULL, 0, NULL, 0, NULL, NULL, '2025-05-27 07:09:11', '2025-05-27 07:09:12'),
(241, 250, 112, NULL, 'purchase', 216000.00, 'pending', 'Chuyển khoản ngân hàng', NULL, 0, NULL, 0, NULL, NULL, '2025-05-27 07:09:24', '2025-05-27 07:09:24'),
(242, 251, 113, NULL, 'purchase', 540000.00, 'pending', 'Chuyển khoản ngân hàng', NULL, 0, NULL, 0, NULL, NULL, '2025-05-27 07:24:43', '2025-05-27 07:34:48'),
(243, 252, 114, NULL, 'purchase', 270000.00, 'pending', 'Chuyển khoản ngân hàng', NULL, 0, NULL, 0, NULL, NULL, '2025-05-27 07:41:59', '2025-05-27 07:41:59'),
(244, 253, 114, NULL, 'purchase', 216000.00, 'pending', 'Chuyển khoản ngân hàng', NULL, 0, NULL, 0, NULL, NULL, '2025-05-27 07:53:12', '2025-05-27 07:53:17'),
(245, 254, 114, NULL, 'purchase', 216000.00, 'pending', 'Chuyển khoản ngân hàng', NULL, 0, NULL, 0, NULL, NULL, '2025-05-27 07:53:31', '2025-05-27 07:59:02'),
(246, 255, 115, NULL, 'purchase', 216000.00, 'pending', 'Chuyển khoản ngân hàng', NULL, 0, NULL, 0, NULL, NULL, '2025-05-27 08:02:27', '2025-05-27 08:03:02'),
(247, 256, 115, NULL, 'purchase', 216000.00, 'pending', 'Chuyển khoản ngân hàng', NULL, 0, NULL, 0, NULL, NULL, '2025-05-27 08:04:36', '2025-05-27 08:13:39'),
(248, 257, 115, 14, 'purchase', 270000.00, 'pending', 'Chuyển khoản ngân hàng', NULL, 0, NULL, 0, NULL, NULL, '2025-05-27 08:13:56', '2025-05-27 08:13:56'),
(249, 258, 116, NULL, 'purchase', 270000.00, 'pending', 'Chuyển khoản ngân hàng', NULL, 0, NULL, 0, NULL, NULL, '2025-05-27 08:16:42', '2025-05-27 08:16:42'),
(250, 259, 117, NULL, 'purchase', 216000.00, 'pending', 'Chuyển khoản ngân hàng', NULL, 0, NULL, 0, NULL, NULL, '2025-05-27 08:19:49', '2025-05-27 08:20:13'),
(251, 260, 117, 14, 'purchase', 270000.00, 'pending', 'Chuyển khoản ngân hàng', NULL, 0, NULL, 0, NULL, NULL, '2025-05-27 08:20:45', '2025-05-27 08:20:45'),
(252, 261, 118, NULL, 'purchase', 475200.00, 'pending', 'Chuyển khoản ngân hàng', NULL, 0, NULL, 0, NULL, NULL, '2025-05-27 08:41:52', '2025-05-27 08:41:52'),
(253, 262, 118, 14, 'purchase', 1000000.00, 'pending', 'Chuyển khoản ngân hàng', NULL, 0, NULL, 0, NULL, NULL, '2025-05-27 08:47:22', '2025-05-27 08:47:22');

--
-- Triggers `transaction_history`
--
DELIMITER $$
CREATE TRIGGER `trg_auto_approve_commission` AFTER UPDATE ON `transaction_history` FOR EACH ROW BEGIN
    -- If transaction is completed and payment is confirmed 
    IF NEW.status = 'completed' THEN
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
(90, 'Long2002', 'tranhailong2407@gmail.com', '$2y$10$BX.l.Ty9P3ey6FRT135e1OSXM8zlhAB1AOApW.EBohGbZhU2X3sli', '0999999443', 1, 'ad', '1233333324', '123', NULL, '2025-05-04 09:08:01', '2025-05-19 21:19:24', NULL, 1, 1, NULL),
(91, 'nguyendozxc15@gmail.com', 'nguyendozxc15@gmail.com', '$2y$10$y8rSLvI2J48XZjTCb9IIgOmEf5Tz42r0OVLOrrlovJHt8JjVrNRvq', '0981190564', 0, NULL, NULL, NULL, NULL, '2025-05-05 11:09:06', '2025-05-05 11:09:26', NULL, 1, 1, NULL),
(92, 'dovannguyen2005bv@gmail.com', 'dovannguyen2005bv@gmail.com', '$2y$10$BUQc5aTNhk0h1mBfQhrlG.1kkVfb8t.9Hj6lnHZYr43CEQUqBlLpS', '0981190562', 0, 'Công ty cổ phần công nghệ Otek', '2222333332', NULL, NULL, '2025-05-05 14:33:57', '2025-05-13 14:47:04', '2025-05-13 07:47:04', 1, 1, NULL),
(93, 'nguyendozxc20@gmail.com', 'nguyendozxc20@gmail.com', '$2y$10$1vCsFFE0crdwZYqv/K97reAlFpU1vuIOSs5/hs0lhnwypkzBj/nCm', '0981190522', 1, 'Công ty cổ phần công nghệ Otek', '2222233332', 'Ha Noi', NULL, '2025-05-07 22:39:46', '2025-05-10 21:14:34', NULL, 1, 1, NULL),
(94, 'onf52053@toaik.com', 'onf52053@toaik.com', '$2y$10$b3Cyd8bwZ5cFc690DISmd.ZrAfzkvbvTsbbAEP/Vaiy/kmPNZcv1i', '0981130564', 0, NULL, NULL, NULL, NULL, '2025-05-10 21:00:57', '2025-05-12 13:44:47', NULL, 1, 1, 'bcf3432eb3e495acfab64b2cc82ecf505a4f729cacc343aca5b8a03b14d94853'),
(95, 'kei65757@toaik.com', 'kei65757@toaik.com', '$2y$10$E0jVblq.5EoERWBOXd2lvObfw.r8nko6ySx8o7x/eLgzN/qmiub1W', '0281190564', 0, NULL, NULL, NULL, NULL, '2025-05-11 10:51:20', '2025-05-11 10:54:04', NULL, 1, 1, NULL),
(96, 'adddd@gmail.com', 'addd@gmail.com', '$2y$10$W8VAJit.yg0ZqAIq0e5gz.yuncGYw5gGkEg8XEWb.4oFcAK01EGdC', '0982290564', 0, NULL, NULL, NULL, NULL, '2025-05-11 14:44:27', '2025-05-19 13:52:59', NULL, 1, 1, '95a07bd3451bba76c31096ae73277e55b6a6a660c9d46b8ab7ea10b38b06a502'),
(99, 'Long2009', 'acook6962@gmail.com', '$2y$10$0oit33nfU.c0XQ1nRI5d.un3M7HoYmkT5dnvT3B3p5DkFhtlvEbMa', '0912345695', 0, NULL, NULL, NULL, NULL, '2025-05-12 21:18:06', '2025-05-15 22:45:17', '2025-05-15 15:45:17', 1, 0, NULL),
(100, 'zkz9696@toaik.com', 'zkz96960@toaik.com', '$2y$10$48F2kmtApFftzTJx31ruyeSeaf3j4oYDhMKiCC1.fHN8aGPflU.mS', '0999999223', 0, NULL, NULL, NULL, NULL, '2025-05-12 22:46:48', '2025-05-15 22:45:17', '2025-05-15 15:45:17', 1, 1, NULL),
(102, 'Long2001', 'acook6968@gmail.com', '$2y$10$xhPFgwaGPOuiJjc/b7E/T.f/Ho.TQyIF9At7AZzTGNGnmTSCxXWFK', '091234569', 1, '12', 'a', NULL, NULL, '2025-05-14 17:39:24', '2025-05-15 15:00:08', NULL, 1, 0, NULL),
(104, 'Long1999', '123@gmail.com', '$2y$10$dL.AhqQ2Pd4CBZT.NC9XrOGQClR91MhpOEOvfKJs3zMdJLsf/IE0y', '12', 0, NULL, NULL, NULL, NULL, '2025-05-15 22:45:46', '2025-05-15 22:45:52', NULL, 1, 0, NULL),
(105, 'Long99999', 'osn14300@jioso.com', '$2y$10$YmhPRiarvfNbAaemNOuMHeVykkJfrsMzKDGCvSHnDQGasS7oVTh2S', '09999993232', 1, 'as', '1233333333', '123', NULL, '2025-05-16 18:13:06', '2025-05-23 13:04:55', NULL, 1, 1, NULL),
(106, 'Long12323', 'tranhailong2499@gmail.com', '$2y$10$PxMSQCg81bG3ohJCZVixM.EkUePHyzjm.e4MElZAofgJLgTtICjLS', '123', 0, NULL, NULL, NULL, NULL, '2025-05-16 21:41:32', '2025-05-18 06:51:58', '2025-05-17 23:51:58', 1, 0, NULL),
(108, 'Chào tất cả', 'acook6990@gmail.com', '$2y$10$LFiie8I8OrqNc2EPC7yGje8SAn9bTiuaZwal4zes3cSBeCxMF40Ni', '0900000001', 1, 'Công Ty TNHH', '123', 'Thái cực', NULL, '2025-05-18 06:53:13', '2025-05-22 22:40:04', NULL, 1, 0, NULL),
(109, 'zzj07635@toaik.com', 'zzj07635@toaik.com', '$2y$10$RmjoBS8/bwxS3IJnpP6GpeMeR83XnFML3JxmXm1Xld3WVdMFJtgWW', '09123456923', 0, NULL, NULL, NULL, NULL, '2025-05-26 23:16:05', '2025-05-26 23:19:21', NULL, 1, 1, NULL),
(110, 'b23dccn510trr1n13fa24', 'mfc90967@toaik.com', '$2y$10$me/BtbN.W.x5oSwCJWrse.e7RoDqTJQSXHDuur6twuemtpRnL93Oi', '0912345322', 0, NULL, NULL, NULL, NULL, '2025-05-26 23:26:39', '2025-05-26 23:28:09', NULL, 1, 1, NULL),
(111, 'whi18324@jioso.com', 'whi18324@jioso.com', '$2y$10$jRs.io00qk1vGdgjDZPppu0dgWWoc4L/dlgu2mCeKn88.ovgKY1ei', '0912345699', 0, NULL, NULL, NULL, NULL, '2025-05-26 23:33:17', '2025-05-26 23:34:32', NULL, 1, 1, NULL),
(112, 'xes60082@toaik.com', 'xes60082@toaik.com', '$2y$10$6ZuNU87ZXivHv.E98bgdEuofj1dslIifd/p1H833PNzQgXNJPUhzy', '0900000020', 0, NULL, NULL, NULL, NULL, '2025-05-27 07:07:32', '2025-05-27 07:08:01', NULL, 1, 1, NULL),
(113, 'lgo61401', 'lgo61401@jioso.com', '$2y$10$p/XPMYM5ba/nbgTOoGRyg.7RLjdPDaIzM8WUccTnKDR1MYvNikjJW', '0999999442', 0, NULL, NULL, NULL, NULL, '2025-05-27 07:21:57', '2025-05-27 07:23:44', NULL, 1, 1, NULL),
(114, 'gop25047@jioso.com', 'gop25047@jioso.com', '$2y$10$27QfS.Tbf2Vv/5j.8WADL.dN8MvxXssT9nlnMuR/6S4j3xuu7sNB.', '0999999432', 0, NULL, NULL, NULL, NULL, '2025-05-27 07:40:40', '2025-05-27 07:41:35', NULL, 1, 1, NULL),
(115, 'vxn91269@jioso.com', 'vxn91269@jioso.com', '$2y$10$pgQ3362cJX8G9Shv0uGI5O7gcUYgIr7arNdQXFckQWWjoIATtKFoe', '0987999443', 0, NULL, NULL, NULL, NULL, '2025-05-27 08:01:30', '2025-05-27 08:02:02', NULL, 1, 1, NULL),
(116, 'nva79925@jioso.com', 'nva79925@jioso.com', '$2y$10$LdPGh91Yu3RGPnu14XsuLeSPML049tDU03Tx1O.VMrDQPbR0uWXl6', '09232238233', 0, NULL, NULL, NULL, NULL, '2025-05-27 08:14:42', '2025-05-27 08:16:22', NULL, 1, 1, NULL),
(117, 'pyn61579@toaik.com', 'pyn61579@toaik.com', '$2y$10$Krs8J1rgq2Dl0OrkImcLTedqijKaHIjdvoFlw0lN4nEhpy88PVkKi', '0912985695', 0, NULL, NULL, NULL, NULL, '2025-05-27 08:18:00', '2025-05-27 08:19:25', NULL, 1, 1, NULL),
(118, 'nci02654@toaik.com', 'nci02654@toaik.com', '$2y$10$I1giAWlMD/tDzBBZZ2pYRuUGwYNcImxYVz3d3LbiuwtoJwc3FB5tW', '0932838322', 0, NULL, NULL, NULL, NULL, '2025-05-27 08:41:06', '2025-05-27 08:41:29', NULL, 1, 1, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `user_devices`
--

CREATE TABLE `user_devices` (
  `id` int NOT NULL,
  `user_id` int NOT NULL,
  `device_fingerprint` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `ip_address` varchar(45) COLLATE utf8mb4_unicode_ci NOT NULL,
  `user_agent` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `last_login_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `login_count` int DEFAULT '1',
  `voucher_code` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `voucher_used` tinyint(1) DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `user_devices`
--

INSERT INTO `user_devices` (`id`, `user_id`, `device_fingerprint`, `ip_address`, `user_agent`, `created_at`, `last_login_at`, `login_count`, `voucher_code`, `voucher_used`) VALUES
(12, 118, 'bfbefb14', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/136.0.0.0 Safari/537.36', '2025-05-27 01:41:12', '2025-05-27 01:47:22', 2, 'VIP20', 1);

-- --------------------------------------------------------

--
-- Table structure for table `user_ranking`
--

CREATE TABLE `user_ranking` (
  `id` int NOT NULL,
  `user_id` int NOT NULL,
  `referral_count` int NOT NULL DEFAULT '0',
  `monthly_commission` decimal(15,2) NOT NULL DEFAULT '0.00',
  `total_commission` decimal(15,2) NOT NULL DEFAULT '0.00',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `user_ranking`
--

INSERT INTO `user_ranking` (`id`, `user_id`, `referral_count`, `monthly_commission`, `total_commission`, `created_at`, `updated_at`) VALUES
(1, 88, 0, 0.00, 0.00, '2025-05-20 15:27:20', '2025-05-23 11:00:19'),
(2, 89, 0, 0.00, 0.00, '2025-05-20 15:27:20', '2025-05-23 11:00:19'),
(3, 90, 0, 0.00, 0.00, '2025-05-20 15:27:20', '2025-05-23 11:00:19'),
(4, 91, 0, 0.00, 0.00, '2025-05-20 15:27:20', '2025-05-23 11:00:19'),
(5, 92, 5, 3260000.00, 3260000.00, '2025-05-20 15:27:20', '2025-05-23 11:00:19'),
(6, 93, 0, 0.00, 0.00, '2025-05-20 15:27:20', '2025-05-23 11:00:19'),
(7, 94, 0, 0.00, 0.00, '2025-05-20 15:27:20', '2025-05-23 11:00:19'),
(8, 95, 1, 10580.00, 10580.00, '2025-05-20 15:27:20', '2025-05-23 11:00:19'),
(9, 96, 0, 0.00, 0.00, '2025-05-20 15:27:20', '2025-05-23 11:00:19'),
(10, 97, 0, 0.00, 0.00, '2025-05-20 15:27:20', '2025-05-23 11:00:19'),
(11, 98, 0, 0.00, 0.00, '2025-05-20 15:27:20', '2025-05-23 11:00:19'),
(12, 99, 0, 0.00, 0.00, '2025-05-20 15:27:20', '2025-05-23 11:00:19'),
(13, 101, 0, 0.00, 0.00, '2025-05-20 15:27:20', '2025-05-23 11:00:19'),
(14, 105, 0, 0.00, 0.00, '2025-05-20 15:27:20', '2025-05-23 11:00:19'),
(15, 110, 0, 0.00, 0.00, '2025-05-20 15:27:20', '2025-05-23 11:00:19'),
(16, 111, 0, 0.00, 0.00, '2025-05-20 15:27:20', '2025-05-23 11:00:19'),
(17, 112, 5, 1620000.00, 1620000.00, '2025-05-20 15:27:20', '2025-05-23 11:00:19'),
(18, 113, 0, 0.00, 0.00, '2025-05-20 15:27:20', '2025-05-23 11:00:19'),
(19, 114, 0, 0.00, 0.00, '2025-05-20 15:27:20', '2025-05-23 11:00:19'),
(20, 115, 0, 0.00, 0.00, '2025-05-20 15:27:20', '2025-05-23 11:00:19'),
(21, 116, 0, 0.00, 0.00, '2025-05-20 15:27:20', '2025-05-23 11:00:19'),
(43, 117, 0, 0.00, 0.00, '2025-05-21 04:41:02', '2025-05-23 11:00:19'),
(44, 118, 0, 0.00, 0.00, '2025-05-21 04:41:02', '2025-05-23 11:00:19'),
(45, 119, 0, 0.00, 0.00, '2025-05-21 04:41:02', '2025-05-23 11:00:19'),
(46, 120, 0, 0.00, 0.00, '2025-05-21 04:41:02', '2025-05-23 11:00:19'),
(247, 121, 0, 0.00, 0.00, '2025-05-21 04:54:25', '2025-05-23 11:00:19'),
(378, 122, 0, 0.00, 0.00, '2025-05-21 05:42:08', '2025-05-23 11:00:19'),
(433, 123, 0, 0.00, 0.00, '2025-05-21 14:29:34', '2025-05-23 11:00:19'),
(462, 124, 0, 0.00, 0.00, '2025-05-21 14:32:08', '2025-05-23 11:00:19'),
(492, 125, 0, 0.00, 0.00, '2025-05-21 16:47:17', '2025-05-23 11:00:19'),
(523, 126, 0, 0.00, 0.00, '2025-05-23 04:50:38', '2025-05-23 11:00:19'),
(524, 128, 0, 0.00, 0.00, '2025-05-23 04:50:38', '2025-05-23 11:00:19'),
(525, 129, 0, 0.00, 0.00, '2025-05-23 04:50:38', '2025-05-23 11:00:19'),
(526, 130, 0, 0.00, 0.00, '2025-05-23 04:50:38', '2025-05-23 11:00:19'),
(561, 131, 0, 0.00, 0.00, '2025-05-23 10:44:50', '2025-05-23 11:00:19'),
(562, 132, 0, 0.00, 0.00, '2025-05-23 10:44:50', '2025-05-23 11:00:19'),
(599, 133, 0, 0.00, 0.00, '2025-05-23 10:56:14', '2025-05-23 11:00:19');

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
('33l9ojgsk5jp6en8s0ahs9rv8t', 6, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/136.0.0.0 Safari/537.36', '2025-05-27 08:54:14', '2025-05-27 14:23:30', 1),
('42mhvstjh560lq15hvgjn59gav', 6, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/136.0.0.0 Safari/537.36', '2025-05-23 15:52:51', '2025-05-23 15:52:55', 0),
('43dkpv8s2foo8o8c7ppo9ojp5e', 6, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/136.0.0.0 Safari/537.36', '2025-05-23 15:01:00', '2025-05-23 15:17:18', 0),
('48nbopk0pcafpoepvphvh82107', 6, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/136.0.0.0 Safari/537.36', '2025-05-18 09:42:44', '2025-05-18 10:35:43', 1),
('48pu7b1p7m5lktb9b8d6jhfsbk', 6, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/136.0.0.0 Safari/537.36', '2025-05-25 10:23:52', '2025-05-25 12:44:52', 1),
('4c2hesergl9j3q8h5m13g9agkf', 6, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/136.0.0.0 Safari/537.36', '2025-05-17 15:55:56', '2025-05-17 17:20:16', 0),
('4ne9odh9lg5rc8mvvtgbeup4fj', 6, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/136.0.0.0 Safari/537.36', '2025-05-14 16:24:24', '2025-05-14 17:57:16', 0),
('4sdaom0dfk79bu1fki4duhg8ts', 6, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/136.0.0.0 Safari/537.36', '2025-05-14 09:46:29', '2025-05-14 11:41:12', 1),
('50uk071h5e0qjoiugbrc0n0b0k', 12, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/136.0.0.0 Safari/537.36', '2025-05-21 17:33:46', '2025-05-21 17:34:03', 0),
('518fs65n7tbu8igvai4s6ai6ik', 6, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/136.0.0.0 Safari/537.36', '2025-05-23 16:33:23', '2025-05-23 17:46:14', 1),
('53j7dsm30d0c68h4pn5tp61l0u', 6, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/136.0.0.0 Safari/537.36', '2025-05-23 06:27:02', '2025-05-23 10:10:04', 1),
('5gid0qej0ocs3fnfc2skgldi8a', 6, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/136.0.0.0 Safari/537.36', '2025-05-26 08:58:32', '2025-05-26 10:13:59', 0),
('5lnjnrg1b4eqte48cv6krcmpb5', 6, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/136.0.0.0 Safari/537.36', '2025-05-12 15:54:56', '2025-05-12 16:44:12', 1),
('5m2s5ji9a7gn8v8t5h6bpsip27', 6, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/136.0.0.0 Safari/537.36', '2025-05-24 11:25:06', '2025-05-24 11:27:18', 1),
('5n3oqmgtocmrr6orqqehr8btrt', 6, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/136.0.0.0 Safari/537.36', '2025-05-16 06:26:34', '2025-05-16 07:49:09', 1),
('5oqijcr1gkc5juuapk7strd5aq', 6, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/136.0.0.0 Safari/537.36', '2025-05-22 20:43:52', '2025-05-22 20:44:11', 1),
('6267jlhrk53dv2k5ad96hiumrt', 6, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/136.0.0.0 Safari/537.36', '2025-05-21 09:16:05', '2025-05-21 09:16:24', 0),
('705kv3i4q433sc0c498hv24d2t', 6, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/136.0.0.0 Safari/537.36', '2025-05-19 13:55:50', '2025-05-19 15:29:31', 1),
('739qv06joie78afl4ka2aj8b13', 6, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/136.0.0.0 Safari/537.36', '2025-05-25 11:50:27', '2025-05-25 13:01:36', 1),
('74i734hlmpul2r93is77if9kca', 6, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/136.0.0.0 Safari/537.36', '2025-05-15 23:01:31', '2025-05-15 23:22:53', 1),
('7ae1m4kgqeijuavqfsq1h0838d', 6, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/136.0.0.0 Safari/537.36', '2025-05-13 09:36:06', '2025-05-13 12:09:56', 1),
('7bqendcdlgquvkundhoe36ei38', 6, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/136.0.0.0 Safari/537.36', '2025-05-17 20:40:56', '2025-05-17 20:41:06', 0),
('7d02amrpfnb9d83ppnadrrkiov', 6, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/136.0.0.0 Safari/537.36', '2025-05-21 09:21:57', '2025-05-21 09:22:05', 1),
('7i6r0r8ahj0tu6168jrdi8tumn', 6, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/136.0.0.0 Safari/537.36', '2025-05-24 23:13:25', '2025-05-24 23:16:56', 1),
('7is621tk8ie672s8rrc4h7gf5j', 6, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/136.0.0.0 Safari/537.36', '2025-05-13 19:54:01', '2025-05-13 23:41:22', 1),
('802amtsejvub7ra0cg02rpanjb', 6, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/136.0.0.0 Safari/537.36', '2025-05-17 11:03:43', '2025-05-17 11:45:14', 1),
('833eavssu8l3mnvn0h3rtju099', 6, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/136.0.0.0 Safari/537.36', '2025-05-14 15:31:15', '2025-05-14 16:24:20', 0),
('83vbjis4lka52tneei52ism55d', 6, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/136.0.0.0 Safari/537.36', '2025-05-18 07:17:29', '2025-05-18 07:21:20', 0),
('86e9r4iv1e2q7ntafeffsadgce', 6, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/136.0.0.0 Safari/537.36', '2025-05-19 19:06:21', '2025-05-19 23:34:34', 1),
('89be43sc5uk4upigv76mqosf4u', 6, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/136.0.0.0 Safari/537.36', '2025-05-26 06:57:44', '2025-05-26 08:06:47', 1),
('8ak58mef77r20tqliijnkga13d', 12, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/136.0.0.0 Safari/537.36', '2025-05-18 07:21:24', '2025-05-18 07:22:19', 1),
('8d9rsrdp9samil3il77j3peejs', 6, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/136.0.0.0 Safari/537.36', '2025-05-21 17:34:07', '2025-05-21 17:51:03', 0),
('8ei44jj0jguihnk75g22ln1d79', 6, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/136.0.0.0 Safari/537.36', '2025-05-19 23:16:58', '2025-05-19 23:29:28', 1),
('9etn7oc1j2hg423grh1b34sgcm', 6, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/136.0.0.0 Safari/537.36', '2025-05-22 10:09:44', '2025-05-22 10:15:28', 1),
('9t2h616mgstnl2rc79n2dj969l', 6, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/136.0.0.0 Safari/537.36', '2025-05-26 10:24:17', '2025-05-26 10:48:27', 1),
('a5k4nq4mjkp9rmr2kb167bv5mm', 6, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/136.0.0.0 Safari/537.36', '2025-05-12 21:09:16', '2025-05-12 22:37:18', 1),
('arjoeh46l0mdkt9pbfjvc2059k', 12, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/136.0.0.0 Safari/537.36', '2025-05-23 10:50:19', '2025-05-23 11:55:10', 0),
('as93r4gpjvhochg390igmp2vq2', 6, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/136.0.0.0 Safari/537.36', '2025-05-14 13:40:11', '2025-05-14 15:29:31', 1),
('b4roleb5l0inatksmftpk7eicf', 6, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/136.0.0.0 Safari/537.36', '2025-05-23 13:11:42', '2025-05-23 14:11:47', 1),
('bcigqr11e1kop9iofi6ma448d7', 6, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/136.0.0.0 Safari/537.36', '2025-05-21 18:01:36', '2025-05-21 18:06:59', 0),
('bf3m2ous2uqkabch00smijl28u', 6, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/136.0.0.0 Safari/537.36', '2025-05-11 22:30:49', '2025-05-11 22:39:04', 1),
('bikhcpe6viic9efqmngpop9772', 6, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/136.0.0.0 Safari/537.36', '2025-05-14 07:49:44', '2025-05-14 08:01:55', 1),
('buavm1vr7d3vr3vfd5g3bncn7e', 6, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/136.0.0.0 Safari/537.36', '2025-05-23 15:58:20', '2025-05-23 16:15:47', 1),
('c4haeknqjojm63nij9bhb9hu9f', 6, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/136.0.0.0 Safari/537.36', '2025-05-16 17:58:39', '2025-05-16 18:22:55', 1),
('c6rffaehpbq1g5fv0vsgcbvfpm', 12, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/136.0.0.0 Safari/537.36', '2025-05-17 17:20:20', '2025-05-17 18:47:19', 0),
('c8bs9dptrksse3r5j685p73krm', 6, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/136.0.0.0 Safari/537.36', '2025-05-11 19:45:11', '2025-05-11 22:30:45', 1),
('c8c5dakv67l5dslpc28cjgsbv1', 6, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/136.0.0.0 Safari/537.36', '2025-05-23 11:55:13', '2025-05-23 13:04:55', 1),
('ce9iv7utg6regb0f4sev2o45mq', 6, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/136.0.0.0 Safari/537.36', '2025-05-22 10:09:56', '2025-05-22 10:43:12', 1),
('clp69irqtpdp4itiv09ae884dn', 6, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/136.0.0.0 Safari/537.36', '2025-05-14 18:16:07', '2025-05-14 23:42:12', 1),
('dcajg4l4runb3v5rcp2v5anc07', 6, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/136.0.0.0 Safari/537.36', '2025-05-17 22:25:45', '2025-05-17 23:18:18', 1),
('dh7fcqrcom2m56rbrt6outb9cd', 6, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/136.0.0.0 Safari/537.36', '2025-05-23 21:20:48', '2025-05-23 21:21:06', 1),
('dnqeee8lc6tp0k6q8l5qrgm0kh', 6, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/136.0.0.0 Safari/537.36', '2025-05-16 15:05:56', '2025-05-16 16:41:24', 0),
('e1q3onvrn5sfi4qqnjjqm74l2e', 6, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/136.0.0.0 Safari/537.36', '2025-05-17 20:40:24', '2025-05-17 20:40:34', 0),
('e3nh4o5ugpu3hugplckugk7h57', 6, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/136.0.0.0 Safari/537.36', '2025-05-16 20:50:57', '2025-05-16 20:50:57', 1),
('eara260oap433ev60va377ui72', 6, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/136.0.0.0 Safari/537.36', '2025-05-23 17:10:36', '2025-05-23 17:20:59', 1),
('echjadqmmdt7be0jckbqa4bc3g', 6, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/136.0.0.0 Safari/537.36', '2025-05-23 22:45:01', '2025-05-23 22:45:13', 1),
('een5lmf382cnn93m4tid39thgm', 6, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/136.0.0.0 Safari/537.36', '2025-05-21 18:00:20', '2025-05-21 18:00:47', 0),
('ekoghfktp7kp9d29npvdtn9bvd', 6, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/136.0.0.0 Safari/537.36', '2025-05-16 16:41:54', '2025-05-16 16:51:42', 0),
('fm2vjvohkld140hluhelmfm0ps', 6, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/136.0.0.0 Safari/537.36', '2025-05-27 14:23:33', '2025-05-27 14:23:36', 1),
('fqk1c2ik14ii4vjcs0irojq17q', 12, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/136.0.0.0 Safari/537.36', '2025-05-17 06:17:37', '2025-05-17 06:25:16', 0),
('gopni5l0c4nt2ov3mtnfkllk3r', 6, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/136.0.0.0 Safari/537.36', '2025-05-21 09:21:39', '2025-05-21 09:21:49', 0),
('gp04msh40mcsjvm86ttd9qvqpp', 6, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/136.0.0.0 Safari/537.36', '2025-05-22 19:20:16', '2025-05-22 19:20:37', 0),
('gsjv50ksn9e10nbvs46d5m9ech', 12, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/136.0.0.0 Safari/537.36', '2025-05-21 18:07:02', '2025-05-21 18:31:08', 1),
('gtbo10s3i6p8em1tr1if53rbco', 6, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/136.0.0.0 Safari/537.36', '2025-05-19 16:44:29', '2025-05-19 16:49:51', 1),
('guhp8fhveh1pr1hcbofpguqs3l', 6, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/136.0.0.0 Safari/537.36', '2025-05-26 10:52:49', '2025-05-26 11:31:39', 1),
('gvtdbhaelpo7s317pq9e6g3n7m', 6, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/136.0.0.0 Safari/537.36', '2025-05-21 06:43:21', '2025-05-21 07:32:28', 0),
('h2tv3a9reen4c85pf8lb4ttunt', 6, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/136.0.0.0 Safari/537.36', '2025-05-24 11:46:28', '2025-05-24 11:50:00', 1),
('hukanq224h9ij6qk5pgb33l6qd', 6, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/136.0.0.0 Safari/537.36', '2025-05-21 09:17:39', '2025-05-21 09:18:05', 0),
('i3uogru3gs240i822to4gg8lrs', 6, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/136.0.0.0 Safari/537.36', '2025-05-14 06:39:28', '2025-05-14 07:45:27', 1),
('i8el0veaehmnfqkhkhun9mld0o', 6, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/136.0.0.0 Safari/537.36', '2025-05-24 21:44:45', '2025-05-24 21:44:50', 1),
('io2alhrcvr4j3opbp858irfr3j', 6, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/136.0.0.0 Safari/537.36', '2025-05-15 12:17:42', '2025-05-15 15:40:54', 1),
('iqc1bl9ndmbh3op1pfu1l6v9kq', 6, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/136.0.0.0 Safari/537.36', '2025-05-17 06:11:36', '2025-05-17 06:16:25', 0),
('iqj0f7d1j15cfu0dtecnr2p5ve', 6, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/136.0.0.0 Safari/537.36', '2025-05-11 18:44:00', '2025-05-11 19:45:07', 1),
('is9kb5v0kn7aubgocrkv99u5pn', 6, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/136.0.0.0 Safari/537.36', '2025-05-24 10:00:17', '2025-05-24 10:00:59', 1),
('itrgln8o6hrt1ihj6vd8c499ii', 6, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/136.0.0.0 Safari/537.36', '2025-05-24 11:56:37', '2025-05-24 11:56:37', 1),
('j431kq1piqs913tgpjuej5cnn0', 12, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/136.0.0.0 Safari/537.36', '2025-05-17 20:40:45', '2025-05-17 20:40:53', 0),
('jbrpnbjj7lq0s4ct79t2k6e7ua', 10, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/136.0.0.0 Safari/537.36', '2025-05-15 23:01:03', '2025-05-15 23:01:23', 0),
('jmj547av0fe6umhi1ekmqq23v3', 6, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/136.0.0.0 Safari/537.36', '2025-05-12 20:36:52', '2025-05-12 21:05:21', 0),
('jtk15dhtfqb12vaqtik84cn6o9', 6, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/136.0.0.0 Safari/537.36', '2025-05-21 09:18:14', '2025-05-21 09:18:49', 0),
('k56bonlb2tfoecnl7k9k3oeua7', 6, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/136.0.0.0 Safari/537.36', '2025-05-24 23:25:23', '2025-05-24 23:29:22', 1),
('ki3fbll4apinnf06pckln0kr0m', 6, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/136.0.0.0 Safari/537.36', '2025-05-17 06:27:01', '2025-05-17 07:46:39', 1),
('ktsk0vp0svigq2614rfgilljp0', 6, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/136.0.0.0 Safari/537.36', '2025-05-24 22:25:19', '2025-05-24 22:25:27', 1),
('l3rehqtm17v18rs03tgc02r716', 12, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/136.0.0.0 Safari/537.36', '2025-05-21 18:00:51', '2025-05-21 18:01:08', 0),
('leqpdtt52otbqo95jrpllggttb', 6, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/136.0.0.0 Safari/537.36', '2025-05-25 15:30:03', '2025-05-25 18:11:12', 1),
('lp0ec2on4as215e4qc6mv79d40', 6, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/136.0.0.0 Safari/537.36', '2025-05-13 06:30:06', '2025-05-13 06:49:46', 1),
('lplplpna8jm08d3p9f88gkle0a', 6, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/136.0.0.0 Safari/537.36', '2025-05-18 08:58:30', '2025-05-18 09:42:34', 0),
('m85dv17c83o7393lnf7e8g5evj', 6, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/136.0.0.0 Safari/537.36', '2025-05-21 07:37:03', '2025-05-21 07:37:09', 0),
('mjqo7k0u10tfmhn5919socvith', 6, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/136.0.0.0 Safari/537.36', '2025-05-24 11:41:17', '2025-05-24 11:46:22', 1),
('mpeuahoqb9blsqpjiik52fqh8k', 12, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/136.0.0.0 Safari/537.36', '2025-05-17 10:25:35', '2025-05-17 11:03:40', 0),
('mrvao206ckuqpkto7030q398kj', 6, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/136.0.0.0 Safari/537.36', '2025-05-16 16:52:07', '2025-05-16 17:36:41', 1),
('n8nss4psjqgbmfhg74edh2s6t5', 6, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/136.0.0.0 Safari/537.36', '2025-05-25 09:02:52', '2025-05-25 11:50:23', 1),
('nnhmbedalo8snhg1uptq9k8doj', 6, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/136.0.0.0 Safari/537.36', '2025-05-11 18:21:26', '2025-05-11 18:41:50', 1),
('o19udhdsii4ucj360rd0d2uibv', 6, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/136.0.0.0 Safari/537.36', '2025-05-24 23:06:48', '2025-05-24 23:07:33', 1),
('o7e5tq2118ab2460s881t6obor', 11, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/136.0.0.0 Safari/537.36', '2025-05-16 16:41:28', '2025-05-16 16:41:45', 0),
('ob6cho21421qrr73vd44o70lln', 6, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/136.0.0.0 Safari/537.36', '2025-05-15 07:32:51', '2025-05-15 07:58:28', 1),
('ograjkh3ufhpad8kcdclok9dne', 6, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/136.0.0.0 Safari/537.36', '2025-05-26 11:23:47', '2025-05-26 11:33:27', 1),
('oin3g13u5oi8ebrv6mih46e05h', 12, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/136.0.0.0 Safari/537.36', '2025-05-17 06:25:42', '2025-05-17 06:26:53', 0),
('oom9dfvqugb0e1ha1onhtljbir', 6, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/136.0.0.0 Safari/537.36', '2025-05-26 10:55:20', '2025-05-26 10:55:27', 1),
('ott4a58m3fmccbhqint9mt1lkr', 8, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/136.0.0.0 Safari/537.36', '2025-05-14 18:15:41', '2025-05-14 18:16:02', 0),
('peo9husvdmu2umb0m1pq0pi2o9', 6, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/136.0.0.0 Safari/537.36', '2025-05-21 13:38:06', '2025-05-21 15:16:49', 1),
('pk57o11foot20911umd8qph900', 6, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/136.0.0.0 Safari/537.36', '2025-05-17 06:25:21', '2025-05-17 06:25:25', 0),
('q18ngh7ns4ftcv7ange37agu66', 6, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/136.0.0.0 Safari/537.36', '2025-05-12 22:56:18', '2025-05-12 23:33:04', 1),
('q377883ge9u7t8jj8l3u052npl', 6, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/136.0.0.0 Safari/537.36', '2025-05-12 13:45:47', '2025-05-12 15:33:14', 1),
('r22656o8imf398ufs29l8aohn0', 6, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/136.0.0.0 Safari/537.36', '2025-05-22 19:20:48', '2025-05-22 19:22:06', 0),
('raauihma6durgfmte65rtb5vrt', 12, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/136.0.0.0 Safari/537.36', '2025-05-17 20:53:45', '2025-05-17 22:25:41', 0),
('rftksimiarmf5epopbe71bv4r9', 6, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/136.0.0.0 Safari/537.36', '2025-05-16 19:22:47', '2025-05-16 23:49:22', 1),
('rgrduo7lbfvqtgh40h17801ll5', 6, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/136.0.0.0 Safari/537.36', '2025-05-23 16:31:53', '2025-05-23 16:33:19', 0),
('rmg0gee55hvj46cnt6h11kh956', 6, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/136.0.0.0 Safari/537.36', '2025-05-17 20:44:01', '2025-05-17 20:53:42', 0),
('rrm3jjoovn385ig72omv0cmidi', 11, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/136.0.0.0 Safari/537.36', '2025-05-16 16:51:49', '2025-05-16 16:51:59', 0),
('rtc1iqtjgd5bkkvvh3qfk6a946', 6, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/136.0.0.0 Safari/537.36', '2025-05-21 09:15:18', '2025-05-21 09:15:58', 0),
('sc61q8tndeidctff5m8aeigjok', 6, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/136.0.0.0 Safari/537.36', '2025-05-16 18:03:48', '2025-05-16 18:05:08', 1),
('sdf0140ga1hr6rme67nodrp8b3', 6, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/136.0.0.0 Safari/537.36', '2025-05-22 19:22:11', '2025-05-22 20:39:27', 1),
('se21l8kb3eb8ebn0c30n313kd6', 6, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/136.0.0.0 Safari/537.36', '2025-05-24 11:31:16', '2025-05-24 11:35:30', 1),
('see9h077q2l30mvu8egp22kv00', 6, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/136.0.0.0 Safari/537.36', '2025-05-13 06:53:21', '2025-05-13 09:36:02', 1),
('sghv5vtr6nlqp7h8knq5je7bdv', 6, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/136.0.0.0 Safari/537.36', '2025-05-26 11:03:52', '2025-05-26 11:18:57', 1),
('si3ssb74q5ptrfdhhlq40tf2jm', 6, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/136.0.0.0 Safari/537.36', '2025-05-21 15:16:53', '2025-05-21 17:33:08', 0),
('t57uarqkc4kd39rfmntu6uggao', 6, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/136.0.0.0 Safari/537.36', '2025-05-17 12:56:47', '2025-05-17 13:56:45', 1),
('t5fki6ssr5mi5tia9dk3l47ase', 6, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/136.0.0.0 Safari/537.36', '2025-05-12 21:05:25', '2025-05-12 21:07:19', 1),
('tdijrq0k0qo32podgaddarr06n', 6, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/136.0.0.0 Safari/537.36', '2025-05-26 22:06:40', '2025-05-26 22:06:40', 1),
('tku7old1u90dus7jj48k00rgmh', 12, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/136.0.0.0 Safari/537.36', '2025-05-17 20:26:21', '2025-05-17 20:40:20', 0),
('tpagt41j4jeqlrl25fn843i8dh', 6, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/136.0.0.0 Safari/537.36', '2025-05-13 12:46:17', '2025-05-13 13:59:45', 1),
('tqlcmj9siudejq0a9517ggi9pu', 6, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/136.0.0.0 Safari/537.36', '2025-05-25 08:29:29', '2025-05-25 08:34:24', 0),
('tr70dh3ppsiubihtsq7aruu9v1', 6, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/136.0.0.0 Safari/537.36', '2025-05-25 18:54:50', '2025-05-25 23:45:34', 1),
('trcq0ee4orhco9diggqrl72c1g', 6, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/136.0.0.0 Safari/537.36', '2025-05-26 10:53:34', '2025-05-26 10:53:50', 1),
('ub6aublc15g1gh4kucim12pm99', 6, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/136.0.0.0 Safari/537.36', '2025-05-19 13:06:01', '2025-05-19 13:53:46', 1),
('uk0civ4t3h0454lvi2tfa88kgi', 6, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/136.0.0.0 Safari/537.36', '2025-05-24 23:32:07', '2025-05-24 23:35:00', 1),
('ukie4f4g7fgtblv77vuto28q7a', 6, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/136.0.0.0 Safari/537.36', '2025-05-26 23:15:31', '2025-05-26 23:15:35', 1),
('um5anedjherccd7ue14ri8v3ru', 6, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/136.0.0.0 Safari/537.36', '2025-05-12 07:04:20', '2025-05-12 07:56:32', 1),
('unbmphj0kgnud841carmaspqss', 6, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/136.0.0.0 Safari/537.36', '2025-05-11 23:40:02', '2025-05-11 23:40:04', 1),
('usrci1mtv27ogkuf7u7h3bm65i', 6, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/136.0.0.0 Safari/537.36', '2025-05-17 06:16:41', '2025-05-17 06:16:52', 0),
('v4cerim3ds3eok82rv2qndjlub', 12, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/136.0.0.0 Safari/537.36', '2025-05-17 20:41:10', '2025-05-17 20:43:57', 0),
('v9ru4da2hfvo0rlv28mkrd0bhm', 6, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/136.0.0.0 Safari/537.36', '2025-05-23 15:45:46', '2025-05-23 15:45:50', 0),
('vaanvs08vojsp44rmcao9dkfgu', 6, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/136.0.0.0 Safari/537.36', '2025-05-14 08:58:38', '2025-05-14 09:17:12', 1),
('vi8gk37c1t7fbde4g42cepedc6', 6, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/136.0.0.0 Safari/537.36', '2025-05-18 06:06:27', '2025-05-18 07:17:23', 0),
('vqoliptrjqcrs3lh5p71ha82vu', 6, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/136.0.0.0 Safari/537.36', '2025-05-12 13:30:36', '2025-05-12 13:30:48', 1),
('vv30n889lt7dg2k6nk2fhfil7d', 6, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/136.0.0.0 Safari/537.36', '2025-05-21 09:20:29', '2025-05-21 09:20:50', 0);

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
(10, 96, 1, 0, 'light', '2025-05-11 14:44:27', NULL),
(11, 99, 1, 0, 'light', '2025-05-12 21:18:06', NULL),
(12, 100, 1, 0, 'light', '2025-05-12 22:46:48', NULL),
(13, 102, 1, 0, 'light', '2025-05-14 17:39:24', NULL),
(14, 104, 1, 0, 'light', '2025-05-15 22:45:46', NULL),
(15, 105, 1, 0, 'light', '2025-05-16 18:13:06', NULL),
(16, 106, 1, 0, 'light', '2025-05-16 21:41:32', NULL),
(17, 108, 1, 0, 'light', '2025-05-18 06:53:13', NULL),
(18, 109, 1, 0, 'light', '2025-05-26 23:16:05', NULL),
(19, 110, 1, 0, 'light', '2025-05-26 23:26:39', NULL),
(20, 111, 1, 0, 'light', '2025-05-26 23:33:17', NULL),
(21, 112, 1, 0, 'light', '2025-05-27 07:07:32', NULL),
(22, 113, 1, 0, 'light', '2025-05-27 07:21:57', NULL),
(23, 114, 1, 0, 'light', '2025-05-27 07:40:40', NULL),
(24, 115, 1, 0, 'light', '2025-05-27 08:01:30', NULL),
(25, 116, 1, 0, 'light', '2025-05-27 08:14:42', NULL),
(26, 117, 1, 0, 'light', '2025-05-27 08:18:00', NULL),
(27, 118, 1, 0, 'light', '2025-05-27 08:41:06', NULL);

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
(1, 93, 5, NULL, '2025-05-10 10:57:20'),
(3, 93, 4, NULL, '2025-05-10 11:02:36'),
(4, 93, 9, NULL, '2025-05-10 11:18:46'),
(6, 93, 9, NULL, '2025-05-10 11:29:05'),
(7, 93, 9, NULL, '2025-05-10 11:40:02'),
(10, 93, 7, NULL, '2025-05-10 20:28:21'),
(13, 93, 7, NULL, '2025-05-10 20:39:02'),
(14, 93, 7, NULL, '2025-05-10 20:47:02'),
(18, 93, 3, NULL, '2025-05-11 14:58:56'),
(19, 95, 1, NULL, '2025-05-11 18:05:02'),
(20, 90, 3, NULL, '2025-05-12 22:43:45'),
(21, 90, 3, NULL, '2025-05-12 22:48:52'),
(22, 105, 4, NULL, '2025-05-16 18:17:29'),
(23, 90, 3, NULL, '2025-05-19 22:18:29');

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
  `updated_at` datetime DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  `max_sa` int DEFAULT NULL COMMENT 'Số lượng tài khoản survey tối đa được phép áp dụng mã voucher. NULL = không giới hạn',
  `location_id` int DEFAULT NULL COMMENT 'Tỉnh được áp dụng mã voucher. NULL = áp dụng cho tất cả các tỉnh',
  `package_id` int DEFAULT NULL COMMENT 'Gói được áp dụng mã voucher. NULL = áp dụng cho tất cả các gói'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `voucher`
--

INSERT INTO `voucher` (`id`, `code`, `description`, `voucher_type`, `discount_value`, `max_discount`, `min_order_value`, `quantity`, `limit_usage`, `used_quantity`, `start_date`, `end_date`, `is_active`, `created_at`, `updated_at`, `max_sa`, `location_id`, `package_id`) VALUES
(1, 'FIXED100K', 'Giảm 100,000đ cho đơn hàng từ 500,000đ', 'fixed_discount', 100000.00, NULL, 500000.00, 100, NULL, 1, '2025-05-10 00:00:00', '2025-06-10 23:59:59', 1, '2025-05-10 09:34:47', '2025-05-11 18:05:02', NULL, NULL, NULL),
(2, 'PERCENT15', 'Giảm 15% tối đa 200,000đ cho đơn hàng từ 1 triệu đồng', 'percentage_discount', 15.00, 200000.00, 1000000.00, 50, NULL, 0, '2025-05-10 00:00:00', '2025-07-31 23:59:59', 1, '2025-05-10 09:34:47', '2025-05-11 14:58:44', NULL, NULL, NULL),
(3, 'EXTRAMONTH', 'Tặng thêm 1 tháng khi gia hạn gói dịch vụ', 'extend_duration', 1.00, NULL, NULL, 200, NULL, 4, '2025-05-10 00:00:00', '2025-12-31 23:59:59', 1, '2025-05-10 09:34:47', '2025-05-19 22:18:29', NULL, NULL, NULL),
(4, 'PERCENT10', 'Giảm 10% tối đa 100.000đ cho đơn hàng từ 200.000đ', 'percentage_discount', 10.00, 100000.00, 200000.00, 120, NULL, 2, '2025-05-01 00:00:00', '2025-07-31 23:59:59', 1, '2025-05-10 10:20:40', '2025-05-16 18:17:29', NULL, NULL, NULL),
(5, 'FIXED200K', 'Giảm 200.000đ cho đơn hàng từ 1.000.000đ', 'fixed_discount', 200000.00, NULL, 1000000.00, 30, NULL, 1, '2025-05-01 00:00:00', '2025-12-31 23:59:59', 1, '2025-05-10 10:20:40', '2025-05-10 10:57:20', NULL, NULL, NULL),
(6, 'PERCENT20', 'Giảm 20% tối đa 300.000đ cho đơn hàng từ 800.000đ', 'percentage_discount', 20.00, 300000.00, 800000.00, 40, NULL, 0, '2025-05-01 00:00:00', '2025-08-31 23:59:59', 1, '2025-05-10 10:20:40', '2025-05-10 10:20:40', NULL, NULL, NULL),
(7, 'FIXED30K', 'Giảm 30.000đ cho đơn hàng từ 150.000đ', 'fixed_discount', 30000.00, NULL, 150000.00, 200, NULL, 3, '2025-05-01 00:00:00', '2025-06-30 23:59:59', 1, '2025-05-10 10:20:40', '2025-05-10 21:27:35', NULL, NULL, NULL),
(9, 'FIXED25K', 'Giảm 25.000đ cho đơn hàng từ 100.000đ', 'fixed_discount', 25000.00, NULL, 100000.00, 250, NULL, 3, '2025-05-01 00:00:00', '2025-07-31 23:59:59', 1, '2025-05-10 10:20:40', '2025-05-10 11:40:02', NULL, NULL, NULL),
(10, 'PERCENT12', 'Giảm 12% tối đa 120.000đ cho đơn hàng từ 400.000đ', 'percentage_discount', 12.00, 120000.00, 400000.00, 60, NULL, 0, '2025-05-01 00:00:00', '2025-09-30 23:59:59', 1, '2025-05-10 10:20:40', '2025-05-10 10:20:40', NULL, NULL, NULL),
(11, 'FIXED80K', 'Giảm 80.000đ cho đơn hàng từ 250.000đ', 'fixed_discount', 80000.00, NULL, 250000.00, 70, NULL, 0, '2025-05-01 00:00:00', '2025-08-31 23:59:59', 1, '2025-05-10 10:20:40', '2025-05-10 10:20:40', NULL, NULL, NULL),
(12, 'PERCENT18', 'Giảm 18% tối đa 180.000đ cho mọi đơn hàng', 'percentage_discount', 18.00, 180000.00, NULL, 80, NULL, 0, '2025-05-01 00:00:00', '2025-07-31 23:59:59', 1, '2025-05-10 10:20:40', '2025-05-10 10:20:40', NULL, NULL, NULL),
(13, 'WELCOME500', 'Giảm 50.000đ cho đơn hàng từ 200.000đ, mỗi tài khoản chỉ dùng 1 lần', 'fixed_discount', 50000.00, 0.00, 2000000.00, 100, 1, 0, '2025-05-01 00:00:00', '2025-12-31 00:00:00', 0, '2025-05-10 10:52:34', '2025-05-25 18:55:57', NULL, NULL, NULL),
(14, 'VIP20', 'Giảm 20% tối đa 500.000đ, mỗi tài khoản dùng tối đa 3 lần', 'percentage_discount', 20.00, 500000.00, 0.00, 200, 3, 0, '2025-05-01 00:00:00', '2025-12-31 00:00:00', 1, '2025-05-10 10:52:34', '2025-05-14 22:51:28', NULL, NULL, NULL);

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
(1, 92, 100000.00, 'Test Bank', '12345', 'Test User', 'rejected', 'Test', '2025-05-11 06:26:00', '2025-05-17 09:37:03'),
(3, 93, 100000.00, 'Test Bank', '12345', 'Test User', 'completed', 'Test', '2025-05-11 06:32:25', '2025-05-17 23:53:58'),
(5, 93, 400000.00, 'Mbbank', '0981190564', 'DO VAN NGUYEN', 'completed', NULL, '2025-05-11 07:01:41', '2025-05-17 09:52:45'),
(6, 93, 500000.00, 'Mbbank', '0981 1905 64', 'DO VAN NGUYEN', 'rejected', NULL, '2025-05-11 07:08:21', '2025-05-17 02:40:45'),
(7, 93, 500000.00, 'Mbbank', '0981190564', 'DO VAN NGUYEN', 'rejected', NULL, '2025-05-11 07:48:57', '2025-05-17 09:52:42'),
(8, 93, 500000.00, 'Mbbank', '0981 1905 64', 'DO VAN NGUYEN', 'rejected', NULL, '2025-05-11 08:16:02', '2025-05-17 09:46:18'),
(9, 93, 500000.00, 'Mbbank', '0981 1905 64', 'DO VAN NGUYEN', 'rejected', NULL, '2025-05-11 09:55:43', '2025-05-12 06:24:09'),
(10, 93, 100000.00, 'Test Bank', '0981 1905 64', 'DO VAN NGUYEN', 'rejected', NULL, '2025-05-11 10:39:05', '2025-05-17 09:40:49'),
(11, 93, 2000000.00, 'Mbbank', '0981 1905 64', 'DO VAN NGUYEN', 'completed', NULL, '2025-05-11 11:06:49', '2025-05-11 11:07:09'),
(12, 90, 100000.00, 'MB', '088888888', 'TRAN HAI', 'completed', NULL, '2025-05-16 14:33:43', '2025-05-17 09:37:12');

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
-- Indexes for table `custom_roles`
--
ALTER TABLE `custom_roles`
  ADD PRIMARY KEY (`role_key`);

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
-- Indexes for table `user_devices`
--
ALTER TABLE `user_devices`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_device_fingerprint` (`device_fingerprint`),
  ADD KEY `idx_user_devices_ip` (`ip_address`),
  ADD KEY `idx_user_devices_user_id` (`user_id`);

--
-- Indexes for table `user_ranking`
--
ALTER TABLE `user_ranking`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_user_ranking` (`user_id`),
  ADD KEY `idx_total_commission` (`total_commission`),
  ADD KEY `idx_monthly_commission` (`monthly_commission`);

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
  ADD KEY `idx_voucher_dates` (`start_date`,`end_date`,`is_active`),
  ADD KEY `idx_voucher_max_sa` (`max_sa`),
  ADD KEY `idx_voucher_location` (`location_id`),
  ADD KEY `idx_voucher_package` (`package_id`);

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
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=513;

--
-- AUTO_INCREMENT for table `admin`
--
ALTER TABLE `admin`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

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
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=18;

--
-- AUTO_INCREMENT for table `invoice`
--
ALTER TABLE `invoice`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT for table `location`
--
ALTER TABLE `location`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=64;

--
-- AUTO_INCREMENT for table `manager`
--
ALTER TABLE `manager`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `package`
--
ALTER TABLE `package`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `referral`
--
ALTER TABLE `referral`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `referral_commission`
--
ALTER TABLE `referral_commission`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT for table `referred_user`
--
ALTER TABLE `referred_user`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `registration`
--
ALTER TABLE `registration`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=263;

--
-- AUTO_INCREMENT for table `support_requests`
--
ALTER TABLE `support_requests`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=32;

--
-- AUTO_INCREMENT for table `transaction_history`
--
ALTER TABLE `transaction_history`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=254;

--
-- AUTO_INCREMENT for table `user`
--
ALTER TABLE `user`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=119;

--
-- AUTO_INCREMENT for table `user_devices`
--
ALTER TABLE `user_devices`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT for table `user_ranking`
--
ALTER TABLE `user_ranking`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=637;

--
-- AUTO_INCREMENT for table `user_settings`
--
ALTER TABLE `user_settings`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=28;

--
-- AUTO_INCREMENT for table `user_voucher_usage`
--
ALTER TABLE `user_voucher_usage`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=24;

--
-- AUTO_INCREMENT for table `voucher`
--
ALTER TABLE `voucher`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=19;

--
-- AUTO_INCREMENT for table `withdrawal_request`
--
ALTER TABLE `withdrawal_request`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

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
-- Constraints for table `voucher`
--
ALTER TABLE `voucher`
  ADD CONSTRAINT `fk_voucher_location` FOREIGN KEY (`location_id`) REFERENCES `location` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_voucher_package` FOREIGN KEY (`package_id`) REFERENCES `package` (`id`) ON DELETE SET NULL ON UPDATE CASCADE;

--
-- Constraints for table `withdrawal_request`
--
ALTER TABLE `withdrawal_request`
  ADD CONSTRAINT `fk_withdrawal_user` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

DELIMITER $$
--
-- Events
--
CREATE DEFINER=`root`@`localhost` EVENT `ev_delete_old_activity_logs` ON SCHEDULE EVERY 1 DAY STARTS '2025-05-20 07:50:21' ON COMPLETION NOT PRESERVE ENABLE COMMENT 'Xóa activity_logs cũ hơn 7 ngày' DO DELETE FROM `activity_logs`
  WHERE `created_at` < NOW() - INTERVAL 7 DAY$$

DELIMITER ;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
