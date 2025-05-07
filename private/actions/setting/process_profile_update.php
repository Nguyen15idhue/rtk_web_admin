<?php
require_once __DIR__ . '/../../config/constants.php';        // Load BASE_PATH
require_once BASE_PATH . '/classes/Database.php';
require_once BASE_PATH . '/utils/functions.php'; // For api_error/api_success
require_once __DIR__ . '/../../includes/error_handler.php'; // thêm

header('Content-Type: application/json');

// Check if admin is logged in
require_once __DIR__ . '/../../classes/Auth.php';
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

$db = Database::getInstance();
$conn = $db->getConnection();

if (!$conn) {
    error_log("Database connection failed in process_profile_update.php");
    api_error('Database connection error.', 500);
    exit;
}

try {
    $stmt = $conn->prepare("UPDATE admin SET name = :name, updated_at = NOW() WHERE id = :id");
    $stmt->bindParam(':name', $name, PDO::PARAM_STR);
    $stmt->bindParam(':id', $admin_id, PDO::PARAM_INT);

    if ($stmt->execute()) {
        api_success(null, 'Profile updated successfully.');
    } else {
        api_error('Failed to update profile.', 500);
    }
} catch (PDOException $e) {
    error_log("Error updating admin profile: " . $e->getMessage());
    error_log("Stack trace: " . $e->getTraceAsString());
    api_error('An error occurred while updating the profile.', 500);
} finally {
    $db->close();
}
?>
