<?php

namespace Cone\Root\Table\Actions;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Routing\Router;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;

class Actions extends Collection
{
    /**
     * Register the given actions.
     */
    public function register(array|Action $actions): static
    {
        foreach (Arr::wrap($actions) as $action) {
            $this->push($action);
        }

        return $this;
    }

    /**
     * Filter the actions that are available for the current request and model.
     */
    public function authorized(Request $request, Model $model = null): static
    {
        return $this->filter->authorized($request, $model)->values();
    }

    /**
     * Register the action routes.
     */
    public function registerRoutes(Router $router): void
    {
        $router->prefix('actions')->group(function (Router $router): void {
            $this->each->registerRoutes($router);
        });
    }
}
