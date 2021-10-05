<?php

namespace Cone\Root\Interfaces\Registries;

interface ResourceRegistry
{
    /**
     * Register all of the resources in the given directory.
     *
     * @param  array|string  $paths
     * @return void
     */
    public function discover(array|string $paths): void;
}
