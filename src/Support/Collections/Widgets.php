<?php

namespace Cone\Root\Support\Collections;

use Cone\Root\Http\Requests\RootRequest;
use Cone\Root\Widgets\Widget;
use Illuminate\Routing\Router;
use Illuminate\Support\Collection;

class Widgets extends Collection
{
    /**
     * Filter the widgets that are visible for the given request.
     */
    public function available(RootRequest $request): static
    {
        return $this->filter(static function (Widget $widget) use ($request): bool {
            return $widget->authorized($request) && $widget->visible($request);
        })->values();
    }

    /**
     * Register the widget routes.
     */
    public function registerRoutes(RootRequest $request, Router $router): void
    {
        $router->prefix('widgets')->group(function (Router $router) use ($request): void {
            $this->each->registerRoutes($request, $router);
        });
    }
}
