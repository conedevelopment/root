<?php

namespace Cone\Root;

use Cone\Root\Exceptions\SaveFormDataException;
use Cone\Root\Models\Medium;
use Cone\Root\Models\User;
use Cone\Root\Policies\MediumPolicy;
use Cone\Root\Resources\Resource;
use Cone\Root\Support\Alert;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Contracts\Debug\ExceptionHandler;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\View;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Console\AboutCommand;
use Illuminate\Foundation\Events\VendorTagPublished;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Route;
use Illuminate\Routing\Router;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\ServiceProvider;

class RootServiceProvider extends ServiceProvider
{
    /**
     * All of the container bindings that should be registered.
     *
     * @var array
     */
    public $bindings = [
        Interfaces\Models\AuthCode::class => Models\AuthCode::class,
        Interfaces\Models\Medium::class => Models\Medium::class,
        Interfaces\Models\Meta::class => Models\Meta::class,
        Interfaces\Models\Notification::class => Models\Notification::class,
        Interfaces\Models\Option::class => Models\Option::class,
        Interfaces\Models\User::class => Models\User::class,
    ];

    /**
     * All of the container singletons that should be registered.
     *
     * @var array
     */
    public $singletons = [
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

        $this->app->afterResolving(EncryptCookies::class, static function (EncryptCookies $middleware): void {
            $middleware->disableFor('__root_theme');
        });

        $this->app->booted(static function (Application $app): void {
            $app->make(Root::class)->boot();
        });

        $this->app['request']->macro('isTurboFrameRequest', function (): bool {
            return $this->hasHeader('Turbo-Frame');
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

        $this->registerViews();
        $this->registerRoutes();
        $this->registerExceptions();
        $this->registerAuth();
        $this->registerEvents();
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
            __DIR__.'/../stubs/RootServiceProvider.stub' => $this->app->basePath('app/Providers/RootServiceProvider.php'),
            __DIR__.'/../stubs/UserResource.stub' => $this->app->basePath('app/Root/Resources/UserResource.php'),
        ], 'root-stubs');
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

        $this->app['router']->bind('resource', function (string $key) use ($root): Resource {
            return $root->resources->resolve($key);
        });

        $this->app['router']->bind('resourceModel', function (string $id, Route $route): Model {
            return $id === 'create'
                ? $route->parameter('_resource')->getModelInstance()
                : $route->parameter('_resource')->resolveRouteBinding($this->app['request'], $id);
        });

        $this->app['router']
            ->middleware(['web'])
            ->domain($root->getDomain())
            ->prefix($root->getPath())
            ->as('root.auth.')
            ->group(function (): void {
                $this->loadRoutesFrom(__DIR__.'/../routes/auth.php');
            });

        $root->routes(function (Router $router): void {
            $router->prefix('api')->as('api.')->group(function (): void {
                $this->loadRoutesFrom(__DIR__.'/../routes/api.php');
            });

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
        AboutCommand::add('Root', fn (): array => ['Version' => Root::VERSION]);

        $this->commands([
            Console\Commands\ActionMake::class,
            Console\Commands\ClearChunks::class,
            Console\Commands\ClearMedia::class,
            Console\Commands\FieldMake::class,
            Console\Commands\FilterMake::class,
            Console\Commands\Install::class,
            Console\Commands\Publish::class,
            Console\Commands\ResourceMake::class,
            Console\Commands\TrendMake::class,
            Console\Commands\ValueMake::class,
            Console\Commands\WidgetMake::class,
        ]);
    }

    /**
     * Register the views.
     */
    protected function registerViews(): void
    {
        $this->loadViewsFrom(__DIR__.'/../resources/views', 'root');

        Blade::componentNamespace('Cone\\Root\\View\\Components', 'root');

        $this->app['view']->composer('root::*', function (View $view): void {
            $request = $this->app->make('request');

            $view->with([
                'alerts' => $request->session()->get('alerts', []),
                'user' => $request->user(),
            ]);
        });
    }

    /**
     * Register the custom exceptions.
     */
    protected function registerExceptions(): void
    {
        $this->app->make(ExceptionHandler::class)->renderable(
            static function (SaveFormDataException $exception): RedirectResponse {
                return Redirect::back()
                    ->withInput()
                    ->with('alerts.form-save', Alert::error($exception->getMessage()));
            }
        );
    }

    /**
     * Register the auth features.
     */
    protected function registerAuth(): void
    {
        Gate::define('viewRoot', static function (User $user): bool {
            return Root::instance()->authorized($user);
        });

        Gate::policy(Medium::getProxiedClass(), MediumPolicy::class);
    }

    /**
     * Register the events.
     */
    protected function registerEvents(): void
    {
        $this->app['events']->listen(VendorTagPublished::class, Listeners\FormatRootStubs::class);
    }
}
