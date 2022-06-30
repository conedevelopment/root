<?php

namespace Cone\Root\Tests\Fields;

use Cone\Root\Fields\Tag;
use Cone\Root\Tests\Post;
use Cone\Root\Tests\TestCase;

class TagTest extends TestCase
{
    protected $field;

    public function setUp(): void
    {
        parent::setUp();

        $this->field = new Tag('Labels');
    }

    /** @test */
    public function a_tag_field_has_select_component()
    {
        $this->assertSame('Tag', $this->field->getComponent());
    }

    /** @test */
    public function a_tag_field_custom_format()
    {
        $model = new Post();

        $model->setAttribute('labels', ['Root', 'Bazar']);

        $this->assertSame(
            'Root, Bazar',
            $this->field->resolveFormat($this->request, $model)
        );
    }
}
