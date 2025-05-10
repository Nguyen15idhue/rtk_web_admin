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

// New: session idle timeout in seconds (e.g. 1800 s = 30 min)
define('SESSION_TIMEOUT', 1800);
define('USER_SESSIONS_TABLE', 'user_sessions');

// Add full path to error handler
define('ERROR_HANDLER_PATH', PRIVATE_CORE_PATH . 'error_handler.php');
// Add a constant for the logs directory
define('LOGS_PATH', BASE_PATH . '/logs');

// Thư mục chung cho tất cả uploads
define('UPLOADS_PATH', BASE_PATH . '/../public/uploads/');

// API Keys for external services
define('API_ACCESS_KEY', 'Zb5F6iKUuAISy4qY');
define('API_SECRET_KEY', 'KL1KEEJj2s6HA8LB');