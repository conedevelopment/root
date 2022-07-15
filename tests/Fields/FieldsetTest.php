<?php

namespace Cone\Root\Tests\Fields;

use Cone\Root\Fields\Fieldset;
use Cone\Root\Fields\Number;
use Cone\Root\Support\Collections\Fields;
use Cone\Root\Tests\Post;
use Cone\Root\Tests\TestCase;

class FieldsetTest extends TestCase
{
    protected $field;

    public function setUp(): void
    {
        parent::setUp();

        $this->field = new Fieldset('Inventory');
    }

    /** @test */
    public function a_fieldset_field_has_json_component()
    {
        $this->assertSame('Fieldset', $this->field->getComponent());
    }

    /** @test */
    public function a_fieldset_field_resolves_fields()
    {
        $this->field->withFields([
            Number::make('Quantity'),
        ]);

        $this->assertSame(
            Fields::make(array_merge($this->field->fields($this->request), [Number::make('Quantity')]))->toArray(),
            $this->field->resolveFields($this->request)->toArray()
        );
    }

    /** @test */
    public function a_fieldset_field_registers_routes()
    {
        $this->app['router']->prefix('posts/fields')->group(function ($router) {
            $this->field->registerRoutes($this->request, $router);
        });

        $this->assertSame('posts/fields/inventory', $this->field->getUri());
    }

    /** @test */
    public function a_fieldset_field_has_custom_input_representation()
    {
        $model = new Post(['quantity' => 10]);

        $field = Number::make('Quantity');

        $this->field->withFields([$field]);

        $this->assertSame([
            'label' => 'Inventory',
            'name' => 'inventory',
            'id' => 'inventory',
            'component' => 'Fieldset',
            'formatted_value' => null,
            'value' => null,
            'fields' => [$field->toInput($this->request, $model)],
        ], $this->field->toInput($this->request, $model));
    }

    /** @test */
    public function a_fieldset_field_has_custom_validation()
    {
        $model = new Post();

        $field = Number::make('Quantity')->rules(['required']);

        $this->field->withFields([$field]);

        $this->assertSame(
            ['quantity' => ['required']],
            $this->field->toValidate($this->request, $model)
        );
    }
}
