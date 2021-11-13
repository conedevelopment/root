<?php

namespace Cone\Root\Support\Facades;

use Cone\Root\Interfaces\Registries\AssetRegistry;
use Illuminate\Support\Facades\Facade;

/**
 * @method static void register(string $key, \Cone\Root\Support\Asset $item)
 * @method static array scripts()
 * @method static array styles()
 *
 * @see \Cone\Root\Interfaces\Registries\AssetRegistry
 */
class Asset extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor(): string
    {
        return AssetRegistry::class;
    }
}
