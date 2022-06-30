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
    public function a_belongs_to_field_has_custom_hydration()
    {
        $model = new Post();

        $value = Author::query()->get()->first();

        $this->field->resolveHydrate($this->request, $model, $value);

        $this->assertSame($value, $model->getRelation('author'));
    }
}
