<?php

namespace Cone\Root;

use Cone\Root\Http\Middleware\HandleRootRequests;
use Illuminate\Support\ServiceProvider;

class RootServiceProvider extends ServiceProvider
{
    /**
     * All of the container bindings that should be registered.
     *
     * @var array
     */
    public array $bindings = [
        Interfaces\Models\Medium::class => Models\Medium::class,
        Interfaces\Models\User::class => Models\User::class,
    ];

    /**
     * All of the container singletons that should be registered.
     *
     * @var array
     */
    public array $singletons = [
        Interfaces\Conversion\Manager::class => Conversion\Manager::class,
        Interfaces\Registries\ResourceRegistry::class => Registries\ResourceRegistry::class,
    ];

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register(): void
    {
        if (! $this->app->configurationIsCached()) {
            $this->mergeConfigFrom(__DIR__.'/../config/root.php', 'root');
        }
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot(): void
    {
        if ($this->app->runningInConsole()) {
            $this->loadMigrationsFrom(__DIR__.'/../database/migrations');
        }

        $this->app->booted(function (): void {
            $this->app['router']
                ->as('root.')
                ->prefix('root')
                ->middleware(['web', /*'auth', 'verified',*/ HandleRootRequests::class])
                ->group(function (): void {
                    $this->loadRoutesFrom(__DIR__.'/../routes/web.php');
                });
        });

        $this->loadViewsFrom(__DIR__.'/../resources/views', 'root');

        (Models\User::proxy())::registerResource();
    }
}
