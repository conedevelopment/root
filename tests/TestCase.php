<?php

namespace Cone\Root\Tests;

use Cone\Root\Interfaces\Models\User as UserInterface;
use Cone\Root\Root;
use Cone\Root\Tests\Resources\UserResource;
use Illuminate\Contracts\Console\Kernel;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use Illuminate\Support\Facades\Storage;

abstract class TestCase extends BaseTestCase
{
    use DatabaseMigrations;

    public function createApplication(): Application
    {
        $app = require __DIR__.'/app.php';

        $app->booting(static function () use ($app): void {
            $app->afterResolving('migrator', function ($migrator) {
                $migrator->path(__DIR__.'/migrations');
            });

            $app->bind(UserInterface::class, User::class);

            Root::instance()->resources->register([
                new UserResource,
            ]);
        });

        $app->make(Kernel::class)->bootstrap();

        return $app;
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this->app['router']->getRoutes()->refreshNameLookups();

        $this->startSession();

        $this->app['request']->setLaravelSession(
            $this->app['session']->driver()
        );

        $this->withoutVite();

        Storage::fake('local');
        Storage::fake('public');

        Storage::disk('local')->makeDirectory('root-tmp');

        $this->app['config']->set('root.media.tmp_dir', Storage::disk('local')->path('root-tmp'));
        $this->app['config']->set('root.media.chunk_expiration', 0);
        $this->app['config']->set('auth.providers.users.model', User::class);
    }
}
