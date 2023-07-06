<?php

namespace Cone\Root\Support\Facades;

use Closure;
use Cone\Root\Interfaces\Navigation\Manager;
use Illuminate\Support\Facades\Facade;

/**
 * @method static \Cone\Root\Navigation\Location location(string $location)
 *
 * @see \Cone\Root\Interfaces\Navigation\Manager
 */
class Conversion extends Facade
{
    /**
     * Get the registered name of the component.
     */
    protected static function getFacadeAccessor(): string
    {
        return Manager::class;
    }
}
