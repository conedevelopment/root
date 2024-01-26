<?php

namespace Cone\Root\Tests;

use Cone\Root\Interfaces\Models\User as UserInterface;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use Illuminate\Support\Facades\Storage;

abstract class TestCase extends BaseTestCase
{
    use CreatesApplication;
    use LazilyRefreshDatabase;

    public function setUp(): void
    {
        parent::setUp();

        $this->app['router']->getRoutes()->refreshNameLookups();

        $this->app->bind(UserInterface::class, User::class);

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
