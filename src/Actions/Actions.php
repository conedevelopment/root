<?php

namespace Cone\Root\Actions;

use Cone\Root\Traits\RegistersRoutes;
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
     * Map the action to table components.
     */
    public function mapToTableComponents(Request $request): array
    {
        return $this->map->toTableComponent($request)->all();
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
