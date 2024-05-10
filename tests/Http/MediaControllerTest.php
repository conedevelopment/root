<?php

namespace Cone\Root\Tests\Http;

use Cone\Root\Fields\Media;
use Cone\Root\Jobs\MoveFile;
use Cone\Root\Jobs\PerformConversions;
use Cone\Root\Models\Medium;
use Cone\Root\Root;
use Cone\Root\Tests\TestCase;
use Cone\Root\Tests\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Queue;

class MediaControllerTest extends TestCase
{
    protected User $admin;

    protected Media $field;

    public function setUp(): void
    {
        parent::setUp();

        $this->admin = User::factory()->create();

        $this->field = Root::instance()
            ->resources
            ->resolve('users')
            ->resolveFields($this->app['request'])
            ->first(function ($field) {
                return $field->getModelAttribute() === 'media';
            });
    }

    public function test_a_media_controller_handles_index(): void
    {
        $this->actingAs($this->admin)
            ->get('/root/users/'.$this->admin->getKey().'/fields/media')
            ->assertOk()
            ->assertJson($this->field->paginateRelatable($this->app['request'], $this->admin)->toArray());
    }

    public function test_a_media_controller_handles_store(): void
    {
        Queue::fake();

        $this->actingAs($this->admin)
            ->post(
                '/root/users/'.$this->admin->getKey().'/fields/media',
                ['file' => UploadedFile::fake()->image('test.png')],
                ['X-Chunk-Index' => 1, 'X-Chunk-Total' => 1]
            )
            ->assertCreated()
            ->assertJson(['processing' => false]);

        $this->assertDatabaseHas('root_media', ['name' => 'test']);

        Queue::assertPushedWithChain(MoveFile::class, [PerformConversions::class]);
    }

    public function test_a_media_controller_handles_destroy(): void
    {
        $medium = Medium::factory()->create();

        $this->actingAs($this->admin)
            ->delete('/root/users/'.$this->admin->getKey().'/fields/media', ['ids' => [$medium->getKey()]])
            ->assertOk()
            ->assertJson(['deleted' => [1]]);

        $this->assertDatabaseMissing('root_media', ['id' => $medium->getKey()]);
    }
}
