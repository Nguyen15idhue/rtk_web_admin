<?php
// filepath: private\utils\dashboard_helpers.php

/**
 * Formats a number into a short representation (K for thousands, M for millions).
 *
 * @param int|float $n The number to format.
 * @return string The formatted number string.
 */
function format_number_short($n) {
    if ($n >= 1000000) {
        return round($n / 1000000, 1) . 'M';
    } elseif ($n >= 1000) {
        return round($n / 1000, 1) . 'K';
    }
    return number_format($n);
}

// new: fetch voucher code via transaction_history → voucher
function get_voucher_code_by_registration($registration_id) {
    $pdo = Database::getInstance()->getConnection(); // or your DB connection
    $sql = "
        SELECT v.code 
        FROM transaction_history th
        JOIN voucher v ON th.voucher_id = v.id
        WHERE th.registration_id = ?
        ORDER BY th.created_at DESC
        LIMIT 1
    ";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$registration_id]);
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    return $row ? htmlspecialchars($row['code']) : '';
}

/**
 * Formats an activity log entry into a user-friendly message and icon.
 *
 * @param array $log The activity log data array.
 * @param array $voucher_details_map Optional. A map of registration_id => voucher_code to avoid N+1 queries.
 * @return array An array containing the formatted 'icon', 'message', 'time', 'action_type', 'details_url', 'required_permission', and 'customer_id'.
 */
