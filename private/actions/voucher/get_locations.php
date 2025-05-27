<?php
// filepath: private/actions/voucher/get_locations.php
require_once dirname(__DIR__, 2) . '/config/constants.php';
require_once BASE_PATH . '/classes/Database.php';
require_once BASE_PATH . '/utils/functions.php';

try {
    $db = Database::getInstance()->getConnection();
    $stmt = $db->query("SELECT id, province as name FROM location ORDER BY province ASC"); // Removed WHERE status = 1
    $locations = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Debug log
    error_log('Locations fetched: ' . count($locations) . ' records');
    
    api_success($locations);
} catch (Exception $e) {
    error_log('Error in get_locations: ' . $e->getMessage());
    api_error('Lỗi khi lấy danh sách tỉnh thành: ' . $e->getMessage());
}
