<?php
// Ensure Composer's autoloader is loaded
require_once __DIR__ . '/../../vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Font;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Border;

class ExcelExportService {

    /**
     * Exports an array of data to an Excel file and streams it to the browser.
     *
     * @param array $data An array of associative arrays, where each associative array represents a row.
     *                    The keys of the first associative array will be used as header row.
     * @param string $filename The desired filename for the downloaded Excel file (e.g., "export.xlsx").
     * @throws \PhpOffice\PhpSpreadsheet\Exception
     * @throws \PhpOffice\PhpSpreadsheet\Writer\Exception
     */
    public static function export(array $data, string $filename = 'export.xlsx'): void {
        if (empty($data)) {
            // Or handle this case as you see fit, maybe throw an exception or return an error message.
            // For now, we'll just exit if there's no data.
            error_log('ExcelExportService: No data provided for export.');
            // You might want to redirect back with an error message
            // header('Location: ' . $_SERVER['HTTP_REFERER'] . '?error=no_data_to_export');
            // exit;
            // For simplicity in this example, we'll create an empty file with headers if data is empty
            // but ideally, you should prevent calling this function with empty data.
            if (isset($_SESSION['last_headers']) && !empty($_SESSION['last_headers'])) {
                $data = [array_fill_keys($_SESSION['last_headers'], '')]; // Create one empty row with previous headers
            } else {
                 // Fallback if no headers were ever set, though this shouldn't happen in normal flow
                $data = [['No data to export']];
            }
        }

        $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // Set headers
        $headers = array_keys(reset($data)); // Get keys from the first row as headers
        $_SESSION['last_headers'] = $headers; // Store headers for potential empty data export
        $column = 'A';
        foreach ($headers as $header) {
            $sheet->setCellValue($column . '1', $header);
            $column++;
        }

        // Set data rows
        $rowNumber = 2;
        foreach ($data as $row) {
            $column = 'A';
            foreach ($row as $cellValue) {
                $sheet->setCellValue($column . $rowNumber, $cellValue);
                $column++;
            }
            $rowNumber++;
        }

        // Auto-size columns
        $column = 'A';
        foreach ($headers as $header) {
            $sheet->getColumnDimension($column)->setAutoSize(true);
            $column++;
        }

        // Left-align all cells for readability
        $dimension = $sheet->calculateWorksheetDimension();
        $sheet->getStyle($dimension)
              ->getAlignment()
              ->setHorizontal(Alignment::HORIZONTAL_LEFT);

        // Stream the file to the browser
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="' . $filename . '"');
        header('Cache-Control: max-age=0');
        // If you're serving to IE 9, then the following may be needed
        header('Cache-Control: max-age=1');
        // If you're serving to IE over SSL, then the following may be needed
        header('Expires: Mon, 26 Jul 1997 05:00:00 GMT'); // Date in the past
        header('Last-Modified: ' . gmdate('D, d M Y H:i:s') . ' GMT'); // always modified
        header('Cache-Control: cache, must-revalidate'); // HTTP/1.1
        header('Pragma: public'); // HTTP/1.0

        $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
        $writer->save('php://output');
        exit;
    }
    
