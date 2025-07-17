<?php
header('Content-Type: application/json; charset=UTF-8');

// Bootstrap and auth
$bootstrap = require __DIR__ . '/../../core/page_bootstrap.php';
$db = $bootstrap['db'];
require_once __DIR__ . '/../../classes/Auth.php';
Auth::ensureAuthorized('account_management_edit');

try {
    // Disable foreign key checks for bulk sync
    $db->exec('SET FOREIGN_KEY_CHECKS=0');
    $db->beginTransaction();
    
    // Step 1: Backup current data to survey_account_backup (excluding deleted_at so it defaults to NULL)
    $db->exec('DELETE FROM survey_account_backup');
    $db->exec(
        "INSERT INTO survey_account_backup (
            id, registration_id, start_time, end_time, username_acc, password_acc,
            concurrent_user, enabled, caster, user_type, regionIds, customerBizType,
            area, temp_phone, created_at, updated_at, backup_date
        ) SELECT
            id, registration_id, start_time, end_time, username_acc, password_acc,
            concurrent_user, enabled, caster, user_type, regionIds, customerBizType,
            area, temp_phone, created_at, updated_at,
            NOW() AS backup_date
        FROM survey_account"
    );
    
    // Step 2: Fetch ALL remote RTK accounts via API
    require_once __DIR__ . '/../../classes/RtkApiClient.php';
    $client = new RtkApiClient();
    $params = ['page' => 1, 'size' => 999999]; // Get all data
    $resp = $client->request('GET', '/openapi/broadcast/users', $params);
    
    if (!$resp['success']) {
        throw new Exception($resp['error'] ?? 'Failed to fetch remote accounts');
    }
    
    $records = $resp['data']['records'] ?? [];
    
    // Step 3: Clear current survey_account table
    $db->exec('DELETE FROM survey_account');
    
    // Step 4: Insert new data from API
    $insertStmt = $db->prepare('INSERT INTO survey_account (
        id, username_acc, password_acc, start_time, end_time, enabled, concurrent_user, caster,
        registration_id, user_type, regionIds, customerBizType, area, temp_phone
    ) VALUES (
        :id, :username_acc, :password_acc, :start_time, :end_time, :enabled, :concurrent_user, :caster,
        :registration_id, :user_type, :regionIds, :customerBizType, :area, :temp_phone
    )');
    
    // Step 5: Prepare backup data for merging
    $backupStmt = $db->prepare('SELECT * FROM survey_account_backup WHERE username_acc = :username');
    
    $insertedCount = 0;
    foreach ($records as $r) {
        $username = $r['name'] ?? null;
        if (!$username) continue;
        
        // Get backup data for this username (if exists)
        $backupStmt->execute([':username' => $username]);
        $backupData = $backupStmt->fetch(PDO::FETCH_ASSOC) ?: [];
        
        // Convert timestamps to datetime
        $startTime = isset($r['startTime']) ? date('Y-m-d H:i:s', $r['startTime'] / 1000) : null;
        $endTime = isset($r['endTime']) ? date('Y-m-d H:i:s', $r['endTime'] / 1000) : null;
        
        // Prepare caster data
        $caster = !empty($r['mountNames']) ? implode(',', $r['mountNames']) : null;
        
        // Merge API data with backup data
        $insertStmt->execute([
            ':id' => $r['id'],
            ':username_acc' => $username,
            ':password_acc' => $r['userPwd'] ?? '',
            ':start_time' => $startTime,
            ':end_time' => $endTime,
            ':enabled' => $r['enabled'] ?? 1,
            ':concurrent_user' => $r['numOnline'] ?? 1,
            ':caster' => $caster,
            
            // From backup data (keep existing values)
            ':registration_id' => $backupData['registration_id'] ?? 0,
            ':user_type' => $backupData['user_type'] ?? null,
            ':regionIds' => $backupData['regionIds'] ?? null,
            ':customerBizType' => $backupData['customerBizType'] ?? 1,
            ':area' => $backupData['area'] ?? null,
            ':temp_phone' => $backupData['temp_phone'] ?? null
        ]);
        
        $insertedCount++;
    }
    
    $db->commit();
    // Re-enable foreign key checks
    $db->exec('SET FOREIGN_KEY_CHECKS=1');
    
    api_success([
        'inserted_count' => $insertedCount,
        'total_records' => count($records)
    ], "Đồng bộ hoàn tất! Đã cập nhật {$insertedCount} tài khoản từ API RTK.");
    
} catch (Exception $e) {
    $db->rollBack();
    api_error('Lỗi khi đồng bộ: ' . $e->getMessage(), 500);
}
