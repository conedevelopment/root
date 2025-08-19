<?php

declare(strict_types=1);

namespace Cone\Root\Interfaces\Navigation;

use Cone\Root\Navigation\Location;

interface Registry
{
    /**
     * Get or register a new the location.
     */
    public function location(string $name): Location;

    /**
     * Get the registered. locations
     */
    public function locations(): array;
}