function format_activity_log($log, $voucher_details_map = []) { // Added $voucher_details_map parameter
    $actor = htmlspecialchars($log['actor_name'] ?? 'System'); // Add null coalescing for safety
    $action = htmlspecialchars($log['action']);
    $entity_type = htmlspecialchars($log['entity_type']);
    $entity_id = htmlspecialchars($log['entity_id']);
    // Ensure created_at exists and is valid before formatting
    $time = isset($log['created_at']) ? date('H:i d/m/Y', strtotime($log['created_at'])) : 'N/A';

    $message = "";
    $icon = "fas fa-info-circle text-gray-500"; // Default icon
    $action_type = null;
    $details_url = null;
    $required_permission = null;

    switch ($action) {
        case 'purchase':
            // shortened purchase message
            $details      = json_decode($log['new_values'] ?? '', true) ?: [];
            $package      = htmlspecialchars($details['package'] ?? '');
            $accounts     = isset($details['selected_accounts']) && is_array($details['selected_accounts'])
                             ? count($details['selected_accounts']) : 0;
            $registration = $details['registration_id'] ?? null;
            $voucher      = '';
            if ($registration && isset($voucher_details_map[$registration])) {
                $voucher = $voucher_details_map[$registration];
            } else if ($registration) {
                // Fallback if not pre-fetched, or if called without the map.
                $voucher = get_voucher_code_by_registration($registration); 
            }
            $location     = htmlspecialchars($details['location'] ?? '');
            $message = "<strong>{$actor}</strong> đăng ký <strong>{$package}</strong> x{$accounts} tài khoản"
                     .($voucher ? " (<strong>Voucher: {$voucher}</strong>)" : "")
                     .($location ? " tại <strong>{$location}</strong>" : "").".";
            $icon    = "fas fa-shopping-cart text-green-500";
            if (isset($registration)) {
                $action_type = 'navigate';
                $details_url = BASE_URL . 'public/pages/purchase/invoice_management.php#transaction-' . $registration;
            }
            $required_permission = 'invoice_management_view';
            break;
        case 'create_support_request':
            // shortened support request message
            $details  = json_decode($log['new_values'] ?? '', true) ?: [];
            $subject  = htmlspecialchars($details['subject'] ?? '');
            $category = htmlspecialchars($details['category'] ?? '');
            $message = "<strong>{$actor}</strong> tạo yêu cầu hỗ trợ #{$entity_id}"
                     .($category ? " [<strong>{$category}</strong>]" : "")
                     .($subject  ? ": <strong>{$subject}</strong>" : "").".";
            $icon    = "fas fa-headset text-blue-500";
            $action_type    = 'navigate';
            $details_url    = BASE_URL . 'public/pages/support/support_management.php#support-' . $entity_id;
            $required_permission = 'support_management_view';
            break;
        case 'request_invoice':
            // shortened invoice request
            $details = json_decode($log['new_values'] ?? '', true) ?: [];
            $transaction_id = htmlspecialchars($details['transaction_history_id'] ?? $entity_id); // Sử dụng transaction_history_id từ new_values, fallback về entity_id
            $message = "<strong>{$actor}</strong> yêu cầu hóa đơn #<strong>{$transaction_id}</strong>.";
            $icon    = "fas fa-file-invoice text-blue-500";
            $action_type = 'navigate';
            $details_url = BASE_URL . 'public/pages/invoice/invoice_review.php#invoice-' . $transaction_id;
            $required_permission = 'invoice_review_view';
            break;
        case 'renewal_request':
            // shortened renewal message
            $details = json_decode($log['new_values'] ?? '', true) ?: [];
            $package = htmlspecialchars($details['package'] ?? '');
            $accounts = isset($details['selected_accounts']) && is_array($details['selected_accounts'])
                        ? count($details['selected_accounts']) : 0;
            $registration = $details['registration_id'] ?? null;
            $voucher = '';
            if ($registration && isset($voucher_details_map[$registration])) {
                $voucher = $voucher_details_map[$registration];
            } else if ($registration) {
                // Fallback if not pre-fetched, or if called without the map.
                $voucher = get_voucher_code_by_registration($registration);
            }
            $location = htmlspecialchars($details['location'] ?? '');
            $message = "<strong>{$actor}</strong> gia hạn <strong>{$package}</strong> x{$accounts} tài khoản"
                     .($voucher ? " (<strong>Voucher: {$voucher}</strong>)" : "")
                     .($location ? " tại <strong>{$location}</strong>" : "").".";
            $icon = "fas fa-sync-alt text-purple-500";
            if (isset($registration)) {
                $action_type = 'navigate';
                $details_url = BASE_URL . 'public/pages/purchase/invoice_management.php#transaction-' . $registration;
            }
            $required_permission = 'invoice_management_view';
            break;
        case 'withdrawal_request':
            // shortened withdrawal message
            $details = json_decode($log['new_values'] ?? '', true) ?: [];
            $amount  = isset($details['amount'])
                       ? number_format((float)$details['amount'],0,',','.')
                       : '';
            $bank    = htmlspecialchars($details['bank_name'] ?? '');
            $accNum  = htmlspecialchars($details['account_number'] ?? '');
            $holder  = htmlspecialchars($details['account_holder'] ?? '');
            $message = "<strong>{$actor}</strong> rút <strong>{$amount} VND</strong> về <strong>{$bank}</strong>"
                     .($accNum || $holder ? " (<strong>{$accNum}</strong>" .($holder ? " - <strong>{$holder}</strong>" : "").")" : "").".";
            $icon    = "fas fa-money-bill-wave text-yellow-500";
            $action_type = 'navigate';
            $details_url = BASE_URL . 'public/pages/referral/referral_management.php?tab=withdrawals#request-' . $entity_id;
            $required_permission = 'referral_management_view';
            break;
        default:
            $message = "<strong class='font-medium'>{$actor}</strong> thực hiện: {$action} trên {$entity_type} <strong class='font-medium'>{$entity_id}</strong>.";
    }

    // parse customer_id from new_values for use on client side
    $decodedNew = json_decode($log['new_values'] ?? '', true);
    $customer_id = is_array($decodedNew) && !empty($decodedNew['customer_id']) ? $decodedNew['customer_id'] : null;

    return [
        'icon' => $icon,
        'message' => $message,
        'time' => $time,
        'action_type' => $action_type,
        'details_url' => $details_url,
        'required_permission' => $required_permission,
        'customer_id' => $customer_id,
    ];
}

?>