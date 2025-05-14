<?php
header('Content-Type: application/json');
require_once __DIR__ . '/../../classes/Auth.php'; // Include the Auth class
Auth::ensureAuthorized('permission_management'); // Only admins can delete other admins

// Parse request
$input = json_decode(file_get_contents('php://input'), true);
$id = $input['id'] ?? null;
if (!$id || !is_numeric($id)) {
    api_error('Invalid ID', 400);
}

// Include AdminModel
require_once __DIR__ . '/../../classes/AdminModel.php';
$model = new AdminModel();

try {
    $ok = $model->delete($id);
    if ($ok) {
        api_success([], 'Xóa thành công.');
    } else {
        api_error('Không tìm thấy tài khoản để xóa.', 400);
    }
} catch (PDOException $e) {
    error_log('process_admin_delete error: ' . $e->getMessage());
    api_error('Error deleting admin', 500);
}
