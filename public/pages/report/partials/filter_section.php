<div class="mb-6 bg-white p-3 sm:p-4 rounded-lg shadow-sm border border-gray-200">
    <h3 class="text-base md:text-lg font-semibold text-gray-800 mb-3">Bộ lọc chung</h3>
    <!-- Preset Date Range Buttons -->
    <div class="preset-buttons-container">
        <h4>Chọn nhanh khoảng thời gian</h4>
        <div class="preset-buttons">
            <button type="button" class="preset-btn" data-days="7">7 ngày qua</button>
            <button type="button" class="preset-btn" data-days="30">30 ngày qua</button>
            <button type="button" class="preset-btn" data-period="this_quarter">Quý này</button>
            <button type="button" class="preset-btn" data-period="this_year">Năm nay</button>
        </div>
    </div>
    <div class="filter-bar flex items-end gap-2">
        <form id="report-filter-form" method="GET" action="" class="flex items-end gap-2">
            <input type="date" id="report-start-date" name="start_date"
                   value="<?php echo htmlspecialchars($start_date); ?>" placeholder="Từ ngày">
            <input type="date" id="report-end-date" name="end_date"
                   value="<?php echo htmlspecialchars($end_date); ?>" placeholder="Đến ngày">
            <button type="submit" class="btn btn-primary">
                <i class="fas fa-filter"></i> Xem báo cáo
            </button>
            <a href="<?php echo strtok($_SERVER['REQUEST_URI'], '?'); ?>" class="btn btn-secondary">
                <i class="fas fa-times"></i> Xóa lọc
            </a>
        </form>
        <form id="export-report-excel" method="POST" action="<?php echo $base_url; ?>public/handlers/excel_index.php" class="inline-block">
            <input type="hidden" name="action" value="export_report_excel">
            <input type="hidden" name="start_date" value="<?php echo htmlspecialchars($start_date); ?>">
            <input type="hidden" name="end_date" value="<?php echo htmlspecialchars($end_date); ?>">
            <button type="submit" class="btn btn-success ml-2">
                <i class="fas fa-file-excel"></i> Xuất Excel
            </button>
        </form>
    </div>
</div>
