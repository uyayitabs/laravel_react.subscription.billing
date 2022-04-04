<?php

namespace App\Helpers\Facades;

use App\Models\LogActivity;
use Illuminate\Support\Facades\Facade;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Request;

/// This class is meant as a replacement for addToLog
/// The method was used in a lot of varying ways
class Logging extends Facade
{
    public static function getFacadeAccessor()
    {
        return 'logging';
    }

    public static function __callStatic($method, $args)
    {
        return (self::resolveFacadeInstance('logging'))
            ->$method(...$args);
    }
}
