<?php

namespace Cone\Root\Tests;

use Cone\Root\RootApplicationServiceProvider;
use Cone\Root\RootServiceProvider;
use Illuminate\Contracts\Console\Kernel;
use Illuminate\Foundation\Application;

trait CreatesApplication
{
    /**
     * Creates the application.
     */
    public function createApplication(): Application
    {
        $app = require dirname(__DIR__).'/vendor/laravel/laravel/bootstrap/app.php';

        $app->booting(static function () use ($app): void {
            $app->register(RootServiceProvider::class);
            $app->register(RootApplicationServiceProvider::class);
        });

        $app->make(Kernel::class)->bootstrap();

        return $app;
    }
}
