<?php
header('Content-Type: application/json; charset=UTF-8');

// Bootstrap and auth
$bootstrap = require __DIR__ . '/../../core/page_bootstrap.php';
$db = $bootstrap['db'];
require_once __DIR__ . '/../../classes/Auth.php';
Auth::ensureAuthorized('station_management_edit');

try {
    // Disable foreign key checks for bulk sync
    $db->exec('SET FOREIGN_KEY_CHECKS=0');
    $db->beginTransaction();
    
    // Step 1: Backup current data to mountpoint_backup
    $db->exec('DELETE FROM mountpoint_backup');
    $db->exec(
        "INSERT INTO mountpoint_backup (
            id, location_id, ip, port, mountpoint, created_at, updated_at, backup_date
        ) SELECT
            id, location_id, ip, port, mountpoint, 
            NOW() AS created_at, NOW() AS updated_at, NOW() AS backup_date
        FROM mount_point"
    );
    
    // Step 2: Fetch ALL remote mountpoints via API
    require_once __DIR__ . '/../../classes/RtkApiClient.php';
    $client = new RtkApiClient();
    
    $allRemoteMountpoints = [];
    $page = 1;
    $size = 100;
    
    do {
        $params = ['page' => $page, 'size' => $size];
        $resp = $client->request('GET', '/openapi/broadcast/mounts', $params);
        
        if (!$resp['success']) {
            throw new Exception($resp['error'] ?? 'Failed to fetch remote mountpoints');
        }
        
        $records = $resp['data']['records'] ?? [];
        if (empty($records)) {
            break;
        }
        
        $allRemoteMountpoints = array_merge($allRemoteMountpoints, $records);
        $page++;
        
        // Safety check
        if ($page > 50) {
            break;
        }
        
    } while (!empty($records));
    
    // Step 3: Clear current mount_point table
    $db->exec('DELETE FROM mount_point');
    
    // Step 4: Insert new data from API
    $insertStmt = $db->prepare('INSERT INTO mount_point (
        id, location_id, ip, port, mountpoint
    ) VALUES (
        :id, :location_id, :ip, :port, :mountpoint
    )');
    
    // Step 5: Prepare backup data for merging
    $backupStmt = $db->prepare('SELECT * FROM mountpoint_backup WHERE mountpoint = :mountpoint');
    
    $insertedCount = 0;
    foreach ($allRemoteMountpoints as $r) {
        $mountpointName = $r['name'] ?? null;
        if (!$mountpointName) continue;
        
        // Get backup data for this mountpoint (if exists)
        $backupStmt->execute([':mountpoint' => $mountpointName]);
        $backupData = $backupStmt->fetch(PDO::FETCH_ASSOC) ?: [];
        
        // Merge API data with backup data
        $insertStmt->execute([
            ':id' => $r['id'],
            ':ip' => $r['ip'] ?? '',
            ':port' => $r['port'] ?? 0,
            ':mountpoint' => $mountpointName,
            
            // From backup data (keep existing location assignment)
            ':location_id' => $backupData['location_id'] ?? null
        ]);
        
        $insertedCount++;
    }
    
    $db->commit();
    // Re-enable foreign key checks
    $db->exec('SET FOREIGN_KEY_CHECKS=1');
    
    api_success([
        'inserted_count' => $insertedCount,
        'total_records' => count($allRemoteMountpoints)
    ], "Đồng bộ hoàn tất! Đã cập nhật {$insertedCount} mountpoint từ API RTK.");
    
} catch (Exception $e) {
    $db->rollBack();
    api_error('Lỗi khi đồng bộ: ' . $e->getMessage(), 500);
}
