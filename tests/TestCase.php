<?php

namespace Cone\Root\Tests;

use Cone\Root\Interfaces\Models\User as UserInterface;
use Cone\Root\Root;
use Cone\Root\RootApplicationServiceProvider;
use Cone\Root\RootServiceProvider;
use Cone\Root\Tests\Resources\UserResource;
use Illuminate\Contracts\Console\Kernel;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use Illuminate\Support\Facades\Storage;

abstract class TestCase extends BaseTestCase
{
    use LazilyRefreshDatabase;

    public function createApplication(): Application
    {
        $app = require dirname(__DIR__).'/vendor/laravel/laravel/bootstrap/app.php';

        $app->booting(static function () use ($app): void {
            $app->register(RootServiceProvider::class);
            $app->register(RootApplicationServiceProvider::class);

            $app->bind(UserInterface::class, User::class);

            Root::instance()->resources->register(new UserResource());
        });

        $app->make(Kernel::class)->bootstrap();

        return $app;
    }

    public function setUp(): void
    {
        parent::setUp();

        $this->app['router']->getRoutes()->refreshNameLookups();

        $this->startSession();

        $this->app['request']->setLaravelSession($this->app['session']);

        $this->withoutVite();

        Storage::fake('local');
        Storage::fake('public');

        Storage::disk('local')->makeDirectory('root-tmp');

        $this->app['config']->set('root.media.tmp_dir', Storage::disk('local')->path('root-tmp'));
        $this->app['config']->set('root.media.chunk_expiration', 0);
        $this->app['config']->set('auth.providers.users.model', User::class);
    }
}
