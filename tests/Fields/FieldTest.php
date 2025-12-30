<?php

declare(strict_types=1);

namespace Cone\Root\Tests\Fields;

use Cone\Root\Fields\Field;
use Cone\Root\Fields\Text;
use Cone\Root\Tests\TestCase;
use Illuminate\Database\Eloquent\Model;

final class FieldTest extends TestCase
{
    protected Field $field;

    protected Model $model;

    protected function setUp(): void
    {
        parent::setUp();

        $this->field = new Text('Title');

        $this->model = new class(['title' => 'Test Model']) extends Model
        {
            protected $guarded = [];

            public function save(array $options = []): bool
            {
                $this->fireModelEvent('saving');

                $this->fireModelEvent('saved');

                return true;
            }
        };
    }

    public function test_a_field_has_attributes(): void
    {
        $this->assertSame('title', $this->field->getAttribute('name'));

        $this->assertTrue($this->field->hasAttribute('name'));
        $this->assertFalse($this->field->hasAttribute('foo'));

        $this->assertSame(
            ['name' => 'title', 'id' => 'title', 'class' => $this->field->classList(), 'type' => 'text'],
            $this->field->getAttributes()
        );

        $this->assertNull($this->field->getAttribute('max'));
        $this->field->setAttributes(['max' => 30]);
        $this->assertSame(30, $this->field->getAttribute('max'));

        $this->field->removeAttributes(['max']);
        $this->assertNull($this->field->getAttribute('max'));

        $this->field->clearAttributes();
        $this->assertEmpty($this->field->getAttributes());
    }

    public function test_a_field_has_value(): void
    {
        $this->assertSame(
            $this->model->title,
            $this->field->resolveValue($this->app['request'], $this->model)
        );

        $this->field->value(fn (): string => '__fake__');

        $this->assertSame(
            '__fake__',
            $this->field->resolveValue($this->app['request'], $this->model)
        );
    }

    public function test_a_field_has_formatted_value(): void
    {
        $this->assertSame(
            'Test Model',
            $this->field->resolveFormat($this->app['request'], $this->model)
        );

        $this->field->format(function ($request, $model, $value): string {
            return strtoupper($value);
        });

        $this->assertSame(
            'TEST MODEL',
            $this->field->resolveFormat($this->app['request'], $this->model)
        );
    }

    public function test_a_field_persists_model_attribute(): void
    {
        $this->app['request']->merge(['title' => 'Persisted']);

        $this->assertSame('Test Model', $this->model->title);

        $this->field->persist(
            $this->app['request'], $this->model, $this->field->getValueForHydrate($this->app['request'])
        );

        $this->model->save();

        $this->assertSame('Persisted', $this->model->title);
    }

    public function test_a_field_hydrates_model_attribute(): void
    {
        $this->assertSame('Test Model', $this->model->title);

        $this->field->resolveHydrate($this->app['request'], $this->model, 'Hydrated');

        $this->assertSame('Hydrated', $this->model->title);

        $this->field->hydrate(function ($request, $model, $value) {
            $model->setAttribute($this->field->getModelAttribute(), strtoupper($value));
        });

        $this->field->resolveHydrate($this->app['request'], $this->model, 'Test');

        $this->assertSame('TEST', $this->model->title);
    }

    public function test_a_field_has_validation_rules(): void
    {
        $this->field->rules(['required'])
            ->createRules(['unique:users'])
            ->updateRules(['unique:users,1']);

        $this->assertSame(
            [$this->field->getValidationKey() => ['required', 'unique:users']],
            $this->field->toValidate($this->app['request'], $this->model)
        );

        $this->model->exists = true;
        $this->assertSame(
            [$this->field->getModelAttribute() => ['required', 'unique:users,1']],
            $this->field->toValidate($this->app['request'], $this->model)
        );
    }

    public function test_a_field_has_display_representation(): void
    {
        $this->assertSame(
            array_merge($this->field->toArray(), [
                'searchable' => $this->field->isSearchable(),
                'sortable' => $this->field->isSortable(),
                'value' => $this->model->title,
                'formattedValue' => $this->model->title,
            ]),
            $this->field->toDisplay($this->app['request'], $this->model)
        );
    }

    public function test_a_field_has_input_representation(): void
    {
        $this->assertSame(
            json_encode(array_merge($this->field->toDisplay($this->app['request'], $this->model), [
                'attrs' => $this->field->newAttributeBag()->class([
                    'form-control--invalid' => $this->field->invalid($this->app['request']),
                ]),
                'error' => $this->field->error($this->app['request']),
                'invalid' => $this->field->invalid($this->app['request']),
            ])),
            json_encode($this->field->toInput($this->app['request'], $this->model))
        );
    }

    public function test_a_field_can_be_searchable(): void
    {
        $this->assertFalse($this->field->isSearchable());

        $this->field->searchable();

        $this->assertTrue($this->field->isSearchable());

        $this->field->searchable(fn (): bool => false);

        $this->assertFalse($this->field->isSearchable());
    }

    public function test_a_field_can_be_sortable(): void
    {
        $this->assertFalse($this->field->isSortable());

        $this->field->sortable();

        $this->assertTrue($this->field->isSortable());

        $this->field->sortable(fn (): bool => false);

        $this->assertFalse($this->field->isSortable());
    }
}
