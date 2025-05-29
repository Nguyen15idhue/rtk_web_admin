<?php
// filepath: private/actions/invoice/get_transaction_details.php
declare(strict_types=1);

require_once __DIR__ . '/../../utils/functions.php';  

// Prevent direct access
if (basename(__FILE__) === basename($_SERVER['SCRIPT_FILENAME'])) {
    api_forbidden('Forbidden: Direct access not allowed');
}

$bootstrap = require_once __DIR__ . '/../../core/page_bootstrap.php';

Auth::ensureAuthorized('revenue_management_view');
if ($bootstrap === true) {
    $bootstrap = $GLOBALS['__PAGE_BOOTSTRAP_INSTANCE_DATA__'];
}
$db = $bootstrap['db'];

// Đảm bảo đóng PDO khi script kết thúc
register_shutdown_function(function() use (&$db) {
    $db = null;
});

/**
 * Get detailed transaction information by transaction ID
 *
 * @param int $transactionId Transaction ID from transaction_history table
 * @return array|null Transaction details or null if not found
 */
function get_transaction_details(int $transactionId): ?array {
    global $db;
    
    try {
        if (!$db) {
            throw new Exception("Database connection failed.");
        }

        $sql = "
            SELECT
                th.id AS transaction_id,
                th.transaction_type,
                th.amount,
                th.status,
                th.created_at AS request_date,
                th.updated_at,
                th.payment_image,
                th.voucher_id,
                u.id AS user_id,
                u.email AS user_email,
                u.username AS user_name,
                u.phone AS user_phone,
                p.id AS package_id,
                p.name AS package_name,
                p.duration_in_days,
                p.price AS package_price,
                r.id AS registration_id,
                r.start_time,
                r.end_time,
                r.num_account,
                r.rejection_reason,
                l.province,
                l.district,
                v.code AS voucher_code,
                v.discount_value,
                v.voucher_type,
                v.description AS voucher_description,
                v.start_date AS voucher_start_date,
                v.end_date AS voucher_end_date,
                v.max_discount AS voucher_max_discount,
                v.min_order_value AS voucher_min_order_value
            FROM transaction_history th
            JOIN registration r ON th.registration_id = r.id AND r.deleted_at IS NULL
            JOIN user u ON r.user_id = u.id
            JOIN package p ON r.package_id = p.id
            LEFT JOIN location l ON r.location_id = l.id
            LEFT JOIN voucher v ON th.voucher_id = v.id
            WHERE th.id = :transaction_id
            LIMIT 1
        ";

        $stmt = $db->prepare($sql);
        $stmt->bindValue(':transaction_id', $transactionId, PDO::PARAM_INT);
        $stmt->execute();
        
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if (!$result) {
            return null;
        }

        // Format the data for better presentation
        $result['formatted_amount'] = number_format($result['amount'], 0, ',', '.') . ' đ';
        $result['formatted_request_date'] = date('d/m/Y H:i:s', strtotime($result['request_date']));
        $result['formatted_updated_at'] = $result['updated_at'] ? date('d/m/Y H:i:s', strtotime($result['updated_at'])) : '';
        
        // Format transaction type
        $result['transaction_type_text'] = $result['transaction_type'] === 'renewal' ? 'Gia hạn' : 'Đăng ký mới';
        
        // Format status
        $status_map = [
            'pending' => ['text' => 'Chờ duyệt', 'class' => 'status-pending'],
            'completed' => ['text' => 'Đã duyệt', 'class' => 'status-approved'],
            'failed' => ['text' => 'Từ chối', 'class' => 'status-rejected'],
            'active' => ['text' => 'Đã duyệt', 'class' => 'status-approved'],
            'rejected' => ['text' => 'Từ chối', 'class' => 'status-rejected']
        ];
        
        $status_info = $status_map[$result['status']] ?? ['text' => 'Không xác định', 'class' => 'status-unknown'];
        $result['status_text'] = $status_info['text'];
        $result['status_class'] = $status_info['class'];
        
        // Format voucher information
        if ($result['voucher_code']) {
            if ($result['voucher_type'] === 'percentage_discount') {
                $result['voucher_display'] = "Giảm {$result['discount_value']}%";
            } elseif ($result['voucher_type'] === 'fixed_discount') {
                $result['voucher_display'] = "Giảm " . number_format($result['discount_value'], 0, ',', '.') . " đ";
            } elseif ($result['voucher_type'] === 'extend_duration') {
                $result['voucher_display'] = "Gia hạn {$result['discount_value']} ngày";
            } else {
                $result['voucher_display'] = $result['voucher_description'] ?: 'Voucher khuyến mãi';
            }
        }
        
        // Format package duration
        if ($result['duration_in_days']) {
            $result['package_duration_text'] = $result['duration_in_days'] . ' ngày';
        }
        
        // Format location
        $location_parts = array_filter([$result['district'], $result['province']]);
        $result['location_text'] = implode(', ', $location_parts);
        
        // Format start and end time
        if ($result['start_time']) {
            $result['formatted_start_time'] = date('d/m/Y H:i', strtotime($result['start_time']));
        }
        if ($result['end_time']) {
            $result['formatted_end_time'] = date('d/m/Y H:i', strtotime($result['end_time']));
        }
        
        // Generate payment proof URL if exists
        if ($result['payment_image']) {
            $result['payment_proof_url'] = IMAGE_HOST_BASE_URL . 'public/uploads/payment_proofs/' . $result['payment_image'];
        }

        return $result;

    } catch (PDOException $e) {
        error_log("Database error in get_transaction_details: " . $e->getMessage());
        return null;
    } catch (Exception $e) {
        error_log("Error in get_transaction_details: " . $e->getMessage());
        return null;
    }
}

// Handle AJAX request
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $transactionId = isset($_GET['id']) ? (int)$_GET['id'] : 0;
    
    if ($transactionId <= 0) {
        api_error('Invalid transaction ID', 400);
    }
    
    $details = get_transaction_details($transactionId);
    
    if ($details === null) {
        api_error('Transaction not found', 404);
    }
    
    api_success($details, 'Transaction details fetched successfully');
}
?>
