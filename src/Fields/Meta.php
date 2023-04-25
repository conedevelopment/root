<?php

namespace Cone\Root\Fields;

use Closure;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphOne as EloquentRelation;
use Illuminate\Http\Request;

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

            return $model->metaData()
                ->one()
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
    public function as(string $field, Closure $callback = null): static
    {
        $this->field = new $field($this->label, $this->name);

        if (! is_null($callback)) {
            call_user_func_array($callback, [$this->field]);
        }

        return $this;
    }

    /**
     * Set the meta field as boolean.
     */
    public function asBoolean(Closure $callback = null): static
    {
        return $this->as(Boolean::class, $callback);
    }

    /**
     * Set the meta field as checkbox.
     */
    public function asCheckbox(Closure $callback = null): static
    {
        return $this->as(Checkbox::class, $callback);
    }

    /**
     * Set the meta field as color.
     */
    public function asColor(Closure $callback = null): static
    {
        return $this->as(Color::class, $callback);
    }

    /**
     * Set the meta field as date.
     */
    public function asDate(Closure $callback = null): static
    {
        return $this->as(Date::class, $callback);
    }

    /**
     * Set the meta field as editor.
     */
    public function asEditor(Closure $callback = null): static
    {
        return $this->as(Editor::class, $callback);
    }

    /**
     * Set the meta field as hidden.
     */
    public function asHidden(Closure $callback = null): static
    {
        return $this->as(Hidden::class, $callback);
    }

    /**
     * Set the meta field as JSON.
     */
    public function asJson(Closure $callback = null): static
    {
        return $this->as(Json::class, $callback);
    }

    /**
     * Set the meta field as number.
     */
    public function asNumber(Closure $callback = null): static
    {
        return $this->as(Number::class, $callback);
    }

    /**
     * Set the meta field as radio.
     */
    public function asRadio(Closure $callback = null): static
    {
        return $this->as(Radio::class, $callback);
    }

    /**
     * Set the meta field as range.
     */
    public function asRange(Closure $callback = null): static
    {
        return $this->as(Range::class, $callback);
    }

    /**
     * Set the meta field as select.
     */
    public function asSelect(Closure $callback = null): static
    {
        return $this->as(Select::class, $callback);
    }

    /**
     * Set the meta field as tag.
     */
    public function asTag(Closure $callback = null): static
    {
        return $this->as(Tag::class, $callback);
    }

    /**
     * Set the meta field as text.
     */
    public function asText(Closure $callback = null): static
    {
        return $this->as(Text::class, $callback);
    }

    /**
     * Set the meta field as textarea.
     */
    public function asTextarea(Closure $callback = null): static
    {
        return $this->as(Textarea::class, $callback);
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
    public function resolveOptions(Request $request, Model $model): array
    {
        return [];
    }

    /**
     * {@inheritdoc}
     */
    public function getValue(Request $request, Model $model): mixed
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
    public function resolveValue(Request $request, Model $model): mixed
    {
        if (is_null($this->valueResolver)) {
            $this->valueResolver = static function (Request $request, Model $model, mixed $value): mixed {
                return $value;
            };
        }

        return parent::resolveValue($request, $model);
    }

    /**
     * {@inheritdoc}
     */
    public function resolveHydrate(Request $request, Model $model, mixed $value): void
    {
        if (is_null($this->hydrateResolver)) {
            $this->hydrateResolver = function () use ($request, $model, $value): void {
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
    public function toInput(Request $request, Model $model): array
    {
        $this->field->value(fn (): mixed => $this->resolveValue($request, $model));

        return $this->field->toInput($request, $model);
    }

    /**
     * {@inheritdoc}
     */
    public function toDisplay(Request $request, Model $model): array
    {
        $this->field->format(fn (): mixed => $this->resolveFormat($request, $model));

        return $this->field->toDisplay($request, $model);
    }

    /**
     * {@inheritdoc}
     */
    public function toValidate(Request $request, Model $model): array
    {
        return $this->field->toValidate($request, $model);
    }
}
