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

// Fetch local accounts usernames and IDs
$stmt = $db->query('SELECT id, username_acc FROM survey_account');
$localAccounts = $stmt->fetchAll(PDO::FETCH_ASSOC);
$localMap = [];
foreach ($localAccounts as $la) {
    $localMap[$la['username_acc']] = $la['id'];
}

// Find matches by username
$matches = [];
foreach ($records as $r) {
    $name = $r['name'] ?? null;
    if ($name !== null && isset($localMap[$name])) {
        $matches[] = [
            'local_id'  => $localMap[$name],
            'username'  => $name,
            'remote_id' => $r['id'],
        ];
    }
}

// Return matches for frontend confirmation
api_success(['matches' => $matches], 'Found matches to sync');
