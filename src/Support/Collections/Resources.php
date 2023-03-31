<?php

namespace Cone\Root\Support\Collections;

use Cone\Root\Exceptions\ResourceResolutionException;
use Cone\Root\Interfaces\Support\Collections\Resources as Contract;
use Cone\Root\Resources\Resource;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;

class Resources extends Collection implements Contract
{
    /**
     * Filter the available resources.
     */
    public function available(Request $request): static
    {
        return $this->filter(static function (Resource $resource) use ($request): bool {
            return $resource->authorized($request);
        });
    }

    /**
     * Register the given resource into the colleciton.
     */
    public function register(Resource $resource): void
    {
        $this->put($resource->getKey(), $resource);
    }

    /**
     * Resolve the resource registered with the given key.
     */
    public function resolve(string $key): Resource
    {
        if (! $this->has($key)) {
            throw new ResourceResolutionException();
        }

        return $this->get($key);
    }
}
