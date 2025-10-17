<?php

declare(strict_types=1);

namespace Cone\Root\Tests\Models;

use Cone\Root\Models\Medium;
use Cone\Root\Tests\TestCase;
use Cone\Root\Tests\User;

class MediumTest extends TestCase
{
    protected User $user;

    protected Medium $medium;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->create();

        $this->medium = Medium::factory()->create();
    }

    public function test_a_medium_belongs_to_user(): void
    {
        $this->medium->user()->associate($this->user)->save();

        $this->assertTrue($this->medium->user->is($this->user));
    }

    public function test_a_medium_can_determine_if_image(): void
    {
        $this->medium->setAttribute('mime_type', 'image/jpg');
        $this->assertTrue($this->medium->isImage);

        $this->medium->setAttribute('mime_type', 'application/pdf');
        $this->assertFalse($this->medium->isImage);
    }

    public function test_a_medium_has_urls(): void
    {
        $this->assertEquals(['original'], array_keys($this->medium->urls));

        $this->assertStringContainsString('-thumb', $this->medium->getUrl('thumb'));
    }

    public function test_a_medium_has_path(): void
    {
        $this->assertStringContainsString("{$this->medium->uuid}/{$this->medium->name}", $this->medium->getPath());
        $this->assertStringContainsString("{$this->medium->uuid}/{$this->medium->name}", $this->medium->getAbsolutePath());
    }

    public function test_a_medium_has_search_query_scope(): void
    {
        $query = Medium::query()->search('test');
        $this->assertSame(
            'select * from "root_media" where "root_media"."name" like ?',
            $query->toSql()
        );
        $this->assertSame(['%test%'], $query->getBindings());
    }

    public function test_a_medium_has_type_query_scope(): void
    {
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

        $this->assertSame(
            'select * from "root_media"',
            Medium::query()->type('test')->toSql()
        );
    }
}
