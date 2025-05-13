<?php
require_once __DIR__ . '/../../classes/ReportModel.php';

$reportModel = new ReportModel();
$reportData = $reportModel->getComprehensiveReportData($pdo, $start_datetime, $end_datetime);

extract($reportData);

?>
