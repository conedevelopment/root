<?php

namespace Cone\Root;

use Cone\Root\Support\Collections\Widgets;
use Illuminate\Routing\Router;
use Illuminate\Support\ServiceProvider;

abstract class RootApplicationServiceProvider extends ServiceProvider
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

        Root::routes(function (Router $router): void {
            $this->app->make('root.widgets')->registerRoutes($this->app['request'], $router);
        }, true);
    }

    /**
     * Register the default authorization gate.
     *
     * @return void
     */
    abstract protected function gate(): void;

    /**
     * The dashboard widgets.
     *
     * @return array
     */
    abstract protected function widgets(): array;
}
