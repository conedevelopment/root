<?php

namespace Cone\Root\Support\Facades;

use Cone\Root\Interfaces\Registries\ResourceRegistry;
use Illuminate\Support\Facades\Facade;

/**
 * @method static void register(string $key, \Cone\Root\Resources\Resource $item)
 *
 * @see \Cone\Root\Interfaces\Registries\ResourceRegistry
 */
class Resource extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor(): string
    {
        return ResourceRegistry::class;
    }
}
