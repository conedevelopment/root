<?php

namespace Cone\Root\Interfaces\Options;

use Cone\Root\Options\Group;

interface Registry
{
    /**
     * Get or create a new group.
     */
    public function group(string $name): Group;

    /**
     * Get the option groups.
     */
    public function groups(): array;
}
