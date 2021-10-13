<?php

namespace Cone\Root\Traits;

use Cone\Root\Resources\Resource;

trait InteractsWithResources
{
    /**
     * Get the resource representation of the model.
     *
     * @return \Cone\Root\Resources\Resource
     */
    public static function toResource(): Resource
    {
        return new Resource(static::class);
    }
}
