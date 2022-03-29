<?php

namespace Cone\Root;

use Cone\Root\Http\Requests\RootRequest;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\View;
use Illuminate\Routing\Router;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Str;

class RootServiceProvider extends ServiceProvider
{
    /**
     * All of the container bindings that should be registered.
     *
     * @var array
     */
    public array $bindings = [
        Interfaces\Models\Medium::class => Models\Medium::class,
        Interfaces\Models\Record::class => Models\Record::class,
        Interfaces\Models\User::class => Models\User::class,
    ];

    /**
     * All of the container singletons that should be registered.
     *
     * @var array
     */
    public array $singletons = [
        Interfaces\Conversion\Manager::class => Conversion\Manager::class,
        Interfaces\Support\Collections\Assets::class => Support\Collections\Assets::class,
        Interfaces\Support\Collections\Resources::class => Support\Collections\Resources::class,
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

        $this->app->booted(static function (Application $app): void {
            if ($app->runningInConsole() || Root::shouldRun($app['request'])) {
                Root::run($app['request']);
            }
        });

        $this->app->resolving(RootRequest::class, static function (RootRequest $request, Application $app): void {
            RootRequest::createFrom($app['request'], $request);
        });
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
            $this->registerCommands();
            $this->registerPublishes();
        }

        $this->loadViewsFrom(__DIR__.'/../resources/views', 'root');

        $this->registerComposers();
        $this->registerRoutes();
        $this->registerMacros();
    }

    /**
     * Register publishes.
     *
     * @return void
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
    }

    /**
     * Register the routes.
     *
     * @return void
     */
    protected function registerRoutes(): void
    {
        $this->app['router']->middlewareGroup(
            'root', $this->app['config']->get('root.middleware', [])
        );

        Root::routes(function (): void {
            $this->loadRoutesFrom(__DIR__.'/../routes/web.php');
        });
    }

    /**
     * Register commands.
     *
     * @return void
     */
    protected function registerCommands(): void
    {
        $this->commands([
            Console\Commands\ActionMake::class,
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
     *
     * @return void
     */
    protected function registerComposers(): void
    {
        $this->app['view']->composer('root::app', static function (View $view): void {
            $app = $view->getFactory()->getContainer();

            $view->with('root', [
                'resources' => Support\Facades\Resource::available($app['request'])->values(),
                'translations' => (object) $app['translator']->getLoader()->load($app->getLocale(), '*', '*'),
                'user' => $app['request']->user()->toRoot(),
                'config' => [
                    'name' => $app['config']->get('app.name'),
                    'url' => $app['url']->route('root.dashboard'),
                ],
            ]);
        });
    }

    /**
     * Register the macros.
     *
     * @return void
     */
    protected function registerMacros(): void
    {
        $this->app['router']->macro('prependGroupStackPrefix', function (string $prefix): Router {
            $attributes = $this->mergeWithLastGroup(['prefix' => $prefix], false);

            if (! empty(Root::getPath())) {
                $attributes = array_replace($attributes, [
                    'prefix' => Str::replaceFirst(
                        Root::getPath(),
                        sprintf('%s/%s', Root::getPath(), $prefix),
                        $this->getLastGroupPrefix()
                    ),
                ]);
            }

            $this->groupStack[array_key_last($this->groupStack)] = $attributes;

            return $this;
        });
    }
}
