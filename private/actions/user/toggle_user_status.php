<?php
$config = require_once __DIR__ . '/../../core/page_bootstrap.php';

Auth::ensureAuthorized('user_management_edit'); 
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

    // --- NEW: bulk invert logic ---
    if (isset($input['bulk_operation']) && $input['bulk_operation'] === 'invert_status') {
        $ids = $input['user_ids'] ?? [];
        if (!is_array($ids)) {
            abort('Invalid user_ids parameter.', 400);
        }
        // ép về int và loại bỏ giá trị không hợp lệ
        $user_ids = array_filter(array_map('intval', $ids));
        if (empty($user_ids)) {
            abort('Invalid or missing user_ids.', 400);
        }
        $errors = [];
        foreach ($user_ids as $id) {
            try {
                $user = $userModel->getOne($id);
                if (!$user) {
                    throw new Exception('User not found');
                }
                // nếu đang active (deleted_at IS NULL) thì disable, ngược lại enable
                $disable = ($user['deleted_at'] === null);
                $userModel->toggleStatus($id, $disable);
            } catch (Exception $e) {
                $errors[] = "ID $id: " . $e->getMessage();
            }
        }
        if (empty($errors)) {
            api_success(null, 'Cập nhật trạng thái hàng loạt thành công.');
        } else {
            api_success(['errors' => $errors], 'Hoàn thành với một số lỗi.');
        }
        exit;
    }
    // --- END bulk logic ---

    // single toggle
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
