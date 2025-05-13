<?php

class ReportModel {
    public function getComprehensiveReportData($pdo, $start_datetime, $end_datetime) {
        $data = [];

        // Total registrations
        $stmt = $pdo->query("SELECT COUNT(id) as count FROM user");
        $data['total_registrations'] = ($row = $stmt->fetch(PDO::FETCH_ASSOC)) ? $row['count'] : 0;

        // New registrations in period
        $stmt = $pdo->prepare("
            SELECT COUNT(id) as count
            FROM user
            WHERE deleted_at IS NULL
              AND created_at BETWEEN :start AND :end
        ");
        $stmt->execute([':start'=>$start_datetime,':end'=>$end_datetime]);
        $data['new_registrations'] = ($row = $stmt->fetch(PDO::FETCH_ASSOC)) ? $row['count'] : 0;

        // Active accounts
        $stmt = $pdo->query("SELECT COUNT(id) as count FROM user WHERE deleted_at IS NULL");
        $data['active_accounts'] = ($row = $stmt->fetch(PDO::FETCH_ASSOC)) ? $row['count'] : 0;

        // Locked accounts (non-active)
        $stmt = $pdo->query("SELECT COUNT(id) as count FROM user WHERE deleted_at IS NOT NULL");
        $data['locked_accounts'] = ($row = $stmt->fetch(PDO::FETCH_ASSOC)) ? $row['count'] : 0;

        // Active survey accounts
        $stmt = $pdo->query("SELECT COUNT(sa.id) as count FROM survey_account sa JOIN registration r ON sa.registration_id = r.id WHERE sa.enabled = 1 AND sa.deleted_at IS NULL AND r.deleted_at IS NULL");
        $data['active_survey_accounts'] = ($row = $stmt->fetch(PDO::FETCH_ASSOC)) ? $row['count'] : 0;

        // New active survey accounts in period
        $stmt = $pdo->prepare("
            SELECT COUNT(sa.id) as count
            FROM survey_account sa
            JOIN registration r ON sa.registration_id = r.id
            WHERE sa.deleted_at IS NULL
              AND r.deleted_at IS NULL
              AND sa.created_at BETWEEN :start AND :end
        ");
        $stmt->execute([':start'=>$start_datetime,':end'=>$end_datetime]);
        $data['new_active_survey_accounts'] = ($row = $stmt->fetch(PDO::FETCH_ASSOC)) ? $row['count'] : 0;

        // Accounts expiring in 30 days
        $stmt = $pdo->query("
            SELECT COUNT(sa.id) as count
            FROM survey_account sa
            JOIN registration r ON sa.registration_id = r.id
            WHERE sa.end_time BETWEEN NOW() AND DATE_ADD(NOW(), INTERVAL 30 DAY)
              AND sa.deleted_at IS NULL
              AND r.deleted_at IS NULL
        ");
        $data['expiring_accounts'] = ($row = $stmt->fetch(PDO::FETCH_ASSOC)) ? $row['count'] : 0;

        // Accounts expired in period
        $stmt = $pdo->prepare("
            SELECT COUNT(sa.id) as count
            FROM survey_account sa
            JOIN registration r ON sa.registration_id = r.id
            WHERE sa.end_time BETWEEN :start AND :end
              AND sa.deleted_at IS NULL
              AND r.deleted_at IS NULL
        ");
        $stmt->execute([':start'=>$start_datetime,':end'=>$end_datetime]);
        $data['expired_accounts'] = ($row = $stmt->fetch(PDO::FETCH_ASSOC)) ? $row['count'] : 0;

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

        // Completed transactions
        $stmt = $pdo->prepare("SELECT COUNT(id) as count FROM transaction_history WHERE status = 'completed' AND created_at BETWEEN :start AND :end");
        $stmt->execute([':start'=>$start_datetime,':end'=>$end_datetime]);
        $data['completed_transactions'] = ($row = $stmt->fetch(PDO::FETCH_ASSOC)) ? $row['count'] : 0;

        // Pending transactions
        $stmt = $pdo->prepare("SELECT COUNT(id) as count FROM transaction_history WHERE status = 'pending' AND created_at BETWEEN :start AND :end");
        $stmt->execute([':start'=>$start_datetime,':end'=>$end_datetime]);
        $data['pending_transactions'] = ($row = $stmt->fetch(PDO::FETCH_ASSOC)) ? $row['count'] : 0;

        // Failed transactions
        $stmt = $pdo->prepare("SELECT COUNT(id) as count FROM transaction_history WHERE status = 'failed' AND created_at BETWEEN :start AND :end");
        $stmt->execute([':start'=>$start_datetime,':end'=>$end_datetime]);
        $data['failed_transactions'] = ($row = $stmt->fetch(PDO::FETCH_ASSOC)) ? $row['count'] : 0;

        // Referrals
        // New referrals in period
        $stmt = $pdo->prepare("SELECT COUNT(id) as count FROM registration WHERE collaborator_id IS NOT NULL AND deleted_at IS NULL AND created_at BETWEEN :start AND :end");
        $stmt->execute([':start'=>$start_datetime,':end'=>$end_datetime]);
        $data['new_referrals'] = ($row = $stmt->fetch(PDO::FETCH_ASSOC)) ? $row['count'] : 0;

        // Commission generated (sum of withdrawal_request)
        $stmt = $pdo->prepare("
            SELECT SUM(amount) as total
            FROM withdrawal_request
            WHERE created_at BETWEEN :start AND :end
        ");
        $stmt->execute([':start'=>$start_datetime,':end'=>$end_datetime]);
        $data['commission_generated'] = ($row = $stmt->fetch(PDO::FETCH_ASSOC)) ? $row['total'] : 0;
        if ($data['commission_generated'] === null) {
            $data['commission_generated'] = 0;
        }

        // Commission paid
        $stmt = $pdo->prepare("
            SELECT SUM(amount) as total
            FROM withdrawal_request
            WHERE status = 'completed'
              AND updated_at BETWEEN :start AND :end
        ");
        $stmt->execute([':start'=>$start_datetime,':end'=>$end_datetime]);
        $data['commission_paid'] = ($row = $stmt->fetch(PDO::FETCH_ASSOC)) ? $row['total'] : 0;
        if ($data['commission_paid'] === null) {
            $data['commission_paid'] = 0;
        }

        // Commission pending
        $stmt = $pdo->prepare("
            SELECT SUM(amount) as total
            FROM withdrawal_request
            WHERE status = 'pending'
              AND created_at BETWEEN :start AND :end
        ");
        $stmt->execute([':start'=>$start_datetime,':end'=>$end_datetime]);
        $data['commission_pending'] = ($row = $stmt->fetch(PDO::FETCH_ASSOC)) ? $row['total'] : 0;
        if ($data['commission_pending'] === null) {
            $data['commission_pending'] = 0;
        }

        return $data;
    }
}
?>
