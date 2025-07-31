<?php
// Trang hiển thị nhóm tự động của user dựa trên voucher đã sử dụng
$GLOBALS['required_permission'] = 'user_management';
$bootstrap_data = require_once __DIR__ . '/../../../private/core/page_bootstrap.php';
$base_url = $bootstrap_data['base_url'];
$private_layouts_path = $bootstrap_data['private_layouts_path'];

require_once BASE_PATH . '/classes/UserAutoGroupModel.php';
require_once BASE_PATH . '/classes/UserModel.php';

$model = new UserAutoGroupModel();
$userModel = new UserModel();

// Lấy user có voucher đầu tiên (và customer_source)

$pdo = Database::getInstance()->getConnection();

// Đọc rule từ file cấu hình
$rules_file = __DIR__ . '/customer_source_rules.json';
$rules = file_exists($rules_file) ? json_decode(file_get_contents($rules_file), true) : [];
if (!is_array($rules)) $rules = [];


// Lấy tất cả user từng dùng voucher khớp rule
$sql = "SELECT u.id, u.username, u.email, u.customer_source, v.code as voucher
FROM user u
JOIN user_voucher_usage uvu ON u.id = uvu.user_id
JOIN voucher v ON uvu.voucher_id = v.id
ORDER BY u.id ASC";
$stmt = $pdo->prepare($sql);
$stmt->execute();
$all_users = $stmt->fetchAll(PDO::FETCH_ASSOC);
$users = [];
foreach ($all_users as $user) {
    $voucher = $user['voucher'] ?? '';
    foreach ($rules as $rule) {
        if (isset($rule['voucher']) && $rule['voucher'] !== '' && $voucher === $rule['voucher']) {
            $user['target_group'] = $rule['group'];
            $users[] = $user;
            break;
        }
    }
}

include $private_layouts_path . 'admin_header.php';
include $private_layouts_path . 'admin_sidebar.php';
?>
<main class="content-wrapper">
    <div class="content-section">
        <div style="display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 8px;">
            <h3 style="margin-bottom: 0;">Nhóm tự động theo voucher đã sử dụng</h3>
            <form method="post" style="margin-bottom: 0;">
                <button type="submit" name="update_customer_source" class="btn btn-primary">Cập nhật trường Nguồn khách hàng tự động</button>
            </form>
        </div>
        <form method="post">
            <table class="table table-bordered" style="margin-top: 12px;">
                <thead>
                    <tr>
                        <th><input type="checkbox" id="selectAll"></th>
                        <th>User ID</th>
                        <th>Tên đăng nhập</th>
                        <th>Email</th>
                        <th>Voucher</th>
                        <th>Nguồn khách hàng mới</th>
                        <th>Đã có nguồn?</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($users as $user): ?>
                    <tr>
                        <td><input type="checkbox" name="user_ids[]" value="<?php echo $user['id']; ?>" <?php echo (empty($user['customer_source']) ? 'checked' : ''); ?>></td>
                        <td><?php echo htmlspecialchars($user['id']); ?></td>
                        <td><?php echo htmlspecialchars($user['username'] ?? '-'); ?></td>
                        <td><?php echo htmlspecialchars($user['email'] ?? '-'); ?></td>
                        <td><?php echo htmlspecialchars($user['voucher'] ?? '-'); ?></td>
                        <td><?php echo htmlspecialchars($user['target_group'] ?? '-'); ?></td>
                        <td><?php echo $user['customer_source'] ? '✔️' : '❌'; ?></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </form>
        <?php
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_customer_source']) && !empty($_POST['user_ids'])) {
            // Chỉ update cho user có trong danh sách $users đã render ra bảng
            $user_map = [];
            foreach ($users as $u) {
                $user_map[$u['id']] = $u;
            }
            $updated = 0;
            foreach ($_POST['user_ids'] as $uid) {
                if (!isset($user_map[$uid])) continue; // chỉ update user có trong bảng
                $new_source = $user_map[$uid]['target_group'] ?? $user_map[$uid]['first_voucher'];
                $update = $pdo->prepare("UPDATE user SET customer_source = ? WHERE id = ?");
                if ($update->execute([$new_source, $uid])) $updated++;
            }
            echo '<div class="alert alert-success">Đã cập nhật customer_source cho ' . $updated . ' user.</div>';
        }
        ?>
        <script>
        // JS: select all checkbox
        document.addEventListener('DOMContentLoaded', function() {
            var selectAll = document.getElementById('selectAll');
            if (selectAll) {
                selectAll.addEventListener('change', function() {
                    var checkboxes = document.querySelectorAll('input[name="user_ids[]"]:not(:disabled)');
                    for (var cb of checkboxes) cb.checked = selectAll.checked;
                });
            }
        });
        </script>
        <p class="mt-3 text-muted">Chỉ hiển thị voucher đầu tiên làm nhóm tự động.<br>
        Chỉ tick chọn mặc định cho user chưa có "Nguồn khách hàng".<br>
        Bạn có thể chọn user và bấm nút để cập nhật trường "Nguồn khách hàng".</p>
    </div>
</main>
<?php include $private_layouts_path . 'admin_footer.php'; ?>
