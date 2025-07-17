<?php
require_once __DIR__ . '/../../core/page_bootstrap.php';
require_once __DIR__ . '/../../classes/Logger.php';

// Optimized headers for better performance
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
            'error' => 'Method not allowed',
            'code' => 'METHOD_NOT_ALLOWED'
        ]);
        exit;
    }

    if (!isset($_FILES['file'])) {
        http_response_code(400);
        echo json_encode([
            'error' => 'No file uploaded',
            'code' => 'NO_FILE'
        ]);
        exit;
    }

    $file = $_FILES['file'];
    
    // Enhanced file validation
    if ($file['error'] !== UPLOAD_ERR_OK) {
        $errorMessages = [
            UPLOAD_ERR_INI_SIZE => 'File exceeds server upload limit',
            UPLOAD_ERR_FORM_SIZE => 'File exceeds form limit',
            UPLOAD_ERR_PARTIAL => 'File was partially uploaded',
            UPLOAD_ERR_NO_FILE => 'No file was uploaded',
            UPLOAD_ERR_NO_TMP_DIR => 'Missing temporary folder',
            UPLOAD_ERR_CANT_WRITE => 'Failed to write file to disk',
            UPLOAD_ERR_EXTENSION => 'File upload stopped by extension'
        ];
        
        $errorMessage = $errorMessages[$file['error']] ?? 'Unknown upload error';
        echo json_encode([
            'error' => $errorMessage,
            'code' => 'UPLOAD_ERROR',
            'error_code' => $file['error']
        ]);
        exit;
    }

    // Enhanced size validation with better limits
    $maxSize = 10 * 1024 * 1024; // Increased to 10MB for better flexibility
    if ($file['size'] > $maxSize) {
        echo json_encode([
            'error' => 'File too large. Max size: ' . round($maxSize / 1024 / 1024) . 'MB',
            'code' => 'FILE_TOO_LARGE',
            'actual_size' => $file['size'],
            'max_size' => $maxSize
        ]);
        exit;
    }

    // Enhanced MIME type validation with security checks
    $allowedTypes = [
        'image/jpeg', 'image/jpg', 'image/png', 
        'image/gif', 'image/webp', 'image/bmp'
    ];
    
    $finfo = new finfo(FILEINFO_MIME_TYPE);
    $mimeType = $finfo->file($file['tmp_name']);
    
    if (!in_array($mimeType, $allowedTypes)) {
        echo json_encode([
            'error' => 'Invalid file type. Only JPEG, PNG, GIF, WebP, BMP allowed',
            'code' => 'INVALID_TYPE',
            'detected_type' => $mimeType
        ]);
        exit;
    }

    // Additional security: Check file extension matches MIME type
    $extension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
    $mimeToExtension = [
        'image/jpeg' => ['jpg', 'jpeg'],
        'image/png' => ['png'],
        'image/gif' => ['gif'],
        'image/webp' => ['webp'],
        'image/bmp' => ['bmp']
    ];
    
    $validExtensions = [];
    foreach ($mimeToExtension as $mime => $exts) {
        if ($mimeType === $mime) {
            $validExtensions = $exts;
            break;
        }
    }
    
    if (!in_array($extension, $validExtensions)) {
        echo json_encode([
            'error' => 'File extension does not match content type',
            'code' => 'EXTENSION_MISMATCH',
            'extension' => $extension,
            'mime_type' => $mimeType
        ]);
        exit;
    }

    // Optimized directory structure with date-based organization
    $year = date('Y');
    $month = date('m');
    $uploadDir = __DIR__ . "/../../../public/uploads/guide/content/{$year}/{$month}/";
    $relativeUrl = "/public/uploads/guide/content/{$year}/{$month}/";
    // Create nested directory structure
    if (!is_dir($uploadDir)) {
        if (!mkdir($uploadDir, 0755, true)) {
            echo json_encode([
                'error' => 'Failed to create upload directory',
                'code' => 'DIRECTORY_ERROR'
            ]);
            exit;
        }
    }

    // Enhanced filename generation with collision prevention
    $originalName = pathinfo($file['name'], PATHINFO_FILENAME);
    $safeOriginalName = preg_replace('/[^a-zA-Z0-9_-]/', '_', $originalName);
    $safeOriginalName = substr($safeOriginalName, 0, 50); // Limit length
    
    $filename = sprintf(
        'guide_%s_%s_%s.%s',
        $safeOriginalName,
        uniqid(),
        time(),
        $extension
    );
    
    $filepath = $uploadDir . $filename;
    
    // Ensure filename is unique (collision prevention)
    $counter = 1;
    while (file_exists($filepath)) {
        $filename = sprintf(
            'guide_%s_%s_%s_%d.%s',
            $safeOriginalName,
            uniqid(),
            time(),
            $counter,
            $extension
        );
        $filepath = $uploadDir . $filename;
        $counter++;
        
        if ($counter > 100) { // Safety limit
            echo json_encode([
                'error' => 'Unable to generate unique filename',
                'code' => 'FILENAME_COLLISION'
            ]);
            exit;
        }
    }

    // Move uploaded file with error handling
    if (!move_uploaded_file($file['tmp_name'], $filepath)) {
        echo json_encode([
            'error' => 'Failed to save file to server',
            'code' => 'SAVE_FAILED'
        ]);
        exit;
    }

    // Enhanced image optimization with quality control
    $originalSize = $file['size'];
    $optimizedPath = optimizeImage($filepath, $mimeType);
    
    // Generate optimized URL structure
    $baseUrl = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' ? 'https' : 'http') . 
               '://' . $_SERVER['HTTP_HOST'];
    $imageUrl = $baseUrl . $relativeUrl . basename($optimizedPath);

    // Enhanced logging with performance metrics
    $executionTime = round((microtime(true) - $startTime) * 1000, 2);
    $optimizedSize = filesize($optimizedPath);
    $compressionRatio = round((1 - $optimizedSize / $originalSize) * 100, 2);
    
    $logger->info("Image uploaded and optimized for guide content", [
        'filename' => $filename,
        'original_size' => $originalSize,
        'optimized_size' => $optimizedSize,
        'compression_ratio' => $compressionRatio . '%',
        'mime_type' => $mimeType,
        'admin_id' => $_SESSION['admin_id'],
        'execution_time_ms' => $executionTime,
        'upload_path' => $relativeUrl
    ]);

    // Enhanced response with metadata
    echo json_encode([
        'location' => $imageUrl,
        'filename' => $filename,
        'size' => $optimizedSize,
        'original_size' => $originalSize,
        'compression_ratio' => $compressionRatio,
        'mime_type' => $mimeType,
        'execution_time_ms' => $executionTime
    ]);

 } catch (Exception $e) {
    $executionTime = round((microtime(true) - $startTime) * 1000, 2);
    
    $logger->error("Image upload error: " . $e->getMessage(), [
        'admin_id' => $_SESSION['admin_id'] ?? null,
        'execution_time_ms' => $executionTime,
        'file' => $e->getFile(),
        'line' => $e->getLine(),
        'trace' => $e->getTraceAsString()
    ]);
    
    http_response_code(500);
    echo json_encode([
        'error' => 'Internal server error',
        'code' => 'INTERNAL_ERROR'
    ]);
}

