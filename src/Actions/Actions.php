<?php

namespace Cone\Root\Actions;

use Cone\Root\Traits\RegistersRoutes;
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
     * Filter the actions that are visible in the given context.
     */
    public function visible(string|array $context): static
    {
        return $this->filter->visible($context)->values();
    }

    /**
     * Map the action to forms.
     */
    public function mapToForms(Request $request, Model $model): array
    {
        return $this->map->toForm($request, $model)->all();
    }

    /**
     * Register the action routes.
     */
    public function registerRoutes(Request $request, Router $router): void
    {
        $router->prefix('actions')->group(function (Router $router) use ($request): void {
            $this->each(static function (Action $action) use ($request, $router): void {
                if (in_array(RegistersRoutes::class, class_uses_recursive($action))) {
                    $action->registerRoutes($request, $router);
                }
            });
        });
    }
}
