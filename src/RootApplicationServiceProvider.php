<?php

namespace Cone\Root;

use Cone\Root\Support\Collections\Widgets;
use Cone\Root\Support\Facades\Resource;
use Illuminate\Routing\Router;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;

class RootApplicationServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register(): void
    {
        $this->app->singleton('root.widgets', function (): Widgets {
            return Widgets::make($this->widgets());
        });
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot(): void
    {
        $this->gate();
        $this->registerRoutes();
        $this->registerResources();
    }

    /**
     * Register the resources.
     *
     * @return void
     */
    protected function registerResources(): void
    {
        Root::running(function (): void {
            foreach ($this->resources() as $resource) {
                Resource::register($resource->getKey(), $resource);
            }
        });
    }

    /**
     * Register the routes.
     *
     * @return void
     */
    protected function registerRoutes(): void
    {
        Root::routes(function (Router $router): void {
            $this->app->make('root.widgets')->registerRoutes($this->app['request'], $router);
        }, true);
    }

    /**
     * Register the default authorization gate.
     *
     * @return void
     */
    protected function gate(): void
    {
        Gate::define('viewRoot', static function (): bool {
            return true;
        });
    }

    /**
     * The resources.
     *
     * @return array
     */
    protected function resources(): array
    {
        return [];
    }

    /**
     * The dashboard widgets.
     *
     * @return array
     */
    protected function widgets(): array
    {
        return [];
    }
}
