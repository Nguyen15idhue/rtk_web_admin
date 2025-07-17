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
    // Fetch remote mountpoints from RTK API
    require_once __DIR__ . '/../../classes/RtkApiClient.php';
    $client = new RtkApiClient();
    
    // Get all mountpoints from API with pagination
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
    
    // Fetch current database mountpoints
    $stmt = $db->prepare('SELECT * FROM mount_point');
    $stmt->execute();
    $localMountpoints = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Create lookup maps
    $localMap = [];
    foreach ($localMountpoints as $local) {
        $localMap[$local['mountpoint']] = $local; // Use mountpoint name as key
    }
    
    $remoteMap = [];
    foreach ($allRemoteMountpoints as $remote) {
        $remoteMap[$remote['name']] = $remote; // Use name as key
    }
    
    // Find matches and changes
    $matches = [];
    
    foreach ($localMap as $localName => $localData) {
        if (isset($remoteMap[$localName])) {
            $remoteData = $remoteMap[$localName];
            $changes = [];
            
            // Compare ID - normalize empty/null values
            $localId = trim($localData['id'] ?? '');
            $remoteId = trim($remoteData['id'] ?? '');
            
            if ($localId !== $remoteId) {
                // Log for debugging
                error_log("ID comparison for {$localName}: local='" . var_export($localId, true) . "' vs remote='" . var_export($remoteId, true) . "'");
                
                $changes['id'] = [
                    'old' => $localData['id'],
                    'new' => $remoteData['id']
                ];
            }
            
            // Compare IP - normalize empty/null values
            $localIp = trim($localData['ip'] ?? '');
            $remoteIp = trim($remoteData['ip'] ?? '');
            
            // Treat empty string and null as the same
            if ($localIp === '') $localIp = null;
            if ($remoteIp === '') $remoteIp = null;
            
            if ($localIp !== $remoteIp) {
                // Log for debugging
                error_log("IP comparison for {$localName}: local='" . var_export($localIp, true) . "' vs remote='" . var_export($remoteIp, true) . "'");
                
                $changes['ip'] = [
                    'old' => $localData['ip'],
                    'new' => $remoteData['ip']
                ];
            }
            
            // Compare Port
            if ((int)$localData['port'] !== (int)$remoteData['port']) {
                $changes['port'] = [
                    'old' => $localData['port'],
                    'new' => $remoteData['port']
                ];
            }
            
            // If there are changes, add to matches
            if (!empty($changes)) {
                $matches[] = [
                    'local_id' => $localData['id'],
                    'mountpoint_name' => $localName,
                    'remote_data' => $remoteData,
                    'local_data' => $localData,
                    'changes' => $changes
                ];
            }
        }
    }
    
    api_success(['matches' => $matches], 'Found ' . count($matches) . ' mountpoints with changes.');
    
} catch (Exception $e) {
    api_error('Lỗi khi tìm kiếm thay đổi: ' . $e->getMessage(), 500);
}
