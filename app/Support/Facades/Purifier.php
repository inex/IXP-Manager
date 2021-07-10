<?php

namespace IXP\Support\Facades;


// Based on https://github.com/LukeTowers/Purifier and embedded as this package
// is stale and does not support PHP 7.4. MIT license per 20210304 (BOD).

use Illuminate\Support\Facades\Facade;

/**
 * @see \IXP\Services\Purifier
 */
class Purifier extends Facade
{

    protected static function getFacadeAccessor()
    {
        return 'purifier';
    }
}
