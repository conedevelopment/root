<?php

namespace Cone\Root\Form\Fields;

use Closure;
use Cone\Root\Form\Form;
use Cone\Root\Traits\ResolvesFields;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany as EloquentRelation;
use Illuminate\Http\Request;
use Illuminate\Routing\Router;

class BelongsToMany extends Relation
{
    use ResolvesFields;

    /**
     * The pivot fields resolver callback.
     */
    protected ?Closure $pivotFieldsResolver = null;

    /**
     * Create a new relation field instance.
     */
    public function __construct(Form $form, string $label, string $key = null, Closure|string $relation = null)
    {
        parent::__construct($form, $label, $key, $relation);

        $this->setAttribute('multiple', true);

        $this->fields = new Fields($form);
    }

    /**
     * {@inheritdoc}
     */
    public function getRelation(): EloquentRelation
    {
        $relation = parent::getRelation();

        return $relation->withPivot($relation->newPivot()->getKeyName());
    }

    /**
     * Set the pivot field resolver.
     */
    public function withPivotFields(Closure $callback): static
    {
        $this->fields->registering(function (Field $field): void {
            $field->key(sprintf('%s.*.%s', $this->getKey(), $field->getKey()));
        });

        $this->withFields($callback);

        $this->pivotFieldsResolver = function (Model $related) use ($callback): Fields {
            $fields = new Fields($this->form);

            $fields->registering(function (Field $field) use ($related): void {
                $attribute = $field->getKey();

                $key = sprintf('%s.%s.%s', $this->getKey(), $related->getKey(), $field->getKey());

                $field->key($key)
                    ->id($key)
                    ->name($key)
                    ->value(function () use ($related, $attribute): mixed {
                        return $related->getRelation($this->getRelation()->getPivotAccessor())->getAttribute($attribute);
                    });
            });

            call_user_func_array($callback, [$fields]);

            return $fields;
        };

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function toOption(Model $related): RelationOption
    {
        $relation = $this->getRelation();

        if (! $related->relationLoaded($relation->getPivotAccessor())) {
            $related->setRelation($relation->getPivotAccessor(), $relation->newPivot());
        }

        $option =  parent::toOption($related);

        if (! is_null($this->pivotFieldsResolver)) {
            $option->withPivotFields($this->pivotFieldsResolver);
        }

        return $option;
    }

    /**
     * {@inheritdoc}
     */
    public function persist(Request $request, mixed $value): void
    {
        $this->resolveModel()->saved(function () use ($request, $value): void {
            $this->resolveHydrate($request, $value);

            $this->getRelation()->sync($value);
        });
    }

    /**
     * {@inheritdoc}
     */
    public function resolveHydrate(Request $request, mixed $value): void
    {
        if (is_null($this->hydrateResolver)) {
            $this->hydrateResolver = function (Request $request, Model $model, mixed $value): void {
                $value = (array) $value;

                $relation = $this->getRelation($model);

                $results = $this->resolveRelatableQuery($request, $model)
                    ->findMany(array_keys($value))
                    ->each(static function (Model $related) use ($relation, $value): void {
                        $related->setRelation(
                            $relation->getPivotAccessor(),
                            $relation->newPivot($value[$related->getKey()])
                        );
                    });

                $model->setRelation($relation->getRelationName(), $results);
            };
        }

        parent::resolveHydrate($request, $value);
    }

    /**
     * {@inheritdoc}
     */
    public function data(Request $request): array
    {
        return array_merge(parent::data($request), [
            'relatedName' => $this->getRelatedName(),
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function registerRoutes(Router $router): void
    {
        parent::registerRoutes($router);

        $router->prefix($this->getUriKey())->group(function (Router $router): void {
            $this->fields->registerRoutes($router);
        });
    }

    /**
     * {@inheritdoc}
     */
    public function toValidate(Request $request): array
    {
        return array_merge(
            parent::toValidate($request),
            $this->fields->mapToValidate($request)
        );
    }
}
