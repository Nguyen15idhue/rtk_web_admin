<?php
// Cron script: Auto-approve 0đ transactions in the last minute
// Place in: public/handlers/purchase/cron_auto_approve_zero_amount.php

require_once dirname(__DIR__, 3) . '/private/config/constants.php';
require_once dirname(__DIR__, 3) . '/private/utils/functions.php';

// Bootstrap DB and Auth (no session needed for cron)
$bootstrap = require_once dirname(__DIR__, 3) . '/private/core/page_bootstrap.php';
$db = $bootstrap['db'];

// Find all pending transactions with amount = 0 in the last minute
$sql = "SELECT id FROM transaction_history WHERE status = 'pending' AND amount = 0 AND created_at >= (NOW() - INTERVAL 1 MINUTE)";
$stmt = $db->query($sql);
$ids = $stmt->fetchAll(PDO::FETCH_COLUMN);

if (!$ids) {
    echo json_encode(['success' => true, 'message' => 'No 0đ transactions to approve.']);
    exit;
}

// Approve each transaction by calling the internal approve logic
$success = [];
$fail = [];
foreach ($ids as $id) {
    // Simulate POST to process_transaction_approve.php
    $_POST = ['transaction_id' => $id];
    $_SERVER['REQUEST_METHOD'] = 'POST';
    try {
        include dirname(__DIR__, 3) . '/private/actions/purchase/process_transaction_approve.php';
        $success[] = $id;
    } catch (Throwable $e) {
        $fail[] = ['id' => $id, 'error' => $e->getMessage()];
        error_log("[CRON_AUTO_APPROVE_ZERO] Failed to approve transaction #$id: " . $e->getMessage());
    }
}

$result = [
    'success' => true,
    'approved' => $success,
    'failed' => $fail
];
echo json_encode($result);
