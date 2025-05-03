<?php
// filepath: e:\Application\laragon\www\rtk_web_admin\private\includes\page_bootstrap.php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// --- Include Core Files ---
// Include constants for base paths
require_once __DIR__ . '/../config/constants.php';
// Replace individual includes to use BASE_PATH
require_once BASE_PATH . '/config/database.php';
require_once BASE_PATH . '/classes/Database.php';
require_once BASE_PATH . '/utils/functions.php';
// Add core classes and API
require_once BASE_PATH . '/classes/AccountModel.php';
require_once BASE_PATH . '/api/rtk_system/account_api.php';
require_once BASE_PATH . '/classes/GuideModel.php';

// --- Base Path Calculation ---
// Replace URL base with filesystem base for includes
$base_path = BASE_PATH;
$base_url = BASE_URL;

// --- Database Connection (synchronous exception handling) ---
try {
    $database = Database::getInstance();
    $db = $database->getConnection();
    // <-- Kiểm tra lại kết nối lần cuối trước khi tiếp tục
    try {
        $db->query('SELECT 1');
    } catch (\Exception $e) {
        error_log("[Bootstrap] Database ping failed: " . $e->getMessage());
        http_response_code(500);
        echo "Hệ thống đang tạm ngưng. Vui lòng thử lại sau.";
        exit;
    }
} catch (\Exception $e) {
    error_log("Database Error (Bootstrap): " . $e->getMessage());
    http_response_code(500);
    echo "Hệ thống đang bảo trì cơ sở dữ liệu. Vui lòng thử lại sau.";
    exit;
}

// --- User Display Name ---
// Ensure session variables are checked safely
$user_display_name = htmlspecialchars($_SESSION['admin_username'] ?? $_SESSION['admin_name'] ?? 'Admin');

// --- Define Private Includes & Actions Path ---
$private_includes_path = PRIVATE_INCLUDES_PATH;
$private_actions_path  = PRIVATE_ACTIONS_PATH;

// Return values needed by the calling page
return [
    'db'                     => $db,
    'base_path'              => $base_path,
    'base_url'               => $base_url,
    'user_display_name'      => $user_display_name,
    'private_includes_path'  => $private_includes_path,
    'private_actions_path'   => $private_actions_path
];

?>