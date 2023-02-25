<?php

namespace Cone\Root\Support\Facades;

use Cone\Root\Interfaces\Support\Collections\Assets;
use Illuminate\Support\Facades\Facade;

/**
 * @method static void register(string $key, \Cone\Root\Support\Asset $item)
 * @method static void script(string $key, string $path, string|null $url)
 * @method static void style(string $key, string $path, string|null $url)
 * @method static void icon(string $key, string $path, string|null $url)
 * @method static \Cone\Root\Interfaces\Support\Collections\Assets scripts()
 * @method static \Cone\Root\Interfaces\Support\Collections\Assets styles()
 * @method static \Cone\Root\Interfaces\Support\Collections\Assets icons()
 *
 * @see \Cone\Root\Interfaces\Support\Collections\Assets
 */
class Asset extends Facade
{
    /**
     * Get the registered name of the component.
     */
    protected static function getFacadeAccessor(): string
    {
        return Assets::class;
    }
}
