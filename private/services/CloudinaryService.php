<?php
require_once __DIR__ . '/../config/cloudinary.php';
/* CloudinaryService in global namespace */
use Cloudinary\Configuration\Configuration;
use Cloudinary\Cloudinary;

class CloudinaryService
{
    private static $cloudinary;

    private static function init()
    {
        if (!self::$cloudinary) {
            // Validate Cloudinary configuration
            if (!defined('CLOUDINARY_CLOUD_NAME') || 
                !defined('CLOUDINARY_API_KEY') || 
                !defined('CLOUDINARY_API_SECRET') ||
                empty(CLOUDINARY_CLOUD_NAME) || 
                empty(CLOUDINARY_API_KEY) || 
                empty(CLOUDINARY_API_SECRET)) {
                throw new \Exception('Dữ liệu cấu hình không hợp lệ, vui lòng thiết lập môi trường của bạn');
            }

            // Use environment URL format for configuration
            $cloudinaryUrl = sprintf(
                'cloudinary://%s:%s@%s',
                CLOUDINARY_API_KEY,
                CLOUDINARY_API_SECRET,
                CLOUDINARY_CLOUD_NAME
            );
            
            self::$cloudinary = new Cloudinary($cloudinaryUrl);
        }
    }

    public static function uploadRaw(string $filePath, array $options = []): array
    {
        self::init();
        $result = self::$cloudinary->uploadApi()->upload($filePath, $options);
        return $result->getArrayCopy();
    }
}