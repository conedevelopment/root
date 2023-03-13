<?php

namespace Cone\Root;

use Cone\Root\Http\Requests\RootRequest;
use Cone\Root\Support\Collections\Widgets;
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
        $this->app->singleton('root.widgets', function (): Widgets {
            return Widgets::make($this->widgets());
        });
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        $this->gate();
        $this->registerRoutes();
        $this->registerResources();
    }

    /**
     * Register the resources.
     */
    protected function registerResources(): void
    {
        //
    }

    /**
     * Register the routes.
     */
    protected function registerRoutes(): void
    {
        $this->app->make('root')->routes(function (Router $router): void {
            $router->prefix('dashboard')->group(function (Router $router): void {
                $this->app->make('root.widgets')->registerRoutes(
                    RootRequest::createFrom($this->app['request']), $router
                );
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
