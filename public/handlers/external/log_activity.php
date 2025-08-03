<?php
// filepath: public/handlers/external/log_activity.php
// MODIFIED: This script now runs as a cron job.
// It scans the activity_log table for entries matching specific actions
// from the last minute and sends Discord notifications.

date_default_timezone_set('UTC'); // Explicitly set PHP to UTC for internal calculations

// --- BEGIN TIME DEBUG ---
$php_utc_now = new DateTime('now', new DateTimeZone('UTC'));
echo "PHP Current Time (UTC): " . $php_utc_now->format('Y-m-d H:i:s P') . "\n";

$oneMinuteAgo_utc_dt = new DateTime('-1 minute', new DateTimeZone('UTC'));
$oneMinuteAgo_utc_str_debug = $oneMinuteAgo_utc_dt->format('Y-m-d H:i:s');

// Define the target timezone for database queries (assumed to be where created_at is stored)
$database_timezone_str = 'Asia/Ho_Chi_Minh'; // IMPORTANT: Change if your DB uses a different local timezone
$database_tz = new DateTimeZone($database_timezone_str);

// Convert the UTC time to the database's local timezone for the query
$oneMinuteAgo_for_query_dt = clone $oneMinuteAgo_utc_dt;
$oneMinuteAgo_for_query_dt->setTimezone($database_tz);
$oneMinuteAgo_for_query = $oneMinuteAgo_for_query_dt->format('Y-m-d H:i:s');

echo "Reference Time (-1 min UTC): " . $oneMinuteAgo_utc_str_debug . "\n";
echo "Query Time (-1 min in " . $database_timezone_str . "): " . $oneMinuteAgo_for_query . "\n";
// --- END TIME DEBUG ---

// Load bootstrap to get $config['db']
$config = require __DIR__ . '/../../../private/core/page_bootstrap.php';
$db = $config['db'];

// Load ActivityLogModel and dashboard_helpers
require_once __DIR__ . '/../../../private/classes/ActivityLogModel.php';
require_once __DIR__ . '/../../../private/utils/dashboard_helpers.php'; // For format_activity_log()

// Actions that trigger notifications
$notifyActions = ['purchase', 'create_support_request', 'request_invoice', 'renewal_request', 'withdrawal_request', 'approve_transaction', 'reject_transaction', 'revert_transaction'];

// Prepare statement to fetch relevant activity logs
// Assumes 'activity_logs.created_at' stores timestamps in the local timezone (e.g., $database_timezone_str)
$placeholders = implode(',', array_fill(0, count($notifyActions), '?'));
$sql = "SELECT user_id, action, entity_type, entity_id, new_values, created_at
        FROM activity_logs
        WHERE action IN ({$placeholders})
          AND user_id IS NOT NULL
          AND created_at >= ? -- This will compare local DB time with the converted local time from PHP
        ORDER BY created_at ASC"; // Process in chronological order

$stmtLogs = $db->prepare($sql);
// Combine actions and the timestamp for parameters.
$params = array_values($notifyActions); // Ensure it's a zero-indexed array
$params[] = $oneMinuteAgo_for_query; // Use the timezone-converted value

try {
    $stmtLogs->execute($params);
    $activities = $stmtLogs->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    // Log error and exit if DB query fails
    $errorMessage = "Cron log_activity: Database query failed: " . $e->getMessage();
    error_log($errorMessage); // Log to PHP error log
    echo $errorMessage . "\n"; // Output to stdout for cron
    exit(1); // Exit with an error code
}

if (empty($activities)) {
    echo "Cron log_activity: No new activities to notify for in the last minute.\n";
    exit;
}

echo "Cron log_activity: Found " . count($activities) . " activities to process.\n";

// Collect all user_ids from activities
$userIds = array_unique(array_filter(array_column($activities, 'user_id')));
$userMap = [];

if (!empty($userIds)) {
    $placeholdersUser = implode(',', array_fill(0, count($userIds), '?'));
    $sqlUser = "SELECT id, username FROM `user` WHERE id IN ({$placeholdersUser})";
    $stmtUsers = $db->prepare($sqlUser);
    try {
        $stmtUsers->execute(array_values($userIds)); // Ensure $userIds is numerically indexed for execute
        $users = $stmtUsers->fetchAll(PDO::FETCH_ASSOC);
        foreach ($users as $user) {
            $userMap[$user['id']] = $user['username'];
        }
    } catch (PDOException $e) {
        $errorMessage = "Cron log_activity: Database query failed while fetching users: " . $e->getMessage();
        error_log($errorMessage);
        echo $errorMessage . "\n";
        // Continue processing, actor names will default to 'System' or 'System (DB Error)'
    }
}


