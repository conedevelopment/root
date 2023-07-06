<?php

namespace Cone\Root\Navigation;

class Manager
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
        if (! array_key_exists($name, $this->locations[$name])) {
            $this->locations[$name] = new Location($name);
        }

        return $this->locations[$name];
    }
}
