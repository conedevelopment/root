<?php

namespace Cone\Root\Fields;

use Closure;
use Cone\Root\Http\Requests\RootRequest;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphOne as EloquentRelation;
use Illuminate\Routing\Router;

class Meta extends MorphOne
{
    /**
     * The field instance.
     */
    protected Field $field;

    /**
     * Create a new relation field instance.
     */
    public function __construct(string $label, string $name = null, string $field = Text::class, Closure|string $relation = null)
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

        parent::__construct($label, $name, $relation);

        $this->field = new $field($label, $name);
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
        if (method_exists($this->field, 'async')) {
            $this->field->async($value);
        }

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
    public function registerRoutes(RootRequest $request, Router $router): void
    {
        parent::registerRoutes($request, $router);

        if (method_exists($this->field, 'registerRoutes')) {
            $this->field->registeRoutes($request, $router);
        }
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
