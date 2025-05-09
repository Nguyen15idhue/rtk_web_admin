<?php
require_once __DIR__ . '/../../classes/Auth.php';
Auth::ensureAuthorized(['admin', 'customercare']); 

$config = require_once __DIR__ . '/../../includes/page_bootstrap.php';
$db     = $config['db'];
if (!isset($_GET['id']) || !filter_var($_GET['id'], FILTER_VALIDATE_INT)) {
    abort('Invalid or missing user ID.', 400);
}

$user_id = (int)$_GET['id'];

require_once __DIR__ . '/../../classes/UserModel.php';
$userModel = new UserModel();

$user = $userModel->getOne($user_id);
if (!$user) {
    abort('User not found.', 404);
}

// Format dates if needed before sending
$user['created_at_formatted'] = !empty($user['created_at']) ? date('d/m/Y H:i:s', strtotime($user['created_at'])) : '-';
$user['updated_at_formatted'] = !empty($user['updated_at']) ? date('d/m/Y H:i:s', strtotime($user['updated_at'])) : '-';
$user['deleted_at_formatted'] = !empty($user['deleted_at']) ? date('d/m/Y H:i:s', strtotime($user['deleted_at'])) : '-';
$user['status_text'] = empty($user['deleted_at']) ? 'Hoạt động' : 'Vô hiệu hóa';
$user['account_type_text'] = $user['is_company'] ? 'Công ty' : 'Cá nhân';

api_success($user);

?>