/**
 * Enhanced image optimization with adaptive quality and format conversion
 */
function optimizeImage($filepath, $mimeType) {
    $maxWidth = 1920;
    $maxHeight = 1080;
    $highQuality = 90; // For smaller images
    $mediumQuality = 80; // For medium images
    $lowQuality = 70; // For large images

    // Get image dimensions and file size
    $imageInfo = getimagesize($filepath);
    if (!$imageInfo) {
        return $filepath; // Not a valid image
    }
    
    list($width, $height) = $imageInfo;
    $fileSize = filesize($filepath);
    
    // Skip optimization if image is already optimal
    if ($width <= $maxWidth && $height <= $maxHeight && $fileSize < 300000) { // 300KB
        return $filepath;
    }

    // Calculate new dimensions maintaining aspect ratio
    $ratio = min($maxWidth / $width, $maxHeight / $height, 1); // Don't upscale
    $newWidth = intval($width * $ratio);
    $newHeight = intval($height * $ratio);

    // Choose quality based on original file size
    $quality = $highQuality;
    if ($fileSize > 2000000) { // 2MB
        $quality = $lowQuality;
    } elseif ($fileSize > 1000000) { // 1MB
        $quality = $mediumQuality;
    }

    // Create new image resource with optimized memory handling
    $newImage = imagecreatetruecolor($newWidth, $newHeight);
    if (!$newImage) {
        return $filepath; // Memory allocation failed
    }
    
    // Preserve transparency for PNG/GIF with optimization
    if ($mimeType === 'image/png' || $mimeType === 'image/gif') {
        imagealphablending($newImage, false);
        imagesavealpha($newImage, true);
        $transparent = imagecolorallocatealpha($newImage, 255, 255, 255, 127);
        imagefilledrectangle($newImage, 0, 0, $newWidth, $newHeight, $transparent);
    } else {
        // For JPEG, set white background
        $white = imagecolorallocate($newImage, 255, 255, 255);
        imagefilledrectangle($newImage, 0, 0, $newWidth, $newHeight, $white);
    }

    // Load original image with error handling
    $originalImage = null;
    switch ($mimeType) {
        case 'image/jpeg':
        case 'image/jpg':
            $originalImage = @imagecreatefromjpeg($filepath);
            break;
        case 'image/png':
            $originalImage = @imagecreatefrompng($filepath);
            break;
        case 'image/gif':
            $originalImage = @imagecreatefromgif($filepath);
            break;
        case 'image/webp':
            $originalImage = @imagecreatefromwebp($filepath);
            break;
        case 'image/bmp':
            $originalImage = @imagecreatefrombmp($filepath);
            break;
        default:
            imagedestroy($newImage);
            return $filepath;
    }

    if (!$originalImage) {
        imagedestroy($newImage);
        return $filepath; // Failed to load image
    }

    // High-quality resampling
    if (!imagecopyresampled($newImage, $originalImage, 0, 0, 0, 0, $newWidth, $newHeight, $width, $height)) {
        imagedestroy($originalImage);
        imagedestroy($newImage);
        return $filepath;
    }

    // Save optimized image with format-specific settings
    $success = false;
    switch ($mimeType) {
        case 'image/jpeg':
        case 'image/jpg':
            // Apply progressive JPEG for better loading
            imageinterlace($newImage, 1);
            $success = imagejpeg($newImage, $filepath, $quality);
            break;
        case 'image/png':
            // PNG compression level (0-9, where 9 is maximum compression)
            $pngQuality = 9 - intval($quality / 10);
            $success = imagepng($newImage, $filepath, $pngQuality);
            break;
        case 'image/gif':
            $success = imagegif($newImage, $filepath);
            break;
        case 'image/webp':
            $success = imagewebp($newImage, $filepath, $quality);
            break;
        case 'image/bmp':
            // Convert BMP to JPEG for better compression
            $newPath = str_replace('.bmp', '.jpg', $filepath);
            $success = imagejpeg($newImage, $newPath, $quality);
            if ($success && $newPath !== $filepath) {
                unlink($filepath); // Remove original BMP
                $filepath = $newPath;
            }
            break;
    }

    // Clean up memory
    imagedestroy($originalImage);
    imagedestroy($newImage);

    return $success ? $filepath : $filepath; // Return original path if optimization failed
}
?>
