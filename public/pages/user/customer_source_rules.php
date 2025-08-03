<?php
// Trang cấu hình điều kiện phân nhóm user theo nguồn khách hàng và voucher
// Lưu cấu hình vào file JSON (public/pages/user/customer_source_rules.json)

$GLOBALS['required_permission'] = 'user_management_edit';
$bootstrap_data = require_once __DIR__ . '/../../../private/core/page_bootstrap.php';
$base_url = $bootstrap_data['base_url'];
$private_layouts_path = $bootstrap_data['private_layouts_path'];


require_once BASE_PATH . '/classes/Database.php';
$db = Database::getInstance()->getConnection();

// Xử lý lưu cấu hình (ghi rules vào DB)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['rules'])) {
    $rules = json_decode($_POST['rules'], true);
    if (is_array($rules)) {
        // Xóa hết rule cũ
        $db->exec('DELETE FROM customer_source_rules');
        // Thêm mới từng rule
        $stmt = $db->prepare('INSERT INTO customer_source_rules (voucher, `group`) VALUES (?, ?)');
        foreach ($rules as $rule) {
            if (!empty($rule['voucher']) && !empty($rule['group'])) {
                $stmt->execute([$rule['voucher'], $rule['group']]);
            }
        }
        $message = 'Lưu cấu hình thành công!';
    } else {
        $message = 'Dữ liệu không hợp lệ!';
    }
}

// Đọc rules từ DB
$rules = [];
$stmt = $db->query('SELECT voucher, `group` FROM customer_source_rules ORDER BY id ASC');
if ($stmt) {
    $rules = $stmt->fetchAll(PDO::FETCH_ASSOC);
}


// Tự động gán nhóm cho user dựa trên cấu hình (đọc rules từ DB)
if (isset($_GET['apply_groups'])) {
    $count = 0;
    // Lấy rules từ DB
    $rules = [];
    $stmt = $db->query('SELECT voucher, `group` FROM customer_source_rules');
    if ($stmt) {
        $rules = $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    if (!empty($rules)) {
        // Lấy tất cả user và voucher đã dùng
        $sql = "SELECT u.id as user_id, v.code as voucher_code
                FROM user u
                LEFT JOIN user_voucher_usage uvu ON u.id = uvu.user_id
                LEFT JOIN voucher v ON uvu.voucher_id = v.id";
        $stmt = $db->prepare($sql);
        $stmt->execute();
        $all = $stmt->fetchAll(PDO::FETCH_ASSOC);
        foreach ($all as $row) {
            $voucher = $row['voucher_code'] ?? '';
            $group = '';
            foreach ($rules as $rule) {
                if ($voucher === $rule['voucher']) {
                    $group = $rule['group'];
                    break;
                }
            }
            if ($group) {
                $update = $db->prepare("UPDATE user SET customer_source = ? WHERE id = ?");
                $update->execute([$group, $row['user_id']]);
                $count++;
            }
        }
    }
    echo '<div class="alert alert-success">Đã gán nhóm cho ' . $count . ' user theo cấu hình.</div>';
}

include $private_layouts_path . 'admin_header.php';
include $private_layouts_path . 'admin_sidebar.php';
?>
<main class="content-wrapper">
    <div class="content-section">
        <div style="display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 8px;">
            <h3 style="margin-bottom: 0;">Phân nhóm khách hàng theo nguồn & voucher</h3>
        </div>
        <?php if (!empty($message)) echo '<div class="alert alert-success">'.$message.'</div>'; ?>
        <form id="rulesForm" method="POST">
            <table class="table table-bordered" id="rulesTable" style="margin-top: 12px;">
                <thead>
                    <tr>
                        <th>Voucher đã sử dụng</th>
                        <th>Tên công ty/nguồn mới</th>
                        <th>Hành động</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($rules)): foreach ($rules as $i => $rule): ?>
                    <tr>
                        <td><input type="text" name="voucher[]" value="<?php echo htmlspecialchars($rule['voucher']); ?>" class="form-control" required></td>
                        <td><input type="text" name="group[]" value="<?php echo htmlspecialchars($rule['group']); ?>" class="form-control" required></td>
                        <td><button type="button" class="btn btn-danger btn-sm" onclick="removeRuleRow(this)">Xóa</button></td>
                    </tr>
                    <?php endforeach; endif; ?>
                </tbody>
            </table>
            <button type="button" class="btn btn-secondary" onclick="addRuleRow()">Thêm điều kiện</button>
            <input type="hidden" name="rules" id="rulesInput">
            <button type="submit" class="btn btn-primary">Lưu cấu hình và cập nhật</button>
        </form>
        <p class="mt-3 text-muted">Mặc định: Nếu không có điều kiện nào khớp, user sẽ không được phân nhóm.</p>
    </div>
</main>
<script>
function addRuleRow() {
    const tbody = document.querySelector('#rulesTable tbody');
    const tr = document.createElement('tr');
    tr.innerHTML = `
        <td><input type="text" name="voucher[]" class="form-control" required></td>
        <td><input type="text" name="group[]" class="form-control" required></td>
        <td><button type="button" class="btn btn-danger btn-sm" onclick="removeRuleRow(this)">Xóa</button></td>
    `;
    tbody.appendChild(tr);
}
function removeRuleRow(btn) {
    btn.closest('tr').remove();
}
document.getElementById('rulesForm').onsubmit = function(e) {
    const vouchers = Array.from(document.getElementsByName('voucher[]')).map(i=>i.value.trim());
    const groups = Array.from(document.getElementsByName('group[]')).map(i=>i.value.trim());
    const rules = [];
    for (let i = 0; i < vouchers.length; i++) {
        if (vouchers[i] && groups[i]) {
            rules.push({voucher: vouchers[i], group: groups[i]});
        }
    }
    document.getElementById('rulesInput').value = JSON.stringify(rules);
};
</script>
<?php include $private_layouts_path . 'admin_footer.php'; ?>
