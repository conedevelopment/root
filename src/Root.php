<?php

namespace Cone\Root;

use Closure;
use Illuminate\Http\Request;

abstract class Root
{
    /**
     * The package version.
     *
     * @var string
     */
    public const VERSION = '1.0.0';

    /**
     * The registered callbacks.
     *
     * @var array
     */
    protected static array $callbacks = [];

    /**
     * Run Press and call the registered callbacks.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return void
     */
    public static function run(Request $request): void
    {
        foreach (static::$callbacks as $callback) {
            call_user_func_array($callback, [$request]);
        }
    }

    /**
     * Register a callback when Root is running.
     *
     * @param  \Closure  $callback
     * @return void
     */
    public static function running(Closure $callback): void
    {
        static::$callbacks[] = $callback;
    }

    /**
     * Flush the registered callbacks.
     *
     * @return void
     */
    public static function flush(): void
    {
        static::$callbacks = [];
    }
}
