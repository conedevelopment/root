<?php

namespace Cone\Root\Support\Collections;

use Cone\Root\Widgets\Widget;
use Illuminate\Database\Eloquent\Model;
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
     * Filter the widgets that are available for the current request and model.
     */
    public function authorized(Request $request, Model $model = null): static
    {
        return $this->filter->authorized($request, $model)->values();
    }

    /**
     * Filter the widgets that are visible in the given context.
     */
    public function visible(string|array $context): static
    {
        return $this->filter->visible($context)->values();
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
