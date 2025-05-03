<?php
require_once __DIR__ . '/../../config/constants.php';
require_once BASE_PATH . '/classes/Database.php';

if (!isset($_SESSION['admin_id'])) {
    http_response_code(401);
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit;
}

$db   = Database::getInstance();
$conn = $db->getConnection();

if (!$conn) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'DB connection failed']);
    exit;
}

try {
    $stmt = $conn->prepare("SELECT name, admin_username, role FROM admin WHERE id = :id");
    $stmt->bindParam(':id', $_SESSION['admin_id'], PDO::PARAM_INT);
    $stmt->execute();
    $profile = $stmt->fetch(PDO::FETCH_ASSOC) ?: [];
    echo json_encode(['success' => true, 'data' => $profile]);
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Error fetching profile']);
} finally {
    $db->close();
}
