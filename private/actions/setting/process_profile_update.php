<?php
require_once __DIR__ . '/../../config/constants.php';        // Load BASE_PATH
require_once BASE_PATH . '/utils/functions.php'; // For api_error/api_success
require_once __DIR__ . '/../../core/error_handler.php'; // thêm
require_once __DIR__ . '/../../classes/Auth.php';
require_once BASE_PATH . '/classes/AdminModel.php'; // Add AdminModel

header('Content-Type: application/json');

// Check if admin is logged in
Auth::ensureAuthenticated();

// Check if it's a POST request
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    api_error('Invalid request method.', 405);
    exit;
}

// Get input data
$input = json_decode(file_get_contents('php://input'), true);
$admin_id = $_SESSION['admin_id'];
$name = $input['name'] ?? null;

// Basic validation
if (empty($name)) {
    api_error('Name cannot be empty.', 400);
    exit;
}

$adminModel = new AdminModel();

try {
    if ($adminModel->updateProfile($admin_id, $name)) {
        api_success(null, 'Profile updated successfully.');
    } else {
        api_error('Failed to update profile.', 500);
    }
} catch (Exception $e) { // Catch generic exceptions as well
    error_log("Error updating admin profile: " . $e->getMessage());
    error_log("Stack trace: " . $e->getTraceAsString());
    api_error('An error occurred while updating the profile.', 500);
}
?>
