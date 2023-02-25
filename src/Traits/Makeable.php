<?php

namespace Cone\Root\Traits;

trait Makeable
{
    /**
     * Make a new object instance.
     *
     * @param  array  ...$parameters
     */
    public static function make(...$parameters): static
    {
        return new static(...$parameters);
    }
}
