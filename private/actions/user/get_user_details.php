<?php
header('Content-Type: application/json');

// Correct the paths relative to the current file's directory (private/actions/setting)
require_once __DIR__ . '/../../config/database.php'; // Go up two levels to 'private', then into 'config'
require_once __DIR__ . '/../../classes/Database.php'; // Go up two levels to 'private', then into 'classes'

// register shutdown to always close DB
register_shutdown_function(function() {
    if (class_exists('Database')) {
        Database::getInstance()->close();
    }
});

// Authorization Check (Admin only)
if (!isset($_SESSION['admin_id']) || !in_array($_SESSION['admin_role'] ?? '', ['admin', 'admin', 'customercare'])) {
    http_response_code(403);
    echo json_encode(['success' => false, 'message' => 'Unauthorized access.']);
    exit;
}

if (!isset($_GET['id']) || !filter_var($_GET['id'], FILTER_VALIDATE_INT)) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Invalid or missing user ID.']);
    exit;
}

$user_id = (int)$_GET['id'];

$db = Database::getInstance();
$conn = $db->getConnection();

if (!$conn) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Database connection failed.']);
    exit;
}

try {
    $stmt = $conn->prepare("SELECT id, username, email, phone, is_company, company_name, tax_code, created_at, updated_at, deleted_at FROM user WHERE id = :id");
    $stmt->bindParam(':id', $user_id, PDO::PARAM_INT);
    $stmt->execute();
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user) {
        // Format dates if needed before sending
        $user['created_at_formatted'] = !empty($user['created_at']) ? date('d/m/Y H:i:s', strtotime($user['created_at'])) : '-';
        $user['updated_at_formatted'] = !empty($user['updated_at']) ? date('d/m/Y H:i:s', strtotime($user['updated_at'])) : '-';
        $user['deleted_at_formatted'] = !empty($user['deleted_at']) ? date('d/m/Y H:i:s', strtotime($user['deleted_at'])) : '-';
        $user['status_text'] = empty($user['deleted_at']) ? 'Hoạt động' : 'Vô hiệu hóa';
        $user['account_type_text'] = $user['is_company'] ? 'Công ty' : 'Cá nhân';

        echo json_encode(['success' => true, 'data' => $user]);
    } else {
        http_response_code(404);
        echo json_encode(['success' => false, 'message' => 'User not found.']);
    }

} catch (PDOException $e) {
    error_log("Database Error fetching user details: " . $e->getMessage());
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Database error occurred.']);
} finally {
    // explicitly free resources
    if (isset($conn)) {
        $conn = null;
    }
    if (isset($db)) {
        $db->close();
        $db = null;
    }
}

?>