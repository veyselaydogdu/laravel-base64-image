<?php

namespace VeyselAydogdu\LaravelBase64Image;

use VeyselAydogdu\LaravelBase64Image\Traits\Base64ImageSaveTraits;

class Base64ImageManager
{
    use Base64ImageSaveTraits;

    /**
     * Save a base64 encoded image
     *
     * @param array $parameters
     * @return array
     */
    public function save(array $parameters): array
    {
        return $this->saveBase64Image($parameters);
    }

    /**
     * Delete an image from storage
     *
     * @param string $path
     * @return bool
     */
    public function delete(string $path): bool
    {
        return $this->deleteImage($path);
    }
}