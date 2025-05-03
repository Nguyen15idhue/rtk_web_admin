<?php
require_once __DIR__ . '/../../includes/page_bootstrap.php';

try {
    if (!isset($_SESSION['admin_id'])) {
        error_log("Unauthorized toggle_user_status");
        abort('Unauthorized access.', 403);
    }
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        abort('Invalid request method.', 405);
    }
    $user_id = filter_input(INPUT_POST, 'user_id', FILTER_VALIDATE_INT);
    $action  = filter_input(INPUT_POST, 'action');
    if (!$user_id || !in_array($action, ['enable', 'disable'])) {
        abort('Invalid or missing parameters (user_id, action).', 400);
    }
    $conn = Database::getInstance()->getConnection() 
        or abort('Database connection failed.', 500);

    $conn->beginTransaction();

    $deleted_at_value = ($action === 'disable') ? date('Y-m-d H:i:s') : null;

    $sql = "UPDATE user SET deleted_at = :deleted_at, updated_at = NOW() WHERE id = :id";
    $stmt = $conn->prepare($sql);

    $stmt->bindParam(':deleted_at', $deleted_at_value, $deleted_at_value === null ? PDO::PARAM_NULL : PDO::PARAM_STR);
    $stmt->bindParam(':id', $user_id, PDO::PARAM_INT);

    if ($stmt->execute()) {
        if ($stmt->rowCount() > 0) {
            $conn->commit();
            echo json_encode(['success' => true, 'message' => 'Cập nhật trạng thái người dùng thành công.']);
            exit;
        } else {
            $conn->rollBack();
            abort('User not found or status already updated.', 404);
        }
    } else {
        $conn->rollBack();
        error_log("Toggle status failed for user $user_id");
        abort('Failed to update user status.', 500);
    }
} catch (PDOException $e) {
    if (isset($conn) && $conn->inTransaction()) $conn->rollBack();
    error_log("PDOException in toggle_user_status: " . $e->getMessage());
    error_log("Stack trace: " . $e->getTraceAsString());
    abort('Database error during status update.', 500);
} catch (Exception $e) {
    error_log("Exception in toggle_user_status: " . $e->getMessage());
    error_log("Stack trace: " . $e->getTraceAsString());
    abort('An unexpected error occurred.', 500);
}
?>