// Prefetch voucher codes for all registration_ids in purchase/renewal_request logs
$registration_ids_for_vouchers = [];
foreach ($activities as $activity) {
    if (in_array($activity['action'], ['purchase', 'renewal_request'])) {
        $details = json_decode($activity['new_values'] ?? '', true);
        if (!empty($details['registration_id'])) {
            $registration_ids_for_vouchers[] = $details['registration_id'];
        }
    }
}
$voucher_details_map = [];
if (!empty($registration_ids_for_vouchers)) {
    $placeholders = implode(',', array_fill(0, count($registration_ids_for_vouchers), '?'));
    $sql_vouchers = "
        SELECT th.registration_id, v.code 
        FROM transaction_history th
        JOIN voucher v ON th.voucher_id = v.id
        WHERE th.registration_id IN ({$placeholders})
          AND th.voucher_id IS NOT NULL
    ";
    $stmt_vouchers = $db->prepare($sql_vouchers);
    $stmt_vouchers->execute($registration_ids_for_vouchers);
    $voucher_rows = $stmt_vouchers->fetchAll(PDO::FETCH_ASSOC);
    foreach ($voucher_rows as $row) {
        $voucher_details_map[$row['registration_id']] = htmlspecialchars($row['code']);
    }
}

$notificationsSent = 0;
$notificationsFailed = 0;

foreach ($activities as $activity) {
    if (empty($activity['user_id'])) {
        // This case should ideally not be reached due to `user_id IS NOT NULL` in SQL
        $logMessage = "Cron log_activity: Skipped activity with empty user_id despite query filter. Action: {$activity['action']}, Entity: {$activity['entity_type']}#{$activity['entity_id']}";
        error_log($logMessage);
        echo $logMessage . "\n";
        continue;
    }

    // Get actor name
    $actor = 'System'; // Default actor name
    if (isset($userMap[$activity['user_id']])) {
        $actor = $userMap[$activity['user_id']];
    } else {
        // User ID was in activities but not found in the batch user fetch (e.g., user deleted, or DB error during batch)
        // Log if user not found, but proceed with 'System' as actor
        $logMessage = "Cron log_activity: User with ID {$activity['user_id']} not found in batch fetch or DB error occurred. Using 'System' as actor name for Action: {$activity['action']}, Entity: {$activity['entity_type']}#{$activity['entity_id']}.";
        error_log($logMessage);
    }

    // Build log details for formatting
    $logDetails = [
        'actor_name'  => $actor,
        'action'      => $activity['action'],
        'entity_type' => $activity['entity_type'],
        'entity_id'   => $activity['entity_id'],
        'new_values'  => $activity['new_values'] ?: '', // Ensure it's a string (empty if null)
        'created_at'  => $activity['created_at'],     // Use the actual creation time from the log
    ];

    // Format the message using the existing helper function, now with voucher_details_map
    $formattedLog = format_activity_log($logDetails, $voucher_details_map);

    if (isset($formattedLog['message']) && !empty(trim($formattedLog['message']))) {
        // Convert HTML to Discord markdown: strip <span>, convert <strong> to **bold**, then remove other tags
        $messageToSend = preg_replace('#<span[^>]*>(.*?)</span>#i', '$1', $formattedLog['message']);
        $messageToSend = preg_replace('#<strong>(.*?)</strong>#i', '**$1**', $messageToSend);
        $messageToSend = strip_tags($messageToSend);
        // Send Discord notification
        $sendSuccess = ActivityLogModel::sendDiscord($messageToSend);
        if ($sendSuccess) {
            echo "Cron log_activity: Notification sent for action: {$activity['action']}, Entity: {$activity['entity_type']}#{$activity['entity_id']}\\n";
            $notificationsSent++;
        } else {
            $logMessage = "Cron log_activity: Failed to send Discord notification for action: {$activity['action']}, Entity: {$activity['entity_type']}#{$activity['entity_id']}. Message: {$messageToSend}";
            error_log($logMessage);
            echo $logMessage . "\\n";
            $notificationsFailed++;
        }
    } else {
        // Log if formatted message is empty or invalid
        $logMessage = "Cron log_activity: Formatted message is empty or invalid for action: {$activity['action']}, Entity: {$activity['entity_type']}#{$activity['entity_id']}. Skipping Telegram notification.";
        error_log($logMessage);
        echo $logMessage . "\n";
    }
    // Optional: Add a small delay if sending many notifications to avoid rate limiting by Telegram API
    if (count($activities) > 10 && ($notificationsSent + $notificationsFailed) < count($activities) -1 ) sleep(1);
}

echo "Cron log_activity: Finished. Notifications sent: {$notificationsSent}. Failed: {$notificationsFailed}.\n";

?>
