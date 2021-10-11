<?php

namespace Cone\Root\Traits;

use Cone\Root\Resources\Resource;
use Cone\Root\Support\Facades\Resource as Registry;

trait Resourceable
{
    /**
     * Register the resource for the model.
     *
     * @return void
     */
    public static function registerResource(): void
    {
        $resource = new Resource(
            method_exists(static::class, 'getProxiedClass') ? static::getProxiedClass() : static::class
        );

        Registry::register($resource->getKey(), $resource);
    }
}
