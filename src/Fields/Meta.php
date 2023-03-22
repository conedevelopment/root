<?php

namespace Cone\Root\Fields;

use Closure;
use Cone\Root\Http\Requests\RootRequest;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphOne as EloquentRelation;

class Meta extends MorphOne
{
    /**
     * The field instance.
     */
    protected Field $field;

    /**
     * Create a new relation field instance.
     */
    public function __construct(string $label, string $name = null, Closure|string $relation = null)
    {
        $relation ??= function (Model $model): EloquentRelation {
            $related = $model->metaData()->getRelated();

            return $model->morphOne(get_class($related), 'metable')
                        ->ofMany(
                            [$related->getCreatedAtColumn() => 'MAX'],
                            fn (Builder $query): Builder => $query->where($related->qualifyColumn('key'), $this->name),
                            'metaData'
                        )
                        ->withDefault(['key' => $this->name]);
        };

        $this->field = new Text($label, $name);

        parent::__construct($label, $name, $relation);
    }

    /**
     * Set the field class.
     */
    public function as(string $field): static
    {
        $this->field = new $field($this->label, $this->name);

        return $this;
    }

    /**
     * Set the meta field as boolean.
     */
    public function asBoolean(): static
    {
        return $this->as(Boolean::class);
    }

    /**
     * Set the meta field as checkbox.
     */
    public function asCheckbox(): static
    {
        return $this->as(Checkbox::class);
    }

    /**
     * Set the meta field as color.
     */
    public function asColor(): static
    {
        return $this->as(Color::class);
    }

    /**
     * Set the meta field as date.
     */
    public function asDate(): static
    {
        return $this->as(Date::class);
    }

    /**
     * Set the meta field as editor.
     */
    public function asEditor(): static
    {
        return $this->as(Editor::class);
    }

    /**
     * Set the meta field as hidden.
     */
    public function asHidden(): static
    {
        return $this->as(Hidden::class);
    }

    /**
     * Set the meta field as number.
     */
    public function asNumber(): static
    {
        return $this->as(Number::class);
    }

    /**
     * Set the meta field as radio.
     */
    public function asRadio(): static
    {
        return $this->as(Radio::class);
    }

    /**
     * Set the meta field as range.
     */
    public function asRange(): static
    {
        return $this->as(Range::class);
    }

    /**
     * Set the meta field as select.
     */
    public function asSelect(): static
    {
        return $this->as(Select::class);
    }

    /**
     * Set the meta field as tag.
     */
    public function asTag(): static
    {
        return $this->as(Tag::class);
    }

    /**
     * Set the meta field as text.
     */
    public function asText(): static
    {
        return $this->as(Text::class);
    }

    /**
     * Set the meta field as textarea.
     */
    public function asTextarea(): static
    {
        return $this->as(Textarea::class);
    }

    /**
     * {@inheritdoc}
     */
    public function asSubResource(bool $value = true): static
    {
        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function async(bool $value = true): static
    {
        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function resolveOptions(RootRequest $request, Model $model): array
    {
        return [];
    }

    /**
     * {@inheritdoc}
     */
    public function getValue(RootRequest $request, Model $model): mixed
    {
        $name = $this->getRelationName();

        if ($this->relation instanceof Closure
            && $model->relationLoaded('metaData')
            && ! $model->relationLoaded($name)
            && ! is_null($value = $model->getRelation('metaData')->sortByDesc('created_at')->firstWhere('key', $this->name))) {
            $model->setRelation($name, $value);
        }

        return parent::getValue($request, $model);
    }

    /**
     * {@inheritdoc}
     */
    public function resolveValue(RootRequest $request, Model $model): mixed
    {
        if (is_null($this->valueResolver)) {
            $this->valueResolver = static function (RootRequest $request, Model $model, mixed $value): mixed {
                return $value?->value;
            };
        }

        return parent::resolveValue($request, $model);
    }

    /**
     * {@inheritdoc}
     */
    public function resolveHydrate(RootRequest $request, Model $model, mixed $value): void
    {
        if (is_null($this->hydrateResolver)) {
            $this->hydrateResolver = function (RootRequest $request, Model $model, mixed $value): void {
                $related = $this->getValue($request, $model);

                $related->setAttribute('value', $value);

                $model->setRelation($this->getRelationName(), $related);
            };
        }

        parent::resolveHydrate($request, $model, $value);
    }

    /**
     * {@inheritdoc}
     */
    public function toInput(RootRequest $request, Model $model): array
    {
        $this->field->value(fn (): mixed => $this->resolveValue($request, $model));

        return $this->field->toInput($request, $model);
    }

    /**
     * {@inheritdoc}
     */
    public function toDisplay(RootRequest $request, Model $model): array
    {
        $this->field->format(fn (): mixed => $this->resolveFormat($request, $model));

        return $this->field->toDisplay($request, $model);
    }

    /**
     * Handle dynamic method calls into the field.
     */
    public function __call(string $method, array $arguments): static
    {
        call_user_func_array([$this->field, $method], $arguments);

        return $this;
    }
}
