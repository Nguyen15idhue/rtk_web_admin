<?php
$config = require_once __DIR__ . '/../../includes/page_bootstrap.php';
$conn     = $config['db'];

if (!isset($_GET['id']) || !filter_var($_GET['id'], FILTER_VALIDATE_INT)) {
    abort('Invalid or missing user ID.', 400);
}

$user_id = (int)$_GET['id'];

if (!$conn) {
    abort('Database connection failed.', 500);
}

try {
    $stmt = $conn->prepare("SELECT id, username, email, phone, is_company, company_name, tax_code, created_at, updated_at, deleted_at FROM user WHERE id = :id");
    $stmt->bindParam(':id', $user_id, PDO::PARAM_INT);
    $stmt->execute();
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user) {
        // Format dates if needed before sending
        $user['created_at_formatted'] = !empty($user['created_at']) ? date('d/m/Y H:i:s', strtotime($user['created_at'])) : '-';
        $user['updated_at_formatted'] = !empty($user['updated_at']) ? date('d/m/Y H:i:s', strtotime($user['updated_at'])) : '-';
        $user['deleted_at_formatted'] = !empty($user['deleted_at']) ? date('d/m/Y H:i:s', strtotime($user['deleted_at'])) : '-';
        $user['status_text'] = empty($user['deleted_at']) ? 'Hoạt động' : 'Vô hiệu hóa';
        $user['account_type_text'] = $user['is_company'] ? 'Công ty' : 'Cá nhân';

        api_success($user);
    } else {
        abort('User not found.', 404);
    }

} catch (PDOException $e) {
    error_log("Database Error fetching user details: " . $e->getMessage());
    error_log("Stack trace: " . $e->getTraceAsString());
    abort('Database error occurred.', 500);
} finally {
    // explicitly free resources
    if (isset($conn)) {
        $conn = null;
    }
    if (isset($db)) {
        $db->close();
        $db = null;
    }
}

?>