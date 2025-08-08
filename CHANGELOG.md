# Changelog

All notable changes to `laravel-base64-image` will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.0.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [Unreleased]

## [1.0.1] - 2025-01-08

### Added
- Laravel 12 support
- Improved version constraints for better compatibility

### Fixed
- Package discovery issues with Laravel 12
- Composer dependency resolution for latest Laravel versions

## [1.0.0] - 2025-01-08

### Added
- Initial release
- Base64 image processing and storage functionality
- Support for multiple image formats (JPG, PNG, WebP, GIF, BMP, SVG)
- Automatic image resizing with aspect ratio control
- Image quality control
- File size validation
- Configurable storage disks and locations
- Auto orientation based on EXIF data
- Unique filename generation
- Laravel service provider and facade integration
- Comprehensive test coverage
- Intervention Image 3.0 support
- PHP 8.0+ compatibility
- Laravel 9.0, 10.0, and 11.0 support

### Security
- Built-in file type validation
- File size limits
- Safe temporary file handling