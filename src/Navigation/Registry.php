<?php

namespace Cone\Root\Navigation;

class Registry
{
    /**
     * The navigation locations.
     */
    protected array $locations = [];

    /**
     * Get or register a new the location.
     */
    public function location(string $name): Location
    {
        return $this->locations[$name] ??= new Location($name);
    }

    /**
     * Get the registered. locations
     */
    public function locations(): array
    {
        return $this->locations;
    }
}
