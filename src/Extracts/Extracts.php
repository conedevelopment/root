<?php

namespace Cone\Root\Extracts;

use Cone\Root\Resources\Resource;
use Illuminate\Http\Request;
use Illuminate\Routing\Router;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;

class Extracts
{
    /**
     * The resource instance.
     */
    protected Resource $resource;

    /**
     * The extracts collection.
     */
    protected Collection $extracts;

    /**
     * Create a new extracts instance.
     */
    public function __construct(Resource $resource, array $extracts = [])
    {
        $this->resource = $resource;
        $this->extracts = new Collection($extracts);
    }

    /**
     * Register the given extracts.
     */
    public function register(array|Extract $extracts): static
    {
        foreach (Arr::wrap($extracts) as $extract) {
            $this->extracts->push($extract);
        }

        return $this;
    }

    /**
     * Make a new extract instance.
     */
    public function extract(string $extract, ...$params): Extract
    {
        $instance = new $extract($this->resource, ...$params);

        $this->register($instance);

        return $instance;
    }

    /**
     * Filter the extracts that are authorized for the given request.
     */
    public function authorized(Request $request): static
    {
        return $this->extracts->filter->authorized($request)->values();
    }

    /**
     * Register the extract routes.
     */
    public function registerRoutes(Router $router): void
    {
        $router->prefix('extracts')->group(function (Router $router): void {
            $this->extracts->each->registerRoutes($router);
        });
    }
}
