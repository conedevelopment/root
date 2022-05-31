<?php

namespace Cone\Root\Tests;

use Cone\Root\Http\Requests\RootRequest;
use Cone\Root\Models\User;
use Cone\Root\Support\Facades\Resource;
use Cone\Root\Tests\CreatesApplication;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use Illuminate\Support\Facades\Storage;

abstract class TestCase extends BaseTestCase
{
    use CreatesApplication;
    use RefreshDatabase;

    protected $admin, $request;

    public function setUp(): void
    {
        parent::setUp();

        Resource::register('posts', new PostResource(Post::class));

        $this->app['router']->getRoutes()->refreshNameLookups();

        $this->withoutMix();

        $this->request = RootRequest::createFrom($this->app['request']);

        Storage::fake('local');
        Storage::fake('public');

        Storage::disk('local')->makeDirectory('chunks');

        $this->admin = User::factory()->create();
    }
}
