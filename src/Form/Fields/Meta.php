<?php

namespace Cone\Root\Form\Fields;

use Closure;
use Cone\Root\Form\Form;
use Illuminate\Contracts\View\View;
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
    public function __construct(Form $form, string $label, string $name = null, Closure|string $relation = null)
    {
        $relation ??= function (Model $model): EloquentRelation {
            $related = $model->metaData()->getRelated();

            return $model->metaData()
                ->one()
                ->ofMany(
                    [$related->getCreatedAtColumn() => 'MAX'],
                    function (Builder $query): Builder {
                        return $query->where($related->qualifyColumn('key'), $this->getAttribute('name'));
                    },
                    'metaData'
                )
                ->withDefault(['key' => $this->getAttribute('name')]);
        };

        $this->asText();

        parent::__construct($form, $label, $name, $relation);
    }

    /**
     * Get the relation name.
     */
    public function getRelationName(): string
    {
        return $this->relation instanceof Closure
            ? sprintf('__root_%s', $this->getAttribute('name'))
            : $this->relation;
    }

    /**
     * Set the field class.
     */
    public function as(string $field, Closure $callback = null): static
    {
        $this->field = new $field($this->label, $this->getAttribute('name'));

        if (! is_null($callback)) {
            call_user_func_array($callback, [$this->field]);
        }

        $this->field->value(fn (): mixed => $this->resolveValue());

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
    public function async(bool $value = true): static
    {
        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function resolveOptions(): array
    {
        return [];
    }

    /**
     * {@inheritdoc}
     */
    public function getValue(): mixed
    {
        $model = $this->resolveModel();

        $name = $this->getRelationName();

        if (
            $this->relation instanceof Closure
            && $model->relationLoaded('metaData')
            && ! $model->relationLoaded($name)
            && ! is_null(
                $value = $model->getRelation('metaData')
                    ->sortByDesc('created_at')
                    ->firstWhere('key', $this->getAttribute('name'))
            )
        ) {
            $model->setRelation($name, $value);
        }

        return parent::getValue($model);
    }

    /**
     * {@inheritdoc}
     */
    public function resolveValue(): mixed
    {
        if (is_null($this->valueResolver)) {
            $this->valueResolver = static function (Model $model, mixed $value): mixed {
                return $value?->value;
            };
        }

        return parent::resolveValue();
    }

    /**
     * {@inheritdoc}
     */
    public function resolveHydrate(Request $request, mixed $value): void
    {
        if (is_null($this->hydrateResolver)) {
            $this->hydrateResolver = function (Request $request, Model $model, mixed $value): void {
                $related = $this->getValue();

                $related->setAttribute('value', $value);

                $model->setRelation($this->getRelationName(), $related);
            };
        }

        parent::resolveHydrate($request, $value);
    }

    /**
     * {@inheritdoc}
     */
    public function render(): View
    {
        return $this->field->render();
    }

    /**
     * {@inheritdoc}
     */
    public function toValidate(Request $request): array
    {
        return $this->field->toValidate($request);
    }
}
