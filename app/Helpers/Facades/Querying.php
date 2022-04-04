<?php

namespace App\Helpers\Facades;

use App\Models\LogActivity;
use Illuminate\Support\Facades\Facade;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Request;

/// This class is meant to give an easy way to filter / sort BaseModels
class Querying extends Facade
{
    public static function getFacadeAccessor()
    {
        return 'querying';
    }

    public static function __callStatic($method, $args)
    {
        return (self::resolveFacadeInstance('querying'))
            ->$method(...$args);
    }
}
