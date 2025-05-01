<?php

// Utility functions can be added here

// Example: Sanitize output
function escape_html($string) {
    return htmlspecialchars($string ?? '', ENT_QUOTES, 'UTF-8');
}

/**
 * Get display properties for admin transaction status.
 * Maps database status ('pending', 'active', 'rejected') to UI display.
 *
 * @param string $status The transaction status from the database.
 * @return array An array containing 'class' and 'text' for the status badge.
 */
function get_admin_transaction_status_display(string $status): array {
    switch (strtolower($status)) {
        case 'pending':
            return ['class' => 'status-pending', 'text' => 'Chờ duyệt'];
        case 'active': // DB 'active' maps to UI 'approved'
            return ['class' => 'status-approved', 'text' => 'Đã duyệt']; // Changed class name for consistency
        case 'rejected':
            return ['class' => 'status-rejected', 'text' => 'Bị từ chối']; // Changed class name for consistency
        default:
            return ['class' => 'status-unknown', 'text' => 'Không xác định'];
    }
}

/**
 * Formats a number as Vietnamese currency.
 *
 * @param float|int|string $amount The amount to format.
 * @param string $currency The currency symbol (default: 'đ').
 * @return string The formatted currency string.
 */
function format_currency($amount, string $currency = 'đ'): string {
    if (!is_numeric($amount)) {
        return 'N/A';
    }
    return number_format((float)$amount, 0, ',', '.') . $currency;
}

/**
 * Formats a datetime string.
 *
 * @param string|null $datetime_string The datetime string to format.
 * @param string $format The desired output format (default: 'd/m/y H:i').
 * @return string The formatted datetime string or 'N/A' on failure.
 */
function format_datetime(?string $datetime_string, string $format = 'd/m/y H:i'): string {
    if (empty($datetime_string)) {
        return 'N/A';
    }
    try {
        $date = new DateTime($datetime_string);
        // Consider timezone if necessary: $date->setTimezone(new DateTimeZone('Asia/Ho_Chi_Minh'));
        return $date->format($format);
    } catch (Exception $e) {
        // Log error if needed: error_log("Error formatting date: {$datetime_string} - " . $e->getMessage());
        return 'N/A';
    }
}

/**
 * Formats a date string into 'd/m/Y' format.
 *
 * @param string|null $date_string The date string to format.
 * @return string The formatted date or '-' if input is empty/invalid.
 */
function format_date(?string $date_string): string {
    if (empty($date_string) || $date_string === '0000-00-00' || $date_string === '0000-00-00 00:00:00') {
        return '-';
    }
    try {
        $date = new DateTime($date_string);
        return $date->format('d/m/Y'); // Format as Day/Month/Year
    } catch (Exception $e) {
        error_log("Error formatting date '{$date_string}': " . $e->getMessage());
        return '-'; // Return '-' on formatting failure
    }
}

/**
 * Generates an HTML status badge for an account status.
 *
 * @param string $status The derived account status string.
 * @return string HTML span element for the badge.
 */
function get_account_status_badge(?string $status): string {
     $status = strtolower($status ?? 'unknown');
    switch ($status) {
        case 'active': // Registration active, enabled, not expired
            return '<span class="status-badge badge-green">Hoạt động</span>';
        case 'pending': // Registration pending
            return '<span class="status-badge badge-yellow">Chờ KH</span>';
        case 'expired': // Registration active, but expired
            return '<span class="status-badge badge-red">Hết hạn</span>';
        case 'suspended': // Registration active, but disabled (enabled=0)
            return '<span class="status-badge badge-gray">Đình chỉ</span>'; // Using gray for suspended/disabled
        case 'rejected': // Registration rejected
            return '<span class="status-badge badge-red">Bị từ chối</span>'; // Using red for rejected
        default:
            return '<span class="status-badge badge-gray">Không xác định</span>';
    }
}

/**
 * Generates HTML action buttons for an account based on its details.
 *
 * @param array $account Associative array containing account details, including 'id', 'enabled', and 'derived_status'.
 * @return string HTML div containing action buttons.
 */
