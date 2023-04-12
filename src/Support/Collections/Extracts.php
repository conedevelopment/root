<?php

namespace Cone\Root\Support\Collections;

use Cone\Root\Extracts\Extract;
use Illuminate\Http\Request;
use Illuminate\Routing\Router;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;

class Extracts extends Collection
{
    /**
     * Register the given extracts.
     */
    public function register(array|Extract $extracts): static
    {
        foreach (Arr::wrap($extracts) as $extract) {
            $this->push($extract);
        }

        return $this;
    }

    /**
     * Filter the extracts that are authorized for the given request.
     */
    public function authorized(Request $request): static
    {
        return $this->filter->authorized($request)->values();
    }

    /**
     * Register the extract routes.
     */
    public function registerRoutes(Router $router): void
    {
        $router->prefix('extracts')->group(function (Router $router): void {
            $this->each->registerRoutes($router);
        });
    }
}
