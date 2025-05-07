<?php
$config = require_once __DIR__ . '/../../includes/page_bootstrap.php';
$db     = $config['db'];

header('Content-Type: application/json');

try {
    if (!isset($_SESSION['admin_id'])) {
        error_log("Unauthorized toggle_user_status");
        abort('Unauthorized access.', 403);
    }
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        abort('Invalid request method.', 405);
    }

    // Get input data
    $rawInput = file_get_contents('php://input');
    $input = json_decode($rawInput, true);
    if (!is_array($input)) {
        $input = $_POST;
    }

    $user_id = filter_var($input['user_id'] ?? null, FILTER_VALIDATE_INT);
    $action  = filter_var($input['action']    ?? '', FILTER_SANITIZE_SPECIAL_CHARS);

    if (!$user_id || !in_array($action, ['enable', 'disable'])) {
        abort('Invalid or missing parameters.', 400);
    }

    $db->beginTransaction();

    $deleted_at_value = ($action === 'disable') ? date('Y-m-d H:i:s') : null;

    $sql = "UPDATE user SET deleted_at = :deleted_at, updated_at = NOW() WHERE id = :id";
    $stmt = $db->prepare($sql);

    $stmt->bindParam(':deleted_at', $deleted_at_value, $deleted_at_value === null ? PDO::PARAM_NULL : PDO::PARAM_STR);
    $stmt->bindParam(':id', $user_id, PDO::PARAM_INT);

    if ($stmt->execute() && $stmt->rowCount() > 0) {
        $db->commit();
        api_success(null, 'Cập nhật trạng thái người dùng thành công.');
    } else {
        $db->rollBack();
        abort('User not found or status unchanged.', 404);
    }
} catch (PDOException $e) {
    if (isset($db) && $db->inTransaction()) $db->rollBack();
    error_log("PDOException in toggle_user_status: " . $e->getMessage());
    error_log("Stack trace: " . $e->getTraceAsString());
    abort('Database error during status update.', 500);
} catch (Exception $e) {
    if ($db->inTransaction()) {
        $db->rollBack();
    }
    error_log("Exception in toggle_user_status: " . $e->getMessage());
    error_log("Stack trace: " . $e->getTraceAsString());
    abort('An unexpected error occurred.', 500);
}
?>
