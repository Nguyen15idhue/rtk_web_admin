<?php
require_once __DIR__ . '/../../config/constants.php';
require_once BASE_PATH . '/utils/functions.php'; 
require_once __DIR__ . '/../../classes/Auth.php';
require_once BASE_PATH . '/classes/AdminModel.php';

header('Content-Type: application/json');

// Check if admin is logged in
Auth::ensureAuthenticated();

// Check if it's a POST request
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    api_error('Invalid request method.', 405);
}

// Get input data
$input = json_decode(file_get_contents('php://input'), true);
$admin_id = $_SESSION['admin_id'];
$current_password = $input['current_password'] ?? null;
$new_password = $input['new_password'] ?? null;
$confirm_password = $input['confirm_password'] ?? null;

// Basic validation
if (empty($current_password) || empty($new_password) || empty($confirm_password)) {
    api_error('All password fields are required.', 400);
}

if (strlen($new_password) < 6) {
    api_error('New password must be at least 6 characters long.', 400);
}

if ($new_password !== $confirm_password) {
    api_error('New password and confirmation password do not match.', 400);
}

$adminModel = new AdminModel();

try {
    // 1. Fetch current password hash
    $current_password_hash_from_db = $adminModel->getPasswordHashById($admin_id);

    if ($current_password_hash_from_db === null) {
        api_error('Admin user not found or error fetching data.', 404);
    }
    if ($current_password_hash_from_db === false) {
        api_error('Database error retrieving current password.', 500);
    }

    // 2. Verify current password
    if (!password_verify($current_password, $current_password_hash_from_db)) {
        api_error('Incorrect current password.', 400);
    }

    // 3. Hash the new password
    $new_password_hash = password_hash($new_password, PASSWORD_DEFAULT);
    if ($new_password_hash === false) {
         error_log("Password hashing failed for admin ID: " . $admin_id);
         api_error('Error processing new password.', 500);
    }

    // 4. Update the password in the database
    if ($adminModel->updatePassword($admin_id, $new_password_hash)) {
        api_success(null, 'Password changed successfully.');
    } else {
        api_error('Failed to change password.', 500);
    }

} catch (Exception $e) {
    error_log("Error changing admin password: " . $e->getMessage());
    error_log("Stack trace: " . $e->getTraceAsString());
    api_error('An error occurred while changing the password.', 500);
}
?>
