<?php

namespace Cone\Root\Tests\Fields;

use Cone\Root\Fields\BelongsToMany;
use Cone\Root\Http\Requests\CreateRequest;
use Cone\Root\Tests\TestCase;

class BelongsToManyTest extends TestCase
{
    protected $field;

    public function setUp(): void
    {
        parent::setUp();

        $this->field = new BelongsToMany('Tag');
    }

    /** @test */
    public function a_belongs_to_many_field_cannot_be_sortable()
    {
        $this->field->sortable();

        $this->assertFalse($this->field->isSortable($this->request));
    }

    /** @test */
    public function a_belongs_to_field_is_not_visible_on_create_if_it_is_a_subresource()
    {
        $this->assertTrue($this->field->visible($this->request));

        $this->field->asSubresource();

        $request = CreateRequest::createFrom($this->request);

        $this->assertFalse($this->field->visible($request));
    }

    /** @test */
    public function a_belongs_to_many_field_can_be_async()
    {
        $this->field->asSubresource()->async();

        $this->assertTrue($this->field->isAsync());
        $this->assertSame('SubResource', $this->field->getComponent());
    }
}
