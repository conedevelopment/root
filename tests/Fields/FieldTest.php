<?php

namespace Cone\Root\Tests\Fields;

use Cone\Root\Fields\Text;
use Cone\Root\Http\Requests\CreateRequest;
use Cone\Root\Http\Requests\UpdateRequest;
use Cone\Root\Models\User;
use Cone\Root\Tests\TestCase;

class FieldTest extends TestCase
{
    protected $field, $model;

    public function setUp(): void
    {
        parent::setUp();

        $this->field = new Text('Name');

        $this->model = User::factory()->make();
    }

    /** @test */
    public function a_field_gets_attributes()
    {
        $this->assertSame('name', $this->field->name);
        $this->assertSame('name', $this->field->getAttribute('name'));

        $this->assertSame(
            ['label' => 'Name', 'name' => 'name', 'id' => 'name', 'type' => 'text'],
            $this->field->getAttributes()
        );
    }

    /** @test */
    public function a_field_checks_attributes()
    {
        $this->assertTrue(isset($this->field->name));
        $this->assertTrue($this->field->hasAttribute('name'));

        $this->assertFalse(isset($this->field->foo));
        $this->assertFalse($this->field->hasAttribute('foo'));
    }

    /** @test */
    public function a_field_sets_attributes()
    {
        $this->assertFalse($this->field->hasAttribute('min'));
        $this->assertFalse($this->field->hasAttribute('max'));

        $this->field->min = 10;
        $this->field->setAttribute('max', 30);

        $this->assertTrue($this->field->hasAttribute('min'));
        $this->assertTrue($this->field->hasAttribute('max'));

        $this->assertSame(10, $this->field->getAttribute('min'));
        $this->assertSame(30, $this->field->getAttribute('max'));
    }

    /** @test */
    public function a_feild_removes_attributes()
    {
        $this->field->setAttribute('max', 30);

        $this->assertTrue($this->field->hasAttribute('max'));

        $this->field->removeAttribute('max');

        $this->assertFalse($this->field->hasAttribute('max'));
    }

    /** @test */
    public function a_field_clears_attributes()
    {
        $this->assertNotEmpty($this->field->getAttributes());

        $this->field->clearAttributes();

        $this->assertEmpty($this->field->getAttributes());
    }

    /** @test */
    public function a_field_has_label_attribute()
    {
        $this->field->label('Test');

        $this->assertSame('Test', $this->field->label);
    }

    /** @test */
    public function a_field_has_name_attribute()
    {
        $this->field->name('test');

        $this->assertSame('test', $this->field->name);
    }

    /** @test */
    public function a_field_has_id_attribute()
    {
        $this->field->id('test');

        $this->assertSame('test', $this->field->id);
    }

    /** @test */
    public function a_field_has_readonly_attribute()
    {
        $this->field->readonly();

        $this->assertTrue($this->field->readonly);

        $this->field->readonly(false);

        $this->assertFalse($this->field->readonly);
    }

    /** @test */
    public function a_field_has_disabled_attribute()
    {
        $this->field->disabled();

        $this->assertTrue($this->field->disabled);

        $this->field->disabled(false);

        $this->assertFalse($this->field->disabled);
    }

    /** @test */
    public function a_field_has_required_attribute()
    {
        $this->field->required();

        $this->assertTrue($this->field->required);

        $this->field->required(false);

        $this->assertFalse($this->field->required);
    }

    /** @test */
    public function a_field_has_type_attribute()
    {
        $this->assertSame('text', $this->field->type);
    }

    /** @test */
    public function a_field_has_placeholder_attribute()
    {
        $this->assertNull($this->field->placeholder);

        $this->field->placeholder('Root');

        $this->assertSame('Root', $this->field->placeholder);
    }

    /** @test */
    public function a_field_has_value()
    {
        $this->assertSame(
            $this->model->name,
            $this->field->resolveValue($this->request, $this->model)
        );

        $this->field->value(function ($request, $model, $value) {
            return '__fake__';
        });

        $this->assertSame(
            '__fake__',
            $this->field->resolveValue($this->request, $this->model)
        );
    }

    /** @test */
    public function a_field_has_formatted_value()
    {
        $this->assertSame(
            $this->model->name,
            $this->field->resolveFormat($this->request, $this->model)
        );

        $this->field->format(function ($request, $model, $value) {
            return strtoupper($value);
        });

        $this->assertSame(
            strtoupper($this->model->name),
            $this->field->resolveFormat($this->request, $this->model)
        );
    }

    /** @test */
    public function a_field_hydrates_model()
    {
        $this->assertNotSame('Root User', $this->model->name);

        $this->field->hydrate(
            $this->request, $this->model, 'Root User'
        );

        $this->model->save();

        $this->assertSame('Root User', $this->model->name);
    }

    /** @test */
    public function a_field_has_validation_rules()
    {
        $this->field->rules(['required'])
                    ->createRules(['unique:users'])
                    ->updateRules(['unique:users,1']);

        $this->assertSame(
            [$this->field->name => ['required']],
            $this->field->toValidate($this->request, $this->model)
        );

        $this->assertSame(
            [$this->field->name => ['required', 'unique:users']],
            $this->field->toValidate(new CreateRequest(), $this->model)
        );

        $this->assertSame(
            [$this->field->name => ['required', 'unique:users,1']],
            $this->field->toValidate(new UpdateRequest(), $this->model)
        );
    }

    /** @test */
    public function a_field_has_display_representation()
    {
        $this->assertSame(
            array_merge($this->field->getAttributes(), [
                'formatted_value' => $this->model->name,
                'searchable' => $this->field->isSearchable($this->request),
                'sortable' => $this->field->isSortable($this->request),
                'value' => $this->model->name,
            ]),
            $this->field->toDisplay($this->request, $this->model)
        );
    }

    /** @test */
    public function a_field_has_input_representation()
    {
        $this->assertSame(
            array_merge($this->field->getAttributes(), [
                'component' => $this->field->getComponent(),
                'formatted_value' => $this->model->name,
                'value' => $this->model->name,
            ]),
            $this->field->toInput($this->request, $this->model)
        );
    }

    /** @test */
    public function a_field_can_be_searchable()
    {
        $this->assertFalse($this->field->isSearchable($this->request));

        $this->field->searchable();

        $this->assertTrue($this->field->isSearchable($this->request));
    }

    /** @test */
    public function a_field_can_be_sortable()
    {
        $this->assertFalse($this->field->isSortable($this->request));

        $this->field->sortable();

        $this->assertTrue($this->field->isSortable($this->request));
    }
}
