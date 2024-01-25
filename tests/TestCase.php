<?php

namespace Cone\Root\Tests;

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

        $this->startSession();

        $this->app['request']->setLaravelSession($this->app['session']);

        $this->withoutVite();

        Storage::fake('local');
        Storage::fake('public');

        Storage::disk('local')->makeDirectory('root-tmp');

        $this->app['config']->set('root.media.tmp_dir', Storage::disk('local')->path('root-tmp'));
        $this->app['config']->set('root.media.chunk_expiration', 0);
    }
}
