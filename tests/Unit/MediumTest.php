<?php

namespace Cone\Root\Tests\Unit;

use Cone\Root\Models\Medium;
use Cone\Root\Tests\TestCase;

class MediumTest extends TestCase
{
    protected $medium;

    public function setUp(): void
    {
        parent::setUp();

        $this->medium = Medium::factory()->create();
    }

    /** @test */
    public function a_medium_belongs_to_a_user()
    {
        $this->medium->user()->associate($this->admin)->save();

        $this->assertSame($this->admin->id, $this->medium->user_id);
    }

    /** @test */
    public function a_medium_can_determine_if_image()
    {
        $this->medium->update(['mime_type' => 'image/jpg']);
        $this->assertTrue($this->medium->isImage);

        $this->medium->update(['mime_type' => 'application/pdf']);
        $this->assertFalse($this->medium->isImage);
    }

    /** @test */
    public function a_medium_has_urls()
    {
        $this->assertEquals(['original'], array_keys($this->medium->urls));

        $this->assertStringContainsString('-thumb', $this->medium->getUrl('thumb'));
    }

    /** @test */
    public function a_medium_has_path()
    {
        $this->assertStringContainsString("{$this->medium->id}/{$this->medium->name}", $this->medium->getPath());
        $this->assertStringContainsString("{$this->medium->id}/{$this->medium->name}", $this->medium->getAbsolutePath());
    }

    /** @test */
    public function a_medium_has_query_scopes()
    {
        $query = Medium::query()->search('test');
        $this->assertSame(
            'select * from "root_media" where "root_media"."name" like ?',
            $query->toSql()
        );
        $this->assertSame(['%test%'], $query->getBindings());

        $query = Medium::query()->type('file');
        $this->assertSame(
            'select * from "root_media" where "root_media"."mime_type" not like ?',
            $query->toSql()
        );
        $this->assertSame(['image%'], $query->getBindings());

        $query = Medium::query()->type('image');
        $this->assertSame(
            'select * from "root_media" where "root_media"."mime_type" like ?',
            $query->toSql()
        );
        $this->assertSame(['image%'], $query->getBindings());
    }
}
