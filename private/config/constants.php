<?php
// Secure session settings: only adjust when session not started
if (session_status() === PHP_SESSION_NONE) {
    ini_set('session.use_strict_mode', 1);
    ini_set('session.cookie_httponly', 1);
    ini_set('session.cookie_secure', isset($_SERVER['HTTPS']) ? 1 : 0);

    // Start session if not already started
    session_start();
}

define('BASE_PATH', dirname(__DIR__));                      
define('BASE_URL',  (isset($_SERVER['HTTPS'])?'https':'http')
                  .'://'.$_SERVER['HTTP_HOST'].'/');

// Thêm hằng số cho private layouts/actions
define('PRIVATE_LAYOUTS_PATH', BASE_PATH . '/layouts/');
define('PRIVATE_CORE_PATH', BASE_PATH . '/core/');
define('PRIVATE_ACTIONS_PATH', BASE_PATH . '/actions/');
define('PRIVATE_CLASSES_PATH', BASE_PATH . '/classes/');

// New: session idle timeout in seconds (e.g. 3600 s = 1 hour)
define('SESSION_TIMEOUT', 3600);
define('USER_SESSIONS_TABLE', 'user_sessions');

// Add full path to error handler
define('ERROR_HANDLER_PATH', PRIVATE_CORE_PATH . 'error_handler.php');
// Add a constant for the logs directory
define('LOGS_PATH', BASE_PATH . '/logs');

// Thư mục chung cho tất cả uploads
define('UPLOADS_PATH', BASE_PATH . '/../public/uploads/');

// New: base URL for hosted images
define('IMAGE_HOST_BASE_URL', 'http://localhost:8000/');

// API Keys for external services
define('API_ACCESS_KEY', 'Zb5F6iKUuAISy4qY');
define('API_SECRET_KEY', 'KL1KEEJj2s6HA8LB');

// Pagination
define('DEFAULT_ITEMS_PER_PAGE', 1);