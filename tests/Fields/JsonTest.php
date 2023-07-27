<?php

namespace Cone\Root\Tests\Fields;

use Cone\Root\Form\Fields;
use Cone\Root\Form\Fields\Json;
use Cone\Root\Form\Fields\Number;
use Cone\Root\Models\FieldsetModel;
use Cone\Root\Tests\Post;
use Cone\Root\Tests\TestCase;

class JsonTest extends TestCase
{
    protected $field;

    public function setUp(): void
    {
        parent::setUp();

        $this->field = new Json('Inventory');
    }

    /** @test */
    public function a_json_field_has_fieldset_component()
    {
        $this->assertSame('Fieldset', $this->field->getComponent());
    }

    /** @test */
    public function a_json_field_resolves_fields()
    {
        $this->field->withFields([
            Number::make('Quantity'),
        ]);

        $this->assertSame(
            Fields::make(array_merge(
                $this->field->fields($this->app['request']),
                [Number::make('Quantity')]
            ))->toArray(),
            $this->field->resolveFields($this->app['request'])->toArray()
        );
    }

    /** @test */
    public function a_json_field_registers_routes()
    {
        $this->app['router']->prefix('posts/fields')->group(function ($router) {
            $this->field->registerRoutes($router);
        });

        $this->assertSame('/posts/fields/inventory', $this->field->getUri());
    }

    /** @test */
    public function a_json_field_has_custom_input_representation()
    {
        $model = (new Post())->forceFill([
            'inventory' => $data = ['quantity' => 10],
        ]);

        $field = Number::make('Quantity');

        $this->field->withFields([$field]);

        $this->assertSame([
            'label' => 'Inventory',
            'name' => 'inventory',
            'id' => 'inventory',
            'component' => 'Fieldset',
            'formattedValue' => $data,
            'help' => null,
            'value' => $data,
            'fields' => [$field->toInput($this->app['request'], FieldsetModel::make()->forceFill($data))],
        ], $this->field->toInput($this->app['request'], $model));
    }

    /** @test */
    public function a_json_field_has_custom_validation()
    {
        $model = new Post();

        $field = Number::make('Quantity')->rules(['required']);

        $this->field->withFields([$field]);

        $this->assertSame(
            ['inventory' => [], 'inventory.quantity' => ['required']],
            $this->field->toValidate($this->app['request'], $model)
        );
    }
}
