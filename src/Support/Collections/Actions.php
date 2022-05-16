<?php

namespace Cone\Root\Support\Collections;

use Cone\Root\Actions\Action;
use Cone\Root\Http\Requests\RootRequest;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Routing\Router;
use Illuminate\Support\Collection;

class Actions extends Collection
{
    /**
     * Filter the actions that are available for the given request.
     *
     * @param  \Cone\Root\Http\Requests\RootRequest  $request
     * @param  array  ...$parameters
     * @return static
     */
    public function available(RootRequest $request, ...$parameters): static
    {
        return $this->filter(static function (Action $action) use ($request, $parameters): bool {
            return $action->authorized($request, ...$parameters)
                && $action->visible($request);
        })->values();
    }

    /**
     * Map the actions to form.
     *
     * @param  \Cone\Root\Http\Requests\RootRequest  $request
     * @param  \Illuminate\Database\Eloquent\Model  $model
     * @return \Illuminate\Support\Collection
     */
    public function mapToForm(RootRequest $request, Model $model): Collection
    {
        return $this->map->toForm($request, $model)->toBase();
    }

    /**
     * Register the action routes.
     *
     * @param  \Cone\Root\Http\Requests\RootRequest  $request
     * @param  \Illuminate\Routing\Router  $router
     * @return void
     */
    public function registerRoutes(RootRequest $request, Router $router): void
    {
        $router->prefix('actions')->group(function (Router $router) use ($request): void {
            $this->each->registerRoutes($request, $router);
        });
    }
}
