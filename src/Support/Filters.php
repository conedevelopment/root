<?php

namespace Cone\Root\Support;

use Illuminate\Support\Arr;

abstract class Filters
{
    /**
     * The registered filters.
     */
    protected static array $callbacks = [];

    /**
     * Register a new filter.
     */
    public static function register(string $hook, callable $callback, int $priority = 10): void
    {
        static::$callbacks[$hook][] = [
            'callback' => $callback,
            'priority' => $priority,
        ];
    }

    /**
     * Remove the given filter.
     */
    public static function remove(string $hook, callable $callback, int $priority = 10): void
    {
        foreach (static::$callbacks[$hook] ?? [] as $key => $filter) {
            if ($filter['callback'] === $callback && $filter['priority'] === $priority) {
                unset(static::$callbacks[$hook][$key]);
            }
        }
    }

    /**
     * Apply the filters on the given hook and value.
     */
    public static function apply(string $hook, mixed $value, ...$parameters): mixed
    {
        return array_reduce(
            Arr::sort(static::$callbacks[$hook] ?? [], 'priority'),
            static function (mixed $value, array $filter) use ($parameters): mixed {
                return call_user_func_array($filter['callback'], [$value, ...$parameters]);
            },
            $value
        );
    }

    /**
     * Flush the filters.
     */
    public static function flush(): void
    {
        static::$callbacks = [];
    }

    /**
     * Get all the registered filters.
     */
    public static function all(): array
    {
        return static::$callbacks;
    }
}
