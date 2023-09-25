<?php

namespace Cone\Root\Form\Fields;

use Closure;
use Cone\Root\Form\Fields\Options\RelationOption;
use Cone\Root\Form\Form;
use Cone\Root\Traits\ResolvesFields;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany as EloquentRelation;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\App;

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
    public function __construct(Form $form, string $label, string $modelAttribute = null, Closure|string $relation = null)
    {
        parent::__construct($form, $label, $modelAttribute, $relation);

        $this->setAttribute('multiple', true);
        $this->name($this->modelAttribute.'[]');
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
     * Create a new fields collection.
     */
    public function newFieldsCollection(): Fields
    {
        return new Fields($this->form);
    }

    /**
     * Handle the callback for the field resolution.
     */
    protected function resolveField(Request $request, Field $field): void
    {
        if (! is_null($this->apiUri)) {
            $field->setApiUri(sprintf('%s/%s', $this->apiUri, $field->getUriKey()));
        }

        $field->setModelAttribute(
            sprintf('%s.*.%s', $this->getModelAttribute(), $field->getModelAttribute())
        );
    }

    /**
     * Set the pivot field resolver.
     */
    public function withPivotFields(Closure $callback): static
    {
        $this->withFields($callback);

        $this->pivotFieldsResolver = function (Model $related) use ($callback): Fields {
            $fields = new Fields($this->form);

            App::call(static function (Request $request) use ($callback, $fields): void {
                call_user_func_array($callback, [$request, $fields]);
            });

            $fields->each(function (Field $field) use ($related): void {
                $attribute = sprintf(
                    '%s.%s.%s',
                    $this->getModelAttribute(),
                    $related->getKey(),
                    $key = $field->getModelAttribute()
                );

                $field->setModelAttribute($attribute)
                    ->name($attribute)
                    ->id($attribute)
                    ->value(function () use ($related, $key): mixed {
                        return $related->getRelation($this->getRelation()->getPivotAccessor())->getAttribute($key);
                    });
            });

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

        $option = parent::toOption($related);

        if (! is_null($this->pivotFieldsResolver)) {
            $option->withPivotFields(call_user_func_array($this->pivotFieldsResolver, [$related]));
        }

        return $option;
    }

    /**
     * {@inheritdoc}
     */
    public function persist(Request $request, mixed $value): void
    {
        $this->getModel()->saved(function () use ($request, $value): void {
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

                $value = Arr::isList($value) ? array_fill_keys($value, []) : $value;

                $relation = $this->getRelation();

                $results = $this->resolveRelatableQuery($request)
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
    public function toArray(): array
    {
        return array_merge(parent::toArray(), [
            'relatedName' => $this->getRelatedName(),
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function toValidate(Request $request): array
    {
        return array_merge(
            parent::toValidate($request),
            $this->resolveFields($request)->mapToValidate($request)
        );
    }
}
