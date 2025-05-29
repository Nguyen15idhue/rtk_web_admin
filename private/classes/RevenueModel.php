<?php
// filepath: private/classes/RevenueModel.php
declare(strict_types=1);

require_once __DIR__ . '/Database.php';

class RevenueModel {
    private $db;

    public function __construct($db = null) {
        if ($db) {
            $this->db = $db;
        } else {
            require_once __DIR__ . '/Database.php';
            $this->db = Database::getInstance()->getConnection();
        }
    }

    /**
     * Get revenue summary data for export
     *
     * @param array $filters Associative array with date and status filters
     * @return array Revenue summary data for Excel export
     */
    public function getRevenueSummaryForExport(array $filters = []): array {
        try {
            // Get revenue sums
            require_once __DIR__ . '/../actions/invoice/get_revenue_sums.php';
            list($total_revenue, $successful_revenue, $pending_revenue, $rejected_revenue) = get_revenue_sums($filters);
            
            // Get revenue stats
            require_once __DIR__ . '/../actions/purchase/get_revenue_stats.php';
            $stats = get_revenue_stats($filters);
            
            // Calculate additional metrics
            $success_rate = $total_revenue > 0 ? ($successful_revenue / $total_revenue * 100) : 0;
            $pending_rate = $total_revenue > 0 ? ($pending_revenue / $total_revenue * 100) : 0;
            $rejection_rate = $total_revenue > 0 ? ($rejected_revenue / $total_revenue * 100) : 0;
            
            // Get transaction count by status
            $transaction_counts = $this->getTransactionCounts($filters);
            
            $export_data = [];
            
            // Summary row
            $export_data[] = [
                'Loại' => 'Tổng quan',
                'Mô tả' => 'Báo cáo tổng hợp doanh thu',
                'Giá trị' => '',
                'Đơn vị' => '',
                'Ghi chú' => 'Thời gian: ' . ($filters['date_from'] ?? 'Từ đầu') . ' đến ' . ($filters['date_to'] ?? 'hiện tại')
            ];
            
            // Revenue metrics
            $export_data[] = [
                'Loại' => 'Doanh thu',
                'Mô tả' => 'Tổng doanh thu',
                'Giá trị' => (int)$total_revenue,
                'Đơn vị' => 'VNĐ',
                'Ghi chú' => 'Bao gồm tất cả giao dịch'
            ];
            
            $export_data[] = [
                'Loại' => 'Doanh thu',
                'Mô tả' => 'Doanh thu thành công',
                'Giá trị' => (int)$successful_revenue,
                'Đơn vị' => 'VNĐ',
                'Ghi chú' => 'Chỉ giao dịch đã được duyệt'
            ];
            
            $export_data[] = [
                'Loại' => 'Doanh thu',
                'Mô tả' => 'Doanh thu chờ duyệt',
                'Giá trị' => (int)$pending_revenue,
                'Đơn vị' => 'VNĐ',
                'Ghi chú' => 'Giao dịch đang chờ phê duyệt'
            ];
            
            $export_data[] = [
                'Loại' => 'Doanh thu',
                'Mô tả' => 'Doanh thu bị từ chối',
                'Giá trị' => (int)$rejected_revenue,
                'Đơn vị' => 'VNĐ',
                'Ghi chú' => 'Giao dịch bị từ chối'
            ];
            
            // Performance metrics
            $export_data[] = [
                'Loại' => 'Hiệu suất',
                'Mô tả' => 'Tỷ lệ thành công',
                'Giá trị' => number_format($success_rate, 2),
                'Đơn vị' => '%',
                'Ghi chú' => 'Tỷ lệ doanh thu thành công/tổng doanh thu'
            ];
            
            $export_data[] = [
                'Loại' => 'Hiệu suất',
                'Mô tả' => 'Tỷ lệ chờ duyệt',
                'Giá trị' => number_format($pending_rate, 2),
                'Đơn vị' => '%',
                'Ghi chú' => 'Tỷ lệ giao dịch chờ duyệt'
            ];
            
            $export_data[] = [
                'Loại' => 'Hiệu suất',
                'Mô tả' => 'Tỷ lệ từ chối',
                'Giá trị' => number_format($rejection_rate, 2),
                'Đơn vị' => '%',
                'Ghi chú' => 'Tỷ lệ giao dịch bị từ chối'
            ];
            
            // Transaction counts
            $export_data[] = [
                'Loại' => 'Giao dịch',
                'Mô tả' => 'Tổng số giao dịch',
                'Giá trị' => number_format($transaction_counts['total'], 0, ',', '.'),
                'Đơn vị' => 'Giao dịch',
                'Ghi chú' => 'Tất cả giao dịch trong kỳ'
            ];
            
            $export_data[] = [
                'Loại' => 'Giao dịch',
                'Mô tả' => 'Giao dịch thành công',
                'Giá trị' => number_format($transaction_counts['completed'], 0, ',', '.'),
                'Đơn vị' => 'Giao dịch',
                'Ghi chú' => 'Giao dịch đã được duyệt'
            ];
            
            $export_data[] = [
                'Loại' => 'Giao dịch',
                'Mô tả' => 'Giao dịch chờ duyệt',
                'Giá trị' => number_format($transaction_counts['pending'], 0, ',', '.'),
                'Đơn vị' => 'Giao dịch',
                'Ghi chú' => 'Giao dịch đang chờ phê duyệt'
            ];
            
            $export_data[] = [
                'Loại' => 'Giao dịch',
                'Mô tả' => 'Giao dịch bị từ chối',
                'Giá trị' => number_format($transaction_counts['failed'], 0, ',', '.'),
                'Đơn vị' => 'Giao dịch',
                'Ghi chú' => 'Giao dịch bị từ chối'
            ];
            
            // Transaction type breakdown
            if (!empty($stats['type_breakdown'])) {
                foreach ($stats['type_breakdown'] as $type) {
                    $type_name = $type['transaction_type'] === 'renewal' ? 'Gia hạn' : 'Đăng ký mới';
                    $export_data[] = [
                        'Loại' => 'Loại giao dịch',
                        'Mô tả' => $type_name,
                        'Giá trị' => number_format((float)$type['amount'], 0, ',', '.'),
                        'Đơn vị' => 'VNĐ',
                        'Ghi chú' => number_format((int)$type['count'], 0, ',', '.') . ' giao dịch'
                    ];
                }
            }
            
            // Top packages
            if (!empty($stats['package_stats'])) {
                $export_data[] = [
                    'Loại' => 'Top gói',
                    'Mô tả' => 'Các gói có doanh thu cao nhất',
                    'Giá trị' => '',
                    'Đơn vị' => '',
                    'Ghi chú' => 'Top ' . count($stats['package_stats']) . ' gói'
                ];
                
                foreach (array_slice($stats['package_stats'], 0, 5) as $package) {
                    $export_data[] = [
                        'Loại' => 'Gói',
                        'Mô tả' => $package['package_name'],
                        'Giá trị' => number_format((float)$package['total_revenue'], 0, ',', '.'),
                        'Đơn vị' => 'VNĐ',
                        'Ghi chú' => number_format((int)$package['transaction_count'], 0, ',', '.') . ' giao dịch'
                    ];
                }
            }
            
            // Daily stats summary (last 7 days)
            if (!empty($stats['daily_stats'])) {
                $export_data[] = [
                    'Loại' => 'Thống kê hàng ngày',
                    'Mô tả' => 'Doanh thu 7 ngày gần nhất',
                    'Giá trị' => '',
                    'Đơn vị' => '',
                    'Ghi chú' => 'Chi tiết theo ngày'
                ];
                
                foreach (array_slice($stats['daily_stats'], 0, 7) as $daily) {
                    $export_data[] = [
                        'Loại' => 'Ngày',
                        'Mô tả' => date('d/m/Y', strtotime($daily['date'])),
                        'Giá trị' => number_format((float)$daily['total_amount'], 0, ',', '.'),
                        'Đơn vị' => 'VNĐ',
                        'Ghi chú' => number_format((int)$daily['transaction_count'], 0, ',', '.') . ' giao dịch'
                    ];
                }
            }
            
            return $export_data;
            
        } catch (Exception $e) {
            error_log("Error in getRevenueSummaryForExport: " . $e->getMessage());
            return [[
                'Loại' => 'Lỗi',
                'Mô tả' => 'Không thể tạo báo cáo',
                'Giá trị' => '',
                'Đơn vị' => '',
                'Ghi chú' => 'Lỗi: ' . $e->getMessage()
            ]];
        }
    }
    
