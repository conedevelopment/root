<?php

namespace Cone\Root\Traits;

trait Makeable
{
    /**
     * Make a new object instance.
     */
    public static function make(...$parameters): static
    {
        return new static(...$parameters);
    }
}
