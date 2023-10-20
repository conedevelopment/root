<?php

namespace Cone\Root\Resources;

use Cone\Root\Exceptions\ResourceResolutionException;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;

class Resources extends Collection
{
    /**
     * Register the given resource into the collection.
     */
    public function register(array|Resource $resources): void
    {
        foreach (Arr::wrap($resources) as $resource) {
            $this->put($resource->getKey(), $resource);
        }
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

    /**
     * Filter the authorized resources.
     */
    public function authorized(Request $request): static
    {
        return $this->filter->authorized($request)->values();
    }
}
