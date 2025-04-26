<?php
$bootstrap = require __DIR__ . '/../../includes/page_bootstrap.php';
$db = $bootstrap['db'];
header('Content-Type: application/json; charset=UTF-8');
$q = trim($_GET['email'] ?? '');
if (!$q) {
    echo json_encode(['success' => true, 'users' => []]);
    exit;
}
$stmt = $db->prepare("SELECT id, username, email, phone FROM `user` WHERE email LIKE :e ORDER BY email LIMIT 10");
$stmt->execute([':e' => "%{$q}%"]);
$users = $stmt->fetchAll(PDO::FETCH_ASSOC);
echo json_encode(['success' => true, 'users' => $users]);
