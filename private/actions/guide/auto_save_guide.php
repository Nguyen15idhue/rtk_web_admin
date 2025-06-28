<?php
require_once __DIR__ . '/../../core/page_bootstrap.php';
require_once __DIR__ . '/../../classes/GuideModel.php';
require_once __DIR__ . '/../../classes/Logger.php';

// Optimize headers and error reporting
header('Content-Type: application/json; charset=utf-8');
header('Cache-Control: no-cache, must-revalidate');
header('X-Content-Type-Options: nosniff');

$logger = Logger::getInstance();
$startTime = microtime(true);

try {
    // Enhanced security checks
    Auth::ensureAuthorized('guide_management_edit');

    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        http_response_code(405);
        echo json_encode([
            'success' => false,
            'message' => 'Method not allowed',
            'code' => 'METHOD_NOT_ALLOWED'
        ]);
        exit;
    }

    // Enhanced input validation with size limits
    $input = file_get_contents('php://input');
    if (strlen($input) > 10485760) { // 10MB limit
        http_response_code(413);
        echo json_encode([
            'success' => false,
            'message' => 'Request payload too large',
            'code' => 'PAYLOAD_TOO_LARGE'
        ]);
        exit;
    }

    $data = json_decode($input, true);
    if (json_last_error() !== JSON_ERROR_NONE) {
        http_response_code(400);
        echo json_encode([
            'success' => false,
            'message' => 'Invalid JSON: ' . json_last_error_msg(),
            'code' => 'INVALID_JSON'
        ]);
        exit;
    }

    // Enhanced field validation with sanitization
    $requiredFields = ['title', 'content'];
    foreach ($requiredFields as $field) {
        if (!isset($data[$field])) {
            http_response_code(400);
            echo json_encode([
                'success' => false,
                'message' => "Missing field: $field",
                'code' => 'MISSING_FIELD'
            ]);
            exit;
        }
        
        $data[$field] = trim($data[$field]);
        if (empty($data[$field])) {
            http_response_code(400);
            echo json_encode([
                'success' => false,
                'message' => "Empty field: $field",
                'code' => 'EMPTY_FIELD'
            ]);
            exit;
        }
    }

    // Additional validation
    if (strlen($data['title']) > 255) {
        http_response_code(400);
        echo json_encode([
            'success' => false,
            'message' => 'Title too long (max 255 characters)',
            'code' => 'TITLE_TOO_LONG'
        ]);
        exit;
    }

    $guideModel = new GuideModel();
    $result = false;
    $guideId = null;
    $isUpdate = false;

    // Optimized database operations
    if (isset($data['id']) && !empty($data['id'])) {
        // Update existing guide with optimized data
        $guideId = (int)$data['id'];
        $isUpdate = true;
        
        // Verify guide exists and user has permission
        $existingGuide = $guideModel->getOne($guideId);
        if (!$existingGuide) {
            http_response_code(404);
            echo json_encode([
                'success' => false,
                'message' => 'Guide not found',
                'code' => 'GUIDE_NOT_FOUND'
            ]);
            exit;
        }

        $updateData = [
            'title' => $data['title'],
            'content' => $data['content'],
            'topic' => isset($data['topic']) ? trim($data['topic']) : $existingGuide['topic'],
            'slug' => isset($data['slug']) ? trim($data['slug']) : $existingGuide['slug'],
            'updated_at' => date('Y-m-d H:i:s')
        ];
        
        // Only include status if it's provided in the data and not empty
        // Otherwise preserve the existing status
        if (isset($data['status']) && !empty($data['status'])) {
            $updateData['status'] = $data['status'];
        }

        $result = $guideModel->update($guideId, $updateData);
        
    } else {
        // Create new guide with optimized data
        $createData = [
            'title' => $data['title'],
            'content' => $data['content'],
            'author_id' => $_SESSION['admin_id'],
            'topic' => isset($data['topic']) ? trim($data['topic']) : 'general',
            'slug' => isset($data['slug']) ? trim($data['slug']) : '',
            'status' => 'draft',
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s')
        ];

        $guideId = $guideModel->create($createData);
        $result = ($guideId !== false);
    }

    if ($result) {
        $executionTime = round((microtime(true) - $startTime) * 1000, 2);
        
        // Enhanced logging with performance metrics
        $logger->info("Auto-save guide " . ($isUpdate ? 'update' : 'create'), [
            'guide_id' => $guideId,
            'admin_id' => $_SESSION['admin_id'],
            'execution_time_ms' => $executionTime,
            'content_length' => strlen($data['content']),
            'action' => $isUpdate ? 'update' : 'create'
        ]);

        echo json_encode([
            'success' => true,
            'message' => 'Auto-save successful',
            'guide_id' => $guideId,
            'timestamp' => date('Y-m-d H:i:s'),
            'execution_time_ms' => $executionTime
        ]);
    } else {
        $logger->error("Auto-save guide failed", [
            'guide_id' => $guideId,
            'admin_id' => $_SESSION['admin_id'],
            'is_update' => $isUpdate
        ]);
        
        http_response_code(500);
        echo json_encode([
            'success' => false,
            'message' => 'Failed to save guide data',
            'code' => 'SAVE_FAILED'
        ]);
    }

} catch (Exception $e) {
    $executionTime = round((microtime(true) - $startTime) * 1000, 2);
    
    $logger->error("Auto-save guide error: " . $e->getMessage(), [
        'admin_id' => $_SESSION['admin_id'] ?? null,
        'execution_time_ms' => $executionTime,
        'file' => $e->getFile(),
        'line' => $e->getLine(),
        'trace' => $e->getTraceAsString()
    ]);
    
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Internal server error',
        'code' => 'INTERNAL_ERROR'
    ]);
}
?>
