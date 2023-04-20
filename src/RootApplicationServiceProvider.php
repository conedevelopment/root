<?php

namespace Cone\Root;

use Illuminate\Routing\Router;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;

class RootApplicationServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        $this->gate();

        $this->app->make('root')->booting(function (Root $root): void {
            $this->registerResources($root);
            $this->registerWidgets($root);
            $this->registerRoutes($root);
        });
    }

    /**
     * Register the resources.
     */
    protected function registerResources(Root $root): void
    {
        foreach ($this->resources() as $resource) {
            $root->resources->register($resource);
        }
    }

    /**
     * Register the widgets.
     */
    protected function registerWidgets(Root $root): void
    {
        foreach ($this->widgets() as $widget) {
            $root->widgets->push($widget);
        }
    }

    /**
     * Register the routes.
     */
    protected function registerRoutes(Root $root): void
    {
        $root->routes(function (Router $router) use ($root): void {
            $router->prefix('dashboard')->group(function (Router $router) use ($root): void {
                $root->widgets->registerRoutes($router);
            });
        });
    }

    /**
     * Register the default authorization gate.
     */
    protected function gate(): void
    {
        Gate::define('viewRoot', static function (): bool {
            return true;
        });
    }

    /**
     * The resources.
     */
    protected function resources(): array
    {
        return [];
    }

    /**
     * The dashboard widgets.
     */
    protected function widgets(): array
    {
        return [];
    }
}
