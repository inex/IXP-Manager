<?php

namespace IXP\Support\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @see \IXP\Services\RipeRestApi
 */
class RipeRestApi extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return \IXP\Services\RipeRestApi::class;
    }
}
