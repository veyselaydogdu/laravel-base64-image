<?php

namespace VeyselAydogdu\LaravelBase64Image;

use Illuminate\Support\ServiceProvider;

class Base64ImageServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        // Publish config file
        $this->publishes([
            __DIR__.'/../config/base64-image.php' => config_path('base64-image.php'),
        ], 'config');

        // Merge config
        $this->mergeConfigFrom(
            __DIR__.'/../config/base64-image.php', 'base64-image'
        );
    }

    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        // Register the main class to use with the facade
        $this->app->singleton('base64-image', function () {
            return new \VeyselAydogdu\LaravelBase64Image\Base64ImageManager();
        });
    }
}