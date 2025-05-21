-- phpMyAdmin SQL Dump
-- version 5.2.2
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: May 21, 2025 at 11:38 AM
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
(255, 105, 'verification_email_sent', 'user', '105', NULL, '{\"email\":\"osn14300@jioso.com\",\"verification_token\":\"188d1b5c2a...\",\"timestamp\":\"2025-05-16 18:13:11\"}', 'Đã gửi email xác thực cho: osn14300@jioso.com', 0, '::1', NULL, '2025-05-16 18:13:11'),
(256, 105, 'email_verified', 'user', '105', NULL, '{\"status\":\"verified\",\"email\":\"osn14300@jioso.com\",\"timestamp\":\"2025-05-16 18:15:16\"}', 'Xác thực email thành công cho: osn14300@jioso.com', 0, '::1', NULL, '2025-05-16 18:15:16'),
(257, 105, 'purchase', 'registration', '224', NULL, '{\"registration_id\":\"224\",\"selected_accounts\":[1],\"total_price\":\"270000\",\"package\":\"Gói 3 Tháng\",\"location\":\"Yên Bái\"}', 'Mua gói dịch vụ: Gói 3 Tháng - Số lượng: 1', 0, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/136.0.0.0 Safari/537.36', '2025-05-16 18:16:43'),
(258, 105, 'apply_voucher', 'voucher', '4', NULL, '{\"voucher_code\":\"PERCENT10\",\"discount_value\":29160,\"new_amount\":262440,\"original_amount\":291600}', 'Áp dụng mã giảm giá: PERCENT10 cho đơn hàng. Giá trị giảm: 29160', 0, NULL, NULL, '2025-05-16 18:17:29'),
(259, 105, 'update_invoice_info', 'user', '105', NULL, NULL, 'Cập nhật thông tin xuất hóa đơn', 0, NULL, NULL, '2025-05-16 20:38:19'),
(260, 105, 'request_invoice', 'invoice', '9', NULL, '{\"transaction_history_id\":217}', 'Yêu cầu xuất hóa đơn cho giao dịch #217', 0, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/136.0.0.0 Safari/537.36', '2025-05-16 20:38:25'),
(261, 105, 'purchase', 'registration', '225', NULL, '{\"registration_id\":\"225\",\"selected_accounts\":[1],\"total_price\":\"270000\",\"package\":\"Gói 3 Tháng\",\"location\":\"Cà Mau\"}', 'Mua gói dịch vụ: Gói 3 Tháng - Số lượng: 1', 0, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/136.0.0.0 Safari/537.36', '2025-05-16 20:48:11'),
(262, 105, 'renewal_request', 'registration', '226', NULL, '{\"registration_id\":\"226\",\"selected_accounts\":[\"2248\"],\"total_price\":270000,\"package\":\"Gói 3 Tháng\",\"location\":\"Cà Mau\"}', 'Yêu cầu gia hạn gói dịch vụ cho đăng ký #226 - Gói: Gói 3 Tháng', 0, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/136.0.0.0 Safari/537.36', '2025-05-16 21:16:51'),
(263, 90, 'withdrawal_request', 'withdrawal_request', '12', NULL, '{\"amount\":\"100000\",\"bank_name\":\"MB\",\"account_number\":\"088888888\",\"account_holder\":\"TRAN HAI\"}', 'Yêu cầu rút tiền: 100.000 VND về ngân hàng MB (088888888)', 0, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/136.0.0.0 Safari/537.36', '2025-05-16 21:33:43'),
(278, NULL, 'approve_transaction', 'transaction', '218', '{\"status\":\"pending\"}', '{\"status\":\"completed\",\"created_accounts\":[],\"customer_id\":105}', NULL, 0, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/136.0.0.0 Safari/537.36', '2025-05-17 08:27:46'),
(279, NULL, 'approve_transaction', 'transaction', '219', '{\"status\":\"pending\"}', '{\"status\":\"completed\",\"scheduled_accounts\":[],\"renewed_accounts\":[],\"customer_id\":105}', NULL, 0, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/136.0.0.0 Safari/537.36', '2025-05-17 08:29:56'),
(280, NULL, 'revert_transaction', 'transaction', '218', '{\"status\":\"active\"}', '{\"status\":\"pending\",\"customer_id\":105}', NULL, 0, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/136.0.0.0 Safari/537.36', '2025-05-17 08:34:19'),
(282, NULL, 'reject_invoice', 'invoice', '8', '{\"status\":\"pending\"}', '{\"status\":\"rejected\",\"reason\":\"c\",\"customer_id\":null}', NULL, 0, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/136.0.0.0 Safari/537.36', '2025-05-17 08:55:40'),
(284, NULL, 'invoice_reverted', 'invoice', '8', '{\"status\":\"rejected\"}', '{\"status\":\"pending\",\"customer_id\":null}', 'Invoice #8 reverted to pending status.', 0, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/136.0.0.0 Safari/537.36', '2025-05-17 09:09:15'),
(285, NULL, 'revert_transaction', 'transaction', '218', '{\"status\":\"active\"}', '{\"status\":\"pending\",\"customer_id\":105}', 'Transaction #218 reverted to pending.', 0, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/136.0.0.0 Safari/537.36', '2025-05-17 09:16:22'),
(286, NULL, 'approve_transaction', 'transaction', '218', '{\"status\":\"pending\"}', '{\"status\":\"completed\",\"created_accounts\":[],\"customer_id\":105}', 'Giao dịch #218 (Tạo mới tài khoản) đã được duyệt. Số tài khoản sẽ tạo: 1', 0, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/136.0.0.0 Safari/537.36', '2025-05-17 09:18:28'),
(287, NULL, 'reject_transaction', 'transaction', '219', '{\"status\":\"completed\"}', '{\"status\":\"failed\",\"reason\":\"c\",\"customer_id\":105}', 'Giao dịch #219 đã bị từ chối. Lý do: c', 0, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/136.0.0.0 Safari/537.36', '2025-05-17 09:18:46'),
(288, NULL, 'reject_invoice', 'invoice', '8', '{\"status\":\"pending\"}', '{\"status\":\"rejected\",\"reason\":\"c\",\"customer_id\":null}', 'Yêu cầu xuất hóa đơn #8 đã bị từ chối. Lý do: c', 0, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/136.0.0.0 Safari/537.36', '2025-05-17 09:26:42'),
(289, 105, 'revert_transaction', 'transaction', '218', '{\"status\":\"active\"}', '{\"status\":\"pending\",\"customer_id\":105}', 'Giao dịch #218 đã được hoàn lại về trạng thái chờ xử lý.', 0, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/136.0.0.0 Safari/537.36', '2025-05-17 09:28:21'),
(290, 90, 'invoice_reverted', 'invoice', '8', '{\"status\":\"rejected\"}', '{\"status\":\"pending\",\"customer_id\":90}', 'Hóa đơn #8 đang gặp vấn đề và cần chỉnh sửa lại.', 0, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/136.0.0.0 Safari/537.36', '2025-05-17 09:31:44'),
(291, 105, 'reject_invoice', 'invoice', '9', '{\"status\":\"pending\"}', '{\"status\":\"rejected\",\"reason\":\"c\",\"customer_id\":105}', 'Yêu cầu xuất hoá đơn #9 đã bị từ chối. Lý do: c', 0, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/136.0.0.0 Safari/537.36', '2025-05-17 09:31:55'),
(292, 105, 'invoice_reverted', 'invoice', '9', '{\"status\":\"rejected\"}', '{\"status\":\"pending\",\"customer_id\":105}', 'Hóa đơn #9 đang gặp vấn đề và cần chỉnh sửa lại.', 0, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/136.0.0.0 Safari/537.36', '2025-05-17 09:34:34'),
(293, 105, 'invoice_sent', 'invoice', '9', '{\"status\":\"pending\"}', '{\"status\":\"approved\",\"file\":\"1747449330_test.pdf\",\"customer_id\":105}', 'Hóa đơn #9 đã được gửi.', 0, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/136.0.0.0 Safari/537.36', '2025-05-17 09:35:30'),
(294, 90, 'support_request_updated', 'support_request', '4', '{\"status\":\"pending\",\"admin_response\":null}', '{\"status\":\"resolved\",\"admin_response\":\"Ok em\"}', 'Yêu cầu hỗ trợ #4 đã được cập nhật trạng thái thành resolved.', 0, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/136.0.0.0 Safari/537.36', '2025-05-17 09:39:43'),
(295, 93, 'withdrawal_rejected', 'withdrawal_request', '6', '{\"status\":\"pending\"}', '{\"status\":\"rejected\"}', 'Yêu cầu rút tiền #6 đã bị từ chối.', 0, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/136.0.0.0 Safari/537.36', '2025-05-17 09:40:45'),
(296, 93, 'withdrawal_rejected', 'withdrawal_request', '3', '{\"status\":\"pending\"}', '{\"status\":\"rejected\"}', 'Yêu cầu rút tiền #3 đã bị từ chối.', 0, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/136.0.0.0 Safari/537.36', '2025-05-17 16:29:01'),
(297, 105, 'invoice_reverted', 'invoice', '9', '{\"status\":\"approved\"}', '{\"status\":\"pending\",\"customer_id\":105}', 'Hóa đơn #9 đang gặp vấn đề và cần chỉnh sửa lại.', 0, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/136.0.0.0 Safari/537.36', '2025-05-17 16:29:33'),
(298, 90, 'reject_invoice', 'invoice', '8', '{\"status\":\"pending\"}', '{\"status\":\"rejected\",\"reason\":\"c\",\"customer_id\":90}', 'Yêu cầu xuất hoá đơn #8 đã bị từ chối. Lý do: c', 0, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/136.0.0.0 Safari/537.36', '2025-05-17 16:29:38'),
(299, 105, 'invoice_reverted', 'invoice', '9', '{\"status\":\"pending\"}', '{\"status\":\"pending\",\"customer_id\":105}', 'Hóa đơn #9 đang gặp vấn đề và cần chỉnh sửa lại.', 0, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/136.0.0.0 Safari/537.36', '2025-05-17 16:29:55'),
(300, 92, 'withdrawal_rejected', 'withdrawal_request', '1', '{\"status\":\"pending\"}', '{\"status\":\"rejected\"}', 'Yêu cầu rút tiền #1 đã bị từ chối.', 0, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/136.0.0.0 Safari/537.36', '2025-05-17 16:37:03'),
(301, 90, 'withdrawal_approved', 'withdrawal_request', '12', '{\"status\":\"pending\"}', '{\"status\":\"completed\"}', 'Yêu cầu rút tiền #12 đã được duyệt.', 0, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/136.0.0.0 Safari/537.36', '2025-05-17 16:37:12'),
(302, 93, 'withdrawal_rejected', 'withdrawal_request', '10', '{\"status\":\"pending\"}', '{\"status\":\"rejected\"}', 'Yêu cầu rút tiền #10 đã bị từ chối.', 0, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/136.0.0.0 Safari/537.36', '2025-05-17 16:40:49'),
(303, 93, 'withdrawal_rejected', 'withdrawal_request', '8', '{\"status\":\"pending\"}', '{\"status\":\"rejected\"}', 'Yêu cầu rút tiền #8 đã bị từ chối.', 0, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/136.0.0.0 Safari/537.36', '2025-05-17 16:46:18'),
(304, 93, 'withdrawal_rejected', 'withdrawal_request', '7', '{\"status\":\"pending\"}', '{\"status\":\"rejected\"}', 'Yêu cầu rút tiền #7 đã bị từ chối.', 0, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/136.0.0.0 Safari/537.36', '2025-05-17 16:52:42'),
(305, 93, 'withdrawal_approved', 'withdrawal_request', '5', '{\"status\":\"pending\"}', '{\"status\":\"completed\"}', 'Yêu cầu rút tiền #5 đã được duyệt.', 0, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/136.0.0.0 Safari/537.36', '2025-05-17 16:52:45'),
(306, 90, 'invoice_reverted', 'invoice', '8', '{\"status\":\"rejected\"}', '{\"status\":\"pending\",\"customer_id\":90}', 'Hóa đơn #8 đang gặp vấn đề và cần chỉnh sửa lại.', 0, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/136.0.0.0 Safari/537.36', '2025-05-17 16:53:32'),
(307, 90, 'invoice_reverted', 'invoice', '8', '{\"status\":\"pending\"}', '{\"status\":\"pending\",\"customer_id\":90}', 'Hóa đơn #8 đang gặp vấn đề và cần chỉnh sửa lại.', 0, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/136.0.0.0 Safari/537.36', '2025-05-17 16:53:35'),
(308, 90, 'reject_invoice', 'invoice', '8', '{\"status\":\"pending\"}', '{\"status\":\"rejected\",\"reason\":\"c\",\"customer_id\":90}', 'Yêu cầu xuất hoá đơn #8 đã bị từ chối. Lý do: c', 0, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/136.0.0.0 Safari/537.36', '2025-05-17 16:53:52'),
(309, 90, 'reject_invoice', 'invoice', '8', '{\"status\":\"rejected\"}', '{\"status\":\"rejected\",\"reason\":\"c\",\"customer_id\":90}', 'Yêu cầu xuất hoá đơn #8 đã bị từ chối. Lý do: c', 0, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/136.0.0.0 Safari/537.36', '2025-05-17 16:54:02'),
(310, 90, 'invoice_reverted', 'invoice', '8', '{\"status\":\"rejected\"}', '{\"status\":\"pending\",\"customer_id\":90}', 'Hóa đơn #8 đang gặp vấn đề và cần chỉnh sửa lại.', 0, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/136.0.0.0 Safari/537.36', '2025-05-17 16:59:52'),
(311, 90, 'reject_invoice', 'invoice', '8', '{\"status\":\"pending\"}', '{\"status\":\"rejected\",\"reason\":\"c\",\"customer_id\":90}', 'Yêu cầu xuất hoá đơn #8 đã bị từ chối. Lý do: c', 0, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/136.0.0.0 Safari/537.36', '2025-05-17 16:59:59'),
(312, 90, 'invoice_reverted', 'invoice', '8', '{\"status\":\"rejected\"}', '{\"status\":\"pending\",\"customer_id\":90}', 'Hóa đơn #8 đang gặp vấn đề và cần chỉnh sửa lại.', 0, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/136.0.0.0 Safari/537.36', '2025-05-17 17:00:41'),
(313, 90, 'invoice_sent', 'invoice', '8', '{\"status\":\"pending\"}', '{\"status\":\"approved\",\"file\":\"1747476049_test.pdf\",\"customer_id\":90}', 'Hóa đơn #8 đã được gửi.', 0, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/136.0.0.0 Safari/537.36', '2025-05-17 17:00:49'),
(314, 105, 'revert_transaction', 'transaction', '217', '{\"status\":\"active\"}', '{\"status\":\"pending\",\"customer_id\":105}', 'Giao dịch #217 đã được hoàn lại về trạng thái chờ xử lý.', 0, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/136.0.0.0 Safari/537.36', '2025-05-17 20:11:14'),
(315, 105, 'approve_transaction', 'transaction', '217', '{\"status\":\"pending\"}', '{\"status\":\"completed\",\"created_accounts\":[],\"customer_id\":105}', 'Giao dịch #217 (Tạo mới tài khoản) đã được duyệt. Số tài khoản sẽ tạo: 1', 0, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/136.0.0.0 Safari/537.36', '2025-05-17 20:11:17'),
(316, 93, 'withdrawal_approved', 'withdrawal_request', '3', '{\"status\":\"pending\"}', '{\"status\":\"completed\"}', 'Yêu cầu rút tiền #3 đã được duyệt.', 0, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/136.0.0.0 Safari/537.36', '2025-05-18 06:53:58'),
(317, 90, 'support_request_updated', 'support_request', '3', '{\"status\":\"pending\",\"admin_response\":null}', '{\"status\":\"closed\",\"admin_response\":\"H\\u1ebft nh\\u00e9\"}', 'Yêu cầu hỗ trợ #3 đã được cập nhật trạng thái thành closed.', 0, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/136.0.0.0 Safari/537.36', '2025-05-18 06:56:29'),
(318, 90, 'support_request_updated', 'support_request', '3', '{\"status\":\"closed\",\"admin_response\":\"H\\u1ebft nh\\u00e9\"}', '{\"status\":\"in_progress\",\"admin_response\":\"H\\u1ebft nh\\u00e9\"}', 'Yêu cầu hỗ trợ #3 đã được cập nhật trạng thái thành in_progress.', 0, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/136.0.0.0 Safari/537.36', '2025-05-18 06:56:36'),
(319, 90, 'invoice_reverted', 'invoice', '8', '{\"status\":\"approved\"}', '{\"status\":\"pending\",\"customer_id\":90}', 'Hóa đơn #8 đang gặp vấn đề và cần chỉnh sửa lại.', 0, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/136.0.0.0 Safari/537.36', '2025-05-18 07:12:11'),
(320, 105, 'reject_invoice', 'invoice', '9', '{\"status\":\"pending\"}', '{\"status\":\"rejected\",\"reason\":\"c\",\"customer_id\":105}', 'Yêu cầu xuất hoá đơn #9 đã bị từ chối. Lý do: c', 0, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/136.0.0.0 Safari/537.36', '2025-05-18 07:12:17'),
(321, 90, 'invoice_sent', 'invoice', '8', '{\"status\":\"pending\"}', '{\"status\":\"approved\",\"file\":\"1747527151_test.pdf\",\"customer_id\":90}', 'Hóa đơn #8 đã được gửi.', 0, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/136.0.0.0 Safari/537.36', '2025-05-18 07:12:31'),
(322, 105, 'revert_transaction', 'transaction', '217', '{\"status\":\"active\"}', '{\"status\":\"pending\",\"customer_id\":105}', 'Giao dịch #217 đã được hoàn lại về trạng thái chờ xử lý.', 0, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/136.0.0.0 Safari/537.36', '2025-05-18 07:17:44'),
(323, 105, 'approve_transaction', 'transaction', '218', '{\"status\":\"pending\"}', '{\"status\":\"completed\",\"created_accounts\":[],\"customer_id\":105}', 'Giao dịch #218 (Tạo mới tài khoản) đã được duyệt. Số tài khoản sẽ tạo: 1', 0, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/136.0.0.0 Safari/537.36', '2025-05-18 07:18:49'),
(324, 105, 'approve_transaction', 'transaction', '217', '{\"status\":\"pending\"}', '{\"status\":\"completed\",\"created_accounts\":[],\"customer_id\":105}', 'Giao dịch #217 (Tạo mới tài khoản) đã được duyệt. Số tài khoản sẽ tạo: 1', 0, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/136.0.0.0 Safari/537.36', '2025-05-18 07:18:51'),
(325, 105, 'revert_transaction', 'transaction', '218', '{\"status\":\"active\"}', '{\"status\":\"pending\",\"customer_id\":105}', 'Giao dịch #218 đã được hoàn lại về trạng thái chờ xử lý.', 0, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/136.0.0.0 Safari/537.36', '2025-05-18 07:21:11'),
(326, 105, 'revert_transaction', 'transaction', '217', '{\"status\":\"active\"}', '{\"status\":\"pending\",\"customer_id\":105}', 'Giao dịch #217 đã được hoàn lại về trạng thái chờ xử lý.', 0, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/136.0.0.0 Safari/537.36', '2025-05-18 07:21:11'),
(327, 105, 'approve_transaction', 'transaction', '217', '{\"status\":\"pending\"}', '{\"status\":\"completed\",\"created_accounts\":[],\"customer_id\":105}', 'Giao dịch #217 (Tạo mới tài khoản) đã được duyệt. Số tài khoản sẽ tạo: 1', 0, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/136.0.0.0 Safari/537.36', '2025-05-18 09:38:29'),
(328, 105, 'revert_transaction', 'transaction', '217', '{\"status\":\"active\"}', '{\"status\":\"pending\",\"customer_id\":105}', 'Giao dịch #217 đã được hoàn lại về trạng thái chờ xử lý.', 0, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/136.0.0.0 Safari/537.36', '2025-05-18 09:40:33'),
(351, 90, 'create_support_request', 'support_requests', '17', NULL, NULL, 'Tạo yêu cầu hỗ trợ mới #17', 0, NULL, NULL, '2025-05-19 20:54:24'),
(352, 90, 'create_support_request', 'support_requests', '18', NULL, NULL, 'Tạo yêu cầu hỗ trợ mới #18', 0, NULL, NULL, '2025-05-19 20:57:24'),
(353, 105, 'invoice_reverted', 'invoice', '9', '{\"status\":\"rejected\"}', '{\"status\":\"pending\",\"customer_id\":105}', 'Hóa đơn #9 đang gặp vấn đề và cần chỉnh sửa lại.', 0, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/136.0.0.0 Safari/537.36', '2025-05-19 21:10:55'),
(354, 105, 'reject_invoice', 'invoice', '9', '{\"status\":\"pending\"}', '{\"status\":\"rejected\",\"reason\":\"ch\\u01b0a th\\u1ea5y l\\u1ed7i \\u0111\\u00e2u\",\"customer_id\":105}', 'Yêu cầu xuất hoá đơn #9 đã bị từ chối. Lý do: chưa thấy lỗi đâu', 0, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/136.0.0.0 Safari/537.36', '2025-05-19 21:11:03'),
(355, 90, 'approve_transaction', 'transaction', '220', '{\"status\":\"pending\"}', '{\"status\":\"completed\",\"created_accounts\":[],\"customer_id\":90}', 'Giao dịch #220 (Tạo mới tài khoản) đã được duyệt. Số tài khoản sẽ tạo: 1', 0, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/136.0.0.0 Safari/537.36', '2025-05-19 21:41:05'),
(357, 90, 'account_updated_by_admin', 'account', '2278', NULL, NULL, 'Admin (ID: 6) updated account \'CMA001\' (Account ID: 2278).', 0, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/136.0.0.0 Safari/537.36', '2025-05-19 21:46:04'),
(360, 90, 'account_updated_by_admin', 'account', '2278', NULL, NULL, 'Quản trị viên đã cập nhật tài khoản \'CMA001\' (ID Tài khoản: 2278).', 0, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/136.0.0.0 Safari/537.36', '2025-05-19 21:55:17'),
(361, 90, 'purchase', 'registration', '230', NULL, '{\"registration_id\":\"230\",\"selected_accounts\":[1],\"total_price\":\"270000\",\"package\":\"Gói 3 Tháng\",\"location\":\"Cà Mau\"}', 'Mua gói dịch vụ: Gói 3 Tháng - Số lượng: 1', 0, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/136.0.0.0 Safari/537.36', '2025-05-19 22:18:09'),
(362, 90, 'apply_voucher', 'voucher', '3', NULL, '{\"voucher_code\":\"EXTRAMONTH\",\"discount_value\":0,\"new_amount\":291600,\"original_amount\":291600}', 'Áp dụng mã giảm giá: EXTRAMONTH cho đơn hàng. Giá trị giảm: 0', 0, NULL, NULL, '2025-05-19 22:18:29'),
(364, 90, 'create_support_request', 'support_requests', '27', NULL, NULL, 'Tạo yêu cầu hỗ trợ mới #27', 0, NULL, NULL, '2025-05-20 07:36:40'),
(366, 90, 'create_support_request', 'support_requests', '30', NULL, NULL, 'Tạo yêu cầu hỗ trợ mới #30', 0, NULL, NULL, '2025-05-20 07:52:05'),
(367, 90, 'create_support_request', 'support_requests', '31', NULL, NULL, 'Tạo yêu cầu hỗ trợ mới #31', 0, NULL, NULL, '2025-05-20 09:08:51'),
(368, 105, 'reject_transaction', 'transaction', '217', '{\"status\":\"pending\"}', '{\"status\":\"failed\",\"reason\":\"c\",\"registration_id\":224}', 'Giao dịch #217 đã bị từ chối. Lý do: c', 0, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/136.0.0.0 Safari/537.36', '2025-05-21 09:20:38'),
(369, 105, 'invoice_reverted', 'invoice', '9', '{\"status\":\"rejected\"}', '{\"status\":\"pending\",\"customer_id\":105}', 'Hóa đơn #9 đang gặp vấn đề và cần chỉnh sửa lại.', 0, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/136.0.0.0 Safari/537.36', '2025-05-21 15:17:00'),
(370, 105, 'invoice_sent', 'invoice', '9', '{\"status\":\"pending\"}', '{\"status\":\"approved\",\"file\":\"1747815452_muclucKTMT.pdf\",\"customer_id\":105}', 'Hóa đơn #9 đã được gửi.', 0, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/136.0.0.0 Safari/537.36', '2025-05-21 15:17:32'),
(371, 105, 'invoice_reverted', 'invoice', '9', '{\"status\":\"approved\"}', '{\"status\":\"pending\",\"customer_id\":105}', 'Hóa đơn #9 đang gặp vấn đề và cần chỉnh sửa lại.', 0, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/136.0.0.0 Safari/537.36', '2025-05-21 15:17:45'),
(372, 105, 'invoice_sent', 'invoice', '9', '{\"status\":\"pending\"}', '{\"status\":\"approved\",\"file\":\"1747818212_test.pdf\",\"customer_id\":105}', 'Hóa đơn #9 đã được gửi.', 0, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/136.0.0.0 Safari/537.36', '2025-05-21 16:03:32'),
(373, 105, 'invoice_reverted', 'invoice', '9', '{\"status\":\"approved\"}', '{\"status\":\"pending\",\"customer_id\":105}', 'Hóa đơn #9 đang gặp vấn đề và cần chỉnh sửa lại.', 0, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/136.0.0.0 Safari/537.36', '2025-05-21 16:03:37'),
(374, 105, 'invoice_sent', 'invoice', '9', '{\"status\":\"pending\"}', '{\"status\":\"approved\",\"file\":\"1747818853_test.pdf\",\"customer_id\":105}', 'Hóa đơn #9 đã được gửi.', 0, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/136.0.0.0 Safari/537.36', '2025-05-21 16:14:13'),
(375, 105, 'invoice_reverted', 'invoice', '9', '{\"status\":\"approved\"}', '{\"status\":\"pending\",\"customer_id\":105}', 'Hóa đơn #9 đang gặp vấn đề và cần chỉnh sửa lại.', 0, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/136.0.0.0 Safari/537.36', '2025-05-21 16:14:18'),
(376, 105, 'invoice_sent', 'invoice', '9', '{\"status\":\"pending\"}', '{\"status\":\"approved\",\"file\":\"1747819976_test.pdf\",\"customer_id\":105}', 'Hóa đơn #9 đã được gửi.', 0, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/136.0.0.0 Safari/537.36', '2025-05-21 16:32:56'),
(377, 105, 'invoice_reverted', 'invoice', '9', '{\"status\":\"approved\"}', '{\"status\":\"pending\",\"customer_id\":105}', 'Hóa đơn #9 đang gặp vấn đề và cần chỉnh sửa lại.', 0, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/136.0.0.0 Safari/537.36', '2025-05-21 16:33:02'),
(378, 105, 'invoice_sent', 'invoice', '9', '{\"status\":\"pending\"}', '{\"status\":\"approved\",\"file\":\"1747821140_muclucKTMT.pdf\",\"customer_id\":105}', 'Hóa đơn #9 đã được gửi.', 0, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/136.0.0.0 Safari/537.36', '2025-05-21 16:52:20'),
(379, 105, 'invoice_reverted', 'invoice', '9', '{\"status\":\"approved\"}', '{\"status\":\"pending\",\"customer_id\":105}', 'Hóa đơn #9 đang gặp vấn đề và cần chỉnh sửa lại.', 0, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/136.0.0.0 Safari/537.36', '2025-05-21 16:52:31'),
(380, 105, 'invoice_sent', 'invoice', '9', '{\"status\":\"pending\"}', '{\"status\":\"approved\",\"file\":\"1747821501_test.pdf\",\"customer_id\":105}', 'Hóa đơn #9 đã được gửi.', 0, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/136.0.0.0 Safari/537.36', '2025-05-21 16:58:21'),
(381, 105, 'invoice_reverted', 'invoice', '9', '{\"status\":\"approved\"}', '{\"status\":\"pending\",\"customer_id\":105}', 'Hóa đơn #9 đang gặp vấn đề và cần chỉnh sửa lại.', 0, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/136.0.0.0 Safari/537.36', '2025-05-21 16:59:04'),
(382, 105, 'invoice_sent', 'invoice', '9', '{\"status\":\"pending\"}', '{\"status\":\"approved\",\"file\":\"1747822305_test.pdf\",\"customer_id\":105}', 'Hóa đơn #9 đã được gửi.', 0, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/136.0.0.0 Safari/537.36', '2025-05-21 17:11:45'),
(383, 105, 'invoice_reverted', 'invoice', '9', '{\"status\":\"approved\"}', '{\"status\":\"pending\",\"customer_id\":105}', 'Hóa đơn #9 đang gặp vấn đề và cần chỉnh sửa lại.', 0, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/136.0.0.0 Safari/537.36', '2025-05-21 17:13:02'),
(384, 90, 'invoice_reverted', 'invoice', '8', '{\"status\":\"approved\"}', '{\"status\":\"pending\",\"customer_id\":90}', 'Hóa đơn #8 đang gặp vấn đề và cần chỉnh sửa lại.', 0, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/136.0.0.0 Safari/537.36', '2025-05-21 17:14:24');

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
(6, 'Nguyễn Văn B', 'ad', '$2y$10$e2k7mdTeR8KIMIge/3iCkOKNLBI3b3ENb6t4bvmtrhHhjMVLo.P06', 'admin', '2025-05-11 18:17:41', '2025-05-18 07:12:44'),
(12, 'Là ai', '123', '$2y$10$TeVJyx6Nbaqkw0Opz4e0pOPXI5oS5k3jJdiknG2WeZIittssP/Ty.', 'operator', '2025-05-17 06:17:24', '2025-05-21 18:06:51');

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
('newbie', 'Người mới', '2025-05-17 23:38:17', '2025-05-17 23:38:17'),
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
(17, 'Thử phát nữa', 'thu-phat-nua', '<p>X&oacute;a <strong>s&aacute;ch tất</strong></p>', 6, 'Hướng dẫn', 'draft', '', NULL, 0, '2025-05-18 07:01:17', '2025-05-21 07:10:36', NULL);

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
(8, 213, 'pending', NULL, NULL, '2025-05-12 21:08:48'),
(9, 217, 'pending', NULL, NULL, '2025-05-16 20:38:25');

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
(2, 'Trần Thị B', '0987654322', 'Hải Phòng');

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
(5, 105, '7N6REZX3', '2025-05-16 11:16:06', '2025-05-16 11:16:06');

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
(9, 90, 100, 216, 135000.00, 'approved', '2025-05-14 02:56:06', '2025-05-16 14:33:16'),
(10, 90, 105, 217, 13122.00, 'approved', '2025-05-16 13:37:55', '2025-05-16 13:37:55'),
(11, 90, 105, 219, 13500.00, 'approved', '2025-05-16 14:40:55', '2025-05-16 14:40:55'),
(12, 90, 105, 218, 13500.00, 'approved', '2025-05-17 00:27:50', '2025-05-17 00:27:50');

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
(3, 90, 105, '2025-05-16 11:13:11');

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
(211, 95, 1, 63, NULL, 444, '2025-05-11 11:57:10', '2025-06-11 11:57:10', 100000.00, 0, 0.00, 44400000.00, 'pending', '2025-05-11 11:57:10', '2025-05-11 11:57:10', NULL, NULL),
(212, 95, 1, 18, NULL, 55, '2025-05-11 12:12:14', '2025-06-11 12:12:14', 100000.00, 0, 0.00, 5500000.00, 'pending', '2025-05-11 12:12:14', '2025-05-11 12:12:14', NULL, NULL),
(213, 95, 1, 51, NULL, 34, '2025-05-11 12:25:42', '2025-06-11 12:25:42', 100000.00, 0, 0.00, 3400000.00, 'pending', '2025-05-11 12:25:42', '2025-05-11 12:25:42', NULL, NULL),
(214, 93, 3, 63, NULL, 6, '2025-05-11 14:58:24', '2025-11-11 14:58:24', 500000.00, 0, 0.00, 3000000.00, 'pending', '2025-05-11 14:58:24', '2025-05-11 14:58:24', NULL, NULL),
(215, 95, 4, 63, NULL, 10, '2025-05-11 18:04:41', '2026-05-11 18:04:41', 900000.00, 0, 0.00, 9000000.00, 'pending', '2025-05-11 18:04:41', '2025-05-11 18:04:41', NULL, NULL),
(216, 95, 4, 63, NULL, 100, '2025-05-11 18:08:43', '2026-05-11 18:08:43', 900000.00, 0, 0.00, 90000000.00, 'pending', '2025-05-11 18:08:43', '2025-05-11 18:08:43', NULL, NULL),
(217, 90, 1, 63, NULL, 2, '2025-11-12 00:00:00', '2026-05-12 23:59:59', 270000.00, 0, 0.00, 540000.00, 'rejected', '2025-05-12 13:45:13', '2025-05-17 07:11:13', NULL, 'c'),
(218, 90, 2, 12, NULL, 1, '2025-05-12 00:00:00', '2025-08-12 00:00:00', 0.00, 0, 0.00, 0.00, 'active', '2025-05-12 22:29:45', NULL, NULL, NULL),
(219, 90, 2, 12, NULL, 1, '2025-05-12 00:00:00', '2025-08-12 00:00:00', 0.00, 0, 0.00, 0.00, 'active', '2025-05-12 22:30:17', NULL, NULL, NULL),
(220, 90, 2, 63, NULL, 1, '2025-05-12 22:43:12', '2025-08-12 22:43:12', 270000.00, 0, 0.00, 270000.00, 'rejected', '2025-05-12 22:43:12', '2025-05-17 07:35:29', NULL, 'c'),
(221, 90, 2, 12, NULL, 1, '2025-05-15 00:00:00', '2025-12-15 23:59:59', 270000.00, 0, 0.00, 270000.00, 'rejected', '2025-05-12 22:48:38', '2025-05-17 07:33:21', NULL, 'c'),
(222, 100, 2, 12, NULL, 1, '2025-05-12 22:54:37', '2025-08-12 22:54:37', 270000.00, 0, 0.00, 270000.00, 'rejected', '2025-05-12 22:54:37', '2025-05-17 07:26:29', NULL, 'c'),
(223, 88, 2, 12, NULL, 1, '2025-02-22 00:00:00', '2025-05-22 00:00:00', 0.00, 0, 0.00, 0.00, 'active', '2025-05-14 10:00:56', NULL, NULL, NULL),
(224, 105, 2, 12, NULL, 1, '2025-05-18 00:00:00', '2026-02-19 23:59:59', 270000.00, 0, 0.00, 270000.00, 'rejected', '2025-05-16 18:16:43', '2025-05-21 09:20:38', NULL, 'c'),
(225, 105, 2, 12, NULL, 1, '2025-05-18 00:00:00', '2025-11-18 23:59:59', 270000.00, 0, 0.00, 270000.00, 'pending', '2025-05-16 20:48:11', '2025-05-18 07:21:11', NULL, NULL),
(226, 105, 2, 12, NULL, 1, '2025-08-16 20:37:55', '2025-11-16 20:37:55', 270000.00, 0, 0.00, 270000.00, 'rejected', '2025-05-16 21:16:51', '2025-05-17 09:18:46', NULL, 'c'),
(227, 90, 1, 12, NULL, 2, '2025-05-18 00:00:00', '2025-06-18 00:00:00', 0.00, 0, 0.00, 0.00, 'active', '2025-05-18 07:20:32', '2025-05-18 07:20:50', NULL, NULL),
(228, 90, 1, 63, NULL, 2, '2025-05-18 00:00:00', '2025-06-18 00:00:00', 0.00, 0, 0.00, 0.00, 'active', '2025-05-18 09:39:56', NULL, NULL, NULL),
(229, 90, 1, 12, NULL, 1, '2025-05-19 16:12:51', '2025-09-19 16:12:51', 100000.00, 0, 0.00, 100000.00, 'active', '2025-05-19 16:12:51', '2025-05-19 22:17:06', NULL, NULL),
(230, 90, 2, 12, NULL, 1, '2025-05-19 22:18:09', '2025-08-19 22:18:09', 270000.00, 0, 0.00, 270000.00, 'pending', '2025-05-19 22:18:09', '2025-05-19 22:18:09', NULL, NULL);

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
('admin', 'reports_view', 0),
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
('customercare', 'permission_management_view', 0),
('customercare', 'referral_management_edit', 0),
('customercare', 'referral_management_view', 1),
('customercare', 'reports_view', 0),
('customercare', 'revenue_management_edit', 1),
('customercare', 'revenue_management_view', 1),
('customercare', 'station_management_edit', 1),
('customercare', 'station_management_view', 1),
('customercare', 'support_management_edit', 1),
('customercare', 'support_management_view', 1),
('customercare', 'user_management_edit', 0),
('customercare', 'user_management_view', 0),
('customercare', 'voucher_management_edit', 0),
('customercare', 'voucher_management_view', 1),
('newbie', 'account_management_edit', 0),
('newbie', 'account_management_view', 0),
('newbie', 'guide_management_edit', 0),
('newbie', 'guide_management_view', 0),
('newbie', 'invoice_management_edit', 0),
('newbie', 'invoice_management_view', 0),
('newbie', 'invoice_review_edit', 0),
('newbie', 'invoice_review_view', 0),
('newbie', 'permission_management_edit', 0),
('newbie', 'permission_management_view', 0),
('newbie', 'referral_management_edit', 0),
('newbie', 'referral_management_view', 0),
('newbie', 'reports_view', 1),
('newbie', 'revenue_management_edit', 0),
('newbie', 'revenue_management_view', 0),
('newbie', 'station_management_edit', 0),
('newbie', 'station_management_view', 1),
('newbie', 'support_management_edit', 0),
('newbie', 'support_management_view', 0),
('newbie', 'user_management_edit', 0),
('newbie', 'user_management_view', 0),
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
(25, 90, 'Test3', 'OK', 'technical', 'pending', NULL, '2025-05-20 07:24:04', NULL),
(26, 90, 'Test3', '123', 'technical', 'pending', NULL, '2025-05-20 07:33:45', NULL),
(27, 90, 'SOS', '123', 'technical', 'pending', NULL, '2025-05-20 07:36:40', NULL),
(28, 90, '333', 'Hello', 'technical', 'pending', NULL, '2025-05-20 07:37:37', NULL),
(29, 90, '123', 'SOS', 'technical', 'pending', NULL, '2025-05-20 07:48:21', NULL),
(30, 90, 'Test8', '123', 'technical', 'pending', NULL, '2025-05-20 07:52:05', NULL),
(31, 90, 'Test3', '123', 'technical', 'pending', NULL, '2025-05-20 09:08:51', NULL);

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
(213, 217, 90, 2, 'purchase', 540000.00, 'failed', NULL, 'reg_217_1747032320.jpg', 0, NULL, 0, NULL, NULL, '2025-05-12 13:45:13', '2025-05-17 07:11:13'),
(214, 220, 90, 3, 'purchase', 291600.00, 'failed', NULL, 'reg_220_1747064639.jpg', 0, NULL, 0, NULL, NULL, '2025-05-12 22:43:12', '2025-05-17 07:35:29'),
(215, 221, 90, 3, 'purchase', 291600.00, 'failed', NULL, 'reg_221_1747189086.jpg', 0, NULL, 0, NULL, NULL, '2025-05-12 22:48:38', '2025-05-17 07:33:21'),
(216, 222, 100, NULL, 'purchase', 270000.00, 'failed', NULL, 'reg_222_1747065298.jpg', 0, NULL, 0, NULL, NULL, '2025-05-12 22:54:37', '2025-05-17 07:26:29'),
(217, 224, 105, 4, 'purchase', 262440.00, 'failed', 'Chuyển khoản ngân hàng', 'reg_224_1747394258.jpg', 0, NULL, 0, NULL, NULL, '2025-05-16 18:16:43', '2025-05-21 09:20:38'),
(218, 225, 105, NULL, 'purchase', 270000.00, 'pending', 'Chuyển khoản ngân hàng', NULL, 0, NULL, 0, NULL, NULL, '2025-05-16 20:48:11', '2025-05-18 07:21:11'),
(219, 226, 105, NULL, 'renewal', 270000.00, 'failed', 'Chuyển khoản ngân hàng', 'reg_226_1747405020.jpg', 0, NULL, 0, NULL, NULL, '2025-05-16 21:16:51', '2025-05-17 09:18:46'),
(220, 229, 90, NULL, 'purchase', 100000.00, 'completed', 'Chuyển khoản ngân hàng', 'reg_229_1747645978.jpg', 0, NULL, 0, NULL, NULL, '2025-05-19 16:12:51', '2025-05-19 21:41:05'),
(221, 230, 90, 3, 'purchase', 291600.00, 'pending', 'Chuyển khoản ngân hàng', 'reg_230_1747667917.png', 0, NULL, 0, NULL, NULL, '2025-05-19 22:18:09', '2025-05-19 22:18:37');

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
(105, 'Long99999', 'osn14300@jioso.com', '$2y$10$Ea5zvtkl1GTPskI0eJFMfuEeT4PSFCRSos6L5p8cUBNfM56U94s/G', '09999993232', 1, 'as', '1233333333', '123', NULL, '2025-05-16 18:13:06', '2025-05-17 20:41:03', '2025-05-17 13:41:03', 1, 1, NULL),
(106, 'Long12323', 'tranhailong2499@gmail.com', '$2y$10$PxMSQCg81bG3ohJCZVixM.EkUePHyzjm.e4MElZAofgJLgTtICjLS', '123', 0, NULL, NULL, NULL, NULL, '2025-05-16 21:41:32', '2025-05-18 06:51:58', '2025-05-17 23:51:58', 1, 0, NULL),
(108, 'Một bốn', 'acook6990@gmail.com', '$2y$10$LFiie8I8OrqNc2EPC7yGje8SAn9bTiuaZwal4zes3cSBeCxMF40Ni', '0900000000', 1, 'Công Ty TNHH', '123', 'Thái cực', NULL, '2025-05-18 06:53:13', '2025-05-21 09:17:47', NULL, 1, 0, NULL);

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
('07puqp13og7h24oqkt6djq0udp', 15, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/136.0.0.0 Safari/537.36', '2025-05-21 17:51:07', '2025-05-21 18:00:17', 0),
('0a0n5lfc00kcufmp1s080bc6cp', 6, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/136.0.0.0 Safari/537.36', '2025-05-17 06:17:05', '2025-05-17 06:17:27', 0),
('0cggrapjnbalmld0g9938t7e0o', 6, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/136.0.0.0 Safari/537.36', '2025-05-17 18:47:23', '2025-05-17 20:26:16', 0),
('0scecrfa9hp12g63dq2tojjkc6', 6, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/136.0.0.0 Safari/537.36', '2025-05-21 17:33:12', '2025-05-21 17:33:42', 0),
('0u3gr4m6riq5m443aojja7h8nc', 6, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/136.0.0.0 Safari/537.36', '2025-05-15 18:50:06', '2025-05-15 23:00:59', 0),
('19n7vjm1dbd8ploa0r2sljbccq', 6, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/136.0.0.0 Safari/537.36', '2025-05-17 08:17:31', '2025-05-17 10:25:32', 0),
('1k7e8p3rh88r9umnm6ebsvr7n0', 6, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/136.0.0.0 Safari/537.36', '2025-05-21 07:59:37', '2025-05-21 09:09:36', 0),
('1kblu115mleaaaso2d3s7kbsmc', 6, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/136.0.0.0 Safari/537.36', '2025-05-21 18:01:12', '2025-05-21 18:01:23', 0),
('1nt8jiuaimlnqmhrupdpo6ucpv', 6, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/136.0.0.0 Safari/537.36', '2025-05-12 13:16:38', '2025-05-12 13:28:31', 1),
('20gkmdpfo0iank62fnml1ql3r2', 6, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/136.0.0.0 Safari/537.36', '2025-05-13 14:46:39', '2025-05-13 19:53:57', 1),
('22o8lf3k3sk5brvke3lkll2c0i', 6, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/136.0.0.0 Safari/537.36', '2025-05-16 08:32:20', '2025-05-16 10:08:37', 0),
('2nlfolrchlmr4mh1upd5uqbivp', 6, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/136.0.0.0 Safari/537.36', '2025-05-21 09:09:44', '2025-05-21 09:12:03', 0),
('2vea0vfblfl0l7d1m40q0frem9', 6, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/136.0.0.0 Safari/537.36', '2025-05-14 08:47:07', '2025-05-14 09:23:28', 1),
('307bu0i6tbs6vvs9n2ctqnn1bs', 12, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/136.0.0.0 Safari/537.36', '2025-05-21 18:01:28', '2025-05-21 18:01:32', 0),
('33glkapif1ub631e4iu2tgova1', 6, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/136.0.0.0 Safari/537.36', '2025-05-12 18:31:45', '2025-05-12 20:36:45', 1),
('3hck9gk2759hf6l802lrb4dvmg', 6, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/136.0.0.0 Safari/537.36', '2025-05-16 10:08:48', '2025-05-16 12:42:07', 1),
('48nbopk0pcafpoepvphvh82107', 6, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/136.0.0.0 Safari/537.36', '2025-05-18 09:42:44', '2025-05-18 10:35:43', 1),
('4c2hesergl9j3q8h5m13g9agkf', 6, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/136.0.0.0 Safari/537.36', '2025-05-17 15:55:56', '2025-05-17 17:20:16', 0),
('4ne9odh9lg5rc8mvvtgbeup4fj', 6, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/136.0.0.0 Safari/537.36', '2025-05-14 16:24:24', '2025-05-14 17:57:16', 0),
('4sdaom0dfk79bu1fki4duhg8ts', 6, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/136.0.0.0 Safari/537.36', '2025-05-14 09:46:29', '2025-05-14 11:41:12', 1),
('50uk071h5e0qjoiugbrc0n0b0k', 12, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/136.0.0.0 Safari/537.36', '2025-05-21 17:33:46', '2025-05-21 17:34:03', 0),
('5lnjnrg1b4eqte48cv6krcmpb5', 6, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/136.0.0.0 Safari/537.36', '2025-05-12 15:54:56', '2025-05-12 16:44:12', 1),
('5n3oqmgtocmrr6orqqehr8btrt', 6, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/136.0.0.0 Safari/537.36', '2025-05-16 06:26:34', '2025-05-16 07:49:09', 1),
('6267jlhrk53dv2k5ad96hiumrt', 6, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/136.0.0.0 Safari/537.36', '2025-05-21 09:16:05', '2025-05-21 09:16:24', 0),
('705kv3i4q433sc0c498hv24d2t', 6, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/136.0.0.0 Safari/537.36', '2025-05-19 13:55:50', '2025-05-19 15:29:31', 1),
('74i734hlmpul2r93is77if9kca', 6, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/136.0.0.0 Safari/537.36', '2025-05-15 23:01:31', '2025-05-15 23:22:53', 1),
('7ae1m4kgqeijuavqfsq1h0838d', 6, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/136.0.0.0 Safari/537.36', '2025-05-13 09:36:06', '2025-05-13 12:09:56', 1),
('7bqendcdlgquvkundhoe36ei38', 6, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/136.0.0.0 Safari/537.36', '2025-05-17 20:40:56', '2025-05-17 20:41:06', 0),
('7d02amrpfnb9d83ppnadrrkiov', 6, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/136.0.0.0 Safari/537.36', '2025-05-21 09:21:57', '2025-05-21 09:22:05', 1),
('7is621tk8ie672s8rrc4h7gf5j', 6, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/136.0.0.0 Safari/537.36', '2025-05-13 19:54:01', '2025-05-13 23:41:22', 1),
('802amtsejvub7ra0cg02rpanjb', 6, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/136.0.0.0 Safari/537.36', '2025-05-17 11:03:43', '2025-05-17 11:45:14', 1),
('833eavssu8l3mnvn0h3rtju099', 6, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/136.0.0.0 Safari/537.36', '2025-05-14 15:31:15', '2025-05-14 16:24:20', 0),
('83vbjis4lka52tneei52ism55d', 6, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/136.0.0.0 Safari/537.36', '2025-05-18 07:17:29', '2025-05-18 07:21:20', 0),
('86e9r4iv1e2q7ntafeffsadgce', 6, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/136.0.0.0 Safari/537.36', '2025-05-19 19:06:21', '2025-05-19 23:34:34', 1),
('8ak58mef77r20tqliijnkga13d', 12, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/136.0.0.0 Safari/537.36', '2025-05-18 07:21:24', '2025-05-18 07:22:19', 1),
('8d9rsrdp9samil3il77j3peejs', 6, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/136.0.0.0 Safari/537.36', '2025-05-21 17:34:07', '2025-05-21 17:51:03', 0),
('8ei44jj0jguihnk75g22ln1d79', 6, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/136.0.0.0 Safari/537.36', '2025-05-19 23:16:58', '2025-05-19 23:29:28', 1),
('a5k4nq4mjkp9rmr2kb167bv5mm', 6, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/136.0.0.0 Safari/537.36', '2025-05-12 21:09:16', '2025-05-12 22:37:18', 1),
('as93r4gpjvhochg390igmp2vq2', 6, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/136.0.0.0 Safari/537.36', '2025-05-14 13:40:11', '2025-05-14 15:29:31', 1),
('bcigqr11e1kop9iofi6ma448d7', 6, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/136.0.0.0 Safari/537.36', '2025-05-21 18:01:36', '2025-05-21 18:06:59', 0),
('bf3m2ous2uqkabch00smijl28u', 6, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/136.0.0.0 Safari/537.36', '2025-05-11 22:30:49', '2025-05-11 22:39:04', 1),
('bikhcpe6viic9efqmngpop9772', 6, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/136.0.0.0 Safari/537.36', '2025-05-14 07:49:44', '2025-05-14 08:01:55', 1),
('c4haeknqjojm63nij9bhb9hu9f', 6, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/136.0.0.0 Safari/537.36', '2025-05-16 17:58:39', '2025-05-16 18:22:55', 1),
('c6rffaehpbq1g5fv0vsgcbvfpm', 12, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/136.0.0.0 Safari/537.36', '2025-05-17 17:20:20', '2025-05-17 18:47:19', 0),
('c8bs9dptrksse3r5j685p73krm', 6, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/136.0.0.0 Safari/537.36', '2025-05-11 19:45:11', '2025-05-11 22:30:45', 1),
('clp69irqtpdp4itiv09ae884dn', 6, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/136.0.0.0 Safari/537.36', '2025-05-14 18:16:07', '2025-05-14 23:42:12', 1),
('dcajg4l4runb3v5rcp2v5anc07', 6, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/136.0.0.0 Safari/537.36', '2025-05-17 22:25:45', '2025-05-17 23:18:18', 1),
('dnqeee8lc6tp0k6q8l5qrgm0kh', 6, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/136.0.0.0 Safari/537.36', '2025-05-16 15:05:56', '2025-05-16 16:41:24', 0),
('e1q3onvrn5sfi4qqnjjqm74l2e', 6, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/136.0.0.0 Safari/537.36', '2025-05-17 20:40:24', '2025-05-17 20:40:34', 0),
('e3nh4o5ugpu3hugplckugk7h57', 6, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/136.0.0.0 Safari/537.36', '2025-05-16 20:50:57', '2025-05-16 20:50:57', 1),
('een5lmf382cnn93m4tid39thgm', 6, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/136.0.0.0 Safari/537.36', '2025-05-21 18:00:20', '2025-05-21 18:00:47', 0),
('ekoghfktp7kp9d29npvdtn9bvd', 6, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/136.0.0.0 Safari/537.36', '2025-05-16 16:41:54', '2025-05-16 16:51:42', 0),
('fqk1c2ik14ii4vjcs0irojq17q', 12, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/136.0.0.0 Safari/537.36', '2025-05-17 06:17:37', '2025-05-17 06:25:16', 0),
('gopni5l0c4nt2ov3mtnfkllk3r', 6, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/136.0.0.0 Safari/537.36', '2025-05-21 09:21:39', '2025-05-21 09:21:49', 0),
('gsjv50ksn9e10nbvs46d5m9ech', 12, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/136.0.0.0 Safari/537.36', '2025-05-21 18:07:02', '2025-05-21 18:31:08', 1),
('gtbo10s3i6p8em1tr1if53rbco', 6, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/136.0.0.0 Safari/537.36', '2025-05-19 16:44:29', '2025-05-19 16:49:51', 1),
('gvtdbhaelpo7s317pq9e6g3n7m', 6, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/136.0.0.0 Safari/537.36', '2025-05-21 06:43:21', '2025-05-21 07:32:28', 0),
('hukanq224h9ij6qk5pgb33l6qd', 6, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/136.0.0.0 Safari/537.36', '2025-05-21 09:17:39', '2025-05-21 09:18:05', 0),
('i3uogru3gs240i822to4gg8lrs', 6, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/136.0.0.0 Safari/537.36', '2025-05-14 06:39:28', '2025-05-14 07:45:27', 1),
('io2alhrcvr4j3opbp858irfr3j', 6, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/136.0.0.0 Safari/537.36', '2025-05-15 12:17:42', '2025-05-15 15:40:54', 1),
('iqc1bl9ndmbh3op1pfu1l6v9kq', 6, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/136.0.0.0 Safari/537.36', '2025-05-17 06:11:36', '2025-05-17 06:16:25', 0),
('iqj0f7d1j15cfu0dtecnr2p5ve', 6, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/136.0.0.0 Safari/537.36', '2025-05-11 18:44:00', '2025-05-11 19:45:07', 1),
('j431kq1piqs913tgpjuej5cnn0', 12, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/136.0.0.0 Safari/537.36', '2025-05-17 20:40:45', '2025-05-17 20:40:53', 0),
('jbrpnbjj7lq0s4ct79t2k6e7ua', 10, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/136.0.0.0 Safari/537.36', '2025-05-15 23:01:03', '2025-05-15 23:01:23', 0),
('jmj547av0fe6umhi1ekmqq23v3', 6, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/136.0.0.0 Safari/537.36', '2025-05-12 20:36:52', '2025-05-12 21:05:21', 0),
('jtk15dhtfqb12vaqtik84cn6o9', 6, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/136.0.0.0 Safari/537.36', '2025-05-21 09:18:14', '2025-05-21 09:18:49', 0),
('ki3fbll4apinnf06pckln0kr0m', 6, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/136.0.0.0 Safari/537.36', '2025-05-17 06:27:01', '2025-05-17 07:46:39', 1),
('l3rehqtm17v18rs03tgc02r716', 12, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/136.0.0.0 Safari/537.36', '2025-05-21 18:00:51', '2025-05-21 18:01:08', 0),
('lp0ec2on4as215e4qc6mv79d40', 6, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/136.0.0.0 Safari/537.36', '2025-05-13 06:30:06', '2025-05-13 06:49:46', 1),
('lplplpna8jm08d3p9f88gkle0a', 6, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/136.0.0.0 Safari/537.36', '2025-05-18 08:58:30', '2025-05-18 09:42:34', 0),
('m85dv17c83o7393lnf7e8g5evj', 6, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/136.0.0.0 Safari/537.36', '2025-05-21 07:37:03', '2025-05-21 07:37:09', 0),
('mpeuahoqb9blsqpjiik52fqh8k', 12, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/136.0.0.0 Safari/537.36', '2025-05-17 10:25:35', '2025-05-17 11:03:40', 0),
('mrvao206ckuqpkto7030q398kj', 6, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/136.0.0.0 Safari/537.36', '2025-05-16 16:52:07', '2025-05-16 17:36:41', 1),
('nnhmbedalo8snhg1uptq9k8doj', 6, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/136.0.0.0 Safari/537.36', '2025-05-11 18:21:26', '2025-05-11 18:41:50', 1),
('o7e5tq2118ab2460s881t6obor', 11, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/136.0.0.0 Safari/537.36', '2025-05-16 16:41:28', '2025-05-16 16:41:45', 0),
('ob6cho21421qrr73vd44o70lln', 6, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/136.0.0.0 Safari/537.36', '2025-05-15 07:32:51', '2025-05-15 07:58:28', 1),
('oin3g13u5oi8ebrv6mih46e05h', 12, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/136.0.0.0 Safari/537.36', '2025-05-17 06:25:42', '2025-05-17 06:26:53', 0),
('ott4a58m3fmccbhqint9mt1lkr', 8, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/136.0.0.0 Safari/537.36', '2025-05-14 18:15:41', '2025-05-14 18:16:02', 0),
('peo9husvdmu2umb0m1pq0pi2o9', 6, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/136.0.0.0 Safari/537.36', '2025-05-21 13:38:06', '2025-05-21 15:16:49', 1),
('pk57o11foot20911umd8qph900', 6, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/136.0.0.0 Safari/537.36', '2025-05-17 06:25:21', '2025-05-17 06:25:25', 0),
('q18ngh7ns4ftcv7ange37agu66', 6, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/136.0.0.0 Safari/537.36', '2025-05-12 22:56:18', '2025-05-12 23:33:04', 1),
('q377883ge9u7t8jj8l3u052npl', 6, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/136.0.0.0 Safari/537.36', '2025-05-12 13:45:47', '2025-05-12 15:33:14', 1),
('raauihma6durgfmte65rtb5vrt', 12, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/136.0.0.0 Safari/537.36', '2025-05-17 20:53:45', '2025-05-17 22:25:41', 0),
('rftksimiarmf5epopbe71bv4r9', 6, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/136.0.0.0 Safari/537.36', '2025-05-16 19:22:47', '2025-05-16 23:49:22', 1),
('rmg0gee55hvj46cnt6h11kh956', 6, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/136.0.0.0 Safari/537.36', '2025-05-17 20:44:01', '2025-05-17 20:53:42', 0),
('rrm3jjoovn385ig72omv0cmidi', 11, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/136.0.0.0 Safari/537.36', '2025-05-16 16:51:49', '2025-05-16 16:51:59', 0),
('rtc1iqtjgd5bkkvvh3qfk6a946', 6, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/136.0.0.0 Safari/537.36', '2025-05-21 09:15:18', '2025-05-21 09:15:58', 0),
('sc61q8tndeidctff5m8aeigjok', 6, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/136.0.0.0 Safari/537.36', '2025-05-16 18:03:48', '2025-05-16 18:05:08', 1),
('see9h077q2l30mvu8egp22kv00', 6, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/136.0.0.0 Safari/537.36', '2025-05-13 06:53:21', '2025-05-13 09:36:02', 1),
('si3ssb74q5ptrfdhhlq40tf2jm', 6, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/136.0.0.0 Safari/537.36', '2025-05-21 15:16:53', '2025-05-21 17:33:08', 0),
('t57uarqkc4kd39rfmntu6uggao', 6, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/136.0.0.0 Safari/537.36', '2025-05-17 12:56:47', '2025-05-17 13:56:45', 1),
('t5fki6ssr5mi5tia9dk3l47ase', 6, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/136.0.0.0 Safari/537.36', '2025-05-12 21:05:25', '2025-05-12 21:07:19', 1),
('tku7old1u90dus7jj48k00rgmh', 12, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/136.0.0.0 Safari/537.36', '2025-05-17 20:26:21', '2025-05-17 20:40:20', 0),
('tpagt41j4jeqlrl25fn843i8dh', 6, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/136.0.0.0 Safari/537.36', '2025-05-13 12:46:17', '2025-05-13 13:59:45', 1),
('ub6aublc15g1gh4kucim12pm99', 6, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/136.0.0.0 Safari/537.36', '2025-05-19 13:06:01', '2025-05-19 13:53:46', 1),
('um5anedjherccd7ue14ri8v3ru', 6, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/136.0.0.0 Safari/537.36', '2025-05-12 07:04:20', '2025-05-12 07:56:32', 1),
('unbmphj0kgnud841carmaspqss', 6, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/136.0.0.0 Safari/537.36', '2025-05-11 23:40:02', '2025-05-11 23:40:04', 1),
('usrci1mtv27ogkuf7u7h3bm65i', 6, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/136.0.0.0 Safari/537.36', '2025-05-17 06:16:41', '2025-05-17 06:16:52', 0),
('v4cerim3ds3eok82rv2qndjlub', 12, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/136.0.0.0 Safari/537.36', '2025-05-17 20:41:10', '2025-05-17 20:43:57', 0),
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
(17, 108, 1, 0, 'light', '2025-05-18 06:53:13', NULL);

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
(20, 90, 3, 214, '2025-05-12 22:43:45'),
(21, 90, 3, 215, '2025-05-12 22:48:52'),
(22, 105, 4, 217, '2025-05-16 18:17:29'),
(23, 90, 3, 221, '2025-05-19 22:18:29');

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
(3, 'EXTRAMONTH', 'Tặng thêm 1 tháng khi gia hạn gói dịch vụ', 'extend_duration', 1.00, NULL, NULL, 200, NULL, 4, '2025-05-10 00:00:00', '2025-12-31 23:59:59', 1, '2025-05-10 09:34:47', '2025-05-19 22:18:29'),
(4, 'PERCENT10', 'Giảm 10% tối đa 100.000đ cho đơn hàng từ 200.000đ', 'percentage_discount', 10.00, 100000.00, 200000.00, 120, NULL, 2, '2025-05-01 00:00:00', '2025-07-31 23:59:59', 1, '2025-05-10 10:20:40', '2025-05-16 18:17:29'),
(5, 'FIXED200K', 'Giảm 200.000đ cho đơn hàng từ 1.000.000đ', 'fixed_discount', 200000.00, NULL, 1000000.00, 30, NULL, 1, '2025-05-01 00:00:00', '2025-12-31 23:59:59', 1, '2025-05-10 10:20:40', '2025-05-10 10:57:20'),
(6, 'PERCENT20', 'Giảm 20% tối đa 300.000đ cho đơn hàng từ 800.000đ', 'percentage_discount', 20.00, 300000.00, 800000.00, 40, NULL, 0, '2025-05-01 00:00:00', '2025-08-31 23:59:59', 1, '2025-05-10 10:20:40', '2025-05-10 10:20:40'),
(7, 'FIXED30K', 'Giảm 30.000đ cho đơn hàng từ 150.000đ', 'fixed_discount', 30000.00, NULL, 150000.00, 200, NULL, 3, '2025-05-01 00:00:00', '2025-06-30 23:59:59', 1, '2025-05-10 10:20:40', '2025-05-10 21:27:35'),
(9, 'FIXED25K', 'Giảm 25.000đ cho đơn hàng từ 100.000đ', 'fixed_discount', 25000.00, NULL, 100000.00, 250, NULL, 3, '2025-05-01 00:00:00', '2025-07-31 23:59:59', 1, '2025-05-10 10:20:40', '2025-05-10 11:40:02'),
(10, 'PERCENT12', 'Giảm 12% tối đa 120.000đ cho đơn hàng từ 400.000đ', 'percentage_discount', 12.00, 120000.00, 400000.00, 60, NULL, 0, '2025-05-01 00:00:00', '2025-09-30 23:59:59', 1, '2025-05-10 10:20:40', '2025-05-10 10:20:40'),
(11, 'FIXED80K', 'Giảm 80.000đ cho đơn hàng từ 250.000đ', 'fixed_discount', 80000.00, NULL, 250000.00, 70, NULL, 0, '2025-05-01 00:00:00', '2025-08-31 23:59:59', 1, '2025-05-10 10:20:40', '2025-05-10 10:20:40'),
(12, 'PERCENT18', 'Giảm 18% tối đa 180.000đ cho mọi đơn hàng', 'percentage_discount', 18.00, 180000.00, NULL, 80, NULL, 0, '2025-05-01 00:00:00', '2025-07-31 23:59:59', 1, '2025-05-10 10:20:40', '2025-05-10 10:20:40'),
(13, 'WELCOME50', 'Giảm 50.000đ cho đơn hàng từ 200.000đ, mỗi tài khoản chỉ dùng 1 lần', 'fixed_discount', 50000.00, 0.00, 2000000.00, 100, 1, 0, '2025-05-01 00:00:00', '2025-12-31 00:00:00', 1, '2025-05-10 10:52:34', '2025-05-18 06:14:01'),
(14, 'VIP20', 'Giảm 20% tối đa 500.000đ, mỗi tài khoản dùng tối đa 3 lần', 'percentage_discount', 20.00, 500000.00, 0.00, 200, 3, 0, '2025-05-01 00:00:00', '2025-12-31 00:00:00', 1, '2025-05-10 10:52:34', '2025-05-14 22:51:28');

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
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=385;

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
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

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
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `referral_commission`
--
ALTER TABLE `referral_commission`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT for table `referred_user`
--
ALTER TABLE `referred_user`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `registration`
--
ALTER TABLE `registration`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=231;

--
-- AUTO_INCREMENT for table `support_requests`
--
ALTER TABLE `support_requests`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=32;

--
-- AUTO_INCREMENT for table `transaction_history`
--
ALTER TABLE `transaction_history`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=222;

--
-- AUTO_INCREMENT for table `user`
--
ALTER TABLE `user`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=109;

--
-- AUTO_INCREMENT for table `user_settings`
--
ALTER TABLE `user_settings`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=18;

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
