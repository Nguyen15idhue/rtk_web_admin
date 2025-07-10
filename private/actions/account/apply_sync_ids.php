<?php
header('Content-Type: application/json; charset=UTF-8');

// Bootstrap and auth
$bootstrap = require __DIR__ . '/../../core/page_bootstrap.php';
$db = $bootstrap['db'];
require_once __DIR__ . '/../../classes/Auth.php';
Auth::ensureAuthorized('account_management_edit');

// Read JSON input
$raw = file_get_contents('php://input');
$input = json_decode($raw, true);
$pairs = $input['pairs'] ?? [];
if (empty($pairs) || !is_array($pairs)) {
    api_error('No account pairs provided for syncing.', 400);
}

// Perform updates in transaction
try {
    $db->beginTransaction();
    $stmt = $db->prepare('UPDATE survey_account SET id = :remote_id WHERE id = :local_id');
    foreach ($pairs as $pair) {
        if (isset($pair['local_id'], $pair['remote_id'])) {
            $stmt->execute([
                ':remote_id' => $pair['remote_id'],
                ':local_id'  => $pair['local_id'],
            ]);
        }
    }
    $db->commit();
    api_success(null, 'Đồng bộ ID thành công.');
} catch (Exception $e) {
    $db->rollBack();
    api_error('Lỗi khi đồng bộ: ' . $e->getMessage(), 500);
}
