<?php

namespace Cone\Root\Support\Collections;

use Cone\Root\Http\Requests\RootRequest;
use Illuminate\Routing\Router;
use Illuminate\Support\Collection;

class Extracts extends Collection
{
    /**
     * Filter the extracts that are available for the given request.
     */
    public function available(RootRequest $request): static
    {
        return $this->filter->authorized($request)->values();
    }

    /**
     * Register the extract routes.
     */
    public function registerRoutes(RootRequest $request, Router $router): void
    {
        $router->prefix('extracts')->group(function (Router $router) use ($request): void {
            $this->each->registerRoutes($request, $router);
        });
    }
}
