<?php
require_once __DIR__ . '/../../core/page_bootstrap.php';
Auth::ensureAuthorized('guide_management_view');

header('Content-Type: application/json');

try {
    $model = new GuideModel();
    $topics = $model->getDistinctTopics();
    api_success($topics);
} catch (\Throwable $e) {
    error_log(sprintf(
        "Critical [fetch_topics.php:%d]: %s\nStack trace:\n%s",
        $e->getLine(),
        $e->getMessage(),
        $e->getTraceAsString()
    ));
    api_error('Error fetching topics: ' . $e->getMessage(), 500);
}
