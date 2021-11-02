<?php

namespace Cone\Root\Support\Facades;

use Cone\Root\Interfaces\Registries\ResourceRegistry;
use Illuminate\Support\Facades\Facade;

/**
 * @method static void register(string $key, \Cone\Root\Resources\Resource $item)
 * @method static \Cone\Root\Resources\Resource resolve(string $key)
 * @method static \Cone\Root\Resources\Resource resolveFromRequest(\Illuminate\Http\Request $request)
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
