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
     * Filter the actions that are available for the given request.
     */
    public function available(Request $request, ...$parameters): static
    {
        return $this->filter(static function (Action $action) use ($request, $parameters): bool {
            return $action->authorized($request, ...$parameters)
                && $action->visible($request);
        })->values();
    }

    /**
     * Map the actions to form.
     */
    public function mapToForm(Request $request, Model $model): Collection
    {
        return $this->map->toForm($request, $model)->toBase();
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
