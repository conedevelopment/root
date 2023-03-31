<?php

namespace Cone\Root\Support\Collections;

use Cone\Root\Widgets\Widget;
use Illuminate\Http\Request;
use Illuminate\Routing\Router;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;

class Widgets extends Collection
{
    /**
     * Register the given widgets.
     */
    public function register(array|Widget $widgets): static
    {
        foreach (Arr::wrap($widgets) as $widget) {
            $this->push($widget);
        }

        return $this;
    }

    /**
     * Filter the widgets that are visible for the given request.
     */
    public function available(Request $request): static
    {
        return $this->filter(static function (Widget $widget) use ($request): bool {
            return $widget->authorized($request) && $widget->visible($request);
        })->values();
    }

    /**
     * Register the widget routes.
     */
    public function registerRoutes(Router $router): void
    {
        $router->prefix('widgets')->group(function (Router $router): void {
            $this->each->registerRoutes($router);
        });
    }
}
