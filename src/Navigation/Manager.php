<?php

namespace Cone\Root\Navigation;

use Cone\Root\Interfaces\Navigation\Manager as Contract;

class Manager implements Contract
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
        if (! array_key_exists($name, $this->locations)) {
            $this->locations[$name] = new Location($name);
        }

        return $this->locations[$name];
    }
}
