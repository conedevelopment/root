<?php

namespace Cone\Root;

use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\View;
use Illuminate\Support\ServiceProvider;

class RootServiceProvider extends ServiceProvider
{
    /**
     * All of the container bindings that should be registered.
     */
    public array $bindings = [
        Interfaces\Models\Medium::class => Models\Medium::class,
        Interfaces\Models\Meta::class => Models\Meta::class,
        Interfaces\Models\Notification::class => Models\Notification::class,
        Interfaces\Models\Record::class => Models\Record::class,
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

        $this->registerComposers();
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
            __DIR__.'/../public' => public_path('vendor/root'),
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

        $this->app->make(Root::class)->routes(function (): void {
            $this->loadRoutesFrom(__DIR__.'/../routes/web.php');
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

    /**
     * Register the view composers.
     */
    protected function registerComposers(): void
    {
        $this->app['view']->composer('root::app', static function (View $view): void {
            $app = $view->getFactory()->getContainer();

            $root = $app->make(Root::class);

            $view->with('root', [
                'resources' => $root->resources->available($app->make('request'))->values(),
                'translations' => (object) $app['translator']->getLoader()->load($app->getLocale(), '*', '*'),
                'user' => $app->make('request')->user()->toRoot(),
                'config' => [
                    'name' => $app['config']->get('app.name'),
                    'url' => $root->getPath(),
                    'branding' => $app['config']->get('root.branding'),
                ],
            ]);
        });
    }
}
