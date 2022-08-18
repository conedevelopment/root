<?php

namespace Cone\Root;

use Cone\Root\Http\Requests\ResourceRequest;
use Cone\Root\Http\Requests\RootRequest;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Model;
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
        Interfaces\Models\Meta::class => Models\Meta::class,
        Interfaces\Models\Notification::class => Models\Notification::class,
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

        $this->publishes([
            __DIR__.'/../stubs/UserResource.stub' => $this->app->path('Root/Resources/UserResource.php'),
        ], 'root-user-resource');
    }

    /**
     * Register the routes.
     *
     * @return void
     */
    protected function registerRoutes(): void
    {
        $this->app['router']->patterns([
            'rootResource' => '[0-9]+|[0-9a-f]{8}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{12}',
            'rootRelated' => '[0-9]+|[0-9a-f]{8}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{12}',
        ]);

        $this->app['router']->bind('rootResource', function (string $id): Model {
            static $request;

            $request = ResourceRequest::createFrom($this->app['request']);

            return $request->resource()->resolveRouteBinding($id);
        });

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
     *
     * @return void
     */
    protected function registerComposers(): void
    {
        $this->app['view']->composer('root::app', static function (View $view): void {
            $app = $view->getFactory()->getContainer();

            $request = RootRequest::createFrom($app['request']);

            $view->with('root', [
                'resources' => Support\Facades\Resource::available($request)->values(),
                'translations' => (object) $app['translator']->getLoader()->load($app->getLocale(), '*', '*'),
                'user' => $request->user()->toRoot(),
                'config' => [
                    'name' => $app['config']->get('app.name'),
                    'url' => Root::getPath(),
                    'branding' => $app['config']->get('root.branding'),
                ],
            ]);
        });
    }
}
