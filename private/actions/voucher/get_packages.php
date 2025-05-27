<?php
// filepath: private/actions/voucher/get_packages.php
require_once dirname(__DIR__, 2) . '/config/constants.php';
require_once BASE_PATH . '/classes/Database.php';
require_once BASE_PATH . '/utils/functions.php';

try {
    $db = Database::getInstance()->getConnection();
    $stmt = $db->query("SELECT id, name FROM package WHERE is_active = 1 ORDER BY display_order ASC, name ASC");
    $packages = $stmt->fetchAll(PDO::FETCH_ASSOC);
    api_success($packages);
} catch (Exception $e) {
    api_error('Lỗi khi lấy danh sách gói: ' . $e->getMessage());
}
