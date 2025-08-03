<?php
// private/actions/station/mountpoint_update_ip_port.php
$bootstrap = require_once __DIR__ . '/../../core/page_bootstrap.php';
$base_url = $bootstrap['base_url'] ?? '/';

require_once __DIR__ . '/../../classes/StationManager/MountPointModel.php';
Auth::ensureAuthorized('station_management_edit');

header('Content-Type: application/json; charset=UTF-8');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Chỉ hỗ trợ phương thức POST.']);
    exit;
}

$mountpointId = $_POST['mountpoint_id'] ?? '';
$ip = trim($_POST['ip'] ?? '');
$port = trim($_POST['port'] ?? '');

if (!$mountpointId) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Thiếu mountpoint_id.']);
    exit;
}
if ($ip === '') $ip = 'rtk.taikhoandodac.vn';
if ($port === '' || !is_numeric($port)) $port = 1509;

$model = new MountPointModel();

// Lấy giá trị hiện tại để kiểm tra thay đổi
$current = null;
try {
    $db = Database::getInstance()->getConnection();
    $stmt = $db->prepare("SELECT ip, port FROM mount_point WHERE id = :id LIMIT 1");
    $stmt->bindParam(':id', $mountpointId, PDO::PARAM_STR);
    $stmt->execute();
    $current = $stmt->fetch(PDO::FETCH_ASSOC);
} catch (Exception $e) {}

if ($current && $current['ip'] === $ip && (string)$current['port'] === (string)(int)$port) {
    // Không thay đổi gì, không gửi thông báo thành công
    echo json_encode(['success' => true, 'message' => 'Không có thay đổi.']);
    exit;
}

$ok = $model->updateMountPointIpPort($mountpointId, $ip, (int)$port);
if ($ok) {
    echo json_encode(['success' => true, 'message' => 'Đã cập nhật IP/Port mountpoint thành công.']);
} else {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Cập nhật IP/Port mountpoint thất bại.']);
}
