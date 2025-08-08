<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Default Storage Disk
    |--------------------------------------------------------------------------
    |
    | The default storage disk for saving images
    |
    */
    'disk' => env('BASE64_IMAGE_DISK', 'public'),

    /*
    |--------------------------------------------------------------------------
    | Default Upload Location
    |--------------------------------------------------------------------------
    |
    | The default directory where images will be stored
    |
    */
    'location' => env('BASE64_IMAGE_LOCATION', 'uploads'),

    /*
    |--------------------------------------------------------------------------
    | Maximum File Size (KB)
    |--------------------------------------------------------------------------
    |
    | Maximum allowed file size in kilobytes
    |
    */
    'max_size' => env('BASE64_IMAGE_MAX_SIZE', 5120), // 5MB

    /*
    |--------------------------------------------------------------------------
    | Supported Image Types
    |--------------------------------------------------------------------------
    |
    | Array of supported image extensions
    |
    */
    'supported_types' => [
        'jpg', 'jpeg', 'png', 'webp', 'gif', 'bmp', 'svg'
    ],

    /*
    |--------------------------------------------------------------------------
    | Default Image Quality
    |--------------------------------------------------------------------------
    |
    | Default quality for image compression (1-100)
    |
    */
    'quality' => env('BASE64_IMAGE_QUALITY', 90),

    /*
    |--------------------------------------------------------------------------
    | Auto Orient
    |--------------------------------------------------------------------------
    |
    | Automatically orient images based on EXIF data
    |
    */
    'auto_orient' => env('BASE64_IMAGE_AUTO_ORIENT', true),

    /*
    |--------------------------------------------------------------------------
    | Filename Settings
    |--------------------------------------------------------------------------
    |
    | Configuration for generated filenames
    |
    */
    'filename' => [
        'length' => 45,
        'max_attempts' => 5,
    ],
];