<?php

namespace Cone\Root;

use Cone\Root\Http\Middleware\HandleRootRequests;
use Illuminate\Contracts\View\View;
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

        $this->loadViewsFrom(__DIR__.'/../resources/views', 'root');

        $this->registerRoutes();
        $this->registerCommands();
        $this->registerComposers();

        Root::running(static function (): void {
            (Models\User::proxy())::registerResource();
        });
    }

    /**
     * Register the routes.
     *
     * @return void
     */
    protected function registerRoutes(): void
    {
        $this->app->booted(function (): void {
            $this->app['router']
                ->as('root.')
                ->prefix('root')
                ->middleware(['web', /*'auth', 'verified',*/ HandleRootRequests::class])
                ->group(function (): void {
                    $this->loadRoutesFrom(__DIR__.'/../routes/web.php');
                });
        });
    }

    /**
     * Register commands.
     *
     * @return void
     */
    protected function registerCommands(): void
    {
        if ($this->app->runningInConsole()) {
            $this->commands([
                Console\Commands\ActionMake::class,
                Console\Commands\Install::class,
                Console\Commands\Publish::class,
            ]);
        }
    }

    /**
     * Register the view composers.
     *
     * @return void
     */
    protected function registerComposers(): void
    {
        $this->app['view']->composer('root::app', function (View $view): void {
            $view->with('root', [
                'resources' => Support\Facades\Resource::toArray(),
                'translations' => (object) $this->app['translator']->getLoader()->load($this->app->getLocale(), '*', '*'),
                'user' => $this->app['request']->user(),
            ]);
        });
    }
}
