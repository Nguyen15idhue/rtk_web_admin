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
$accounts = $input['accounts'] ?? [];
if (empty($accounts) || !is_array($accounts)) {
    api_error('No accounts provided for syncing.', 400);
}

// Perform updates in transaction
try {
    $db->beginTransaction();
    
    // Helper function to update date only while keeping time
    function updateDateOnly($currentDateTime, $newDate) {
        if (!$newDate) return null;
        if (!$currentDateTime) return $newDate . ' 00:00:00';
        
        // Extract current time from existing datetime
        $currentTime = date('H:i:s', strtotime($currentDateTime));
        return $newDate . ' ' . $currentTime;
    }
    
    // Prepare update statement for all fields
    $sql = 'UPDATE survey_account SET 
            id = :new_id,
            password_acc = :password_acc,
            start_time = :start_time,
            end_time = :end_time,
            enabled = :enabled,
            concurrent_user = :concurrent_user,
            updated_at = NOW()
            WHERE id = :old_id';
    
    $stmt = $db->prepare($sql);
    
    // Get current account data for time preservation
    $getCurrentStmt = $db->prepare('SELECT start_time, end_time FROM survey_account WHERE id = :id');
    
    $updatedCount = 0;
    foreach ($accounts as $account) {
        if (isset($account['local_id'], $account['remote_data'])) {
            $remoteData = $account['remote_data'];
            
            // Get current times to preserve
            $getCurrentStmt->execute([':id' => $account['local_id']]);
            $currentData = $getCurrentStmt->fetch(PDO::FETCH_ASSOC);
            
            // Update dates while preserving times
            $newStartTime = updateDateOnly($currentData['start_time'] ?? null, $remoteData['start_time']);
            $newEndTime = updateDateOnly($currentData['end_time'] ?? null, $remoteData['end_time']);
            
            $result = $stmt->execute([
                ':new_id' => $remoteData['id'],
                ':password_acc' => $remoteData['password_acc'],
                ':start_time' => $newStartTime,
                ':end_time' => $newEndTime,
                ':enabled' => $remoteData['enabled'],
                ':concurrent_user' => $remoteData['concurrent_user'],
                // ':caster' => $remoteData['caster'],
                ':old_id' => $account['local_id']
            ]);
            
            if ($result && $stmt->rowCount() > 0) {
                $updatedCount++;
            }
        }
    }
    
    $db->commit();
    api_success(null, "Đồng bộ thành công {$updatedCount} tài khoản.");
} catch (Exception $e) {
    $db->rollBack();
    api_error('Lỗi khi đồng bộ: ' . $e->getMessage(), 500);
}
