<?php
// filepath: e:\Application\laragon\www\rtk_web_admin\public\pages\account_management.php
session_start();
require_once __DIR__ . '/../../private/utils/dashboard_helpers.php';
$private_includes_path = __DIR__ . '/../../private/includes/';
$user_display_name = $_SESSION['admin_username'] ?? 'Admin';
$base_path = '/'; // Adjust if necessary

// TODO: Fetch actual measurement account data here

?>

<div class="dashboard-wrapper">
    <?php include $private_includes_path . 'admin_sidebar.php'; ?>

    <main class="content-wrapper">
        <div class="content-header">
            <h2 class="text-2xl font-semibold">Quản lý TK Đo đạc</h2>
            <div class="user-info">
                <span>Chào mừng, <span class="highlight"><?php echo htmlspecialchars($user_display_name); ?></span>!</span>
                <a href="<?php echo $base_path; ?>public/pages/profile.php">Hồ sơ</a>
                <a href="<?php echo $base_path; ?>public/pages/auth/admin_logout.php">Đăng xuất</a>
            </div>
        </div>

        <div id="admin-account-management" class="content-section">
            <div class="flex flex-col md:flex-row md:justify-between md:items-center mb-4 gap-3 md:gap-2">
                <h3 class="text-lg md:text-xl font-semibold text-gray-900">Quản lý tài khoản đo đạc</h3>
                <button class="btn-primary self-start md:self-auto w-full md:w-auto" onclick="openCreateMeasurementAccountModal()" data-permission="account_create">
                    <i class="fas fa-plus mr-1"></i> Tạo TK thủ công
                </button>
            </div>
            <div class="mb-4 flex flex-wrap gap-2 items-center bg-white p-3 rounded-lg shadow-sm border border-gray-200">
                <input type="search" placeholder="Tìm ID TK, Email..." class="flex-grow min-w-[160px] text-sm">
                <select class="min-w-[120px] text-sm">
                    <option value="">Tất cả gói</option>
                    <option value="1M">Gói 1 Tháng</option>
                    <option value="6M">Gói 6 Tháng</option>
                    <option value="1Y">Gói 1 Năm</option>
                    <!-- PHP: Populate packages -->
                </select>
                <select class="min-w-[130px] text-sm">
                    <option value="">Tất cả trạng thái</option>
                    <option value="active">Hoạt động</option>
                    <option value="pending">Chờ KH</option>
                    <option value="expired">Hết hạn</option>
                    <option value="suspended">Đình chỉ</option>
                    <!-- PHP: Populate statuses -->
                </select>
                <button class="btn-secondary"><i class="fas fa-search mr-1"></i> Tìm</button>
            </div>
            <div class="bg-white rounded-lg shadow border border-gray-200 overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200 text-xs">
                        <thead>
                            <tr>
                                <th class="px-2 py-2">ID TK</th>
                                <th class="px-2 py-2">Email user</th>
                                <th class="px-2 py-2">Gói</th>
                                <th class="px-2 py-2">Ngày KH</th>
                                <th class="px-2 py-2">Ngày HH</th>
                                <th class="px-2 py-2">Trạng thái</th>
                                <th class="px-2 py-2 text-center">Hành động</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200">
                            <!-- Sample Row 1 (Active) -->
                            <tr data-account-id="TK-001">
                                <td class="px-2 py-1">TK-001</td>
                                <td class="px-2 py-1">demo@example.com</td>
                                <td class="px-2 py-1">6 Tháng</td>
                                <td class="px-2 py-1">10/01/24</td>
                                <td class="px-2 py-1">10/07/24</td>
                                <td class="px-2 py-1"><span class="badge badge-green">Hoạt động</span></td>
                                <td class="px-2 py-1 text-center whitespace-nowrap space-x-1">
                                    <button class="btn-icon" title="Xem" onclick="viewAccountDetails('TK-001')"><i class="fas fa-eye text-[11px] md:text-xs"></i></button>
                                    <button class="btn-icon text-yellow-600 hover:text-yellow-700" title="Sửa" onclick="openEditAccountModal('TK-001')" data-permission="account_edit"><i class="fas fa-pencil-alt text-[11px] md:text-xs"></i></button>
                                    <button class="btn-icon text-red-600 hover:text-red-700" title="Đình chỉ" onclick="toggleAccountStatus('TK-001', 'suspend', event)" data-permission="account_status_toggle"><i class="fas fa-ban text-[11px] md:text-xs"></i></button>
                                </td>
                            </tr>
                            <!-- Sample Row 2 (Pending) -->
                            <tr data-account-id="TK-002">
                                <td class="px-2 py-1">TK-002</td> <td class="px-2 py-1">another@user.net</td> <td class="px-2 py-1">1 Năm</td> <td class="px-2 py-1">15/06/24</td> <td class="px-2 py-1">15/06/25</td>
                                <td class="px-2 py-1"><span class="badge badge-yellow">Chờ KH</span></td>
                                <td class="px-2 py-1 text-center whitespace-nowrap space-x-1">
                                    <button class="btn-icon" title="Xem" onclick="viewAccountDetails('TK-002')"><i class="fas fa-eye text-[11px] md:text-xs"></i></button>
                                    <button class="btn-icon text-yellow-600 hover:text-yellow-700" title="Sửa" onclick="openEditAccountModal('TK-002')" data-permission="account_edit"><i class="fas fa-pencil-alt text-[11px] md:text-xs"></i></button>
                                    <button class="btn-icon text-green-600 hover:text-green-700" title="Kích hoạt" onclick="toggleAccountStatus('TK-002', 'activate', event)" data-permission="account_status_toggle"><i class="fas fa-play-circle text-[11px] md:text-xs"></i></button>
                                </td>
                            </tr>
                            <!-- Sample Row 3 (Expired) -->
                            <tr data-account-id="TK-003">
                                <td class="px-2 py-1">TK-003</td> <td class="px-2 py-1">old_user@domain.org</td> <td class="px-2 py-1">1 Tháng</td> <td class="px-2 py-1">01/05/24</td> <td class="px-2 py-1">01/06/24</td>
                                <td class="px-2 py-1"><span class="badge badge-red">Hết hạn</span></td>
                                <td class="px-2 py-1 text-center whitespace-nowrap space-x-1">
                                    <button class="btn-icon" title="Xem" onclick="viewAccountDetails('TK-003')"><i class="fas fa-eye text-[11px] md:text-xs"></i></button>
                                    <button class="btn-icon text-blue-600 hover:text-blue-700" title="Gia hạn" onclick="openRenewAccountModal('TK-003')" data-permission="account_edit"><i class="fas fa-history text-[11px] md:text-xs"></i></button>
                                    <button class="btn-icon text-red-600 hover:text-red-700" title="Xóa" onclick="deleteAccount('TK-003', event)" data-permission="account_delete"><i class="fas fa-trash-alt text-[11px] md:text-xs"></i></button>
                                </td>
                            </tr>
                             <!-- Sample Row 4 (Suspended) -->
                            <tr data-account-id="TK-004">
                                <td class="px-2 py-1">TK-004</td> <td class="px-2 py-1">suspended@user.com</td> <td class="px-2 py-1">1 Năm</td> <td class="px-2 py-1">01/01/24</td> <td class="px-2 py-1">01/01/25</td>
                                <td class="px-2 py-1"><span class="badge badge-red">Đình chỉ</span></td>
                                <td class="px-2 py-1 text-center whitespace-nowrap space-x-1">
                                    <button class="btn-icon" title="Xem" onclick="viewAccountDetails('TK-004')"><i class="fas fa-eye text-[11px] md:text-xs"></i></button>
                                    <button class="btn-icon text-yellow-600 hover:text-yellow-700" title="Sửa" onclick="openEditAccountModal('TK-004')" data-permission="account_edit"><i class="fas fa-pencil-alt text-[11px] md:text-xs"></i></button>
                                    <button class="btn-icon text-green-600 hover:text-green-700" title="Kích hoạt lại" onclick="toggleAccountStatus('TK-004', 'reactivate', event)" data-permission="account_status_toggle"><i class="fas fa-play-circle text-[11px] md:text-xs"></i></button>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                <div class="p-3 flex flex-col sm:flex-row justify-between items-center border-t border-gray-200 bg-gray-50 text-xs gap-2">
                    <div class="text-gray-600">Hiển thị 1-3 của 280 TK</div>
                    <div class="flex space-x-1">
                        <button class="px-2 py-1 border border-gray-300 rounded bg-white hover:bg-gray-100 disabled:opacity-50" disabled>Tr</button>
                        <button class="px-2 py-1 border border-primary-500 rounded text-white bg-primary-500">1</button>
                        <button class="px-2 py-1 border border-gray-300 rounded bg-white hover:bg-gray-100">..</button>
                        <button class="px-2 py-1 border border-gray-300 rounded bg-white hover:bg-gray-100">94</button>
                        <button class="px-2 py-1 border border-gray-300 rounded bg-white hover:bg-gray-100">Sau</button>
                    </div>
                </div>
            </div>
        </div>
    </main>