function get_account_action_buttons(array $account): string {
    $id = htmlspecialchars($account['id'] ?? '');
    if (empty($id)) return ''; // No ID, no buttons

    $status = strtolower($account['derived_status'] ?? 'unknown');
    $isEnabled = isset($account['enabled']) ? (bool)$account['enabled'] : false; // Get the actual enabled state
    $buttons = '';

    // View Button (Always available)
    $buttons .= '<button class="btn-icon btn-view" title="Xem" onclick="viewAccountDetails(\'' . $id . '\')"><i class="fas fa-eye"></i></button>';

    // Edit Button (Available unless rejected or maybe expired?)
    // Allow editing active and suspended. Maybe pending? Not rejected.
    if (in_array($status, ['active', 'suspended', 'pending', 'expired'])) {
        $buttons .= '<button class="btn-icon btn-edit" title="Sửa" onclick="openEditAccountModal(\'' . $id . '\')" data-permission="account_edit"><i class="fas fa-pencil-alt"></i></button>';
    }

    if (!in_array($status, ['active'])) {
        $buttons .= '<button class="btn-icon btn-danger" title="Xóa" onclick="deleteAccount(\'' . $id . '\', event)" data-permission="account_delete"><i class="fas fa-trash-alt"></i></button>';
    }

    // Status Toggle / Specific Actions based on derived status AND enabled flag
    switch ($status) {
        case 'active': // Is currently active (enabled=1, not expired, reg=active)
            // Button to Suspend (Set enabled=0)
            $buttons .= '<button class="btn-icon btn-reject" title="Đình chỉ (Disable)" onclick="toggleAccountStatus(\'' . $id . '\', \'suspend\', event)" data-permission="account_status_toggle"><i class="fas fa-ban"></i></button>';
            break;
        case 'suspended': // Is currently suspended (enabled=0, reg=active)
            // Button to Reactivate (Set enabled=1)
            $buttons .= '<button class="btn-icon btn-approve" title="Kích hoạt lại (Enable)" onclick="toggleAccountStatus(\'' . $id . '\', \'reactivate\', event)" data-permission="account_status_toggle"><i class="fas fa-play-circle"></i></button>';
            break;
        case 'expired':
            break;
        case 'rejected':
            break;
        case 'pending':
             $buttons .= '<button class="btn-icon btn-approve" title="Kích hoạt lại (Enable)" onclick="toggleAccountStatus(\'' . $id . '\', \'reactivate\', event)" data-permission="account_status_toggle"><i class="fas fa-play-circle"></i></button>';
             break;
        case 'unknown':
            // Log error, maybe show delete button?
            error_log("Unknown derived_status ('$status') for account ID: " . $id . " in get_account_action_buttons");
            $buttons .= '<button class="btn-icon btn-danger" title="Xóa (Trạng thái không xác định)" onclick="deleteAccount(\'' . $id . '\', event)" data-permission="account_delete"><i class="fas fa-trash-alt"></i></button>';
            break;
    }

    return '<div class="action-buttons">' . $buttons . '</div>';
}

// Example: Generate a simple CSRF token
function generate_csrf_token() {
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

// Example: Verify CSRF token
function verify_csrf_token($token) {
    return isset($_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], $token);
}

/**
 * Sends a JSON response with an appropriate HTTP status code.
 *
 * @param mixed $data The data to encode as JSON.
 * @param int $statusCode The HTTP status code to send (default: 200).
 */
function send_json_response($data, int $statusCode = 200): void {
    // Ensure headers are not already sent
    if (!headers_sent()) {
        header('Content-Type: application/json; charset=utf-8');
        http_response_code($statusCode);
    } else {
        // Log an error if headers are already sent, as we can't set them now.
        error_log("Warning: Headers already sent. Cannot set Content-Type or status code for JSON response.");
    }
    echo json_encode($data);
    exit; // Terminate script execution after sending the response
}

// Add more helper functions as needed...

?>