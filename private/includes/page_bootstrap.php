<?php
// filepath: e:\Application\laragon\www\rtk_web_admin\private\includes\page_bootstrap.php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// --- Include Core Files ---
require_once __DIR__ . '/../utils/functions.php'; // Include general helper functions
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../classes/Database.php';

// --- Session Check ---
if (!isset($_SESSION['admin_id'])) {
    // Redirect to login logic will be fixed below once we have base_path
}

// --- Calculate Base Path Using Standardized Function ---
$base_path = get_base_path();

// Now that we have base_path, we can properly handle unauthorized access
if (!isset($_SESSION['admin_id'])) {
    // Redirect to login page
    header('Location: ' . $base_path . 'public/pages/auth/admin_login.php');
    exit;
}

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

// --- Define Private Includes Path ---
// Use the standardized path function
$private_includes_path = get_private_path() . 'includes/';

// Return values needed by the calling page
return [
    'db' => $db,
    'base_path' => $base_path,
    'user_display_name' => $user_display_name,
    'private_includes_path' => $private_includes_path // Path to the includes dir
];

?>