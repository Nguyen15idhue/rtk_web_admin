<?php
// filepath: private/actions/referral/process_withdrawal.php
// Process approval or rejection of withdrawal requests
header('Content-Type: application/json; charset=utf-8');
require_once dirname(__DIR__, 3) . '/config/constants.php';
require_once PRIVATE_CORE_PATH . 'error_handler.php';
require_once PRIVATE_CLASSES_PATH . 'Database.php';
require_once BASE_PATH . '/utils/functions.php';

// Ensure request method is POST or GET
$method = $_SERVER['REQUEST_METHOD'];
$data = $method === 'POST' ? $_POST : $_GET;

$id = isset($data['id']) ? (int)$data['id'] : 0;
$status = isset($data['status']) ? strtolower($data['status']) : '';

if (!$id || !in_array($status, ['approve','reject'], true)) {
    api_response(null, 'Invalid parameters', 400);
}

// Map action to DB status
$dbStatus = $status === 'approve' ? 'completed' : 'rejected';

try {
    $db = Database::getInstance()->getConnection();
    $stmt = $db->prepare('UPDATE withdrawal_request SET status = :status, updated_at = NOW() WHERE id = :id');
    $stmt->execute([':status' => $dbStatus, ':id' => $id]);

    if ($stmt->rowCount() === 0) {
        api_response(null, 'No record updated', 404);
    }
    api_response(['id' => $id, 'status' => $dbStatus], 'Update successful');
} catch (Exception $e) {
    error_log('Error processing withdrawal: ' . $e->getMessage());
    api_response(null, 'Server error', 500);
}
