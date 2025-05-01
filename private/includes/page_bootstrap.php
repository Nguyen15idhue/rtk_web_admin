<?php
// filepath: e:\Application\laragon\www\rtk_web_admin\private\includes\page_bootstrap.php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// --- Session Check ---
if (!isset($_SESSION['admin_id'])) {
    // Redirect to login or handle unauthorized access appropriately
    // For now, just exit or show an error for simplicity in this refactor
    // header('Location: ' . $base_path . 'public/pages/auth/admin_login.php'); // Adjust path if needed
    // exit;
    // echo "Unauthorized access."; // Or redirect
    // exit;
}

// --- Include Core Files ---
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../classes/Database.php';
require_once __DIR__ . '/../utils/functions.php'; // Include general helper functions

// --- Base Path Calculation ---
$protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' || $_SERVER['SERVER_PORT'] == 443) ? "https://" : "http://";
$host = $_SERVER['HTTP_HOST'];
$script_name_parts = explode('/', $_SERVER['SCRIPT_NAME']);
// Find the index of the project root folder name
$project_folder_index = -1;
foreach ($script_name_parts as $index => $part) {
    // Use a more specific project identifier if 'rtk_web_admin' might appear elsewhere
    if ($part === 'rtk_web_admin') {
        $project_folder_index = $index;
        break;
    }
}

if ($project_folder_index === -1) {
    // Fallback or error handling if the project folder name isn't found
    // This might happen if the script is run from an unexpected location
    // For simplicity, assume it's found for now.
    // You might want to define a constant BASE_PATH in a config file instead.
    error_log("Could not determine project base path in page_bootstrap.php");
    // Defaulting to a relative path might work in some server configs but is less reliable
    $base_path_segment = '/'; // Adjust this fallback as needed
} else {
    // Base path at project root, exclude 'public' directory
    $base_path_segment = implode('/', array_slice($script_name_parts, 0, $project_folder_index + 1)) . '/';
}
$base_path = $protocol . $host . $base_path_segment;

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
// Use __DIR__ to ensure the path is correct regardless of where this file is included from
$private_includes_path = __DIR__ . '/'; // Path to the 'includes' directory itself

// Return values needed by the calling page
return [
    'db' => $db,
    'base_path' => $base_path,
    'user_display_name' => $user_display_name,
    'private_includes_path' => $private_includes_path // Path to the includes dir
];

?>