<?php
$bootstrap = require __DIR__ . '/../../includes/page_bootstrap.php';
$db = $bootstrap['db'];
// Đảm bảo đóng PDO khi script kết thúc
register_shutdown_function(function() use (&$db) {
    $db = null;
});
header('Content-Type: application/json; charset=UTF-8');

$q = trim($_GET['email'] ?? '');
if ($q === '') {
    api_success(['users' => []], 'No query provided.');
}

$stmt = $db->prepare("SELECT id, username, email, phone FROM `user` WHERE email LIKE :e ORDER BY email LIMIT 10");
$stmt->execute([':e' => "%{$q}%"]);
$users = $stmt->fetchAll(PDO::FETCH_ASSOC);

api_success(['users' => $users], 'User suggestions fetched.');
