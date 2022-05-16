<?php

namespace Cone\Root\Support\Collections;

use Cone\Root\Http\Requests\RootRequest;
use Illuminate\Routing\Router;
use Illuminate\Support\Collection;

class Extracts extends Collection
{
    /**
     * Filter the extracts that are available for the given request.
     *
     * @param  \Cone\Root\Http\Requests\RootRequest  $request
     * @return static
     */
    public function available(RootRequest $request): static
    {
        return $this->filter->authorized($request)->values();
    }

    /**
     * Register the extract routes.
     *
     * @param  \Cone\Root\Http\Requests\RootRequest  $request
     * @param  \Illuminate\Routing\Router  $router
     * @return void
     */
    public function registerRoutes(RootRequest $request, Router $router): void
    {
        $router->prefix('extracts')->group(function (Router $router) use ($request): void {
            $this->each->registerRoutes($request, $router);
        });
    }
}