</div>

<script>
    // Placeholder functions - implement actual logic with modals/AJAX
    function openCreateMeasurementAccountModal() { console.log('Open create measurement account modal'); }
    function viewAccountDetails(accountId) { console.log('View details for account:', accountId); }
    function openEditAccountModal(accountId) { console.log('Open edit modal for account:', accountId); }
    function openRenewAccountModal(accountId) { console.log('Open renew modal for account:', accountId); }
    function deleteAccount(accountId, event) {
        if (confirm(`Bạn có chắc muốn XÓA tài khoản ${accountId}? Hành động này không thể hoàn tác.`)) {
            console.log('Deleting account:', accountId);
            // Add AJAX logic here
            // On success, remove the row from the table
            event.currentTarget.closest('tr').remove();
        }
    }
    function toggleAccountStatus(accountId, action, event) { // action can be 'activate', 'suspend', 'reactivate'
        const actionText = {
            activate: 'KÍCH HOẠT',
            suspend: 'ĐÌNH CHỈ',
            reactivate: 'KÍCH HOẠT LẠI'
        };
        if (confirm(`Bạn có chắc muốn ${actionText[action]} tài khoản ${accountId}?`)) {
            console.log(`${actionText[action]} account:`, accountId);
            // Add AJAX logic here
            // On success, update badge and button icons/titles
            const row = event.currentTarget.closest('tr');
            const badge = row.querySelector('.badge');
            const actionCell = row.querySelector('td:last-child'); // Get the last cell containing actions

            // Define button HTML structure for reusability
            const viewBtnHtml = `<button class="btn-icon" title="Xem" onclick="viewAccountDetails('${accountId}')"><i class="fas fa-eye text-[11px] md:text-xs"></i></button>`;
            const editBtnHtml = `<button class="btn-icon text-yellow-600 hover:text-yellow-700" title="Sửa" onclick="openEditAccountModal('${accountId}')" data-permission="account_edit"><i class="fas fa-pencil-alt text-[11px] md:text-xs"></i></button>`;
            const suspendBtnHtml = `<button class="btn-icon text-red-600 hover:text-red-700" title="Đình chỉ" onclick="toggleAccountStatus('${accountId}', 'suspend', event)" data-permission="account_status_toggle"><i class="fas fa-ban text-[11px] md:text-xs"></i></button>`;
            const activateBtnHtml = `<button class="btn-icon text-green-600 hover:text-green-700" title="Kích hoạt" onclick="toggleAccountStatus('${accountId}', 'activate', event)" data-permission="account_status_toggle"><i class="fas fa-play-circle text-[11px] md:text-xs"></i></button>`;
            const reactivateBtnHtml = `<button class="btn-icon text-green-600 hover:text-green-700" title="Kích hoạt lại" onclick="toggleAccountStatus('${accountId}', 'reactivate', event)" data-permission="account_status_toggle"><i class="fas fa-play-circle text-[11px] md:text-xs"></i></button>`;

            let newActionHtml = '';

            if (action === 'activate' || action === 'reactivate') {
                badge.textContent = 'Hoạt động';
                badge.className = 'badge badge-green';
                newActionHtml = `${viewBtnHtml} ${editBtnHtml} ${suspendBtnHtml}`;
            } else if (action === 'suspend') {
                 badge.textContent = 'Đình chỉ';
                 badge.className = 'badge badge-red';
                 newActionHtml = `${viewBtnHtml} ${editBtnHtml} ${reactivateBtnHtml}`;
            }
            // Add other conditions like 'expire' if needed

            if (newActionHtml) {
                actionCell.innerHTML = newActionHtml;
            }
        }
    }
</script>
