<?php

namespace Cone\Root\Concerns;

use Illuminate\Container\Container;

trait InteractsWithProxy
{
    /**
     * The resolve proxy instance.
     *
     * @var self
     */
    protected static self $proxy;

    /**
     * Get the proxied contract.
     *
     * @return string
     */
    abstract public static function getProxiedContract(): string;

    /**
     * Resolve and get the proxy instance.
     *
     * @return self
     */
    public static function proxy(): self
    {
        if (! isset(static::$proxy)) {
            static::$proxy = Container::getInstance()->make(
                static::getProxiedContract()
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
