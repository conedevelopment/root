<?php

declare(strict_types=1);

namespace Cone\Root\Tests\Fields;

use Cone\Root\Fields\Meta;
use Cone\Root\Fields\Number;
use Cone\Root\Fields\Text;
use Cone\Root\Tests\TestCase;
use Cone\Root\Tests\User;

class MetaTest extends TestCase
{
    protected Meta $field;

    protected function setUp(): void
    {
        parent::setUp();

        $this->field = Meta::make('Price');
    }

    public function test_a_meta_field_has_relation_name(): void
    {
        $this->assertSame('__root_price', $this->field->getRelationName());
    }

    public function test_a_meta_filed_has_different_types(): void
    {
        $this->assertInstanceOf(Text::class, $this->field->getField());

        $this->field->as(Number::class);
        $this->assertInstanceOf(Number::class, $this->field->getField());
    }

    public function test_a_meta_field_has_options(): void
    {
        $model = new User;

        $this->assertSame(
            [], $this->field->resolveOptions($this->app['request'], $model)
        );
    }

    public function test_a_meta_field_hydates_model(): void
    {
        $model = new User;

        $this->assertFalse($model->relationLoaded('__root_price'));

        $this->field->resolveHydrate($this->app['request'], $model, 100);

        $this->assertSame(100, $model->getRelation('__root_price')->value);
    }

    public function test_a_meta_field_has_display_representation(): void
    {
        $model = new User;

        $this->assertSame(
            $this->field->getField()->toDisplay($this->app['request'], $model),
            $this->field->toDisplay($this->app['request'], $model),
        );
    }

    public function test_a_meta_field_has_input_representation(): void
    {
        $model = new User;

        $this->assertSame(
            json_encode($this->field->getField()->toInput($this->app['request'], $model)),
            json_encode($this->field->toInput($this->app['request'], $model)),
        );
    }
}
