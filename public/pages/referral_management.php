<?php
// filepath: public\pages\referral\referral_management.php

// --- Bootstrap and Initialization ---
$bootstrap_data         = require_once __DIR__ . '/../../private/core/page_bootstrap.php';
$db                      = $bootstrap_data['db'];
$base_url                = $bootstrap_data['base_url'];
$private_layouts_path   = $bootstrap_data['private_layouts_path'];
$user_display_name       = $bootstrap_data['user_display_name'];

// authorization check
if (!isset($_SESSION['admin_id'])) {
    header('Location: ' . $base_url . 'public/pages/auth/admin_login.php');
    exit;
}

?>
<?php include $private_layouts_path . 'admin_header.php'; ?>
<?php include $private_layouts_path . 'admin_sidebar.php'; ?>
<main class="content-wrapper">
    <div class="content-header">
        <h2 class="text-2xl font-semibold">Quản lý Giới thiệu</h2>
        <div class="user-info">
            <span>Chào mừng, <span class="highlight"><?php echo htmlspecialchars($user_display_name); ?></span>!</span>
            <a href="<?php echo $base_url; ?>public/pages/setting/profile.php">Hồ sơ</a>
            <a href="<?php echo $base_url; ?>public/pages/auth/admin_logout.php">Đăng xuất</a>
        </div>
    </div>

    <div id="admin-referral-management" class="content-section">
        <div class="mb-4 border-b border-gray-200">
            <nav class="-mb-px flex space-x-4 sm:space-x-6 overflow-x-auto pb-px" aria-label="Tabs" id="referral-tabs">
                <a href="#" data-tab="referrers" class="referral-tab border-primary-500 text-primary-600 whitespace-nowrap py-3 px-2 border-b-2 font-medium text-sm" aria-current="page" onclick="switchReferralTab(event, 'referrers')"> Người GT </a>
                <a href="#" data-tab="referrals" class="referral-tab border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 whitespace-nowrap py-3 px-2 border-b-2 font-medium text-sm" onclick="switchReferralTab(event, 'referrals')"> Lượt GT </a>
                <a href="#" data-tab="referral-settings" class="referral-tab border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 whitespace-nowrap py-3 px-2 border-b-2 font-medium text-sm" data-permission="referral_settings_view" onclick="switchReferralTab(event, 'referral-settings')"> Cấu hình </a>
            </nav>
        </div>

        <div id="referrers" class="referral-content active">
            <h3 class="text-base md:text-lg font-medium text-gray-800 mb-3">Danh sách người giới thiệu (NGT)</h3>
            <div class="mb-4 flex flex-wrap gap-2 items-center bg-white p-3 rounded-lg shadow-sm border border-gray-200">
                <input type="search" placeholder="Tìm Email, Mã GT..." class="flex-grow min-w-[180px] text-sm">
                <button class="btn-secondary"><i class="fas fa-search mr-1"></i> Tìm</button>
            </div>
            <div class="bg-white rounded-lg shadow border border-gray-200 overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200 text-xs">
                        <thead>
                            <tr>
                                <th class="px-2 py-2">Mã GT</th>
                                <th class="px-2 py-2">Email NGT</th>
                                <th class="px-2 py-2">Số lượt GT</th>
                                <th class="px-2 py-2">HH chờ TT</th>
                                <th class="px-2 py-2">HH đã TT</th>
                                <th class="px-2 py-2">Ngày tham gia</th>
                                <th class="px-2 py-2 text-center">Hành động</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200">
                            <!-- Sample Row 1 -->
                            <tr>
                                <td class="px-2 py-1">REF001</td>
                                <td class="px-2 py-1">referrer1@example.com</td>
                                <td class="px-2 py-1 text-center">15</td>
                                <td class="px-2 py-1 text-right">250,000đ</td>
                                <td class="px-2 py-1 text-right">1,500,000đ</td>
                                <td class="px-2 py-1">01/03/24</td>
                                <td class="px-2 py-1 text-center whitespace-nowrap space-x-1">
                                    <button class="btn-icon" title="Xem chi tiết" onclick="viewReferrerDetails('REF001')"><i class="fas fa-eye text-[11px] md:text-xs"></i></button>
                                    <button class="btn-icon text-green-600 hover:text-green-700" title="Thanh toán HH" onclick="processReferralPayout('REF001', event)" data-permission="referral_payout"><i class="fas fa-hand-holding-usd text-[11px] md:text-xs"></i></button>
                                </td>
                            </tr>
                            <!-- Sample Row 2 -->
                            <tr>
                                <td class="px-2 py-1">REF002</td>
                                <td class="px-2 py-1">vip_ref@domain.net</td>
                                <td class="px-2 py-1 text-center">5</td>
                                <td class="px-2 py-1 text-right">0đ</td>
                                <td class="px-2 py-1 text-right">500,000đ</td>
                                <td class="px-2 py-1">10/05/24</td>
                                <td class="px-2 py-1 text-center whitespace-nowrap space-x-1">
                                    <button class="btn-icon" title="Xem chi tiết" onclick="viewReferrerDetails('REF002')"><i class="fas fa-eye text-[11px] md:text-xs"></i></button>
                                    <button class="btn-icon text-gray-400 cursor-not-allowed" title="Không có HH chờ TT" disabled><i class="fas fa-hand-holding-usd text-[11px] md:text-xs"></i></button>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                <div class="p-3 flex flex-col sm:flex-row justify-between items-center border-t border-gray-200 bg-gray-50 text-xs gap-2">
                    <div class="text-gray-600">Hiển thị 1-2 của 45 NGT</div>
                    <div class="flex space-x-1">
                        <button class="px-2 py-1 border border-gray-300 rounded bg-white hover:bg-gray-100 disabled:opacity-50" disabled>Tr</button>
                        <button class="px-2 py-1 border border-primary-500 rounded text-white bg-primary-500">1</button>
                        <button class="px-2 py-1 border border-gray-300 rounded bg-white hover:bg-gray-100">2</button>
                        <button class="px-2 py-1 border border-gray-300 rounded bg-white hover:bg-gray-100">Sau</button>
                    </div>
                </div>
            </div>
        </div>

        <div id="referrals" class="referral-content" style="display: none;">
            <h3 class="text-base md:text-lg font-medium text-gray-800 mb-3">Danh sách lượt giới thiệu</h3>
            <div class="mb-4 flex flex-wrap gap-2 items-center bg-white p-3 rounded-lg shadow-sm border border-gray-200">
                <input type="search" placeholder="Tìm Email, Mã GT..." class="flex-grow min-w-[160px] text-sm">
                <select class="min-w-[130px] text-sm">
                    <option value="">Trạng thái HH</option>
                    <option value="pending">Chờ TT</option>
                    <option value="paid">Đã TT</option>
                    <option value="ineligible">Không hợp lệ</option>
                </select>
                <input type="date" class="min-w-[120px] text-sm p-2">
                <input type="date" class="min-w-[120px] text-sm p-2">
                <button class="btn-secondary"><i class="fas fa-filter mr-1"></i> Lọc</button>
            </div>
            <div class="bg-white rounded-lg shadow border border-gray-200 overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200 text-xs">
                        <thead>
                            <tr>
                                <th class="px-2 py-2">ID Lượt GT</th>
                                <th class="px-2 py-2">Email NĐK</th>
                                <th class="px-2 py-2">Mã NGT</th>
                                <th class="px-2 py-2">Ngày ĐK</th>
                                <th class="px-2 py-2">Gói mua</th>
                                <th class="px-2 py-2">Hoa hồng</th>
                                <th class="px-2 py-2">Trạng thái HH</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200">
                            <!-- Sample Row 1 (Pending) -->
                            <tr>
                                <td class="px-2 py-1">RFL00123</td>
                                <td class="px-2 py-1">new_user1@mail.com</td>
                                <td class="px-2 py-1">REF001</td>
                                <td class="px-2 py-1">15/07/24</td>
                                <td class="px-2 py-1">1 Năm</td>
                                <td class="px-2 py-1 text-right">140,000đ</td>
                                <td class="px-2 py-1"><span class="badge badge-yellow">Chờ TT</span></td>
                            </tr>
                            <!-- Sample Row 2 (Paid) -->
                            <tr>
                                <td class="px-2 py-1">RFL00120</td>
                                <td class="px-2 py-1">old_referred@mail.com</td>
                                <td class="px-2 py-1">REF001</td>
                                <td class="px-2 py-1">10/07/24</td>
                                <td class="px-2 py-1">6 Tháng</td>
                                <td class="px-2 py-1 text-right">80,000đ</td>
                                <td class="px-2 py-1"><span class="badge badge-green">Đã TT</span></td>
                            </tr>
                            <!-- Sample Row 3 (Ineligible) -->
                            <tr>
                                <td class="px-2 py-1">RFL00115</td>
                                <td class="px-2 py-1">test_user@mail.com</td>
                                <td class="px-2 py-1">REF002</td>
                                <td class="px-2 py-1">05/07/24</td>
                                <td class="px-2 py-1">-</td>
                                <td class="px-2 py-1 text-right">0đ</td>
                                <td class="px-2 py-1"><span class="badge badge-gray">Không hợp lệ</span></td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                <div class="p-3 flex flex-col sm:flex-row justify-between items-center border-t border-gray-200 bg-gray-50 text-xs gap-2">
                    <div class="text-gray-600">Hiển thị 1-3 của 85 lượt GT</div>
                    <div class="flex space-x-1">
                        <button class="px-2 py-1 border border-gray-300 rounded bg-white hover:bg-gray-100 disabled:opacity-50" disabled>Tr</button>
                        <button class="px-2 py-1 border border-primary-500 rounded text-white bg-primary-500">1</button>
                        <button class="px-2 py-1 border border-gray-300 rounded bg-white hover:bg-gray-100">2</button>
                        <button class="px-2 py-1 border border-gray-300 rounded bg-white hover:bg-gray-100">Sau</button>
                    </div>
                </div>
            </div>
        </div>

        <div id="referral-settings" class="referral-content bg-white p-4 sm:p-6 rounded-lg shadow border border-gray-200" style="display: none;" data-permission="referral_settings_view">
            <h3 class="text-base md:text-lg font-medium text-gray-800 mb-4">Cấu hình Chương trình giới thiệu</h3>
            <form onsubmit="saveReferralSettings(event)">
                <div class="space-y-4 max-w-xl">
                    <div>
                        <label for="ref-commission-rate" class="block text-sm font-medium text-gray-700 mb-1">Tỷ lệ hoa hồng (%)</label>
                        <input type="number" id="ref-commission-rate" value="10" min="0" max="100" step="0.5" class="text-sm w-32" data-permission="referral_settings_edit">
                        <p class="text-xs text-gray-500 mt-1">Phần trăm hoa hồng trên giá trị gói đăng ký đầu tiên.</p>
                    </div>
                    <div>
                        <label for="ref-min-payout" class="block text-sm font-medium text-gray-700 mb-1">Số dư tối thiểu để TT (VNĐ)</label>
                        <input type="number" id="ref-min-payout" value="100000" min="0" step="10000" class="text-sm w-40" data-permission="referral_settings_edit">
                        <p class="text-xs text-gray-500 mt-1">Số dư hoa hồng tối thiểu để người giới thiệu có thể yêu cầu thanh toán.</p>
                    </div>
                    <div>
                        <label for="ref-cookie-duration" class="block text-sm font-medium text-gray-700 mb-1">Thời hạn cookie (ngày)</label>
                        <input type="number" id="ref-cookie-duration" value="30" min="1" step="1" class="text-sm w-32" data-permission="referral_settings_edit">
                        <p class="text-xs text-gray-500 mt-1">Thời gian lưu cookie giới thiệu trên trình duyệt người được giới thiệu.</p>
                    </div>
                    <div class="flex items-center">
                        <input type="checkbox" id="ref-program-enabled" checked class="h-4 w-4 text-primary-600 border-gray-300 rounded focus:ring-primary-500 mr-2" data-permission="referral_settings_edit">
                        <label for="ref-program-enabled" class="text-sm font-medium text-gray-700">Bật chương trình giới thiệu</label>
                    </div>
                </div>
                <div class="mt-6">
                    <button type="submit" class="btn-primary" data-permission="referral_settings_edit">
                        <i class="fas fa-save mr-1"></i> Lưu cấu hình
                    </button>
                </div>
            </form>
        </div>
    </div>
