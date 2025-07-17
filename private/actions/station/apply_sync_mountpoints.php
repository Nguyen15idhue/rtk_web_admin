<?php
// NOTE: This file is no longer used as of the requirement to remove the "Sync Information" button
// Only "Full Sync" functionality is kept. This file can be removed in future cleanup.
// Date: 2025-07-17

header('Content-Type: application/json; charset=UTF-8');

// Bootstrap and auth
$bootstrap = require __DIR__ . '/../../core/page_bootstrap.php';
$db = $bootstrap['db'];
require_once __DIR__ . '/../../classes/Auth.php';
Auth::ensureAuthorized('station_management_edit');

try {
    $input = json_decode(file_get_contents('php://input'), true);
    $mountpoints = $input['mountpoints'] ?? [];
    
    if (empty($mountpoints)) {
        throw new Exception('Không có mountpoint nào để đồng bộ.');
    }
    
    $db->beginTransaction();
    
    $stmt = $db->prepare('UPDATE mount_point SET 
        id = :new_id, 
        ip = :ip, 
        port = :port 
        WHERE id = :old_id');
    
    $updatedCount = 0;
    
    foreach ($mountpoints as $mountpoint) {
        $localData = $mountpoint['local_data'];
        $remoteData = $mountpoint['remote_data'];
        
        $result = $stmt->execute([
            ':new_id' => $remoteData['id'],
            ':ip' => $remoteData['ip'],
            ':port' => $remoteData['port'],
            ':old_id' => $localData['id']
        ]);
        
        if ($result && $stmt->rowCount() > 0) {
            $updatedCount++;
        }
    }
    
    $db->commit();
    api_success(null, "Đồng bộ thành công {$updatedCount} mountpoint.");
    
} catch (Exception $e) {
    $db->rollBack();
    api_error('Lỗi khi đồng bộ: ' . $e->getMessage(), 500);
}
