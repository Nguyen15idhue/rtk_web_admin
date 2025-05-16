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
    global $pdo; // or your DB connection
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

// thêm hàm kiểm tra tình trạng upload minh chứng
function get_payment_proof_status($registration_id) {
    global $pdo;
    $sql = "
        SELECT payment_image 
        FROM transaction_history 
        WHERE registration_id = ? 
          AND payment_image IS NOT NULL 
          AND payment_image <> '' 
        ORDER BY created_at DESC 
        LIMIT 1
    ";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$registration_id]);
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    if ($row && !empty($row['payment_image'])) {
        return " <span class='text-green-500'>(Đã upload minh chứng)</span>";
    }
    return " <span class='text-red-500'>(Chưa upload minh chứng)</span>";
}

/**
 * Formats an activity log entry into a user-friendly message and icon.
 *
 * @param array $log The activity log data array.
 * @return array An array containing the formatted 'icon', 'message', and 'time'.
 */
function format_activity_log($log) {
    $actor = htmlspecialchars($log['actor_name'] ?? 'System'); // Add null coalescing for safety
    $action = htmlspecialchars($log['action']);
    $entity_type = htmlspecialchars($log['entity_type']);
    $entity_id = htmlspecialchars($log['entity_id']);
    // Ensure created_at exists and is valid before formatting
    $time = isset($log['created_at']) ? date('H:i d/m/Y', strtotime($log['created_at'])) : 'N/A';

    $message = "";
    $icon = "fas fa-info-circle text-gray-500"; // Default icon

    switch ($action) {
        case 'purchase':
            // decode purchase details
            $details      = json_decode($log['new_values'] ?? '', true) ?: [];
            $package      = htmlspecialchars($details['package'] ?? '');
            $accounts     = isset($details['selected_accounts']) && is_array($details['selected_accounts'])
                             ? count($details['selected_accounts']) : 0;
            $registration = $details['registration_id'] ?? null;
            $voucher      = $registration
                             ? get_voucher_code_by_registration($registration)
                             : '';
            $location     = htmlspecialchars($details['location'] ?? '');
            // updated: bold accounts and show voucher code in ()
            $message = "<strong class='font-medium'>{$actor}</strong> đã đăng ký gói "
                     ."<strong class='font-medium'>{$package}</strong> với "
                     ."<strong class='font-medium'>{$accounts}</strong> tài khoản "
                     .($voucher ? "(<strong class='font-medium'>Voucher: {$voucher}</strong>)" : "")
                     .($location ? " tại <strong class='font-medium'>{$location}</strong>." : ".");
            $icon    = "fas fa-shopping-cart text-green-500";
            // check payment proof upload status
            if ($registration) {
                $message .= get_payment_proof_status($registration);
            }
            break;
        case 'create_support_request':
            $message = "<strong class='font-medium'>{$actor}</strong> đã tạo yêu cầu hỗ trợ #<strong>{$entity_id}</strong>.";
            $icon    = "fas fa-headset text-blue-500";
            break;
        case 'request_invoice':
            $details = json_decode($log['new_values'] ?? '', true) ?: [];
            $transaction_id = htmlspecialchars($details['transaction_history_id'] ?? $entity_id); // Sử dụng transaction_history_id từ new_values, fallback về entity_id
            $message = "<strong class='font-medium'>{$actor}</strong> đã yêu cầu xuất hóa đơn cho giao dịch #<strong class='font-medium'>{$transaction_id}</strong>.";
            $icon    = "fas fa-file-invoice text-blue-500";
            break;
        case 'renewal_request':
            $details = json_decode($log['new_values'] ?? '', true) ?: [];
            $package = htmlspecialchars($details['package'] ?? '');
            $accounts = isset($details['selected_accounts']) && is_array($details['selected_accounts'])
                        ? count($details['selected_accounts']) : 0;
            $registration = $details['registration_id'] ?? null;
            $voucher = $registration ? get_voucher_code_by_registration($registration) : '';
            $location = htmlspecialchars($details['location'] ?? '');
            $message = "<strong class='font-medium'>{$actor}</strong> đã yêu cầu gia hạn gói "
                     ."<strong class='font-medium'>{$package}</strong> với "
                     ."<strong class='font-medium'>{$accounts}</strong> tài khoản "
                     .($voucher ? "(<strong class='font-medium'>{$voucher}</strong>)" : "")
                     .($location ? " tại <strong class='font-medium'>{$location}</strong>." : ".");
            $icon = "fas fa-sync-alt text-purple-500";
            // check payment proof upload status
            if ($registration) {
                $message .= get_payment_proof_status($registration);
            }
            break;
        case 'withdrawal_request':
            $details = json_decode($log['new_values'] ?? '', true) ?: [];
            $amount  = isset($details['amount'])
                       ? number_format((float)$details['amount'],0,',','.')
                       : '';
            $bank    = htmlspecialchars($details['bank_name'] ?? '');
            $accNum  = htmlspecialchars($details['account_number'] ?? '');
            $holder  = htmlspecialchars($details['account_holder'] ?? '');
            $message = "<strong class='font-medium'>{$actor}</strong> đã yêu cầu rút tiền: ";
            if ($amount)  $message .= "<strong class='font-medium'>{$amount}</strong> VND ";
            if ($bank)    $message .= "về ngân hàng <strong class='font-medium'>{$bank}</strong> ";
            if ($accNum || $holder) {
                $message .= "(";
                if ($accNum) $message .= "<strong class='font-medium'>{$accNum}</strong>";
                if ($holder) $message .= " - <strong class='font-medium'>{$holder}</strong>";
                $message .= ")";
            }
            $message .= ".";
            $icon    = "fas fa-money-bill-wave text-yellow-500";
            break;
        default:
            $message = "<strong class='font-medium'>{$actor}</strong> thực hiện: {$action} trên {$entity_type} <strong class='font-medium'>{$entity_id}</strong>.";
    }

    return [
        'icon' => $icon,
        'message' => $message,
        'time' => $time
    ];
}

?>