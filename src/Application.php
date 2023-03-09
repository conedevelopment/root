<?php

namespace Cone\Root;

use Cone\Root\Support\Collections\Resources;
use Illuminate\Contracts\Foundation\Application as Container;

class Application
{
    public const VERSION = '1.2.0';

    /**
     * The Laravel application instance.
     */
    protected Container $app;

    /**
     * The resources collection.
     */
    public readonly Resources $resources;

    /**
     * Create a new Root application instance.
     */
    public function __construct(Container $app)
    {
        $this->app = $app;
        $this->resources = new Resources();
    }

    /**
     * Boot the application.
     */
    public function boot(): void
    {
        //
    }
}