</main>
<script>
    function switchReferralTab(event, tabId) {
        event.preventDefault();
        // Hide all content
        document.querySelectorAll('.referral-content').forEach(content => content.style.display = 'none');
        // Remove active class from all tabs
        document.querySelectorAll('.referral-tab').forEach(tab => {
            tab.classList.remove('border-primary-500', 'text-primary-600');
            tab.classList.add('border-transparent', 'text-gray-500', 'hover:text-gray-700', 'hover:border-gray-300');
            // Reset inline styles potentially added before
            tab.style.color = '';
            tab.style.borderBottomColor = '';
        });
        // Show selected content
        document.getElementById(tabId).style.display = 'block';
        // Set active class for the clicked tab
        const activeTab = event.currentTarget;
        activeTab.classList.remove('border-transparent', 'text-gray-500', 'hover:text-gray-700', 'hover:border-gray-300');
        activeTab.classList.add('border-primary-500', 'text-primary-600');
        activeTab.setAttribute('aria-current', 'page');
    }

    // Add other JS functions (viewReferrerDetails, processReferralPayout, saveReferralSettings) if needed
    function viewReferrerDetails(refCode) { console.log('View details for', refCode); }
    function processReferralPayout(refCode, event) {
        if (confirm(`Thanh toán hoa hồng cho ${refCode}?`)) {
            console.log('Processing payout for', refCode);
            // Add AJAX logic
        }
     }
    function saveReferralSettings(event) {
        event.preventDefault();
        console.log('Saving referral settings');
        // Add AJAX logic
    }
</script>
<?php include $private_layouts_path . 'admin_footer.php'; ?>