<?php

namespace Cone\Root\Tests\Fields;

use Cone\Root\Fields\Text;
use Cone\Root\Http\Requests\CreateRequest;
use Cone\Root\Http\Requests\UpdateRequest;
use Cone\Root\Tests\Post;
use Cone\Root\Tests\TestCase;

class FieldTest extends TestCase
{
    protected $field, $model;

    public function setUp(): void
    {
        parent::setUp();

        $this->field = new Text('Title');
    }

    /** @test */
    public function a_field_gets_attributes()
    {
        $this->assertSame('title', $this->field->name);
        $this->assertSame('title', $this->field->getAttribute('name'));

        $this->assertSame(
            ['label' => 'Title', 'name' => 'title', 'id' => 'title', 'type' => 'text'],
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
        $this->field->min = 10;
        $this->field->setAttribute('max', 30);
        $this->field->setAttributes(['disabled' => true]);

        $this->assertSame(10, $this->field->getAttribute('min'));
        $this->assertSame(30, $this->field->getAttribute('max'));
        $this->assertTrue($this->field->getAttribute('disabled'));
    }

    /** @test */
    public function a_field_resolves_attributes()
    {
        $model = new Post();

        $this->field->setAttribute('disabled', function () {
            return false;
        });

        $this->assertFalse($this->field->resolveAttribute($this->request, $model, 'disabled'));
    }

    /** @test */
    public function a_field_removes_attributes()
    {
        $this->field->setAttribute('max', 30);
        $this->field->setAttribute('min', 10);
        $this->field->setAttribute('disabled', true);

        $this->assertTrue($this->field->hasAttribute('max'));
        $this->assertTrue($this->field->hasAttribute('min'));
        $this->assertTrue($this->field->hasAttribute('disabled'));

        $this->field->removeAttribute('max');
        $this->field->removeAttributes(['min']);
        unset($this->field->disabled);

        $this->assertFalse($this->field->hasAttribute('max'));
        $this->assertFalse($this->field->hasAttribute('min'));
        $this->assertFalse($this->field->hasAttribute('disabled'));
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
        $model = new Post(['title' => 'Test Post']);

        $this->assertSame(
            $model->title,
            $this->field->resolveValue($this->request, $model)
        );

        $this->field->value(function ($request, $model, $value) {
            return '__fake__';
        });

        $this->assertSame(
            '__fake__',
            $this->field->resolveValue($this->request, $model)
        );
    }

    /** @test */
    public function a_field_has_formatted_value()
    {
        $model = new Post(['title' => 'Test Post']);

        $this->assertSame(
            'Test Post',
            $this->field->resolveFormat($this->request, $model)
        );

        $this->field->format(function ($request, $model, $value) {
            return strtoupper($value);
        });

        $this->assertSame(
            'TEST POST',
            $this->field->resolveFormat($this->request, $model)
        );
    }

    /** @test */
    public function a_field_persists_model_attribute()
    {
        $model = new Post();

        $this->request->merge(['title' => 'Test']);

        $this->assertNull($model->title);

        $this->field->persist($this->request, $model);

        $model->save();

        $this->assertSame('Test', $model->title);
    }

    /** @test */
    public function a_field_hydrates_model_attribute()
    {
        $model = new Post(['title' => 'Test Post']);

        $this->field->resolveHydrate($this->request, $model, 'Test');

        $this->assertSame('Test', $model->title);

        $this->field->hydrate(function ($request, $model, $value) {
            $model->setAttribute($this->field->name, strtoupper($value));
        });

        $this->field->resolveHydrate($this->request, $model, 'Test');

        $this->assertSame('TEST', $model->title);
    }

    /** @test */
    public function a_field_has_validation_rules()
    {
        $model = new Post();

        $this->field->rules(['required'])
                    ->createRules(['unique:users'])
                    ->updateRules(['unique:users,1']);

        $this->assertSame(
            [$this->field->name => ['required']],
            $this->field->toValidate($this->request, $model)
        );

        $this->assertSame(
            [$this->field->name => ['required', 'unique:users']],
            $this->field->toValidate(new CreateRequest(), $model)
        );

        $this->assertSame(
            [$this->field->name => ['required', 'unique:users,1']],
            $this->field->toValidate(new UpdateRequest(), $model)
        );
    }

    /** @test */
    public function a_field_has_display_representation()
    {
        $model = new Post(['title' => 'Test Post']);

        $this->assertSame(
            array_merge($this->field->getAttributes(), [
                'formatted_value' => $model->title,
                'searchable' => $this->field->isSearchable($this->request),
                'sortable' => $this->field->isSortable($this->request),
                'value' => $model->title,
            ]),
            $this->field->toDisplay($this->request, $model)
        );
    }

    /** @test */
    public function a_field_has_input_representation()
    {
        $model = new Post(['title' => 'Test Post']);

        $this->assertSame(
            array_merge($this->field->getAttributes(), [
                'component' => $this->field->getComponent(),
                'formatted_value' => $model->title,
                'value' => $model->title,
            ]),
            $this->field->toInput($this->request, $model)
        );
    }

    /** @test */
    public function a_field_can_be_searchable()
    {
        $this->assertFalse($this->field->isSearchable($this->request));

        $this->field->searchable();

        $this->assertTrue($this->field->isSearchable($this->request));

        $this->field->searchable(function() {
            return false;
        });

        $this->assertFalse($this->field->isSearchable($this->request));
    }

    /** @test */
    public function a_field_can_be_sortable()
    {
        $this->assertFalse($this->field->isSortable($this->request));

        $this->field->sortable();

        $this->assertTrue($this->field->isSortable($this->request));

        $this->field->sortable(function() {
            return false;
        });

        $this->assertFalse($this->field->isSortable($this->request));
    }
}
