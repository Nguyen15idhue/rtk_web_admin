<?php
// Path and directory constants

define('BASE_PATH', dirname(__DIR__));

define('BASE_URL', (isset($_SERVER['HTTPS']) ? 'https' : 'http') . '://' . $_SERVER['HTTP_HOST'] . '/');

// Paths for private layouts, core, actions, and classes
define('PRIVATE_LAYOUTS_PATH', BASE_PATH . '/layouts/');
define('PRIVATE_CORE_PATH', BASE_PATH . '/core/');
define('PRIVATE_ACTIONS_PATH', BASE_PATH . '/actions/');
define('PRIVATE_CLASSES_PATH', BASE_PATH . '/classes/');

// Paths for error handler and logs
define('ERROR_HANDLER_PATH', PRIVATE_CORE_PATH . 'error_handler.php');
define('LOGS_PATH', BASE_PATH . '/logs');

// Uploads and image hosting
define('UPLOADS_PATH', BASE_PATH . '/../public/uploads/');
// Base URL for hosted images
define('IMAGE_HOST_BASE_URL', 'http://localhost:8000/');
