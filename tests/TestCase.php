<?php

namespace Cone\Root\Tests;

use Cone\Root\Form\Fields\Text;
use Cone\Root\Models\User;
use Cone\Root\Resources\Resource;
use Cone\Root\Root;
use Cone\Root\Tests\Actions\PublishPosts;
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

    protected $admin;

    protected $request;

    protected $resource;

    public function setUp(): void
    {
        parent::setUp();

        $this->setUpResource();

        $this->app['router']->getRoutes()->refreshNameLookups();

        $this->withoutVite();

        Storage::fake('local');
        Storage::fake('public');

        Storage::disk('local')->makeDirectory('root-chunks');

        $this->admin = User::factory()->create();

        Gate::policy(Post::class, ModelPolicy::class);
        Gate::policy(Author::class, ModelPolicy::class);

        $this->app['request']->setUserResolver(function () {
            return $this->admin;
        });
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

        $this->app->make(Root::class)->resources->register($this->resource);
        $this->resource->boot($this->app->make(Root::class));
    }

    protected function beforeRefreshingDatabase()
    {
        $path = $this->app->databasePath('migrations');

        foreach ($this->app['files']->files($path) as $file) {
            if (str_contains($file->getFileName(), 'create_notifications_table')) {
                return;
            }
        }

        $this->artisan('notifications:table');
    }
}
