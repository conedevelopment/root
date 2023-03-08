<?php

namespace Cone\Root;

use Illuminate\Contracts\Foundation\Application as Container;

class Application
{
    /**
     * The Laravel application instance.
     */
    protected Container $app;

    /**
     * Create a new Root application instance.
     */
    public function __construct(Container $app)
    {
        $this->app = $app;
    }
}
