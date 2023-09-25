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

        $this->registerResources();
        $this->registerWidgets();
    }

    /**
     * Register the resources.
     */
    protected function registerResources(): void
    {
        $this->app->make(Root::class)->resources->register($this->resources());
    }

    /**
     * Register the widgets.
     */
    protected function registerWidgets(): void
    {
        $this->app->make(Root::class)->widgets->register($this->widgets());
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
