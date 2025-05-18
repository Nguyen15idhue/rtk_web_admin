<?php
require_once __DIR__ . '/../../config/constants.php';
// require_once BASE_PATH . '/classes/Database.php'; // Removed, handled by AdminModel
require_once BASE_PATH . '/utils/functions.php';    // thêm utils/functions
require_once __DIR__ . '/../../core/error_handler.php';
require_once __DIR__ . '/../../classes/Auth.php';
require_once BASE_PATH . '/classes/AdminModel.php'; // Add AdminModel
require_once BASE_PATH . '/classes/Database.php';

Auth::ensureAuthenticated();

$adminModel = new AdminModel();

try {
    $profile = $adminModel->getProfileById($_SESSION['admin_id']);

    if ($profile === false) { // Check if model method failed (e.g. DB connection)
        api_error('Error fetching profile data.', 500);
    } elseif (empty($profile)) {
        api_success([], 'Profile not found.', 404); // Or handle as an error
    } else {
        // Map role key to display name from custom_roles table
        $dbInstance = Database::getInstance();
        $pdo = $dbInstance->getConnection();
        $stmtRole = $pdo->prepare("SELECT role_display_name FROM custom_roles WHERE role_key = :role_key LIMIT 1");
        $stmtRole->bindParam(':role_key', $profile['role'], PDO::PARAM_STR);
        $stmtRole->execute();
        $displayName = $stmtRole->fetchColumn();
        if ($displayName) {
            $profile['role'] = $displayName;
        } else {
            $profile['role'] = ucfirst($profile['role']);
        }
        api_success($profile, '', 200);
    }
} catch (Exception $e) { // Catch generic exceptions as well
    error_log("Error fetching profile: " . $e->getMessage());
    api_error('An unexpected error occurred while fetching the profile.', 500);
}
?>
