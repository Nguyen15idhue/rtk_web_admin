<?php
/**
 * Quick Search API
 * Provides fast search across users, invoices, stations, etc.
 */

// Include necessary files
$bootstrap_data = require_once __DIR__ . '/../../private/core/page_bootstrap.php';
$db = $bootstrap_data['db'];

header('Content-Type: application/json');

// Check if user is authenticated
if (!isset($_SESSION['admin_id'])) {
    http_response_code(401);
    echo json_encode(['error' => 'Unauthorized']);
    exit;
}

// Get search query from JSON body (if POST) or GET parameter
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $input = json_decode(file_get_contents('php://input'), true);
    $query = isset($input['query']) ? trim($input['query']) : '';
} else {
    $query = trim($_GET['q'] ?? '');
}

if (strlen($query) < 2) {
    echo json_encode(['success' => true, 'results' => []]);
    exit;
}

try {
    $pdo = $db; // Corrected: $db is already the PDO connection
    $results = [];

    $userStmt = $pdo->prepare("
        SELECT username, email, 'user' as type
        FROM user
        WHERE username LIKE ? OR email LIKE ?
        LIMIT 3
    ");
    $searchTerm = '%' . $query . '%';
    $userStmt->execute([$searchTerm, $searchTerm]);
    
    while ($row = $userStmt->fetch(PDO::FETCH_ASSOC)) {
        $results[] = [
            'type' => 'user',
            'title' => 'Người dùng: ' . $row['username'],
            'subtitle' => $row['email'],
            'url' => '/public/pages/user/user_management.php?q=' . urlencode($row['username']),
            'icon' => 'fa-user'
        ];
    }

    // Search invoices (limit to first 3 results)
    $invoiceStmt = $pdo->prepare("
        SELECT i.id, th.amount as total_amount, i.status, 'invoice' as type
        FROM invoice i
        JOIN transaction_history th ON i.transaction_history_id = th.id
        WHERE i.id LIKE ? OR CAST(th.amount AS CHAR) LIKE ?
        LIMIT 3
    ");
    $invoiceStmt->execute([$searchTerm, $searchTerm]);
    
    while ($row = $invoiceStmt->fetch(PDO::FETCH_ASSOC)) {
        $results[] = [
            'type' => 'invoice',
            'title' => 'Hóa đơn #' . $row['id'],
            'subtitle' => number_format($row['total_amount']) . ' VNĐ - ' . ucfirst($row['status']),
            'url' => '/public/pages/invoice/invoice_review.php?invoice_id=' . urlencode($row['id']),
            'icon' => 'fa-file-invoice'
        ];
    }

    // Search stations (limit to first 3 results)
    $stationStmt = $pdo->prepare("
        SELECT id, station_name, status, 'station' as type
        FROM station 
        WHERE id LIKE ? OR station_name LIKE ?
        LIMIT 3
    ");
    $stationStmt->execute([$searchTerm, $searchTerm]);
    
    while ($row = $stationStmt->fetch(PDO::FETCH_ASSOC)) {
        $statusText = $row['status'] === 'active' ? 'Đang hoạt động' : 'Không hoạt động';
        $results[] = [
            'type' => 'station',
            'title' => 'Trạm: ' . $row['station_name'],
            'subtitle' => $row['id'] . ' - ' . $statusText,
            'url' => '/public/pages/station/station_management.php?tab=station&q=' . urlencode($row['station_name']),
            'icon' => 'fa-broadcast-tower'
        ];
    }

    // Limit total results to prevent overwhelming UI
    $results = array_slice($results, 0, 9);

    echo json_encode(['success' => true, 'results' => $results]);

} catch (PDOException $e) {
    error_log("Quick search error: " . $e->getMessage());
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Search failed']);
} catch (Exception $e) {
    error_log("Quick search error: " . $e->getMessage());
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Search failed']);
}
?>
