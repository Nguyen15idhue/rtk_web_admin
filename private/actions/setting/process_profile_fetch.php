<?php
require_once __DIR__ . '/../../config/constants.php';
require_once BASE_PATH . '/classes/Database.php';
require_once BASE_PATH . '/utils/functions.php';    // thêm utils/functions
require_once __DIR__ . '/../../includes/error_handler.php';

if (!isset($_SESSION['admin_id'])) {
    api_error('Unauthorized', 401);
}

$db   = Database::getInstance();
$conn = $db->getConnection();
if (!$conn) {
    api_error('DB connection failed', 500);
}

try {
    $stmt = $conn->prepare("SELECT name, admin_username, role FROM admin WHERE id = :id");
    $stmt->bindParam(':id', $_SESSION['admin_id'], PDO::PARAM_INT);
    $stmt->execute();
    $profile = $stmt->fetch(PDO::FETCH_ASSOC) ?: [];
    api_success($profile, '', 200);
} catch (PDOException $e) {
    error_log("Error fetching profile: " . $e->getMessage());
    api_error('Error fetching profile', 500);
} finally {
    $db->close();
}
?>
