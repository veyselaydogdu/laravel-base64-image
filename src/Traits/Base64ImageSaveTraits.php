<?php

namespace VeyselAydogdu\LaravelBase64Image\Traits;

use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver;
use Exception;

trait Base64ImageSaveTraits
{
    /**
     * Save a base64 encoded image to storage
     *
     * @param array $parameters
     * @return array
     * @throws Exception
     */
    public function saveBase64Image(array $parameters): array
    {
        $base64Image = $parameters['base64'] ?? null;
        $disk = $parameters['disk'] ?? config('base64-image.disk');
        $location = $parameters['location'] ?? config('base64-image.location');
        $filename = $parameters['filename'] ?? null;
        $quality = $parameters['quality'] ?? config('base64-image.quality');
        $width = $parameters['width'] ?? null;
        $height = $parameters['height'] ?? null;
        $maintainAspectRatio = $parameters['maintain_aspect_ratio'] ?? true;

        if (empty($base64Image)) {
            throw new Exception('Base64 image data is required');
        }

        // Remove data URL prefix if present
        $base64Image = preg_replace('#^data:image/[^;]+;base64,#', '', $base64Image);

        // Decode base64 data
        $imageData = base64_decode($base64Image);
        if ($imageData === false) {
            throw new Exception('Invalid base64 image data');
        }

        // Validate file size
        $maxSize = config('base64-image.max_size') * 1024; // Convert KB to bytes
        if (strlen($imageData) > $maxSize) {
            throw new Exception('Image size exceeds maximum allowed size');
        }

        // Create temporary file to determine image type
        $tempFile = tempnam(sys_get_temp_dir(), 'base64_image');
        file_put_contents($tempFile, $imageData);

        // Get image information
        $imageInfo = getimagesize($tempFile);
        if ($imageInfo === false) {
            unlink($tempFile);
            throw new Exception('Invalid image data');
        }

        $mimeType = $imageInfo['mime'];
        $extension = $this->getExtensionFromMimeType($mimeType);

        // Validate image type
        $supportedTypes = config('base64-image.supported_types');
        if (!in_array($extension, $supportedTypes)) {
            unlink($tempFile);
            throw new Exception('Unsupported image type: ' . $extension);
        }

        // Generate filename if not provided
        if (empty($filename)) {
            $filename = $this->generateUniqueFilename($disk, $location, $extension);
        } else {
            // Ensure filename has correct extension
            $filename = pathinfo($filename, PATHINFO_FILENAME) . '.' . $extension;
        }

        // Process image with Intervention Image
        try {
            $manager = new ImageManager(new Driver());
            $image = $manager->read($tempFile);

            // Auto orient if enabled
            if (config('base64-image.auto_orient')) {
                $image = $image->orient();
            }

            // Resize if dimensions provided
            if ($width || $height) {
                if ($maintainAspectRatio) {
                    $image = $image->scale($width, $height);
                } else {
                    $image = $image->resize($width, $height);
                }
            }

            // Encode with quality
            $encodedImage = $image->encode($extension, $quality);

            // Clean up temp file
            unlink($tempFile);

            // Save to storage
            $path = $location . '/' . $filename;
            $saved = Storage::disk($disk)->put($path, $encodedImage->toString());

            if (!$saved) {
                throw new Exception('Failed to save image to storage');
            }

            return [
                'success' => true,
                'path' => $path,
                'filename' => $filename,
                'url' => Storage::disk($disk)->url($path),
                'size' => strlen($encodedImage->toString()),
                'mime_type' => $mimeType,
                'extension' => $extension,
                'dimensions' => [
                    'width' => $image->width(),
                    'height' => $image->height()
                ]
            ];

        } catch (Exception $e) {
            if (file_exists($tempFile)) {
                unlink($tempFile);
            }
            throw new Exception('Image processing failed: ' . $e->getMessage());
        }
    }

    /**
     * Delete an image from storage
     *
     * @param string $path
     * @param string|null $disk
     * @return bool
     */
    public function deleteImage(string $path, string $disk = null): bool
    {
        $disk = $disk ?? config('base64-image.disk');
        
        try {
            return Storage::disk($disk)->delete($path);
        } catch (Exception $e) {
            return false;
        }
    }

    /**
     * Get file extension from MIME type
     *
     * @param string $mimeType
     * @return string
     */
    private function getExtensionFromMimeType(string $mimeType): string
    {
        $mimeToExt = [
            'image/jpeg' => 'jpg',
            'image/png' => 'png',
            'image/gif' => 'gif',
            'image/bmp' => 'bmp',
            'image/webp' => 'webp',
            'image/svg+xml' => 'svg'
        ];

        return $mimeToExt[$mimeType] ?? 'jpg';
    }

    /**
     * Generate a unique filename
     *
     * @param string $disk
     * @param string $location
     * @param string $extension
     * @return string
     */
    private function generateUniqueFilename(string $disk, string $location, string $extension): string
    {
        $length = config('base64-image.filename.length', 45);
        $maxAttempts = config('base64-image.filename.max_attempts', 5);

        for ($i = 0; $i < $maxAttempts; $i++) {
            $filename = Str::random($length) . '.' . $extension;
            $path = $location . '/' . $filename;

            if (!Storage::disk($disk)->exists($path)) {
                return $filename;
            }
        }

        // If we can't generate a unique filename, append timestamp
        return Str::random($length - 10) . '_' . time() . '.' . $extension;
    }
}