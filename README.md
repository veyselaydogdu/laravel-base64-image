# Laravel Base64 Image

[![Latest Version on Packagist](https://img.shields.io/packagist/v/veyselaydogdu/laravel-base64-image.svg?style=flat-square)](https://packagist.org/packages/veyselaydogdu/laravel-base64-image)
[![Total Downloads](https://img.shields.io/packagist/dt/veyselaydogdu/laravel-base64-image.svg?style=flat-square)](https://packagist.org/packages/veyselaydogdu/laravel-base64-image)
[![GitHub Tests Action Status](https://img.shields.io/github/actions/workflow/status/veyselaydogdu/laravel-base64-image/run-tests.yml?branch=main&label=tests&style=flat-square)](https://github.com/veyselaydogdu/laravel-base64-image/actions?query=workflow%3Arun-tests+branch%3Amain)
[![License](https://img.shields.io/packagist/l/veyselaydogdu/laravel-base64-image.svg?style=flat-square)](https://packagist.org/packages/veyselaydogdu/laravel-base64-image)

A powerful Laravel package for handling base64 encoded images with automatic resizing, validation, and storage management. This package provides a simple and flexible way to save base64 encoded images to your Laravel application's storage system with advanced image processing capabilities.

## Features

- 🖼️ **Base64 Image Processing**: Convert and save base64 encoded images
- 🔄 **Image Resizing**: Automatic image resizing with aspect ratio control
- 📏 **Multiple Image Formats**: Support for JPG, PNG, WebP, GIF, BMP, and SVG
- 🛡️ **Validation**: Built-in file size and type validation
- 💾 **Flexible Storage**: Support for multiple Laravel storage disks
- 🎨 **Quality Control**: Adjustable image quality settings
- 🧭 **Auto Orientation**: Automatic image orientation based on EXIF data
- 🔧 **Configurable**: Highly configurable via configuration file
- ✅ **Well Tested**: Comprehensive test coverage
- 📚 **Laravel Integration**: Native Laravel service provider and facade

## Requirements

- PHP 8.0 or higher
- Laravel 9.0, 10.0, or 11.0
- Intervention Image 3.0

## Installation

You can install the package via Composer:

```bash
composer require veyselaydogdu/laravel-base64-image
```

The package will automatically register its service provider.

### Publish Configuration

Publish the configuration file to customize the package settings:

```bash
php artisan vendor:publish --provider="VeyselAydogdu\LaravelBase64Image\Base64ImageServiceProvider" --tag="config"
```

This will create a `config/base64-image.php` file where you can customize the package settings.

## Configuration

The configuration file allows you to customize various aspects of the package:

```php
return [
    // Default storage disk
    'disk' => env('BASE64_IMAGE_DISK', 'public'),

    // Default upload location
    'location' => env('BASE64_IMAGE_LOCATION', 'uploads'),

    // Maximum file size in KB
    'max_size' => env('BASE64_IMAGE_MAX_SIZE', 5120), // 5MB

    // Supported image types
    'supported_types' => [
        'jpg', 'jpeg', 'png', 'webp', 'gif', 'bmp', 'svg'
    ],

    // Default image quality (1-100)
    'quality' => env('BASE64_IMAGE_QUALITY', 90),

    // Auto orient images based on EXIF data
    'auto_orient' => env('BASE64_IMAGE_AUTO_ORIENT', true),

    // Filename generation settings
    'filename' => [
        'length' => 45,
        'max_attempts' => 5,
    ],
];
```

### Environment Variables

You can set these environment variables in your `.env` file:

```env
BASE64_IMAGE_DISK=public
BASE64_IMAGE_LOCATION=uploads
BASE64_IMAGE_MAX_SIZE=5120
BASE64_IMAGE_QUALITY=90
BASE64_IMAGE_AUTO_ORIENT=true
```

## Usage

### Basic Usage

```php
use VeyselAydogdu\LaravelBase64Image\Facades\Base64Image;

// Save a base64 image
$result = Base64Image::save([
    'base64' => 'data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAYAAAAfFcSJAAAADUlEQVR42mNkYPhfDwAChwGA60e6kgAAAABJRU5ErkJggg=='
]);

if ($result['success']) {
    echo "Image saved to: " . $result['path'];
    echo "Image URL: " . $result['url'];
}
```

### Advanced Usage

```php
use VeyselAydogdu\LaravelBase64Image\Facades\Base64Image;

// Save with custom parameters
$result = Base64Image::save([
    'base64' => $base64ImageData,
    'disk' => 'public',
    'location' => 'user-uploads',
    'filename' => 'profile-picture',
    'quality' => 85,
    'width' => 800,
    'height' => 600,
    'maintain_aspect_ratio' => true
]);
```

### Using the Manager Directly

```php
use VeyselAydogdu\LaravelBase64Image\Base64ImageManager;

$manager = new Base64ImageManager();

$result = $manager->save([
    'base64' => $base64ImageData,
    // ... other parameters
]);
```

### Deleting Images

```php
use VeyselAydogdu\LaravelBase64Image\Facades\Base64Image;

// Delete an image
$deleted = Base64Image::delete('uploads/image.jpg');

if ($deleted) {
    echo "Image deleted successfully";
}
```

### Response Structure

The `save` method returns an array with the following structure:

```php
[
    'success' => true,
    'path' => 'uploads/generated-filename.jpg',
    'filename' => 'generated-filename.jpg',
    'url' => 'https://your-app.com/storage/uploads/generated-filename.jpg',
    'size' => 15432, // Size in bytes
    'mime_type' => 'image/jpeg',
    'extension' => 'jpg',
    'dimensions' => [
        'width' => 800,
        'height' => 600
    ]
]
```

## Available Parameters

### Save Method Parameters

| Parameter | Type | Default | Description |
|-----------|------|---------|-------------|
| `base64` | string | **required** | Base64 encoded image data |
| `disk` | string | `config value` | Storage disk to use |
| `location` | string | `config value` | Directory to save the image |
| `filename` | string | `generated` | Custom filename (without extension) |
| `quality` | int | `config value` | Image quality (1-100) |
| `width` | int | `null` | Target width for resizing |
| `height` | int | `null` | Target height for resizing |
| `maintain_aspect_ratio` | bool | `true` | Whether to maintain aspect ratio when resizing |

## Error Handling

The package throws exceptions for various error conditions:

```php
use VeyselAydogdu\LaravelBase64Image\Facades\Base64Image;

try {
    $result = Base64Image::save([
        'base64' => $invalidBase64Data
    ]);
} catch (\Exception $e) {
    // Handle the error
    echo "Error: " . $e->getMessage();
}
```

Common exceptions:
- `Base64 image data is required`
- `Invalid base64 image data`
- `Image size exceeds maximum allowed size`
- `Invalid image data`
- `Unsupported image type: {extension}`
- `Image processing failed: {reason}`
- `Failed to save image to storage`

## Testing

Run the package tests:

```bash
composer test
```

Run tests with coverage:

```bash
composer test-coverage
```

## Security Vulnerabilities

If you discover a security vulnerability, please send an e-mail to Veysel Aydogdu via [weysel.aydogdu@gmail.com](mailto:weysel.aydogdu@gmail.com). All security vulnerabilities will be promptly addressed.

## Credits

- [Veysel Aydogdu](https://github.com/veyselaydogdu)

## License

The MIT License (MIT). Please see [License File](LICENSE) for more information.

## Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information on what has changed recently.

---

**Made with ❤️ by [Veysel Aydogdu](https://veyselaydogdu.com.tr)**