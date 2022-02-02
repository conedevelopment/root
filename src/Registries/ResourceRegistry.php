<?php

namespace Cone\Root\Registries;

use Cone\Root\Exceptions\ResourceResolutionException;
use Cone\Root\Interfaces\Registries\ResourceRegistry as Contract;
use Cone\Root\Resources\Resource;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;

class ResourceRegistry extends Registry implements Contract
{
    /**
     * {@inheritdoc}
     */
    public function register(string $key, object $item): void
    {
        parent::register($key, $item);

        App::call([$item, 'registered']);
    }

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
     * Filter the available resources.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function available(Request $request): array
    {
        return array_filter($this->items, static function (Resource $resource) use ($request): bool {
            return $resource->authorized($request);
        });
    }
}
