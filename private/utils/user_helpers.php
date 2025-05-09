<?php
// filepath: private\utils\user_helpers.php
declare(strict_types=1);

if (basename(__FILE__) === basename($_SERVER['SCRIPT_FILENAME'])) {
    // Load chung error handler để có abort()
    require_once __DIR__ . '/../core/error_handler.php';
    abort('Forbidden: Direct access is not allowed.', 403);
}

/**
 * Returns HTML span element for user status based on deleted_at timestamp.
 *
 * @param array|null $user User data array containing 'deleted_at' key, or null.
 * @return string HTML span element indicating status.
 */
function get_user_status_display(?array $user): string {
    if ($user === null) {
        return '<span class="status-inactive">Không xác định</span>'; // Handle null case
    }
    // Check if 'deleted_at' key exists and is not null or empty string
    $is_inactive = isset($user['deleted_at']) && $user['deleted_at'] !== null && $user['deleted_at'] !== '';
    return $is_inactive
        ? '<span class="status-inactive">Vô hiệu hóa</span>'
        : '<span class="status-active">Hoạt động</span>';
}
?>
