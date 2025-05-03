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

// --- Database Connection ---
$database = Database::getInstance();
$db = $database->getConnection();

if (!$db) {
    error_log("Failed to connect to database in page_bootstrap.php");
    // Display a user-friendly error message or redirect
    die("Database connection failed. Please check logs or contact support.");
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