<?php
require_once __DIR__ . '/../../classes/Auth.php';
Auth::ensureAuthorized(['admin', 'customercare']); 

$config = require_once __DIR__ . '/../../includes/page_bootstrap.php';
$db     = $config['db'];
header('Content-Type: application/json');

require_once __DIR__ . '/../../classes/UserModel.php';
$userModel = new UserModel();

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

    try {
        $userModel->toggleStatus($user_id, $action === 'disable');
        api_success(null, 'Cập nhật trạng thái người dùng thành công.');
    } catch (Exception $e) {
        abort('Không thể cập nhật trạng thái.', 500);
    }
} catch (Exception $e) {
    error_log("Exception in toggle_user_status: " . $e->getMessage());
    error_log("Stack trace: " . $e->getTraceAsString());
    abort('An unexpected error occurred.', 500);
}
?>
