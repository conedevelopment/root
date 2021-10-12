<?php

namespace Cone\Root\Traits;

use Illuminate\Container\Container;
use Throwable;

trait InteractsWithProxy
{
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
        static $proxy;

        if (! isset($proxy)) {
            try {
                $proxy = Container::getInstance()->make(
                    static::getProxiedInterface()
                );
            } catch (Throwable) {
                $proxy = new static();
            }
        }

        return $proxy;
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
