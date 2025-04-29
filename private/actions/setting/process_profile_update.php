<?php
require_once __DIR__ . '/../../classes/Database.php';
require_once __DIR__ . '/../../utils/functions.php'; // For send_json_response

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
$name = $input['name'] ?? null;

// Basic validation
if (empty($name)) {
    send_json_response(['success' => false, 'message' => 'Name cannot be empty.'], 400);
    exit;
}

$db = Database::getInstance();
$conn = $db->getConnection();

if (!$conn) {
    error_log("Database connection failed in process_profile_update.php");
    send_json_response(['success' => false, 'message' => 'Database connection error.'], 500);
    exit;
}

try {
    $stmt = $conn->prepare("UPDATE admin SET name = :name, updated_at = NOW() WHERE id = :id");
    $stmt->bindParam(':name', $name, PDO::PARAM_STR);
    $stmt->bindParam(':id', $admin_id, PDO::PARAM_INT);

    if ($stmt->execute()) {
        // Optionally update session variable if name is used elsewhere
        // $_SESSION['admin_name'] = $name; // Assuming you might store/use admin name in session
        send_json_response(['success' => true, 'message' => 'Profile updated successfully.']);
    } else {
        send_json_response(['success' => false, 'message' => 'Failed to update profile.'], 500);
    }
} catch (PDOException $e) {
    error_log("Error updating admin profile: " . $e->getMessage());
    send_json_response(['success' => false, 'message' => 'An error occurred while updating the profile.'], 500);
} finally {
    $db->close();
}
?>
