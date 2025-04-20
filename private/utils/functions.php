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

// Add more helper functions as needed...

?>