<?php

namespace Cone\Root\Tests\Fields;

use Cone\Root\Fields\Select;
use Cone\Root\Tests\TestCase;
use Cone\Root\Tests\User;

class SelectTest extends TestCase
{
    protected Select $field;

    public function setUp(): void
    {
        parent::setUp();

        $this->field = new Select('Permissions');
    }

    public function test_a_select_field_has_select_template(): void
    {
        $this->assertSame('root::fields.select', $this->field->getTemplate());
    }

    public function test_a_select_field_can_be_nullable(): void
    {
        $this->assertFalse($this->field->isNullable());

        $this->field->nullable();

        $this->assertTrue($this->field->isNullable());

        $this->field->nullable(false);

        $this->assertFalse($this->field->isNullable());
    }

    public function test_a_select_field_has_multiple_attribute(): void
    {
        $this->assertNull($this->field->getAttribute('multiple'));

        $this->field->multiple();

        $this->assertTrue($this->field->getAttribute('multiple'));

        $this->field->multiple(false);

        $this->assertFalse($this->field->getAttribute('multiple'));
    }

    public function test_a_select_field_has_size_attribute(): void
    {
        $this->assertNull($this->field->getAttribute('multiple'));

        $this->field->size(10);

        $this->assertSame(10, $this->field->getAttribute('size'));
    }

    public function test_a_select_field_has_options(): void
    {
        $model = new User;

        $this->assertEmpty($this->field->resolveOptions($this->app->request, $model));

        $this->field->options([
            'edit' => 'Edit',
            'create' => 'Create',
        ]);

        $this->assertSame(
            ['edit', 'create'],
            array_column($this->field->resolveOptions($this->app->request, $model), 'value')
        );

        $this->field->options(function () {
            return [
                'update' => 'Update',
                'delete' => 'Delete',
            ];
        });

        $this->assertSame(
            ['update', 'delete'],
            array_column($this->field->resolveOptions($this->app->request, $model), 'value')
        );
    }

    public function test_a_select_field_resolves_format(): void
    {
        $model = new User;

        $this->field->options([
            'edit' => 'Edit',
            'create' => 'Create',
            'update' => 'Update',
        ]);

        $model->forceFill(['permissions' => ['edit', 'create']]);

        $this->assertSame(
            'Edit, Create',
            $this->field->resolveFormat($this->app['request'], $model)
        );
    }

    public function test_a_select_field_has_input_representation(): void
    {
        $model = new User;

        $this->assertEmpty(
            array_diff(
                ['nullable' => false],
                $this->field->toInput($this->app['request'], $model)
            )
        );
    }
}
