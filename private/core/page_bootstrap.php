<?php
require_once __DIR__ . '/../config/constants.php';
require_once ERROR_HANDLER_PATH;

// Autoload external libraries via Composer
require_once BASE_PATH . '/../vendor/autoload.php';

// filepath: private/core/page_bootstrap.php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
    // Session fixation protection: regenerate ID on first use or every 5 minutes
    if (!isset($_SESSION['created'])) {
        $_SESSION['created'] = time();
    } elseif (time() - $_SESSION['created'] > 300) {
        session_regenerate_id(true);
        $_SESSION['created'] = time();
    }
}

// --- Load config, DB and helpers for session validation ---
require_once BASE_PATH . '/config/database.php';
require_once BASE_PATH . '/classes/Database.php';
require_once BASE_PATH . '/utils/functions.php';

// --- Enforce multi-device session validity ---
if (isset($_SESSION['admin_id'])) {
    validateSession($_SESSION['admin_id'], session_id());
}

// --- Include Core Files ---
// Add core classes and API
require_once BASE_PATH . '/classes/AccountModel.php';
require_once BASE_PATH . '/api/rtk_system/account_api.php';
require_once BASE_PATH . '/classes/GuideModel.php';

// Enforce session idle timeout for security
if (isset($_SESSION['last_activity']) 
    && (time() - $_SESSION['last_activity']) > SESSION_TIMEOUT) {
    session_unset();
    session_destroy();
    header('Location: ' . BASE_URL . 'public/pages/auth/admin_login.php');
    exit;
}
// Update last activity timestamp
$_SESSION['last_activity'] = time();

// --- Base Path Calculation ---
// Replace URL base with filesystem base for layouts
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
        abort('Hệ thống đang tạm ngưng. Vui lòng thử lại sau.', 500);
    }
} catch (\Exception $e) {
    error_log("Database Error (Bootstrap): " . $e->getMessage());
    abort('Hệ thống đang bảo trì cơ sở dữ liệu. Vui lòng thử lại sau.', 500);
}

// --- User Display Name ---
// Ensure session variables are checked safely
$user_display_name = htmlspecialchars($_SESSION['admin_username'] ?? $_SESSION['admin_name'] ?? 'Admin');
$admin_role = $_SESSION['admin_role'] ?? '';
// --- Define Private Includes & Actions Path ---
$private_layouts_path = PRIVATE_LAYOUTS_PATH;
$private_actions_path  = PRIVATE_ACTIONS_PATH;

// Return values needed by the calling page
$final_bootstrap_data = [
    'db'                     => $db,
    'base_path'              => $base_path,
    'base_url'               => $base_url,
    'user_display_name'      => $user_display_name,
    'private_layouts_path'   => $private_layouts_path,
    'private_actions_path'   => $private_actions_path,
    'admin_role'             => $admin_role
];

// Make this data globally accessible for subsequent require_once calls
$GLOBALS['__PAGE_BOOTSTRAP_INSTANCE_DATA__'] = $final_bootstrap_data;

return $final_bootstrap_data;

?>