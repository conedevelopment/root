<?php

namespace Cone\Root\Tests\Fields;

use Cone\Root\Fields\Json;
use Cone\Root\Fields\Number;
use Cone\Root\Models\TemporaryJson;
use Cone\Root\Support\Collections\Fields;
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
    public function a_json_field_has_json_component()
    {
        $this->assertSame('Json', $this->field->getComponent());
    }

    /** @test */
    public function a_json_field_resolves_fields()
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
    public function a_json_field_registers_routes()
    {
        $this->app['router']->prefix('posts/fields')->group(function ($router) {
            $this->field->registerRoutes($this->request, $router);
        });

        $this->assertSame('posts/fields/inventory', $this->field->getUri());
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
            'component' => 'Json',
            'formatted_value' => $data,
            'value' => $data,
            'fields' => [$field->toInput($this->request, TemporaryJson::make()->forceFill($data))],
            'with_legend' => true,
        ], $this->field->toInput($this->request, $model));
    }
}
