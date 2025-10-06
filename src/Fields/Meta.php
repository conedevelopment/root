<?php

declare(strict_types=1);

namespace Cone\Root\Fields;

use Closure;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphOne as EloquentRelation;
use Illuminate\Http\Request;

/**
 * @extends \Cone\Root\Fields\MorphOne<\Illuminate\Database\Eloquent\Relations\MorphOne>
 */
class Meta extends MorphOne
{
    /**
     * The field instance.
     */
    protected Field $field;

    /**
     * Create a new relation field instance.
     */
    public function __construct(string $label, Closure|string|null $modelAttribute = null, Closure|string|null $relation = null)
    {
        $relation ??= function (Model $model): EloquentRelation {
            /** @phpstan-var \Cone\Root\Tests\phpstan\MetaDataModel $model */
            $related = $model->metaData()->make();

            return $model->metaData()
                ->one()
                ->ofMany(
                    [$related->getCreatedAtColumn() => 'MAX'],
                    function (Builder $query) use ($related): Builder {
                        return $query->where($related->qualifyColumn('key'), $this->getModelAttribute());
                    },
                    'metaData'
                )
                ->withDefault(['key' => $this->getModelAttribute()]);
        };

        parent::__construct($label, $modelAttribute, $relation);

        $this->as(Text::class);
    }

    /**
     * Get the relation name.
     */
    public function getRelationName(): string
    {
        return $this->relation instanceof Closure
            ? sprintf('__root_%s', $this->getModelAttribute())
            : $this->relation;
    }

    /**
     * Get the field instance.
     */
    public function getField(): Field
    {
        return $this->field;
    }

    /**
     * Set the field class.
     */
    public function as(string $field, ?Closure $callback = null): static
    {
        $this->field = new $field($this->label, $this->getModelAttribute());

        $this->field->value(fn (Request $request, Model $model): mixed => $this->resolveValue($request, $model));

        if (! is_null($callback)) {
            call_user_func_array($callback, [$this->field]);
        }

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
    public function getValue(Model $model): mixed
    {
        $name = $this->getRelationName();

        if ($this->relation instanceof Closure
            && $model->relationLoaded('metaData')
            && ! $model->relationLoaded($name)
        ) {
            $model->setRelation($name, $model->metaValue($this->getModelAttribute()));
        }

        return parent::getValue($model);
    }

    /**
     * {@inheritdoc}
     */
    public function resolveValue(Request $request, Model $model): mixed
    {
        if (is_null($this->valueResolver)) {
            $this->valueResolver = static fn (Request $request, Model $model, mixed $value): mixed => $value?->value;
        }

        return parent::resolveValue($request, $model);
    }

    /**
     * {@inheritdoc}
     */
    public function resolveHydrate(Request $request, Model $model, mixed $value): void
    {
        if (is_null($this->hydrateResolver)) {
            $this->hydrateResolver = function (Request $request, Model $model, mixed $value): void {
                $related = $this->getValue($model);

                $related->setAttribute('value', $value);

                $model->setRelation($this->getRelationName(), $related);
            };
        }

        parent::resolveHydrate($request, $model, $value);
    }

    /**
     * {@inheritdoc}
     */
    public function toDisplay(Request $request, Model $model): array
    {
        return $this->field->toDisplay($request, $model);
    }

    /**
     * {@inheritdoc}
     */
    public function toInput(Request $request, Model $model): array
    {
        return $this->field->toInput($request, $model);
    }

    /**
     * {@inheritdoc}
     */
    public function toValidate(Request $request, Model $model): array
    {
        return $this->field->toValidate($request, $model);
    }
}
