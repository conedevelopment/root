<?php

namespace Cone\Root\Support\Collections;

use Cone\Root\Exceptions\ResourceResolutionException;
use Cone\Root\Resources\Resource;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;

class Resources extends Collection
{
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

    /**
     * Filter the authorized resources.
     */
    public function authorized(Request $request): static
    {
        return $this->filter->authorized($request)->values();
    }

    /**
     * Map the resources into navigation compatible format.
     */
    public function mapToNavigation(Request $request): array
    {
        return $this->map->toNavigation($request)->toArray();
    }
}
