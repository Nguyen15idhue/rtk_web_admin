<?php
// filepath: public/handlers/station/mountpoint_index.php

$bootstrap = require_once __DIR__ . '/../../../private/core/page_bootstrap.php';
$base_url = $bootstrap['base_url'] ?? '/';

$action = $_GET['action'] ?? $_POST['action'] ?? '';

// Handle sync actions
// Removed sync_mountpoint_ids functionality as per requirement
// if ($action === 'sync_mountpoint_ids') {
//     require_once __DIR__ . '/../../../private/actions/station/sync_mountpoint_ids.php';
//     exit;
// }

// Removed apply_sync_mountpoints functionality as per requirement  
// if ($action === 'apply_sync_mountpoints') {
//     require_once __DIR__ . '/../../../private/actions/station/apply_sync_mountpoints.php';
//     exit;
// }

if ($action === 'full_sync_mountpoints') {
    require_once __DIR__ . '/../../../private/actions/station/full_sync_mountpoints.php';
    exit;
}

if ($action === 'update_mountpoint_ip_port') {
    require_once __DIR__ . '/../../../private/actions/station/mountpoint_update_ip_port.php';
    exit;
}

// Handle regular mountpoint actions
require_once __DIR__ . '/../../../private/actions/station/mountpoint_actions.php';
exit;