    /**
     * Export comprehensive reports with charts and multiple sheets
     */
    public static function exportReportsWithCharts($reportData, $startDate, $endDate, $pdo) {
        try {
            $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
            
            // Remove default sheet
            $spreadsheet->removeSheetByIndex(0);
            
            // Create multiple sheets
            self::createReportOverviewSheet($spreadsheet, $reportData, $startDate, $endDate);
            self::createReportTransactionDetailsSheet($spreadsheet, $pdo, $startDate, $endDate);
            self::createReportCommissionDetailsSheet($spreadsheet, $pdo, $startDate, $endDate);
            self::createReportChartsDataSheet($spreadsheet, $reportData);
            self::createReportVisualizationsSheet($spreadsheet, $reportData);
            
            // Set first sheet as active
            $spreadsheet->setActiveSheetIndex(0);
            
            // Generate filename
            $filename = 'Bao_cao_chi_tiet_' . date('Ymd', strtotime($startDate)) . '_' . date('Ymd', strtotime($endDate)) . '.xlsx';
            
            // Stream to browser
            header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
            header('Content-Disposition: attachment;filename="' . $filename . '"');
            header('Cache-Control: max-age=0');
            header('Cache-Control: max-age=1');
            header('Expires: Mon, 26 Jul 1997 05:00:00 GMT');
            header('Last-Modified: ' . gmdate('D, d M Y H:i:s') . ' GMT');
            header('Cache-Control: cache, must-revalidate');
            header('Pragma: public');

            $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
            $writer->save('php://output');
            exit;
        } catch (Exception $e) {
            error_log('ExcelExportService Reports Error: ' . $e->getMessage());
            header('Content-Type: text/html; charset=UTF-8');
            echo '<script>alert("Lỗi khi xuất Excel: ' . addslashes($e->getMessage()) . '"); window.history.back();</script>';
            exit;
        }
    }
    
