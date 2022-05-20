<?php

namespace Cone\Root\Traits;

trait Makeable
{
    /**
     * Make a new object instance.
     *
     * @param  array  ...$parameters
     * @return static
     */
    public static function make(...$parameters): static
    {
        return new static(...$parameters);
    }
}
