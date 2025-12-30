<?php

declare(strict_types=1);

namespace Cone\Root\Fields;

use Closure;
use Cone\Root\Http\Controllers\BelongsToManyController;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo as BelongsToRelation;
use Illuminate\Database\Eloquent\Relations\BelongsToMany as EloquentRelation;
use Illuminate\Database\Eloquent\Relations\Pivot;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;

/**
 * @template TRelation of \Illuminate\Database\Eloquent\Relations\BelongsToMany
 *
 * @extends \Cone\Root\Fields\Relation<TRelation>
 */
class BelongsToMany extends Relation
{
    /**
     * The pivot fields resolver callback.
     */
    protected ?Closure $pivotFieldsResolver = null;

    /**
     * The default pivot values that should be saved.
     */
    protected array $pivotValues = [];

    /**
     * Indicates if the field allows duplicate relations.
     */
    protected bool $allowDuplicateRelations = false;

    /**
     * Create a new relation field instance.
     */
    public function __construct(string $label, Closure|string|null $modelAttribute = null, Closure|string|null $relation = null)
    {
        parent::__construct($label, $modelAttribute, $relation);

        $this->setAttribute('multiple', true);
        $this->name(sprintf('%s[]', $this->getAttribute('name')));
    }

    /**
     * {@inheritdoc}
     */
    public function getRelation(Model $model): EloquentRelation
    {
        $relation = parent::getRelation($model);

        return $relation->withPivot([
            $relation->newPivot()->getKeyName(),
            $relation->getForeignPivotKeyName(),
            $relation->getRelatedPivotKeyName(),
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function fields(Request $request): array
    {
        return [
            BelongsTo::make($this->getRelatedName(), 'related', static fn (Pivot $model): BelongsToRelation => $model->belongsTo(
                $model->getRelation('related')::class,
                $model->getRelatedKey(),
                $model->getForeignKey(),
                'related'
            )->withDefault())
                ->withRelatableQuery(fn (Request $request, Builder $query, Pivot $model): Builder => $this->resolveRelatableQuery($request, $model->pivotParent)
                    ->unless($this->allowDuplicateRelations, fn (Builder $query): Builder => $query->whereNotIn(
                        $query->getModel()->getQualifiedKeyName(),
                        $this->getRelation($model->pivotParent)->select($query->getModel()->getQualifiedKeyName())
                    )))->hydrate(function (Request $request, Pivot $model, mixed $value): void {
                        $model->setAttribute(
                            $this->getRelation($model->pivotParent)->getRelatedPivotKeyName(),
                            $value
                        );
                    })->display(fn (Model $model): ?string => $this->resolveDisplay($model)),
        ];
    }

    /**
     * Allow duplicate relations.
     */
    public function allowDuplicateRelations(bool $value = true): static
    {
        $this->allowDuplicateRelations = $value;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    protected function resolveField(Request $request, Field $field): void
    {
        if (! $this->isSubResource()) {
            $field->setModelAttribute(
                sprintf('%s.*.%s', $this->getModelAttribute(), $field->getModelAttribute())
            );
        }

        if ($field instanceof Relation) {
            $field->resolveRouteKeyNameUsing(function () use ($field): string {
                return Str::of($field->getRelationName())
                    ->singular()
                    ->ucfirst()
                    ->prepend($this->getRouteKeyName())
                    ->value();
            });
        }

        parent::resolveField($request, $field);
    }

    /**
     * Set the pivot field resolver.
     */
    public function withPivotFields(Closure $callback): static
    {
        $this->withFields($callback);

        $this->pivotFieldsResolver = function (Request $request, Model $model, Model $related) use ($callback): Fields {
            $fields = new Fields;

            $fields->register(Arr::wrap(call_user_func_array($callback, [$request])));

            $fields->each(function (Field $field) use ($model, $related): void {
                $attribute = sprintf(
                    '%s.%s.%s',
                    $this->getModelAttribute(),
                    $related->getKey(),
                    $key = $field->getModelAttribute()
                );

                $field->setModelAttribute($attribute)
                    ->name($attribute)
                    ->id($attribute)
                    ->value(fn (): mixed => $related->getRelation($this->getRelation($model)->getPivotAccessor())->getAttribute($key));
            });

            return $fields;
        };

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getValueForHydrate(Request $request): mixed
    {
        $value = (array) parent::getValueForHydrate($request);

        return $this->mergePivotValues($value);
    }

    /**
     * Merge the pivot values.
     */
    public function mergePivotValues(array $value): array
    {
        $value = array_is_list($value) ? array_fill_keys($value, []) : $value;

        return array_map(fn (array $pivot): array => array_merge($this->pivotValues, $pivot), $value);
    }

    /**
     * {@inheritdoc}
     */
    public function persist(Request $request, Model $model, mixed $value): void
    {
        if ($this->isSubResource()) {
            parent::persist($request, $model, $value);
        } else {
            $model->saved(function (Model $model) use ($request, $value): void {
                $this->resolveHydrate($request, $model, $value);

                $this->getRelation($model)->sync($value);
            });
        }
    }

    /**
     * {@inheritdoc}
     */
    public function resolveHydrate(Request $request, Model $model, mixed $value): void
    {
        if (is_null($this->hydrateResolver)) {
            $this->hydrateResolver = function (Request $request, Model $model, mixed $value): void {
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

        parent::resolveHydrate($request, $model, $value);
    }

    /**
     * {@inheritdoc}
     */
    public function resolveRouteBinding(Request $request, string $id): Model
    {
        $relation = $this->getRelation($request->route()->parentOfParameter($this->getRouteKeyName()));

        $related = $relation->wherePivot($relation->newPivot()->getQualifiedKeyName(), $id)->firstOrFail();

        return tap($related, static function (Model $related) use ($relation, $id): void {
            $pivot = $related->getRelation($relation->getPivotAccessor());

            $pivot->setRelation('related', $related)->setAttribute($pivot->getKeyName(), $id);
        });
    }

    /**
     * {@inheritdoc}
     */
    public function mapRelated(Request $request, Model $model, Model $related): array
    {
        $relation = $this->getRelation($model);

        $pivot = $related->getRelation($relation->getPivotAccessor());

        $pivot->setRelation('related', $related);

        return [
            'id' => $related->getKey(),
            'model' => $pivot,
            'url' => $this->relatedUrl($pivot),
            'fields' => $this->resolveFields($request)
                ->subResource(false)
                ->authorized($request, $related)
                ->visible('index')
                ->mapToDisplay($request, $pivot),
            'abilities' => $this->mapRelatedAbilities($request, $model, $related),
        ];
    }

    /**
     * Get the related URL.
     */
    public function relatedUrl(Model $related): string
    {
        return match (true) {
            $related instanceof Pivot => sprintf('%s/%s', $this->modelUrl($related->pivotParent), $related->getKey()),
            default => parent::relatedUrl($related),
        };
    }

    /**
     * Get the relation controller class.
     */
    public function getRelationController(): string
    {
        return BelongsToManyController::class;
    }

    /**
     * {@inheritdoc}
     */
    public function toOption(Request $request, Model $model, Model $related): array
    {
        $relation = $this->getRelation($model);

        if (! $related->relationLoaded($relation->getPivotAccessor())) {
            $related->setRelation($relation->getPivotAccessor(), $relation->newPivot());
        }

        $option = parent::toOption($request, $model, $related);

        $option['attrs']['name'] = sprintf(
            '%s[%s][%s]',
            $this->getAttribute('name'),
            $related->getKey(),
            $this->getRelation($model)->getRelatedPivotKeyName()
        );

        $option['fields'] = is_null($this->pivotFieldsResolver)
            ? []
            : call_user_func_array($this->pivotFieldsResolver, [$request, $model, $related])->mapToInputs($request, $model);

        return $option;
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
    public function toValidate(Request $request, Model $model): array
    {
        return array_merge(
            parent::toValidate($request, $model),
            $this->resolveFields($request)->mapToValidate($request, $model)
        );
    }

    /**
     * {@inheritdoc}
     */
    public function toCreate(Request $request, Model $model): array
    {
        $relation = $this->getRelation($model);

        $pivot = $relation->newPivot();

        $pivot->setRelation('related', $relation->make());

        return array_merge($this->toSubResource($request, $model), [
            'template' => 'root::resources.form',
            'title' => __('Attach :model', ['model' => $this->getRelatedName()]),
            'model' => $pivot,
            'action' => $this->modelUrl($model),
            'method' => 'POST',
            'uploads' => $this->hasFileField($request),
            'fields' => $this->resolveFields($request)
                ->subResource(false)
                ->authorized($request, $pivot)
                ->visible('create')
                ->mapToInputs($request, $pivot),
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function toShow(Request $request, Model $model, Model $related): array
    {
        $relation = $this->getRelation($model);

        $pivot = $related->getRelation($relation->getPivotAccessor());

        $pivot->setRelation('related', $related);

        return array_merge($this->toSubResource($request, $model), [
            'template' => 'root::resources.show',
            'title' => $this->resolveDisplay($related),
            'model' => $pivot,
            'action' => $this->relatedUrl($pivot),
            'fields' => $this->resolveFields($request)
                ->subResource(false)
                ->authorized($request, $related)
                ->visible('show')
                ->mapToDisplay($request, $pivot),
            'actions' => $this->resolveActions($request)
                ->authorized($request, $related)
                ->visible('show')
                ->standalone(false)
                ->mapToForms($request, $pivot),
            'abilities' => array_merge(
                $this->mapRelationAbilities($request, $model),
                $this->mapRelatedAbilities($request, $model, $related)
            ),
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function toEdit(Request $request, Model $model, Model $related): array
    {
        $relation = $this->getRelation($model);

        $pivot = $related->getRelation($relation->getPivotAccessor());

        $pivot->setRelation('related', $related);

        return array_merge($this->toSubResource($request, $model), [
            'template' => 'root::resources.form',
            'title' => __('Edit :model', ['model' => $this->resolveDisplay($related)]),
            'model' => $pivot,
            'action' => $this->relatedUrl($pivot),
            'method' => 'PATCH',
            'uploads' => $this->hasFileField($request),
            'fields' => $this->resolveFields($request)
                ->subResource(false)
                ->authorized($request, $related)
                ->visible('update')
                ->mapToInputs($request, $pivot),
            'abilities' => array_merge(
                $this->mapRelationAbilities($request, $model),
                $this->mapRelatedAbilities($request, $model, $related)
            ),
        ]);
    }
}
