<?php

declare(strict_types=1);

namespace TareqAlqadi\FilamentMapPicker\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @see \TareqAlqadi\FilamentMapPicker\FilamentMapPicker
 */
class FilamentMapPicker extends Facade
{
    protected static function getFacadeAccessor()
    {
        return \TareqAlqadi\FilamentMapPicker\FilamentMapPicker::class;
    }
}
