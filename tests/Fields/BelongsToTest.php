<?php

namespace Cone\Root\Tests\Fields;

use Cone\Root\Fields\BelongsTo;
use Cone\Root\Tests\Author;
use Cone\Root\Tests\Post;
use Cone\Root\Tests\TestCase;

class BelongsToTest extends TestCase
{
    protected $field;

    public function setUp(): void
    {
        parent::setUp();

        $this->field = new BelongsTo('Author');
    }

    /** @test */
    public function a_belongs_to_field_has_defaults()
    {
        $this->assertSame('Select', $this->field->getComponent());
    }

    /** @test */
    public function a_belongs_to_field_has_options()
    {
        $post = new Post();

        $this->assertSame(
            Author::query()->get()->pluck('id', 'id')->toArray(),
            $this->field->resolveOptions($this->app['request'], $post)
        );

        $this->field->display('name');

        $this->assertSame(
            Author::query()->get()->pluck('name', 'id')->toArray(),
            $this->field->resolveOptions($this->app['request'], $post)
        );
    }
}
