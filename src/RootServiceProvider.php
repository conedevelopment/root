<?php

namespace Cone\Root;

use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\ServiceProvider;

class RootServiceProvider extends ServiceProvider
{
    /**
     * All of the container bindings that should be registered.
     */
    public array $bindings = [
        Interfaces\Models\Medium::class => Models\Medium::class,
        Interfaces\Models\Meta::class => Models\Meta::class,
        Interfaces\Models\User::class => Models\User::class,
    ];

    /**
     * All of the container singletons that should be registered.
     */
    public array $singletons = [
        Interfaces\Conversion\Manager::class => Conversion\Manager::class,
    ];

    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->singleton(Root::class, static function (Application $app): Root {
            return new Root($app);
        });

        $this->app->alias(Root::class, 'root');

        if (! $this->app->configurationIsCached()) {
            $this->mergeConfigFrom(__DIR__.'/../config/root.php', 'root');
        }

        $this->app->booted(static function (Application $app): void {
            $app->make(Root::class)->boot();
        });
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        if ($this->app->runningInConsole()) {
            $this->loadMigrationsFrom(__DIR__.'/../database/migrations');
            $this->registerCommands();
            $this->registerPublishes();
        }

        $this->loadViewsFrom(__DIR__.'/../resources/views', 'root');

        Blade::componentNamespace('Cone\\Root\\View\\Components', 'root');

        $this->registerRoutes();
    }

    /**
     * Register publishes.
     */
    protected function registerPublishes(): void
    {
        $this->publishes([
            __DIR__.'/../config/root.php' => $this->app->configPath('root.php'),
        ], 'root-config');

        $this->publishes([
            __DIR__.'/../public' => $this->app->publicPath('vendor/root'),
        ], 'root-compiled');

        $this->publishes([
            __DIR__.'/../resources/js' => $this->app->resourcePath('js/vendor/root'),
            __DIR__.'/../resources/sass' => $this->app->resourcePath('sass/vendor/root'),
        ], 'root-vendor');

        $this->publishes([
            __DIR__.'/../resources/views' => $this->app->resourcePath('views/vendor/root'),
        ], 'root-views');

        $this->publishes([
            __DIR__.'/../stubs/RootServiceProvider.stub' => $this->app->path('Providers/RootServiceProvider.php'),
        ], 'root-provider');

        $this->publishes([
            __DIR__.'/../stubs/UserResource.stub' => $this->app->path('Root/Resources/UserResource.php'),
        ], 'root-user-resource');
    }

    /**
     * Register the routes.
     */
    protected function registerRoutes(): void
    {
        $this->app['router']->middlewareGroup(
            'root', $this->app['config']->get('root.middleware', [])
        );

        $root = $this->app->make(Root::class);

        $this->app['router']
            ->middleware(['web'])
            ->domain($root->getDomain())
            ->prefix($root->getPath())
            ->as('root.auth.')
            ->group(function (): void {
                $this->loadRoutesFrom(__DIR__.'/../routes/auth.php');
            });

        $this->app->make(Root::class)->routes(function (): void {
            $this->loadRoutesFrom(__DIR__.'/../routes/web.php');
        });

        RateLimiter::for('root.auth', static function (Request $request): Limit {
            return Limit::perMinute(6)->by($request->user()?->id ?: $request->ip());
        });
    }

    /**
     * Register commands.
     */
    protected function registerCommands(): void
    {
        $this->commands([
            Console\Commands\ActionMake::class,
            Console\Commands\ClearChunks::class,
            Console\Commands\ExtractMake::class,
            Console\Commands\FieldMake::class,
            Console\Commands\FilterMake::class,
            Console\Commands\Install::class,
            Console\Commands\Publish::class,
            Console\Commands\ResourceMake::class,
            Console\Commands\WidgetMake::class,
        ]);
    }
}
