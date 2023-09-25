<?php

namespace Cone\Root\Interfaces\Navigation;

use Cone\Root\Navigation\Location;

interface Manager
{
    /**
     * Get the location.
     */
    public function location(string $name): Location;
}
