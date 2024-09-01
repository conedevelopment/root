<?php

namespace Cone\Root\Traits;

use Illuminate\Container\Container;
use Throwable;

trait InteractsWithProxy
{
    /**
     * Get the proxied interface.
     */
    abstract public static function getProxiedInterface(): string;

    /**
     * Resolve and get the proxy instance.
     */
    public static function proxy(): static
    {
        static $proxy;

        if (! isset($proxy)) {
            try {
                $proxy = Container::getInstance()->make(
                    static::getProxiedInterface()
                );
            } catch (Throwable) {
                $proxy = new static;
            }
        }

        return $proxy;
    }

    /**
     * Get the proxied class.
     */
    public static function getProxiedClass(): string
    {
        return get_class(static::proxy());
    }
}
