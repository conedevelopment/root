<?php

namespace Cone\Root\Support\Collections;

use Cone\Root\Actions\Action;
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
     * Map the actions to form.
     */
    public function mapToForm(Request $request, Model $model): Collection
    {
        return $this->map->toForm($request, $model)->each->build($request)->toBase();
    }

    /**
     * Register the routes.
     */
    public function registerRoutes(Router $router): void
    {
        $router->prefix('actions')->group(function (Router $router): void {
            $this->each->registerRoutes($router);
        });
    }
}
