<?php

namespace VeyselAydogdu\LaravelBase64Image\Facades;

use Illuminate\Support\Facades\Facade;

class Base64Image extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return 'base64-image';
    }
}