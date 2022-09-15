<?php

declare(strict_types = 1);

namespace Cone\Root\Support\Facades;

use Cone\Root\Exceptions\ResourceResolutionException;
use Cone\Root\Http\Requests\RootRequest;
use Cone\Root\Interfaces\Support\Collections\Resources;
use Cone\Root\Resources\Resource as Item;
use Illuminate\Support\Facades\Facade;

/**
 * @method static \Cone\Root\Interfaces\Support\Collections\Resources available(\Illuminate\Http\Request $request)
 *
 * @see \Cone\Root\Interfaces\Support\Collections\Resources
 */
class Resource extends Facade
{
    /**
     * Register the given item.
     *
     * @param  string  $key
     * @param  \Cone\Root\Resources\Resource  $item
     * @return void
     */
    public static function register(string $key, Item $item): void
    {
        static $request;

        $request = RootRequest::createFrom(static::getFacadeApplication()['request']);

        static::getFacadeRoot()->put($key, $item);

        $item->registered($request);
    }

    /**
     * Resolve the resource by its key.
     *
     * @param  string  $key
     * @return \Cone\Root\Resources\Resource
     *
     * @throws \Cone\Root\Exceptions\ResourceResolutionException
     */
    public static function resolve(string $key): Item
    {
        if (! static::getFacadeRoot()->has($key)) {
            throw new ResourceResolutionException("Unable to resolve resource with key [{$key}].");
        }

        return static::getFacadeRoot()->get($key);
    }

    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor(): string
    {
        return Resources::class;
    }
}
