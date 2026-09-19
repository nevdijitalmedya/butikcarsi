<?php
/**
 * ImageProcessor.php — Image upload, resize, WebP conversion
 */

class ImageProcessor {
    private static string $uploadBase = '';

    private static function getUploadBase(): string {
        if (empty(self::$uploadBase)) {
            self::$uploadBase = PANEL_PATH . '/uploads';
        }
        return self::$uploadBase;
    }

    /**
     * Upload and process an image
     * @return array ['original' => path, 'webp' => path, 'thumb' => path]
     */
    public static function upload(array $file, string $subDir = 'products', int $maxWidth = 1200, int $thumbWidth = 400): array {
        $uploadDir = self::getUploadBase() . '/' . $subDir;
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0755, true);
        }

        // Validate file
        $allowedTypes = ['image/jpeg', 'image/png', 'image/webp', 'image/gif'];
        if (!in_array($file['type'], $allowedTypes)) {
            throw new Exception('Geçersiz dosya türü. Sadece JPG, PNG, WebP ve GIF desteklenir.');
        }

        $maxSize = 10 * 1024 * 1024; // 10MB
        if ($file['size'] > $maxSize) {
            throw new Exception('Dosya boyutu çok büyük. Maksimum 10MB.');
        }

        // Generate unique filename
        $ext = pathinfo($file['name'], PATHINFO_EXTENSION);
        $baseName = time() . '_' . bin2hex(random_bytes(4));
        $originalName = $baseName . '.' . $ext;
        $webpName = $baseName . '.webp';
        $thumbName = $baseName . '_thumb.webp';

        $originalPath = $uploadDir . '/' . $originalName;

        // Move uploaded file
        if (!move_uploaded_file($file['tmp_name'], $originalPath)) {
            throw new Exception('Dosya yüklenemedi.');
        }

        $result = [
            'original' => '/uploads/' . $subDir . '/' . $originalName,
            'webp' => null,
            'thumb' => null,
        ];

        // Try to create WebP and thumbnail versions
        if (function_exists('imagecreatefrompng') || function_exists('imagecreatefromjpeg')) {
            try {
                $source = self::createImageFromFile($originalPath, $file['type']);
                if ($source) {
                    // Resize original if too wide
                    $width = imagesx($source);
                    $height = imagesy($source);

                    if ($width > $maxWidth) {
                        $ratio = $maxWidth / $width;
                        $newHeight = (int)($height * $ratio);
                        $resized = imagecreatetruecolor($maxWidth, $newHeight);
                        imagealphablending($resized, false);
                        imagesavealpha($resized, true);
                        imagecopyresampled($resized, $source, 0, 0, 0, 0, $maxWidth, $newHeight, $width, $height);
                        $source = $resized;
                        $width = $maxWidth;
                        $height = $newHeight;
                    }

                    // Save WebP
                    $webpPath = $uploadDir . '/' . $webpName;
                    if (imagewebp($source, $webpPath, 85)) {
                        $result['webp'] = '/uploads/' . $subDir . '/' . $webpName;
                    }

                    // Create thumbnail
                    if ($width > $thumbWidth) {
                        $thumbRatio = $thumbWidth / $width;
                        $thumbHeight = (int)($height * $thumbRatio);
                        $thumb = imagecreatetruecolor($thumbWidth, $thumbHeight);
                        imagealphablending($thumb, false);
                        imagesavealpha($thumb, true);
                        imagecopyresampled($thumb, $source, 0, 0, 0, 0, $thumbWidth, $thumbHeight, $width, $height);

                        $thumbPath = $uploadDir . '/' . $thumbName;
                        if (imagewebp($thumb, $thumbPath, 80)) {
                            $result['thumb'] = '/uploads/' . $subDir . '/' . $thumbName;
                        }
                        imagedestroy($thumb);
                    }

                    imagedestroy($source);
                }
            } catch (Exception $e) {
                error_log("Image processing failed: " . $e->getMessage());
            }
        }

        return $result;
    }

    /**
     * Create GD image from file based on MIME type
     */
    private static function createImageFromFile(string $path, string $mimeType): \GdImage|false {
        return match ($mimeType) {
            'image/jpeg' => imagecreatefromjpeg($path),
            'image/png' => imagecreatefrompng($path),
            'image/webp' => imagecreatefromwebp($path),
            'image/gif' => imagecreatefromgif($path),
            default => false,
        };
    }

    /**
     * Delete image and its variants
     */
    public static function delete(string $imagePath): void {
        $fullPath = PANEL_PATH . $imagePath;
        if (file_exists($fullPath)) {
            unlink($fullPath);
        }
    }
}
