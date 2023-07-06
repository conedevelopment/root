<?php

namespace Cone\Root\Navigation;

class Location
{
    use HasItems;

    /**
     * The locaiton name.
     */
    protected string $name;

    /**
     * Create a new location instance.
     */
    public function __construct(string $name)
    {
        $this->name = $name;
    }
}
