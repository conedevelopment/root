<?php

namespace Cone\Root\Tests\Fields;

use Cone\Root\Fields\HasMany;
use Cone\Root\Tests\Author;
use Cone\Root\Tests\Post;
use Cone\Root\Tests\TestCase;

class HasManyTest extends TestCase
{
    protected $field;

    public function setUp(): void
    {
        parent::setUp();

        $this->field = new HasMany('Posts');
    }

    /** @test */
    public function a_has_many_field_has_defaults()
    {
        $this->assertSame('Select', $this->field->getComponent());
    }

    /** @test */
    public function a_has_many_field_has_options()
    {
        $post = new Author();

        $this->assertSame(
            Post::query()->get()->pluck('id', 'id')->toArray(),
            $this->field->resolveOptions($this->app['request'], $post)
        );

        $this->field->display('title');

        $this->assertSame(
            Post::query()->get()->pluck('title', 'id')->toArray(),
            $this->field->resolveOptions($this->app['request'], $post)
        );
    }
}
