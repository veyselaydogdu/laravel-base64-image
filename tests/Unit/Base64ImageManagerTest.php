<?php

namespace VeyselAydogdu\LaravelBase64Image\Tests\Unit;

use Orchestra\Testbench\TestCase;
use VeyselAydogdu\LaravelBase64Image\Base64ImageManager;
use VeyselAydogdu\LaravelBase64Image\Base64ImageServiceProvider;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Config;

class Base64ImageManagerTest extends TestCase
{
    protected Base64ImageManager $manager;

    protected function setUp(): void
    {
        parent::setUp();
        
        $this->manager = new Base64ImageManager();
        
        Storage::fake('public');
        
        Config::set('base64-image.disk', 'public');
        Config::set('base64-image.location', 'test-uploads');
        Config::set('base64-image.max_size', 1024);
        Config::set('base64-image.quality', 90);
        Config::set('base64-image.supported_types', ['jpg', 'jpeg', 'png', 'webp', 'gif', 'bmp']);
        Config::set('base64-image.auto_orient', true);
        Config::set('base64-image.filename', ['length' => 45, 'max_attempts' => 5]);
    }

    protected function getPackageProviders($app)
    {
        return [Base64ImageServiceProvider::class];
    }

    public function test_manager_can_be_instantiated()
    {
        $this->assertInstanceOf(Base64ImageManager::class, $this->manager);
    }

    public function test_save_method_works()
    {
        $base64Image = 'data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAYAAAAfFcSJAAAADUlEQVR42mNkYPhfDwAChwGA60e6kgAAAABJRU5ErkJggg==';
        
        $result = $this->manager->save([
            'base64' => $base64Image
        ]);

        $this->assertTrue($result['success']);
        $this->assertArrayHasKey('path', $result);
        $this->assertArrayHasKey('filename', $result);
        $this->assertArrayHasKey('url', $result);
        $this->assertArrayHasKey('size', $result);
        $this->assertArrayHasKey('mime_type', $result);
        $this->assertArrayHasKey('extension', $result);
        $this->assertArrayHasKey('dimensions', $result);
    }

    public function test_delete_method_works()
    {
        // First save an image
        $base64Image = 'data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAYAAAAfFcSJAAAADUlEQVR42mNkYPhfDwAChwGA60e6kgAAAABJRU5ErkJggg==';
        
        $result = $this->manager->save(['base64' => $base64Image]);
        $this->assertTrue($result['success']);

        // Then delete it
        $deleted = $this->manager->delete($result['path']);
        $this->assertTrue($deleted);
    }

    public function test_delete_returns_false_for_non_existent_file()
    {
        $deleted = $this->manager->delete('non-existent-path/file.jpg');
        $this->assertFalse($deleted);
    }
}