<?php
// filepath: e:\Application\laragon\www\rtk_web_admin\private\actions\dashboard\fetch_dashboard_data.php

/**
 * Fetches all necessary data for the admin dashboard.
 *
 * @return array An associative array containing all dashboard data.
 */
function fetch_dashboard_data(): array
{
    // Include the Database class
    require_once __DIR__ . '/../../classes/Database.php';

    $dashboard_data = [
        'total_web_users' => 0,
        'users_with_package' => 0,
        'active_survey_accounts' => 0,
        'monthly_sales' => 0,
        'total_referrers' => 0,
        'referred_registrations' => 0,
        'total_commission_paid' => 0,
        'recent_activities' => [],
        // Add default structure for chart data
        'new_registrations_chart_data' => ['labels' => [], 'data' => []],
    ];

    try {
        $database = Database::getInstance();
        $pdo = $database->getConnection();

        if ($pdo === null) {
            error_log("Dashboard Data Fetch Error: Failed to connect to the database.");
            return $dashboard_data; // Return default data on connection failure
        }

        // Total Web Users
        $stmt = $pdo->query("SELECT COUNT(id) as count FROM user WHERE deleted_at IS NULL");
        if ($stmt && $row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $dashboard_data['total_web_users'] = $row['count'];
        }

        // Users with Active Packages
        $stmt = $pdo->query("SELECT COUNT(DISTINCT user_id) as count FROM registration WHERE status = 'active' AND deleted_at IS NULL");
        if ($stmt && $row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $dashboard_data['users_with_package'] = $row['count'];
        }

        // Active Survey Accounts
        $stmt = $pdo->query("SELECT COUNT(sa.id) as count
                            FROM survey_account sa
                            JOIN registration r ON sa.registration_id = r.id
                            WHERE sa.enabled = 1 AND r.status = 'active' AND sa.deleted_at IS NULL AND r.deleted_at IS NULL");
        if ($stmt && $row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $dashboard_data['active_survey_accounts'] = $row['count'];
        }

        // Monthly Sales
        $current_month_start = date('Y-m-01 00:00:00');
        $current_month_end = date('Y-m-t 23:59:59');
        $sql = "SELECT SUM(r.total_price) as total
                FROM registration r
                LEFT JOIN payment p ON r.id = p.registration_id
                WHERE r.deleted_at IS NULL
                  AND r.created_at BETWEEN :start_date AND :end_date
                  AND (r.status = 'active' OR p.confirmed = 1)";
        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(':start_date', $current_month_start);
        $stmt->bindParam(':end_date', $current_month_end);
        $stmt->execute();
        if ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $dashboard_data['monthly_sales'] = $row['total'] ?? 0;
        }

        // Total Referrers
        $stmt = $pdo->query("SELECT COUNT(id) as count FROM collaborator WHERE status = 'approved'");
        if ($stmt && $row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $dashboard_data['total_referrers'] = $row['count'];
        }

        // Registrations from Referrals
        $stmt = $pdo->query("SELECT COUNT(id) as count FROM registration WHERE collaborator_id IS NOT NULL AND deleted_at IS NULL");
        if ($stmt && $row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $dashboard_data['referred_registrations'] = $row['count'];
        }

        // Total Commission Paid
        $stmt = $pdo->query("SELECT SUM(amount) as total FROM withdrawal WHERE status = 'completed'");
        if ($stmt && $row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $dashboard_data['total_commission_paid'] = $row['total'] ?? 0;
        }

        // Recent Activities
        $stmt = $pdo->query("SELECT al.*, COALESCE(u.username, a.admin_username, 'System') as actor_name
                            FROM activity_logs al
                            LEFT JOIN user u ON al.user_id = u.id AND al.entity_type LIKE '%user%'
                            LEFT JOIN admin a ON al.user_id = a.id AND al.entity_type LIKE '%admin%'
                            ORDER BY al.created_at DESC
                            LIMIT 10");
        if ($stmt) {
            $dashboard_data['recent_activities'] = $stmt->fetchAll(PDO::FETCH_ASSOC);
        }

        // New Registrations (Last 7 Days) for Chart
        $seven_days_ago = date('Y-m-d 00:00:00', strtotime('-6 days')); // Include today
        $today_end = date('Y-m-d 23:59:59');
        $sql_chart = "SELECT DATE(created_at) as registration_date, COUNT(id) as count
                      FROM registration
                      WHERE created_at BETWEEN :start_date AND :end_date
                        AND deleted_at IS NULL
                      GROUP BY DATE(created_at)
                      ORDER BY registration_date ASC";
        $stmt_chart = $pdo->prepare($sql_chart);
        $stmt_chart->bindParam(':start_date', $seven_days_ago);
        $stmt_chart->bindParam(':end_date', $today_end);
        $stmt_chart->execute();
        $results = $stmt_chart->fetchAll(PDO::FETCH_ASSOC);

        // Prepare data for the chart
        $chart_labels = [];
        $chart_data = [];
        $registrations_by_date = [];
        foreach ($results as $row) {
            $registrations_by_date[$row['registration_date']] = $row['count'];
        }

        // Generate labels and data for the last 7 days, filling missing days with 0
        for ($i = 6; $i >= 0; $i--) {
            $date = date('Y-m-d', strtotime("-$i days"));
            $chart_labels[] = date('d/m', strtotime($date)); // Format date as DD/MM
            $chart_data[] = $registrations_by_date[$date] ?? 0;
        }

        $dashboard_data['new_registrations_chart_data'] = [
            'labels' => $chart_labels,
            'data' => $chart_data,
        ];

    } catch (PDOException $e) {
        error_log("Dashboard PDO Error: " . $e->getMessage());
        // Return default data in case of PDO error
        return [
            'total_web_users' => 0,
            'users_with_package' => 0,
            'active_survey_accounts' => 0,
            'monthly_sales' => 0,
            'total_referrers' => 0,
            'referred_registrations' => 0,
            'total_commission_paid' => 0,
            'recent_activities' => [],
            'new_registrations_chart_data' => ['labels' => [], 'data' => []], // Default chart data
        ];
    } catch (Exception $e) {
        error_log("Dashboard General Error: " . $e->getMessage());
         // Return default data in case of general error
         return [
            'total_web_users' => 0,
            'users_with_package' => 0,
            'active_survey_accounts' => 0,
            'monthly_sales' => 0,
            'total_referrers' => 0,
            'referred_registrations' => 0,
            'total_commission_paid' => 0,
            'recent_activities' => [],
            'new_registrations_chart_data' => ['labels' => [], 'data' => []], // Default chart data
        ];
    }

    return $dashboard_data;
}

?>