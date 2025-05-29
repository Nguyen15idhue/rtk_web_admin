<?php

class ReportModel {
    public function getComprehensiveReportData($pdo, $start_datetime, $end_datetime) {
        $data = [];

        // Replace user counts (total, active, locked) with one combined query
        $stmt = $pdo->prepare("
            SELECT
                COUNT(id) as total_registrations,
                SUM(CASE WHEN deleted_at IS NULL THEN 1 ELSE 0 END) as active_accounts,
                SUM(CASE WHEN deleted_at IS NOT NULL THEN 1 ELSE 0 END) as locked_accounts
            FROM user
        ");
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        $data['total_registrations'] = (int)($row['total_registrations'] ?? 0);
        $data['active_accounts'] = (int)($row['active_accounts'] ?? 0);
        $data['locked_accounts'] = (int)($row['locked_accounts'] ?? 0);

        // New registrations in period
        $stmt = $pdo->prepare("
            SELECT COUNT(id) as count
            FROM user
            WHERE deleted_at IS NULL
              AND created_at BETWEEN :start AND :end
        ");
        $stmt->execute([':start'=>$start_datetime,':end'=>$end_datetime]);
        $data['new_registrations'] = ($row = $stmt->fetch(PDO::FETCH_ASSOC)) ? $row['count'] : 0;

        // Combined survey-account statistics
        $stmt = $pdo->prepare("
            SELECT
                SUM(CASE WHEN sa.enabled = 1 AND sa.deleted_at IS NULL AND r.deleted_at IS NULL THEN 1 ELSE 0 END) AS active_survey_accounts,
                SUM(CASE WHEN sa.deleted_at IS NULL AND r.deleted_at IS NULL AND sa.created_at BETWEEN :start AND :end THEN 1 ELSE 0 END) AS new_active_survey_accounts,
                SUM(CASE WHEN sa.end_time BETWEEN NOW() AND DATE_ADD(NOW(), INTERVAL 30 DAY) AND sa.deleted_at IS NULL AND r.deleted_at IS NULL THEN 1 ELSE 0 END) AS expiring_accounts,
                SUM(CASE WHEN sa.end_time BETWEEN :start2 AND :end2 AND sa.deleted_at IS NULL AND r.deleted_at IS NULL THEN 1 ELSE 0 END) AS expired_accounts
            FROM survey_account sa
            JOIN registration r ON sa.registration_id = r.id
        ");
        $stmt->execute([
            ':start'  => $start_datetime,
            ':end'    => $end_datetime,
            ':start2' => $start_datetime,
            ':end2'   => $end_datetime,
        ]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        $data['active_survey_accounts']     = (int)$row['active_survey_accounts'];
        $data['new_active_survey_accounts'] = (int)$row['new_active_survey_accounts'];
        $data['expiring_accounts']          = (int)$row['expiring_accounts'];
        $data['expired_accounts']           = (int)$row['expired_accounts'];

        // Transactions
        // Total sales in period
        $stmt = $pdo->prepare("
            SELECT SUM(th.amount) AS total
            FROM transaction_history th
            WHERE th.status = 'completed'
              AND th.created_at BETWEEN :start AND :end
        ");
        $stmt->execute([':start' => $start_datetime, ':end' => $end_datetime]);
        $data['total_sales'] = ($row = $stmt->fetch(PDO::FETCH_ASSOC)) ? $row['total'] : 0;
        if ($data['total_sales'] === null) {
            $data['total_sales'] = 0;
        }

        // Combined transaction counts by status
        $stmt = $pdo->prepare("
            SELECT
                COALESCE(SUM(CASE WHEN status = 'completed' THEN 1 ELSE 0 END), 0) as completed_count,
                COALESCE(SUM(CASE WHEN status = 'pending' THEN 1 ELSE 0 END), 0) as pending_count,
                COALESCE(SUM(CASE WHEN status = 'failed' THEN 1 ELSE 0 END), 0) as failed_count
            FROM transaction_history
            WHERE created_at BETWEEN :start AND :end
              AND status IN ('completed', 'pending', 'failed')
        ");
        $stmt->execute([':start' => $start_datetime, ':end' => $end_datetime]);
        $transactionCountsByStatus = $stmt->fetch(PDO::FETCH_ASSOC);
        
        $data['completed_transactions'] = (int)$transactionCountsByStatus['completed_count'];
        $data['pending_transactions']   = (int)$transactionCountsByStatus['pending_count'];
        $data['failed_transactions']    = (int)$transactionCountsByStatus['failed_count'];

        // Referrals
        // New referrals in period
        $stmt = $pdo->prepare("SELECT COUNT(id) as count FROM registration WHERE collaborator_id IS NOT NULL AND deleted_at IS NULL AND created_at BETWEEN :start AND :end");
        $stmt->execute([':start'=>$start_datetime, ':end'=>$end_datetime]);
        $data['new_referrals'] = (int)($stmt->fetchColumn() ?: 0);

        // Combined commission statistics (generated, paid, pending)
        $stmt = $pdo->prepare("
            SELECT
                COALESCE(SUM(CASE WHEN created_at BETWEEN :cg_start AND :cg_end THEN amount ELSE 0 END),0) AS commission_generated,
                COALESCE(SUM(CASE WHEN status = 'completed' AND updated_at BETWEEN :cp_start AND :cp_end THEN amount ELSE 0 END),0) AS commission_paid,
                COALESCE(SUM(CASE WHEN status = 'pending' AND created_at BETWEEN :cpend_start AND :cpend_end THEN amount ELSE 0 END),0) AS commission_pending
            FROM withdrawal_request
        ");
        $stmt->execute([
            ':cg_start'    => $start_datetime,
            ':cg_end'      => $end_datetime,
            ':cp_start'    => $start_datetime,
            ':cp_end'      => $end_datetime,
            ':cpend_start' => $start_datetime,
            ':cpend_end'   => $end_datetime,
        ]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        $data['commission_generated'] = (float)$row['commission_generated'];
        $data['commission_paid']      = (float)$row['commission_paid'];
        $data['commission_pending']   = (float)$row['commission_pending'];

        // Chart data: new registrations per day within period
        $stmt = $pdo->prepare(
            "SELECT DATE(created_at) AS d, COUNT(*) AS c
             FROM user
             WHERE created_at BETWEEN :start AND :end
             GROUP BY DATE(created_at)
             ORDER BY d"
        );
        $stmt->execute([':start' => $start_datetime, ':end' => $end_datetime]);
        $labels = [];
        $dataReg = [];
        foreach ($stmt->fetchAll(PDO::FETCH_ASSOC) as $r) {
            $labels[]  = $r['d'];
            $dataReg[] = (int)$r['c'];
        }
        $data['new_registrations_chart_data'] = ['labels' => $labels, 'data' => $dataReg];

        // Chart data: new referrals per day within period
        $stmt = $pdo->prepare(
            "SELECT DATE(created_at) AS d, COUNT(*) AS c
             FROM referral
             WHERE created_at BETWEEN :start AND :end
             GROUP BY DATE(created_at)
             ORDER BY d"
        );
        $stmt->execute([':start' => $start_datetime, ':end' => $end_datetime]);
        $labels = [];
        $dataRef = [];
        foreach ($stmt->fetchAll(PDO::FETCH_ASSOC) as $r) {
            $labels[] = $r['d'];
            $dataRef[] = (int)$r['c'];
        }
        $data['referral_chart_data'] = ['labels' => $labels, 'data' => $dataRef];

        // User package distribution (new registrations within period)
        $labels = [];
        $counts = [];
        $stmt = $pdo->prepare(
            "SELECT p.name AS package_name, COUNT(r.user_id) AS user_count
             FROM package p
             LEFT JOIN registration r ON p.id = r.package_id
                 AND r.deleted_at IS NULL
                 AND r.created_at BETWEEN :start AND :end
             GROUP BY p.id ORDER BY p.name"
        );
        $stmt->execute([':start' => $start_datetime, ':end' => $end_datetime]);
        foreach ($stmt->fetchAll(PDO::FETCH_ASSOC) as $r) {
            $labels[] = htmlspecialchars($r['package_name']);
            $counts[] = (int)$r['user_count'];
        }
        $data['user_package_distribution'] = ['labels' => $labels, 'data' => $counts];

        // User package ratio for new registrations within period
        $totalNew = $data['new_registrations'];
        $stmt = $pdo->prepare(
            "SELECT COUNT(*) FROM registration
             WHERE package_id IS NOT NULL
               AND deleted_at IS NULL
               AND created_at BETWEEN :start AND :end"
        );
        $stmt->execute([':start' => $start_datetime, ':end' => $end_datetime]);
        $withPkg = (int)$stmt->fetchColumn();
        $withoutPkg = max(0, $totalNew - $withPkg);
        $data['user_package_ratio'] = ['with_package' => $withPkg, 'without_package' => $withoutPkg];

        // Chart data: Revenue trend per day within period
        $stmt = $pdo->prepare(
            "SELECT DATE(created_at) AS d, SUM(amount) AS revenue
             FROM transaction_history
             WHERE status = 'completed'
               AND created_at BETWEEN :start AND :end
             GROUP BY DATE(created_at)
             ORDER BY d"
        );
        $stmt->execute([':start' => $start_datetime, ':end' => $end_datetime]);
        $labels = [];
        $revenueData = [];
        foreach ($stmt->fetchAll(PDO::FETCH_ASSOC) as $r) {
            $labels[] = $r['d'];
            $revenueData[] = (float)($r['revenue'] ?: 0);
        }
        $data['revenue_trend_chart_data'] = ['labels' => $labels, 'data' => $revenueData];

        // Chart data: Transaction status breakdown (pie chart)
        $stmt = $pdo->prepare(
            "SELECT 
                status,
                COUNT(*) as count,
                SUM(amount) as total_amount
             FROM transaction_history 
             WHERE created_at BETWEEN :start AND :end
               AND status IN ('completed', 'pending', 'failed')
             GROUP BY status"
        );
        $stmt->execute([':start' => $start_datetime, ':end' => $end_datetime]);
        
        $statusData = ['completed' => 0, 'pending' => 0, 'failed' => 0];
        $statusAmounts = ['completed' => 0, 'pending' => 0, 'failed' => 0];
        
        foreach ($stmt->fetchAll(PDO::FETCH_ASSOC) as $r) {
            $statusData[$r['status']] = (int)$r['count'];
            $statusAmounts[$r['status']] = (float)$r['total_amount'];
        }
        
        $data['transaction_status_chart_data'] = [
            'labels' => ['Thành công', 'Chờ duyệt', 'Từ chối'],
            'data' => [
                $statusData['completed'], 
                $statusData['pending'], 
                $statusData['failed']
            ],
            'amounts' => [
                $statusAmounts['completed'],
                $statusAmounts['pending'], 
                $statusAmounts['failed']
            ]
        ];

        // Chart data: Commission Analytics breakdown
        $stmt = $pdo->prepare(
            "SELECT 
                wr.status,
                COUNT(*) as count,
                SUM(wr.amount) as total_amount
             FROM withdrawal_request wr
             WHERE wr.created_at BETWEEN :start AND :end
               AND wr.status IN ('completed', 'pending', 'rejected')
             GROUP BY wr.status"
        );
        $stmt->execute([':start' => $start_datetime, ':end' => $end_datetime]);
        
        $commissionStatusData = ['completed' => 0, 'pending' => 0, 'rejected' => 0];
        $commissionStatusAmounts = ['completed' => 0, 'pending' => 0, 'rejected' => 0];
        
        foreach ($stmt->fetchAll(PDO::FETCH_ASSOC) as $r) {
            $commissionStatusData[$r['status']] = (int)$r['count'];
            $commissionStatusAmounts[$r['status']] = (float)$r['total_amount'];
        }
        
        $data['commission_analytics_chart_data'] = [
            'labels' => ['Đã thanh toán', 'Chờ thanh toán', 'Từ chối'],
            'data' => [
                $commissionStatusData['completed'], 
                $commissionStatusData['pending'], 
                $commissionStatusData['rejected']
            ],
            'amounts' => [
                $commissionStatusAmounts['completed'],
                $commissionStatusAmounts['pending'], 
                $commissionStatusAmounts['rejected']
            ]
        ];

        return $data;
    }
}
?>
