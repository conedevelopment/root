<?php

namespace Cone\Root\Support\Collections;

use Cone\Root\Widgets\Widget;
use Illuminate\Http\Request;
use Illuminate\Routing\Router;
use Illuminate\Support\Collection;

class Widgets extends Collection
{
    /**
     * Filter the widgets that are visible for the given request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return static
     */
    public function available(Request $request): static
    {
        return $this->filter(static function (Widget $widget) use ($request): bool {
                        return $widget->authorized($request) && $widget->visible($request);
                    })
                    ->values();
    }

    /**
     * Register the action routes.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Illuminate\Routing\Router  $router
     * @return void
     */
    public function registerRoutes(Request $request, Router $router): void
    {
        $router->prefix('widgets')->group(function (Router $router) use ($request): void {
            $this->each->registerRoutes($request, $router);
        });
    }
}
