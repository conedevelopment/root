<?php

namespace Cone\Root\Navigation;

class Registry
{
    /**
     * The navigation locations.
     */
    protected array $locations = [];

    /**
     * Get the location.
     */
    public function location(string $name): Location
    {
        return $this->locations[$name] ??= new Location($name);
    }
}
