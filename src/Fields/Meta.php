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
     * The Vue component.
     */
    protected string $component = 'Input';

    /**
     * Create a new relation field instance.
     */
    public function __construct(string $label, string $name = null, Closure|string $relation = null)
    {
        $relation ??= function (Model $model): EloquentRelation {
            $related = $model->metaData()->getRelated();

            return $model->morphOne(get_class($related), 'metable')
                        ->ofMany(
                            [$related->getCreatedAtColumn() => 'max'],
                            function (Builder $query) use ($related): Builder {
                                return $query->where($related->qualifyColumn('key'), $this->name);
                            },
                            'metaData'
                        )
                        ->withDefault(['key' => $this->name]);
        };

        parent::__construct($label, $name, $relation);
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

        if (! $this->relation instanceof Closure
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
        return parent::toInput($request, $model);
    }
}
