<?php
require_once __DIR__ . '/../../config/constants.php';
require_once BASE_PATH . '/classes/Database.php';
require_once BASE_PATH . '/utils/functions.php'; 

header('Content-Type: application/json');

// Check if admin is logged in
if (!isset($_SESSION['admin_id'])) {
    send_json_response(['success' => false, 'message' => 'Unauthorized access.'], 401);
    exit;
}

// Check if it's a POST request
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    send_json_response(['success' => false, 'message' => 'Invalid request method.'], 405);
    exit;
}

// Get input data
$input = json_decode(file_get_contents('php://input'), true);
$admin_id = $_SESSION['admin_id'];
$current_password = $input['current_password'] ?? null;
$new_password = $input['new_password'] ?? null;
$confirm_password = $input['confirm_password'] ?? null;

// Basic validation
if (empty($current_password) || empty($new_password) || empty($confirm_password)) {
    send_json_response(['success' => false, 'message' => 'All password fields are required.'], 400);
    exit;
}

if (strlen($new_password) < 6) {
    send_json_response(['success' => false, 'message' => 'New password must be at least 6 characters long.'], 400);
    exit;
}

if ($new_password !== $confirm_password) {
    send_json_response(['success' => false, 'message' => 'New password and confirmation password do not match.'], 400);
    exit;
}

$db = Database::getInstance();;
$conn = $db->getConnection();

if (!$conn) {
    error_log("Database connection failed in process_password_change.php");
    send_json_response(['success' => false, 'message' => 'Database connection error.'], 500);
    exit;
}

try {
    // 1. Fetch current password hash
    $stmt = $conn->prepare("SELECT admin_password FROM admin WHERE id = :id");
    $stmt->bindParam(':id', $admin_id, PDO::PARAM_INT);
    $stmt->execute();
    $admin = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$admin) {
        send_json_response(['success' => false, 'message' => 'Admin user not found.'], 404);
        exit;
    }

    // 2. Verify current password
    if (!password_verify($current_password, $admin['admin_password'])) {
        send_json_response(['success' => false, 'message' => 'Incorrect current password.'], 400);
        exit;
    }

    // 3. Hash the new password
    $new_password_hash = password_hash($new_password, PASSWORD_DEFAULT);
    if ($new_password_hash === false) {
         error_log("Password hashing failed for admin ID: " . $admin_id);
         send_json_response(['success' => false, 'message' => 'Error processing new password.'], 500);
         exit;
    }


    // 4. Update the password in the database
    $updateStmt = $conn->prepare("UPDATE admin SET admin_password = :new_password, updated_at = NOW() WHERE id = :id");
    $updateStmt->bindParam(':new_password', $new_password_hash, PDO::PARAM_STR);
    $updateStmt->bindParam(':id', $admin_id, PDO::PARAM_INT);

    if ($updateStmt->execute()) {
        send_json_response(['success' => true, 'message' => 'Password changed successfully.']);
    } else {
        send_json_response(['success' => false, 'message' => 'Failed to change password.'], 500);
    }

} catch (PDOException $e) {
    error_log("Error changing admin password: " . $e->getMessage());
    send_json_response(['success' => false, 'message' => 'An error occurred while changing the password.'], 500);
} finally {
    $db->close();
}
?>