    /**
     * Get transaction counts by status
     *
     * @param array $filters
     * @return array
     */
    private function getTransactionCounts(array $filters = []): array {
        $conditions = "WHERE 1=1";
        $params = [];
        
        if (!empty($filters['date_from'])) {
            $conditions .= " AND DATE(created_at) >= :date_from";
            $params[':date_from'] = $filters['date_from'];
        }
        
        if (!empty($filters['date_to'])) {
            $conditions .= " AND DATE(created_at) <= :date_to";
            $params[':date_to'] = $filters['date_to'];
        }
        
        $sql = "
            SELECT 
                COUNT(*) as total,
                SUM(CASE WHEN status = 'completed' THEN 1 ELSE 0 END) as completed,
                SUM(CASE WHEN status = 'pending' THEN 1 ELSE 0 END) as pending,
                SUM(CASE WHEN status = 'failed' THEN 1 ELSE 0 END) as failed
            FROM transaction_history 
            $conditions
        ";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        
        return [
            'total' => (int)$result['total'],
            'completed' => (int)$result['completed'],
            'pending' => (int)$result['pending'],
            'failed' => (int)$result['failed']
        ];
    }
    
    /**
     * Get all data for export (fallback method for standard export interface)
     */
    public function getAllDataForExport(array $filters = []): array {
        return $this->getRevenueSummaryForExport($filters);
    }
    
    /**
     * Get data by IDs for export (not applicable for revenue summary)
     */
    public function getDataByIdsForExport(array $ids): array {
        // Revenue summary doesn't use specific IDs, return general summary
        return $this->getRevenueSummaryForExport();
    }
}
