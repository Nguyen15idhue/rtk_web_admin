<?php
// Cron script: Auto-approve transactions with auto-approve vouchers
// URL: public/handlers/purchase/cron_auto_approve.php
// NOTE: This should ideally be executed via CLI or a secured internal cron, NOT exposed publicly.

header('Content-Type: application/json; charset=utf-8');

// --- Start / Prepare Session FIRST ---
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Allow bootstrap to skip strict session validation for this cron bootstrap phase
if (!defined('DISABLE_SESSION_VALIDATION')) {
    define('DISABLE_SESSION_VALIDATION', true);
}

require_once dirname(__DIR__, 3) . '/private/config/constants.php';
require_once dirname(__DIR__, 3) . '/private/utils/functions.php';

// --- Simulate privileged admin session (ensure this admin exists in DB) ---
// Include BOTH invoice & account permissions because create_account / update_account require them
$_SESSION['admin_id']          = 1;          // Adjust if super admin differs
$_SESSION['admin_role']        = 'superadmin';
$_SESSION['admin_username']    = 'cron_auto';
$_SESSION['admin_permissions'] = [
    'invoice_management_edit',
    'account_management_edit'
];

// --- Bootstrap (DB connection etc.) ---
$bootstrap = require dirname(__DIR__, 3) . '/private/core/page_bootstrap.php';
$db = $bootstrap['db'];

// Now record a session row so later internal cURL (create_account/update_account) passes validation
try {
    if (function_exists('recordSession')) {
        // Avoid duplicate row if already recorded in this run
        recordSession((int)$_SESSION['admin_id']);
    }
} catch (Throwable $e) {
    error_log('[CRON_AUTO_APPROVE] Failed to record session: ' . $e->getMessage());
}

// Find all pending transactions with vouchers that have auto_approve = 1 in the last 10 minutes
$sql = "SELECT th.id 
        FROM transaction_history th
        JOIN voucher v ON th.voucher_id = v.id 
        WHERE th.status = 'pending' 
        AND v.auto_approve = 1 
        AND v.is_active = 1 
        AND th.created_at >= (NOW() - INTERVAL 10 MINUTE)";
try {
    $stmt = $db->query($sql);
    $ids = $stmt->fetchAll(PDO::FETCH_COLUMN);
} catch (Throwable $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'DB query failed', 'error' => $e->getMessage()]);
    exit;
}

if (!$ids) {
    echo json_encode(['success' => true, 'message' => 'No auto-approve voucher transactions to approve.']);
    exit;
}

// Approve each transaction by calling the internal approve logic
$success = [];
$fail = [];
foreach ($ids as $id) {
    try {
        // Run approval logic in isolated process via include; define flags BEFORE include
        if (!defined('IS_CRON')) define('IS_CRON', true);

        // Prepare superglobals for included script
        $_POST = ['transaction_id' => $id];
        $_SERVER['REQUEST_METHOD'] = 'POST';

        // Use output buffering to capture anything unexpected from included file
        ob_start();
        include dirname(__DIR__, 3) . '/private/actions/purchase/process_transaction_approve.php';
        $raw = ob_get_clean();

        $decoded = json_decode($raw, true);
        if (is_array($decoded) && ($decoded['success'] ?? false)) {
            $success[] = $id;
        } else {
            $fail[] = ['id' => $id, 'error' => $decoded['message'] ?? 'Unknown failure'];
            error_log('[CRON_AUTO_APPROVE_VOUCHER] Approval partial/failed for #' . $id . ' raw=' . substr($raw,0,300));
        }
    } catch (Throwable $e) {
        $fail[] = ['id' => $id, 'error' => $e->getMessage()];
        error_log("[CRON_AUTO_APPROVE_VOUCHER] Exception approving transaction #$id: " . $e->getMessage());
    }
}

$result = [
    'success' => true,
    'approved' => $success,
    'failed' => $fail
];
echo json_encode($result);

// Optional: force flush
if (function_exists('fastcgi_finish_request')) {
    fastcgi_finish_request();
}
