<?php
require_once __DIR__ . '/../../config/constants.php';
require_once BASE_PATH . '/classes/Database.php';
require_once __DIR__ . '/../../includes/error_handler.php';

if (!isset($_SESSION['admin_id'])) {
    abort('Unauthorized', 401);
}

$db   = Database::getInstance();
$conn = $db->getConnection();

if (!$conn) {
    abort('DB connection failed', 500);
}

try {
    $stmt = $conn->prepare("SELECT name, admin_username, role FROM admin WHERE id = :id");
    $stmt->bindParam(':id', $_SESSION['admin_id'], PDO::PARAM_INT);
    $stmt->execute();
    $profile = $stmt->fetch(PDO::FETCH_ASSOC) ?: [];
    echo json_encode(['success' => true, 'data' => $profile]);
} catch (PDOException $e) {
    error_log("Error fetching profile: " . $e->getMessage());
    error_log("Stack trace: " . $e->getTraceAsString());
    abort('Error fetching profile', 500);
} finally {
    $db->close();
}
?>
