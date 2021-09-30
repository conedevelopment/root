<?php

namespace Cone\Root\Tests\Feature;

use Cone\Root\Jobs\MoveFile;
use Cone\Root\Jobs\PerformConversions;
use Cone\Root\Models\Medium;
use Cone\Root\Tests\TestCase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Queue;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\URL;

class MediaTest extends TestCase
{
    protected $medium;

    public function setUp(): void
    {
        parent::setUp();

        $this->withHeaders([
            'Accept' => 'application/json',
            'X-Requested-With' => 'XMLHttpRequest',
        ]);

        Queue::fake();

        $this->medium = Medium::factory()->create();

        Storage::disk('public')->put($this->medium->path(), 'fake content');
    }

    /** @test */
    public function an_admin_can_index_media()
    {
        $this->actingAs($this->admin)
            ->get(URL::route('root.media.index'))
            ->assertOk()
            ->assertJson(Medium::query()->latest()->cursorPaginate()->toArray());
    }

    /** @test */
    public function an_admin_can_show_medium()
    {
        $this->actingAs($this->admin)
            ->get(URL::route('root.media.show', $this->medium))
            ->assertOk()
            ->assertJson($this->medium->toArray());
    }

    /** @test */
    public function an_admin_can_store_medium_as_image()
    {
        $this->actingAs($this->admin)
            ->post(URL::route('root.media.store'), [
                'file' => UploadedFile::fake()->image('test.png.chunk'),
            ])
            ->assertCreated()
            ->assertJson(['name' => 'test']);

        $this->assertDatabaseHas('root_media', ['name' => 'test']);

        Queue::assertPushedWithChain(MoveFile::class, [
            PerformConversions::class,
        ]);
    }

    /** @test */
    public function an_admin_can_store_medium_as_file()
    {
        $this->actingAs($this->admin)
            ->post(URL::route('root.media.store'), [
                'file' => UploadedFile::fake()->create('test.pdf.chunk'),
            ])
            ->assertCreated()
            ->assertJson(['name' => 'test']);

        $this->assertDatabaseHas('root_media', ['name' => 'test']);
    }

    /** @test */
    public function an_admin_can_update_medium()
    {
        $this->actingAs($this->admin)
            ->patch(URL::route('root.media.update', $this->medium), [])
            ->assertOk()
            ->assertExactJson(['updated' => true]);
    }

    /** @test */
    public function an_admin_can_destroy_medium()
    {
        Storage::disk('public')->assertExists($this->medium->path());

        $this->actingAs($this->admin)
            ->delete(URL::route('root.media.destroy', $this->medium))
            ->assertOk()
            ->assertExactJson(['deleted' => true]);

        Storage::disk('public')->assertMissing($this->medium->path());

        $this->assertDatabaseMissing('root_media', ['id' => $this->medium->id]);
    }
}
