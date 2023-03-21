<?php

namespace Cone\Root;

use Closure;
use Cone\Root\Http\Requests\RootRequest;
use Cone\Root\Support\Collections\Resources;
use Cone\Root\Support\Collections\Widgets;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Str;

class Root
{
    /**
     * The package version.
     *
     * @var string
     */
    public const VERSION = '1.2.0';

    /**
     * The registered booting callbacks.
     */
    protected array $booting = [];

    /**
     * The registered booted callbacks.
     */
    protected array $booted = [];

    /**
     * The Application instance.
     */
    public readonly Application $app;

    /**
     * The resources collection.
     */
    public readonly Resources $resources;

    /**
     * The widgets collection.
     */
    public readonly Widgets $widgets;

    /**
     * Create a new Root instance.
     */
    public function __construct(Application $app)
    {
        $this->app = $app;
        $this->resources = new Resources();
        $this->widgets = new Widgets();
    }

    /**
     * Boot the Root application.
     */
    public function boot(): void
    {
        foreach ($this->booting as $callback) {
            call_user_func_array($callback, [$this]);
        }

        $this->resources->each->boot($this);

        foreach ($this->booted as $callback) {
            call_user_func_array($callback, [$this]);
        }
    }

    /**
     * Register a booting callback.
     */
    public function booting(Closure $callback): void
    {
        $this->booting[] = $callback;
    }

    /**
     * Get the Root Request instance.
     */
    public function request(): RootRequest
    {
        static $request;

        $request = RootRequest::createFrom($this->app['request']);

        return $request;
    }

    /**
     * Register a booted callback.
     */
    public function booted(Closure $callback): void
    {
        $this->booted[] = $callback;
    }

    /**
     * Determine if Root should run on the given request.
     */
    public function shouldRun(Request $request): bool
    {
        $host = empty($this->getDomain())
            ? parse_url(Config::get('app.url'), PHP_URL_HOST)
            : $this->getDomain();

        $segments = explode('/', $request->getRequestUri());

        return (empty($this->getDomain()) || $request->getHost() === $host)
            && ($this->getPath() === '/' || $segments[1] === trim($this->getPath(), '/'));
    }

    /**
     * Register the root routes.
     */
    public function routes(Closure $callback): void
    {
        Route::as('root.')
            ->domain($this->getDomain())
            ->prefix($this->getPath())
            ->middleware(['root'])
            ->group($callback);
    }

    /**
     * Get the Root URI path.
     */
    public function getPath(): string
    {
        return Str::start(Config::get('root.path', 'root'), '/');
    }

    /**
     * Get the Root domain.
     */
    public function getDomain(): string
    {
        return (string) Config::get('root.domain', null);
    }
}