    /**
     * Create Overview Summary sheet
     */
    private static function createReportOverviewSheet($spreadsheet, $reportData, $startDate, $endDate) {
        $sheet = $spreadsheet->createSheet();
        $sheet->setTitle('Tổng quan');
        
        // Header styling
        $sheet->setCellValue('A1', 'BÁO CÁO TỔNG QUAN HỆ THỐNG');
        $sheet->setCellValue('A2', 'Từ ngày: ' . date('d/m/Y', strtotime($startDate)) . ' - Đến ngày: ' . date('d/m/Y', strtotime($endDate)));
        $sheet->setCellValue('A3', 'Xuất lúc: ' . date('d/m/Y H:i:s'));
        
        $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(16);
        $sheet->getStyle('A2:A3')->getFont()->setItalic(true);
        
        $row = 5;
        
        // User statistics section
        $sheet->setCellValue('A' . $row, 'THỐNG KÊ NGƯỜI DÙNG');
        $sheet->getStyle('A' . $row)->getFont()->setBold(true)->setSize(14);
        $sheet->getStyle('A' . $row)->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setRGB('E3F2FD');
        $row += 2;
        
        $userStats = [
            'Tổng số đăng ký' => $reportData['total_registrations'] ?? 0,
            'Đăng ký mới (trong kỳ)' => $reportData['new_registrations'] ?? 0,
            'Tài khoản hoạt động' => $reportData['active_accounts'] ?? 0,
            'Tài khoản bị khóa' => $reportData['locked_accounts'] ?? 0
        ];
        
        foreach ($userStats as $label => $value) {
            $sheet->setCellValue('A' . $row, $label);
            $sheet->setCellValue('B' . $row, number_format($value));
            $row++;
        }
        $row += 2;
        
        // Revenue statistics section
        $sheet->setCellValue('A' . $row, 'THỐNG KÊ DOANH THU');
        $sheet->getStyle('A' . $row)->getFont()->setBold(true)->setSize(14);
        $sheet->getStyle('A' . $row)->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setRGB('E8F5E8');
        $row += 2;
        
        $revenueStats = [
            'Tổng doanh thu (trong kỳ)' => number_format($reportData['total_sales'] ?? 0) . ' VNĐ',
            'Giao dịch thành công' => $reportData['completed_transactions'] ?? 0,
            'Giao dịch chờ duyệt' => $reportData['pending_transactions'] ?? 0,
            'Giao dịch thất bại' => $reportData['failed_transactions'] ?? 0
        ];
        
        foreach ($revenueStats as $label => $value) {
            $sheet->setCellValue('A' . $row, $label);
            $sheet->setCellValue('B' . $row, $value);
            $row++;
        }
        $row += 2;
        
        // Commission statistics section
        $sheet->setCellValue('A' . $row, 'THỐNG KÊ HOA HỒNG');
        $sheet->getStyle('A' . $row)->getFont()->setBold(true)->setSize(14);
        $sheet->getStyle('A' . $row)->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setRGB('FFF3E0');
        $row += 2;
        
        $commissionStats = [
            'Hoa hồng phát sinh' => number_format($reportData['commission_generated'] ?? 0) . ' VNĐ',
            'Hoa hồng đã thanh toán' => number_format($reportData['commission_paid'] ?? 0) . ' VNĐ',
            'Hoa hồng chờ thanh toán' => number_format($reportData['commission_pending'] ?? 0) . ' VNĐ',
            'Giới thiệu mới (trong kỳ)' => $reportData['new_referrals'] ?? 0
        ];
        
        foreach ($commissionStats as $label => $value) {
            $sheet->setCellValue('A' . $row, $label);
            $sheet->setCellValue('B' . $row, $value);
            $row++;
        }
        
        // Auto-size columns
        $sheet->getColumnDimension('A')->setAutoSize(true);
        $sheet->getColumnDimension('B')->setAutoSize(true);
        
        // Add borders to data area
        $dataRange = 'A5:B' . ($row - 1);
        $sheet->getStyle($dataRange)->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);
    }
    
    /**
     * Create Transaction Details sheet
     */
    private static function createReportTransactionDetailsSheet($spreadsheet, $pdo, $startDate, $endDate) {
        $sheet = $spreadsheet->createSheet();
        $sheet->setTitle('Chi tiết giao dịch');
        
        // Headers
        $headers = [
            'ID Giao dịch', 'Người dùng', 'Số tiền (VNĐ)', 'Trạng thái', 
            'Loại giao dịch', 'Ngày tạo', 'Ngày cập nhật'
        ];
        
        $col = 'A';
        foreach ($headers as $header) {
            $sheet->setCellValue($col . '1', $header);
            $sheet->getStyle($col . '1')->getFont()->setBold(true);
            $sheet->getStyle($col . '1')->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setRGB('E2E8F0');
            $col++;
        }
        
        // Get transaction data
        $stmt = $pdo->prepare("
            SELECT 
                th.id,
                u.username,
                th.amount,
                th.status,
                th.transaction_type,
                th.created_at,
                th.updated_at
            FROM transaction_history th
            LEFT JOIN user u ON th.user_id = u.id
            WHERE th.created_at BETWEEN :start AND :end
            ORDER BY th.created_at DESC
            LIMIT 1000
        ");
        $stmt->execute([':start' => $startDate . ' 00:00:00', ':end' => $endDate . ' 23:59:59']);
        
        $row = 2;
        while ($transaction = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $sheet->setCellValue('A' . $row, $transaction['id']);
            $sheet->setCellValue('B' . $row, $transaction['username'] ?: 'N/A');
            $sheet->setCellValue('C' . $row, number_format($transaction['amount']));
            $sheet->setCellValue('D' . $row, self::getTransactionStatusLabel($transaction['status']));
            $sheet->setCellValue('E' . $row, $transaction['transaction_type'] ?: 'N/A');
            $sheet->setCellValue('F' . $row, date('d/m/Y H:i', strtotime($transaction['created_at'])));
            $sheet->setCellValue('G' . $row, $transaction['updated_at'] ? date('d/m/Y H:i', strtotime($transaction['updated_at'])) : 'N/A');
            $row++;
        }
        
        // Auto-size columns
        foreach (range('A', 'G') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }
        
        // Style borders
        if ($row > 2) {
            $dataRange = 'A1:G' . ($row - 1);
            $sheet->getStyle($dataRange)->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);
        }
    }
    
    /**
     * Create Commission Details sheet
     */
    private static function createReportCommissionDetailsSheet($spreadsheet, $pdo, $startDate, $endDate) {
        $sheet = $spreadsheet->createSheet();
        $sheet->setTitle('Chi tiết hoa hồng');
        
        // Headers
        $headers = [
            'ID Yêu cầu', 'Collaborator', 'Số tiền (VNĐ)', 'Trạng thái', 
            'Ngày yêu cầu', 'Ngày xử lý'
        ];
        
        $col = 'A';
        foreach ($headers as $header) {
            $sheet->setCellValue($col . '1', $header);
            $sheet->getStyle($col . '1')->getFont()->setBold(true);
            $sheet->getStyle($col . '1')->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setRGB('E2E8F0');
            $col++;
        }
        
        // Get commission data
        $stmt = $pdo->prepare(
            "SELECT 
                wr.id,
                u.username,
                wr.amount,
                wr.status,
                wr.created_at,
                wr.updated_at
             FROM withdrawal_request wr
             LEFT JOIN user u ON wr.user_id = u.id
             WHERE wr.created_at BETWEEN :start AND :end
             ORDER BY wr.created_at DESC
             LIMIT 1000"
        );
        $stmt->execute([':start' => $startDate . ' 00:00:00', ':end' => $endDate . ' 23:59:59']);
        
        $row = 2;
        while ($commission = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $sheet->setCellValue('A' . $row, $commission['id']);
            $sheet->setCellValue('B' . $row, $commission['username'] ?: 'N/A');
            $sheet->setCellValue('C' . $row, number_format($commission['amount']));
            $sheet->setCellValue('D' . $row, self::getCommissionStatusLabel($commission['status']));
            $sheet->setCellValue('E' . $row, date('d/m/Y H:i', strtotime($commission['created_at'])));
            $sheet->setCellValue('F' . $row, $commission['updated_at'] ? date('d/m/Y H:i', strtotime($commission['updated_at'])) : 'N/A');
            $row++;
        }
        
        // Auto-size columns
        foreach (range('A', 'F') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }
        
        // Style borders
        if ($row > 2) {
            $dataRange = 'A1:F' . ($row - 1);
            $sheet->getStyle($dataRange)->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);
        }
    }
    
    /**
     * Create Charts Data sheet
     */
    private static function createReportChartsDataSheet($spreadsheet, $reportData) {
        $sheet = $spreadsheet->createSheet();
        $sheet->setTitle('Dữ liệu biểu đồ');
        
        $row = 1;
        
        // Revenue Trend Data
        if (isset($reportData['revenue_trend_chart_data'])) {
            $sheet->setCellValue('A' . $row, 'DỮ LIỆU BIỂU ĐỒ DOANH THU');
            $sheet->getStyle('A' . $row)->getFont()->setBold(true)->setSize(14);
            $sheet->getStyle('A' . $row)->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setRGB('E8F5E8');
            $row += 2;
            
            $sheet->setCellValue('A' . $row, 'Ngày');
            $sheet->setCellValue('B' . $row, 'Doanh thu (VNĐ)');
            $sheet->getStyle('A' . $row . ':B' . $row)->getFont()->setBold(true);
            $row++;
            
            $chartData = $reportData['revenue_trend_chart_data'];
            if (!empty($chartData['labels'])) {
                for ($i = 0; $i < count($chartData['labels']); $i++) {
                    $sheet->setCellValue('A' . $row, date('d/m/Y', strtotime($chartData['labels'][$i])));
                    $sheet->setCellValue('B' . $row, $chartData['data'][$i] ?? 0);
                    $row++;
                }
            }
            $row += 3;
        }
        
        // Transaction Status Data
        if (isset($reportData['transaction_status_chart_data'])) {
            $sheet->setCellValue('A' . $row, 'DỮ LIỆU TRẠNG THÁI GIAO DỊCH');
            $sheet->getStyle('A' . $row)->getFont()->setBold(true)->setSize(14);
            $sheet->getStyle('A' . $row)->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setRGB('E3F2FD');
            $row += 2;
            
            $sheet->setCellValue('A' . $row, 'Trạng thái');
            $sheet->setCellValue('B' . $row, 'Số lượng');
            $sheet->setCellValue('C' . $row, 'Tổng tiền (VNĐ)');
            $sheet->getStyle('A' . $row . ':C' . $row)->getFont()->setBold(true);
            $row++;
            
            $statusData = $reportData['transaction_status_chart_data'];
            for ($i = 0; $i < count($statusData['labels']); $i++) {
                $sheet->setCellValue('A' . $row, $statusData['labels'][$i]);
                $sheet->setCellValue('B' . $row, $statusData['data'][$i] ?? 0);
                $sheet->setCellValue('C' . $row, $statusData['amounts'][$i] ?? 0);
                $row++;
            }
            $row += 3;
        }
        
        // Commission Analytics Data
        if (isset($reportData['commission_analytics_chart_data'])) {
            $sheet->setCellValue('A' . $row, 'DỮ LIỆU PHÂN TÍCH HOA HỒNG');
            $sheet->getStyle('A' . $row)->getFont()->setBold(true)->setSize(14);
            $sheet->getStyle('A' . $row)->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setRGB('FFF3E0');
            $row += 2;
            
            $sheet->setCellValue('A' . $row, 'Trạng thái');
            $sheet->setCellValue('B' . $row, 'Số lượng');
            $sheet->setCellValue('C' . $row, 'Tổng tiền (VNĐ)');
            $sheet->getStyle('A' . $row . ':C' . $row)->getFont()->setBold(true);
            $row++;
            
            $commissionData = $reportData['commission_analytics_chart_data'];
            for ($i = 0; $i < count($commissionData['labels']); $i++) {
                $sheet->setCellValue('A' . $row, $commissionData['labels'][$i]);
                $sheet->setCellValue('B' . $row, $commissionData['data'][$i] ?? 0);
                $sheet->setCellValue('C' . $row, $commissionData['amounts'][$i] ?? 0);
                $row++;
            }
        }
        
        // Auto-size columns
        foreach (range('A', 'C') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }
    }
    
    /**
     * Create Visualizations sheet with chart-like tables
     */
    private static function createReportVisualizationsSheet($spreadsheet, $reportData) {
        $sheet = $spreadsheet->createSheet();
        $sheet->setTitle('Biểu đồ & Phân tích');
        
        // Title
        $sheet->setCellValue('A1', 'BIỂU ĐỒ & PHÂN TÍCH DỮ LIỆU');
        $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(16);
        $sheet->getStyle('A1')->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setRGB('A5D6A7');
        $sheet->getStyle('A1')->getFont()->getColor()->setRGB('FFFFFF');
        
        $currentRow = 3;
        
        // 1. Revenue Trend Chart
        if (isset($reportData['revenue_trend_chart_data']) && !empty($reportData['revenue_trend_chart_data']['labels'])) {
            $currentRow = self::createRevenueTrendTable($sheet, $reportData['revenue_trend_chart_data'], $currentRow);
            $currentRow += 3;
        }
        
        // 2. Transaction Status Chart
        if (isset($reportData['transaction_status_chart_data'])) {
            $currentRow = self::createTransactionStatusTable($sheet, $reportData['transaction_status_chart_data'], $currentRow);
            $currentRow += 3;
        }
        
        // 3. Commission Analytics Chart
        if (isset($reportData['commission_analytics_chart_data'])) {
            $currentRow = self::createCommissionAnalyticsTable($sheet, $reportData['commission_analytics_chart_data'], $currentRow);
        }
    }
    
    /**
     * Create Revenue Trend visual table
     */
    private static function createRevenueTrendTable($sheet, $chartData, $startRow) {
        $sheet->setCellValue('A' . $startRow, '📈 XU HƯỚNG DOANH THU');
        $sheet->getStyle('A' . $startRow)->getFont()->setBold(true)->setSize(14);
        $sheet->getStyle('A' . $startRow)->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setRGB('2196F3');
        $sheet->getStyle('A' . $startRow)->getFont()->getColor()->setRGB('FFFFFF');
        $startRow += 2;
        
        // Headers with background color
        $sheet->setCellValue('A' . $startRow, 'Ngày');
        $sheet->setCellValue('B' . $startRow, 'Doanh thu (VNĐ)');
        $sheet->setCellValue('C' . $startRow, 'Biểu đồ cột');
        $sheet->getStyle('A' . $startRow . ':C' . $startRow)->getFont()->setBold(true);
        $sheet->getStyle('A' . $startRow . ':C' . $startRow)->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setRGB('E3F2FD');
        
        $dataStartRow = $startRow + 1;
        $maxValue = max($chartData['data']);
        
        for ($i = 0; $i < count($chartData['labels']); $i++) {
            $rowIndex = $dataStartRow + $i;
            $value = $chartData['data'][$i] ?? 0;
            
            $sheet->setCellValue('A' . $rowIndex, date('d/m', strtotime($chartData['labels'][$i])));
            $sheet->setCellValue('B' . $rowIndex, number_format($value));
            
            // Create visual bar using characters
            $barLength = $maxValue > 0 ? round(($value / $maxValue) * 20) : 0;
            $bar = str_repeat('█', $barLength) . str_repeat('▒', 20 - $barLength);
            $sheet->setCellValue('C' . $rowIndex, $bar);
            
            // Color coding based on value
            if ($value > $maxValue * 0.8) {
                $sheet->getStyle('B' . $rowIndex)->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setRGB('C8E6C9');
            } elseif ($value > $maxValue * 0.5) {
                $sheet->getStyle('B' . $rowIndex)->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setRGB('FFF9C4');
            }
        }
        
        $dataEndRow = $dataStartRow + count($chartData['labels']) - 1;
        
        // Auto-size columns
        $sheet->getColumnDimension('A')->setAutoSize(true);
        $sheet->getColumnDimension('B')->setAutoSize(true);
        $sheet->getColumnDimension('C')->setWidth(25);
        
        // Add borders
        $sheet->getStyle('A' . $startRow . ':C' . $dataEndRow)->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);
        
        return $dataEndRow + 1;
    }
    
    /**
     * Create Transaction Status visual table
     */
    private static function createTransactionStatusTable($sheet, $chartData, $startRow) {
        $sheet->setCellValue('A' . $startRow, '🔄 PHÂN TÍCH TRẠNG THÁI GIAO DỊCH');
        $sheet->getStyle('A' . $startRow)->getFont()->setBold(true)->setSize(14);
        $sheet->getStyle('A' . $startRow)->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setRGB('FF9800');
        $sheet->getStyle('A' . $startRow)->getFont()->getColor()->setRGB('FFFFFF');
        $startRow += 2;
        
        // Headers
        $sheet->setCellValue('A' . $startRow, 'Trạng thái');
        $sheet->setCellValue('B' . $startRow, 'Số lượng');
        $sheet->setCellValue('C' . $startRow, 'Tổng tiền (VNĐ)');
        $sheet->setCellValue('D' . $startRow, 'Tỷ lệ %');
        $sheet->setCellValue('E' . $startRow, 'Biểu đồ tròn');
        $sheet->getStyle('A' . $startRow . ':E' . $startRow)->getFont()->setBold(true);
        $sheet->getStyle('A' . $startRow . ':E' . $startRow)->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setRGB('FFE0B2');
        
        $dataStartRow = $startRow + 1;
        $totalCount = array_sum($chartData['data']);
        $colors = ['C8E6C9', 'FFF9C4', 'FFCDD2', 'E1BEE7']; // pastel green, yellow, light red, light purple
        
        for ($i = 0; $i < count($chartData['labels']); $i++) {
            $rowIndex = $dataStartRow + $i;
            $count = $chartData['data'][$i] ?? 0;
            $amount = $chartData['amounts'][$i] ?? 0;
            $percentage = $totalCount > 0 ? round(($count / $totalCount) * 100, 1) : 0;
            
            $sheet->setCellValue('A' . $rowIndex, $chartData['labels'][$i]);
            $sheet->setCellValue('B' . $rowIndex, number_format($count));
            $sheet->setCellValue('C' . $rowIndex, number_format($amount));
            $sheet->setCellValue('D' . $rowIndex, $percentage . '%');
            
            // Create visual pie slice representation
            $pieSlice = str_repeat('●', round($percentage / 5)) . ' (' . $percentage . '%)';
            $sheet->setCellValue('E' . $rowIndex, $pieSlice);
            
            // Color coding
            $colorIndex = $i % count($colors);
            $sheet->getStyle('A' . $rowIndex . ':E' . $rowIndex)
                  ->getFill()
                  ->setFillType(Fill::FILL_SOLID)
                  ->getStartColor()
                  ->setRGB($colors[$colorIndex]);
        }
        
        $dataEndRow = $dataStartRow + count($chartData['labels']) - 1;
        
        // Auto-size columns
        foreach (range('A', 'E') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }
        
        // Add borders
        $sheet->getStyle('A' . $startRow . ':E' . $dataEndRow)->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);
        
        return $dataEndRow + 1;
    }
    
    /**
     * Create Commission Analytics visual table
     */
    private static function createCommissionAnalyticsTable($sheet, $chartData, $startRow) {
        $sheet->setCellValue('A' . $startRow, '💰 PHÂN TÍCH HOA HỒNG');
        $sheet->getStyle('A' . $startRow)->getFont()->setBold(true)->setSize(14);
        $sheet->getStyle('A' . $startRow)->getFill()
              ->setFillType(Fill::FILL_SOLID)
              ->getStartColor()
              ->setRGB('E1BEE7'); // was '9C27B0'
        $sheet->getStyle('A' . $startRow)->getFont()->getColor()->setRGB('FFFFFF');
        $startRow += 2;
        
        // Headers
        $sheet->setCellValue('A' . $startRow, 'Trạng thái');
        $sheet->setCellValue('B' . $startRow, 'Số yêu cầu');
        $sheet->setCellValue('C' . $startRow, 'Tổng tiền (VNĐ)');
        $sheet->setCellValue('D' . $startRow, 'Tỷ lệ %');
        $sheet->setCellValue('E' . $startRow, 'Mức độ');
        $sheet->getStyle('A' . $startRow . ':E' . $startRow)->getFont()->setBold(true);
        $sheet->getStyle('A' . $startRow . ':E' . $startRow)->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setRGB('E1BEE7');
        
        $dataStartRow = $startRow + 1;
        $totalCount = array_sum($chartData['data']);
        $colors = ['C8E6C9', 'FFE0B2', 'FFCDD2']; // pastel green, light orange, light red
        
        for ($i = 0; $i < count($chartData['labels']); $i++) {
            $rowIndex = $dataStartRow + $i;
            $count = $chartData['data'][$i] ?? 0;
            $amount = $chartData['amounts'][$i] ?? 0;
            $percentage = $totalCount > 0 ? round(($count / $totalCount) * 100, 1) : 0;
            
            $sheet->setCellValue('A' . $rowIndex, $chartData['labels'][$i]);
            $sheet->setCellValue('B' . $rowIndex, number_format($count));
            $sheet->setCellValue('C' . $rowIndex, number_format($amount));
            $sheet->setCellValue('D' . $rowIndex, $percentage . '%');
            
            // Create visual level indicator
            $levelStars = str_repeat('⭐', min(5, round($percentage / 20)));
            $sheet->setCellValue('E' . $rowIndex, $levelStars . ' ' . $percentage . '%');
            
            // Color coding
            $colorIndex = $i % count($colors);
            $sheet->getStyle('A' . $rowIndex . ':E' . $rowIndex)
                  ->getFill()
                  ->setFillType(Fill::FILL_SOLID)
                  ->getStartColor()
                  ->setRGB($colors[$colorIndex]);
        }
        
        $dataEndRow = $dataStartRow + count($chartData['labels']) - 1;
        
        // Auto-size columns
        foreach (range('A', 'E') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }
        
        // Add borders
        $sheet->getStyle('A' . $startRow . ':E' . $dataEndRow)->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);
        
        return $dataEndRow + 1;
    }
    
    /**
     * Get status label for transactions
     */
    private static function getTransactionStatusLabel($status) {
        switch ($status) {
            case 'completed': return 'Thành công';
            case 'pending': return 'Chờ duyệt';
            case 'failed': return 'Thất bại';
            default: return ucfirst($status);
        }
    }
    
    /**
     * Get status label for commissions
     */
    private static function getCommissionStatusLabel($status) {
        switch ($status) {
            case 'completed': return 'Đã thanh toán';
            case 'pending': return 'Chờ thanh toán';
            case 'rejected': return 'Từ chối';
            default: return ucfirst($status);
        }
    }
}
