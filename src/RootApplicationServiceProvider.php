<?php

namespace Cone\Root;

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
        $this->registerRoutes();
    }

    /**
     * Register the resources.
     */
    protected function registerResources(): void
    {
        $this->app->make('root')->booting(function (Root $root): void {
            foreach ($this->resources() as $resource) {
                $root->resources->register($resource);
            }
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
