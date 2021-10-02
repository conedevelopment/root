<?php

namespace Cone\Root\Traits;

use Illuminate\Container\Container;

trait InteractsWithProxy
{
    /**
     * The resolved proxy instance.
     *
     * @var self
     */
    protected static self $proxy;

    /**
     * Get the proxied interface.
     *
     * @return string
     */
    abstract public static function getProxiedInterface(): string;

    /**
     * Resolve and get the proxy instance.
     *
     * @return self
     */
    public static function proxy(): self
    {
        if (! isset(static::$proxy)) {
            static::$proxy = Container::getInstance()->make(
                static::getProxiedInterface()
            );
        }

        return static::$proxy;
    }

    /**
     * Get the proxied class.
     *
     * @return string
     */
    public static function getProxiedClass(): string
    {
        return get_class(static::proxy());
    }
}
