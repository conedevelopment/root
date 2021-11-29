<?php

namespace Cone\Root\Registries;

use Cone\Root\Exceptions\ResourceResolutionException;
use Cone\Root\Interfaces\Registries\Item;
use Cone\Root\Interfaces\Registries\ResourceRegistry as Contract;
use Cone\Root\Resources\Resource;
use Cone\Root\Root;
use Illuminate\Support\Facades\App;

class ResourceRegistry extends Registry implements Contract
{
    /**
     * Register an item into the registry.
     *
     * @param  string  $key
     * @param  \Cone\Root\Interfaces\Registries\Item  $item
     * @return void
     */
    public function register(string $key, Item $item): void
    {
        parent::register($key, $item);

        Root::routes(static function () use ($item) {
            App::call([$item, 'routes']);
        });
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
}
