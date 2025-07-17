<?php
header('Content-Type: application/json; charset=UTF-8');

// Bootstrap and auth
$bootstrap = require __DIR__ . '/../../core/page_bootstrap.php';
$db = $bootstrap['db'];
require_once __DIR__ . '/../../classes/Auth.php';
Auth::ensureAuthorized('account_management_edit');

// Fetch remote RTK accounts via API
require_once __DIR__ . '/../../classes/RtkApiClient.php';
$client = new RtkApiClient();
$params = ['page' => 1, 'size' => 1000];
$resp = $client->request('GET', '/openapi/broadcast/users', $params);
if (!$resp['success']) {
    api_error($resp['error'] ?? 'Failed to fetch remote accounts');
}
$records = $resp['data']['records'] ?? [];

// Fetch local accounts with all relevant fields
$stmt = $db->query('SELECT id, username_acc, password_acc, start_time, end_time, enabled, concurrent_user, caster FROM survey_account');
$localAccounts = $stmt->fetchAll(PDO::FETCH_ASSOC);
$localMap = [];
foreach ($localAccounts as $la) {
    $localMap[$la['username_acc']] = $la;
}

// Helper function to convert timestamp to date only (no time)
function timestampToDate($timestamp) {
    if (!$timestamp) return null;
    return date('Y-m-d', $timestamp / 1000); // Convert milliseconds to seconds, date only
}

// Helper function to extract date from datetime string
function extractDate($datetime) {
    if (!$datetime) return null;
    return date('Y-m-d', strtotime($datetime));
}

// Find matches by username and collect all sync data
$matches = [];
foreach ($records as $r) {
    $name = $r['name'] ?? null;
    if ($name !== null && isset($localMap[$name])) {
        $localAccount = $localMap[$name];
        
        // Prepare remote data for comparison
        $remoteData = [
            'id' => $r['id'],
            'password_acc' => $r['userPwd'] ?? '',
            'start_time' => timestampToDate($r['startTime'] ?? null),
            'end_time' => timestampToDate($r['endTime'] ?? null),
            'enabled' => $r['enabled'] ?? 1,
            'concurrent_user' => $r['numOnline'] ?? 1,
            // 'caster' => !empty($r['mountNames']) ? implode(',', $r['mountNames']) : null
        ];
        
        // Check what fields need updating (compare dates only for time fields)
        $changes = [];
        if ($localAccount['id'] !== $remoteData['id']) {
            $changes['id'] = ['old' => $localAccount['id'], 'new' => $remoteData['id']];
        }
        if ($localAccount['password_acc'] !== $remoteData['password_acc']) {
            $changes['password_acc'] = ['old' => $localAccount['password_acc'], 'new' => $remoteData['password_acc']];
        }
        if (extractDate($localAccount['start_time']) !== $remoteData['start_time']) {
            $changes['start_time'] = ['old' => extractDate($localAccount['start_time']), 'new' => $remoteData['start_time']];
        }
        if (extractDate($localAccount['end_time']) !== $remoteData['end_time']) {
            $changes['end_time'] = ['old' => extractDate($localAccount['end_time']), 'new' => $remoteData['end_time']];
        }
        if ($localAccount['enabled'] != $remoteData['enabled']) {
            $changes['enabled'] = ['old' => $localAccount['enabled'], 'new' => $remoteData['enabled']];
        }
        if ($localAccount['concurrent_user'] != $remoteData['concurrent_user']) {
            $changes['concurrent_user'] = ['old' => $localAccount['concurrent_user'], 'new' => $remoteData['concurrent_user']];
        }
        // if ($localAccount['caster'] !== $remoteData['caster']) {
        //     $changes['caster'] = ['old' => $localAccount['caster'], 'new' => $remoteData['caster']];
        // }
        
        // Only include accounts that have changes
        if (!empty($changes)) {
            $matches[] = [
                'local_id' => $localAccount['id'],
                'username' => $name,
                'remote_id' => $remoteData['id'],
                'remote_data' => $remoteData,
                'changes' => $changes
            ];
        }
    }
}

// Return matches for frontend confirmation
api_success(['matches' => $matches], 'Found matches to sync');
