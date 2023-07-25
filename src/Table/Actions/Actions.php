<?php

namespace Cone\Root\Table\Actions;

use Cone\Root\Table\Table;
use Illuminate\Routing\Router;
use Illuminate\Support\Collection;
use Illuminate\Support\Traits\ForwardsCalls;

class Actions
{
    use ForwardsCalls;

    /**
     * The parent table instance.
     */
    protected Table $table;

    /**
     * The actions collection.
     */
    protected Collection $actions;

    /**
     * Create a new actions instance.
     */
    public function __construct(Table $table, array $actions = [])
    {
        $this->table = $table;
        $this->actions = new Collection($actions);
    }

    /**
     * Make a new action instance.
     */
    public function action(string $action, array ...$params): Action
    {
        $instance = new $action($this, ...$params);

        $this->push($instance);

        return $instance;
    }

    /**
     * Register the action routes.
     */
    public function registerRoutes(Router $router): void
    {
        $router->prefix('actions')->group(function (Router $router): void {
            $this->actions->each->registerRoutes($router);
        });
    }

    /**
     * Handle the dynamic method call.
     */
    public function __call($method, $parameters): mixed
    {
        return $this->forwardCallTo($this->actions, $method, $parameters);
    }
}
