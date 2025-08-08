<?php

namespace VeyselAydogdu\LaravelBase64Image\Tests;

use Orchestra\Testbench\TestCase;
use VeyselAydogdu\LaravelBase64Image\Base64ImageServiceProvider;
use VeyselAydogdu\LaravelBase64Image\Facades\Base64Image;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Config;

class Base64ImageTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        
        // Setup fake storage
        Storage::fake('public');
        
        // Set test configuration
        Config::set('base64-image.disk', 'public');
        Config::set('base64-image.location', 'test-uploads');
        Config::set('base64-image.max_size', 1024); // 1MB for testing
        Config::set('base64-image.quality', 90);
    }

    protected function getPackageProviders($app)
    {
        return [Base64ImageServiceProvider::class];
    }

    protected function getPackageAliases($app)
    {
        return [
            'Base64Image' => Base64Image::class,
        ];
    }

    public function test_can_save_base64_image()
    {
        // Sample 1x1 pixel PNG base64
        $base64Image = 'data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAYAAAAfFcSJAAAADUlEQVR42mNkYPhfDwAChwGA60e6kgAAAABJRU5ErkJggg==';
        
        $result = Base64Image::save([
            'base64' => $base64Image,
            'filename' => 'test-image'
        ]);

        $this->assertTrue($result['success']);
        $this->assertStringContains('test-image.png', $result['path']);
        $this->assertEquals('image/png', $result['mime_type']);
        $this->assertEquals('png', $result['extension']);
        $this->assertArrayHasKey('dimensions', $result);
        
        // Verify file exists in storage
        Storage::disk('public')->assertExists($result['path']);
    }

    public function test_can_save_jpeg_image()
    {
        // Sample JPEG base64
        $base64Image = '/9j/4AAQSkZJRgABAQEAYABgAAD/2wBDAAEBAQEBAQEBAQEBAQEBAQEBAQEBAQEBAQEBAQEBAQEBAQEBAQEBAQEBAQEBAQEBAQEBAQEBAQEBAQEBAQEBAQH/2wBDAQEBAQEBAQEBAQEBAQEBAQEBAQEBAQEBAQEBAQEBAQEBAQEBAQEBAQEBAQEBAQEBAQEBAQEBAQEBAQEBAQEBAQH/wAARCAABAAEDASIAAhEBAxEB/8QAFQABAQAAAAAAAAAAAAAAAAAAAAv/xAAUEAEAAAAAAAAAAAAAAAAAAAAA/8QAFQEBAQAAAAAAAAAAAAAAAAAAAAX/xAAUEQEAAAAAAAAAAAAAAAAAAAAA/9oADAMBAAIRAxEAPwA/gA==';
        
        $result = Base64Image::save([
            'base64' => $base64Image
        ]);

        $this->assertTrue($result['success']);
        $this->assertEquals('image/jpeg', $result['mime_type']);
        $this->assertEquals('jpg', $result['extension']);
        
        Storage::disk('public')->assertExists($result['path']);
    }

    public function test_can_resize_image()
    {
        $base64Image = 'data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAYAAAAfFcSJAAAADUlEQVR42mNkYPhfDwAChwGA60e6kgAAAABJRU5ErkJggg==';
        
        $result = Base64Image::save([
            'base64' => $base64Image,
            'width' => 100,
            'height' => 100
        ]);

        $this->assertTrue($result['success']);
        $this->assertEquals(100, $result['dimensions']['width']);
        $this->assertEquals(100, $result['dimensions']['height']);
    }

    public function test_can_delete_image()
    {
        // First save an image
        $base64Image = 'data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAYAAAAfFcSJAAAADUlEQVR42mNkYPhfDwAChwGA60e6kgAAAABJRU5ErkJggg==';
        
        $result = Base64Image::save([
            'base64' => $base64Image
        ]);

        $this->assertTrue($result['success']);
        Storage::disk('public')->assertExists($result['path']);

        // Then delete it
        $deleted = Base64Image::delete($result['path']);
        
        $this->assertTrue($deleted);
        Storage::disk('public')->assertMissing($result['path']);
    }

    public function test_throws_exception_for_empty_base64()
    {
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Base64 image data is required');

        Base64Image::save([
            'base64' => ''
        ]);
    }

    public function test_throws_exception_for_invalid_base64()
    {
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Invalid base64 image data');

        Base64Image::save([
            'base64' => 'invalid-base64-data'
        ]);
    }

    public function test_throws_exception_for_unsupported_type()
    {
        // Mock an unsupported file type by providing invalid image data
        $this->expectException(\Exception::class);

        Base64Image::save([
            'base64' => 'VGhpcyBpcyBub3QgYW4gaW1hZ2U=' // "This is not an image" in base64
        ]);
    }

    public function test_can_use_custom_disk_and_location()
    {
        Storage::fake('custom');
        
        $base64Image = 'data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAYAAAAfFcSJAAAADUlEQVR42mNkYPhfDwAChwGA60e6kgAAAABJRU5ErkJggg==';
        
        $result = Base64Image::save([
            'base64' => $base64Image,
            'disk' => 'custom',
            'location' => 'custom-folder'
        ]);

        $this->assertTrue($result['success']);
        $this->assertStringContains('custom-folder/', $result['path']);
        
        Storage::disk('custom')->assertExists($result['path']);
    }

    public function test_generates_unique_filename_when_not_provided()
    {
        $base64Image = 'data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAYAAAAfFcSJAAAADUlEQVR42mNkYPhfDwAChwGA60e6kgAAAABJRU5ErkJggg==';
        
        $result1 = Base64Image::save(['base64' => $base64Image]);
        $result2 = Base64Image::save(['base64' => $base64Image]);

        $this->assertTrue($result1['success']);
        $this->assertTrue($result2['success']);
        $this->assertNotEquals($result1['filename'], $result2['filename']);
    }
}