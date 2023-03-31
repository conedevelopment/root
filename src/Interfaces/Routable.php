<?php

namespace Cone\Root\Interfaces;

use Illuminate\Routing\Router;

interface Routable
{
    /**
     * Register the routes using the given router.
     */
    public function registerRoutes(Router $router): void;
}
