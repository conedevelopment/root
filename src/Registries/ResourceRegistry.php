<?php

namespace Cone\Root\Registries;

use Cone\Root\Exceptions\ResourceResolutionException;
use Cone\Root\Interfaces\Registries\ResourceRegistry as Contract;
use Cone\Root\Resources\Resource;
use Illuminate\Support\Facades\App;

class ResourceRegistry extends Registry implements Contract
{
    /**
     * Resolve the resource by its key.
     *
     * @param  string  $key
     * @return \Cone\Root\Resources\Resource
     *
     * @throws \Cone\Root\Exceptions\ResourceResolutionException
     */
    public function resolve(string $key): Resource
    {
        if (! $this->has($key)) {
            throw new ResourceResolutionException("Unable to resolve resource with key [{$key}].");
        }

        return $this->get($key);
    }

    /**
     * Register the resource routes.
     *
     * @return void
     */
    public function routes(): void
    {
        foreach ($this->items as $item) {
            App::call([$item, 'routes']);
        }
    }
}
