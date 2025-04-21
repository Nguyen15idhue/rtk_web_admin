<?php
// filepath: e:\Application\laragon\www\rtk_web_admin\private\utils\dashboard_helpers.php

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
        case 'login':
            $message = "<strong class='font-medium'>{$actor}</strong> đã đăng nhập.";
            $icon = "fas fa-sign-in-alt text-blue-500";
            break;
        case 'logout':
            $message = "<strong class='font-medium'>{$actor}</strong> đã đăng xuất.";
            $icon = "fas fa-sign-out-alt text-gray-500";
            break;
        case 'create':
            $message = "<strong class='font-medium'>{$actor}</strong> đã tạo {$entity_type} <strong class='font-medium'>{$entity_id}</strong>.";
            $icon = "fas fa-plus-circle text-green-500";
            break;
        case 'update':
             $message = "<strong class='font-medium'>{$actor}</strong> đã cập nhật {$entity_type} <strong class='font-medium'>{$entity_id}</strong>.";
             $icon = "fas fa-edit text-orange-500";
             break;
        case 'delete':
             $message = "<strong class='font-medium'>{$actor}</strong> đã xóa {$entity_type} <strong class='font-medium'>{$entity_id}</strong>.";
             $icon = "fas fa-trash-alt text-red-500";
             break;
        case 'approve':
             $message = "<strong class='font-medium'>{$actor}</strong> đã duyệt {$entity_type} <strong class='font-medium'>{$entity_id}</strong>.";
             $icon = "fas fa-check-circle text-green-500";
             break;
        case 'reject':
             $message = "<strong class='font-medium'>{$actor}</strong> đã từ chối {$entity_type} <strong class='font-medium'>{$entity_id}</strong>.";
             $icon = "fas fa-ban text-red-500";
             break;
        case 'register_package':
             $message = "<strong class='font-medium'>{$actor}</strong> đã đăng ký gói ({$entity_type} <strong class='font-medium'>{$entity_id}</strong>).";
             $icon = "fas fa-user-plus text-blue-500";
             break;
        case 'upload_payment':
             $message = "<strong class='font-medium'>{$actor}</strong> đã tải lên MC TT cho GD <strong class='font-medium'>{$entity_id}</strong>.";
             $icon = "fas fa-receipt text-yellow-600";
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