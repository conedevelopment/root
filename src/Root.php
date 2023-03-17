<?php

namespace Cone\Root;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Str;

abstract class Root
{
    /**
     * The package version.
     *
     * @var string
     */
    public const VERSION = '1.1.3';

    /**
     * The registered callbacks.
     */
    protected static array $callbacks = [];

    /**
     * Determine if Root should run on the given request.
     */
    public static function shouldRun(Request $request): bool
    {
        $host = empty(static::getDomain())
            ? parse_url(Config::get('app.url'), PHP_URL_HOST)
            : static::getDomain();

        $segments = explode('/', $request->getRequestUri());

        return (empty(static::getDomain()) || $request->getHost() === $host)
            && (static::getPath() === '/' || $segments[1] === trim(static::getPath(), '/'));
    }

    /**
     * Run Root and call the registered callbacks.
     */
    public static function run(Request $request): void
    {
        foreach (static::$callbacks as $callback) {
            call_user_func_array($callback, [$request]);
        }
    }

    /**
     * Register a callback when Root is running.
     */
    public static function running(Closure $callback): void
    {
        static::$callbacks[] = $callback;
    }

    /**
     * Register the root routes.
     */
    public static function routes(Closure $callback): void
    {
        Route::as('root.')
            ->domain(static::getDomain())
            ->prefix(static::getPath())
            ->middleware(['root'])
            ->group($callback);
    }

    /**
     * Get the Root URI path.
     */
    public static function getPath(): string
    {
        return Str::start(Config::get('root.path', 'root'), '/');
    }

    /**
     * Get the Root domain.
     */
    public static function getDomain(): string
    {
        return (string) Config::get('root.domain', null);
    }
}
