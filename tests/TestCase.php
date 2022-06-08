<?php

namespace Cone\Root\Tests;

use Cone\Root\Fields\Text;
use Cone\Root\Http\Requests\RootRequest;
use Cone\Root\Models\User;
use Cone\Root\Resources\Resource;
use Cone\Root\Support\Facades\Resource as ResourceRegistry;
use Cone\Root\Tests\Actions\PublishPosts;
use Cone\Root\Tests\CreatesApplication;
use Cone\Root\Tests\Extracts\LongPosts;
use Cone\Root\Tests\Filters\Published;
use Cone\Root\Tests\Widgets\PostsCount;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Storage;

abstract class TestCase extends BaseTestCase
{
    use CreatesApplication;
    use RefreshDatabase;

    protected $admin, $request, $resource;

    public function setUp(): void
    {
        parent::setUp();

        $this->setUpResource();

        $this->app['router']->getRoutes()->refreshNameLookups();

        $this->withoutMix();

        $this->request = RootRequest::createFrom($this->app['request']);

        Storage::fake('local');
        Storage::fake('public');

        Storage::disk('local')->makeDirectory('chunks');

        $this->admin = User::factory()->create();

        Gate::policy(Post::class, ModelPolicy::class);
        Gate::policy(Author::class, ModelPolicy::class);
    }

    protected function setUpResource()
    {
        $this->resource = (new Resource(Post::class))
                            ->with(['author'])
                            ->withFields([Text::make('Title')->sortable()->searchable()])
                            ->withFilters([Published::make()])
                            ->withActions([PublishPosts::make()])
                            ->withExtracts([LongPosts::make()])
                            ->withWidgets([PostsCount::make()]);

        ResourceRegistry::register('posts', $this->resource);
    }
}
