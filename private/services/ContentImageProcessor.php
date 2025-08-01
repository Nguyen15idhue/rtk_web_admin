<?php
require_once __DIR__ . '/CloudinaryService.php';

class ContentImageProcessor
{
    /**
     * Process HTML content: find <img> tags with local sources, upload to Cloudinary,
     * and replace src attribute with secure_url.
     *
     * @param string $content Raw HTML content
     * @return string Updated HTML content
     */
    public static function process(string $content): string
    {
        $doc = new DOMDocument();
        libxml_use_internal_errors(true);
        // Ensure proper encoding
        $doc->loadHTML('<?xml encoding="utf-8" ?>' . $content, LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD);
        libxml_clear_errors();

        $imgs = $doc->getElementsByTagName('img');
        foreach ($imgs as $node) {
            if (!($node instanceof \DOMElement)) {
                continue;
            }
            $src = $node->getAttribute('src');
            // Skip if already hosted on Cloudinary
            if (strpos($src, 'res.cloudinary.com') === false) {
                // Resolve local filesystem path
                $basePublic = realpath(__DIR__ . '/..') . '/../public/';
                $localPath = realpath($basePublic . ltrim($src, '/\.'));
                if ($localPath && file_exists($localPath)) {
                    try {
                        $uploadResult = CloudinaryService::uploadRaw(
                            $localPath,
                            ['folder' => 'rtk_web_admin/guide/content']
                        );
                        if (!empty($uploadResult['secure_url'])) {
                            $node->setAttribute('src', $uploadResult['secure_url']);
                        }
                    } catch (Exception $e) {
                        error_log('Cloudinary upload error for content image: ' . $e->getMessage());
                    }
                }
            }
        }

        return $doc->saveHTML();
    }
}
