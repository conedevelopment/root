<?php

namespace Cone\Root\Fields;

use Cone\Root\Http\Controllers\BelongsToManyController;
use Cone\Root\Http\Requests\CreateRequest;
use Cone\Root\Http\Requests\ResourceRequest;
use Cone\Root\Http\Requests\RootRequest;
use Cone\Root\Http\Resources\ModelResource;
use Cone\Root\Http\Resources\RelatedResource;
use Cone\Root\Traits\AsSubResource;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo as BelongsToRelation;
use Illuminate\Database\Eloquent\Relations\BelongsToMany as EloquentRelation;
use Illuminate\Database\Eloquent\Relations\Pivot;
use Illuminate\Routing\Router;

class BelongsToMany extends Relation
{
    use AsSubResource {
        AsSubResource::toCreate as defaultToCreate;
    }

    /**
     * {@inheritdoc}
     */
    public function getRelation(Model $model): EloquentRelation
    {
        $relation = parent::getRelation($model);

        return $relation->withPivot($relation->newPivot()->getKeyName());
    }

    /**
     * {@inheritdoc}
     */
    public function visible(RootRequest $request): bool
    {
        if ($this->asSubResource && $request instanceof CreateRequest) {
            return false;
        }

        return parent::visible($request);
    }

    /**
     * {@inheritdoc}
     */
    public function async(bool $value = true): static
    {
        parent::async($value);

        if ($this->asSubResource) {
            $this->component = 'SubResource';
        }

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function isSortable(RootRequest $request): bool
    {
        return false;
    }

    /**
     * {@inheritdoc}
     */
    public function persist(RootRequest $request, Model $model): void
    {
        if ($this->asSubResource) {
            return;
        }

        $model->saved(function (Model $model) use ($request): void {
            $value = $this->getValueForHydrate($request, $model);

            $this->resolveHydrate($request, $model, $value);

            $this->getRelation($model)->sync($value);
        });
    }

    /**
     * {@inheritdoc}
     */
    public function resolveHydrate(RootRequest $request, Model $model, mixed $value): void
    {
        if (is_null($this->hydrateResolver)) {
            $this->hydrateResolver = function (RootRequest $request, Model $model, mixed $value): void {
                $relation = $this->getRelation($model);

                $results = $this->resolveQuery($request, $model)->findMany((array) $value);

                $model->setRelation($relation->getRelationName(), $results);
            };
        }

        parent::resolveHydrate($request, $model, $value);
    }

    /**
     * Resolve the related model for a bound value.
     *
     * @param  \Cone\Root\Http\Requests\RootRequest  $request
     * @param  string  $id
     * @return \Illuminate\Database\Eloquent\Model
     */
    public function resolveRouteBinding(ResourceRequest $request, string $id): Model
    {
        $relation = $this->getRelation($request->route('rootResource'));

        $model = $relation->wherePivot($relation->newPivot()->getKeyName(), $id)->firstOrFail();;

        return tap($model, static function (Model $related) use ($relation, $id): void {
            $pivot = $related->getRelation($relation->getPivotAccessor());

            $pivot->setRelation('related', $related)->setAttribute($pivot->getKeyName(), $id);
        });
    }

    /**
     * Map the related model.
     *
     * @param  \Cone\Root\Http\Requests\ResourceRequest  $request
     * @param  \Illuminate\Database\Eloquent\Model  $model
     * @param  \Illuminate\Database\Eloquent\Model  $related
     * @return \Cone\Root\Http\Resources\ModelResource
     */
    public function mapItem(ResourceRequest $request, Model $model, Model $related): ModelResource
    {
        $relation = $this->getRelation($model);

        $pivot = $related->relationLoaded($relation->getPivotAccessor())
                    ? $related->getRelation($relation->getPivotAccessor())
                    : $relation->newPivot();

        if ($pivot->exists) {
            $pivot->setAttribute($pivot->getKeyName(), $pivot->getKey());
        }

        $pivot->setRelation('related', $related);

        return new RelatedResource($pivot);
    }

    /**
     * Define the fields for the object.
     *
     * @param  \Cone\Root\Http\Requests\RootRequest  $request
     * @return array
     */
    public function fields(RootRequest $request): array
    {
        return [
            BelongsTo::make($this->getRelatedName(), 'related', static function (Pivot $model): BelongsToRelation {
                return $model->belongsTo(
                    get_class($model->getRelation('related')),
                    $model->getRelatedKey(),
                    $model->getForeignKey(),
                    'related'
                );
            })
            ->async($this->async)
            ->withQuery(function (RootRequest $request, Builder $query, Model $model): Builder {
                return $this->resolveQuery($request, $model->pivotParent);
            })
            ->display(function (RootRequest $request, Model $related) {
                return $this->resolveDisplay($request, $related);
            }),
        ];
    }

    /**
     * Handle the resolving event on the field instance.
     *
     * @param  \Cone\Root\Http\Requests\RootRequest  $request
     * @param  \Cone\Root\Fields\Field  $field
     * @return void
     */
    protected function resolveField(RootRequest $request, Field $field): void
    {
        $field->mergeAuthorizationResolver(function (...$parameters): bool {
            return $this->authorized(...$parameters);
        });
    }

    /**
     * {@inheritdoc}
     */
    public function registerRoutes(RootRequest $request, Router $router): void
    {
        parent::registerRoutes($request, $router);

        if ($this->asSubResource) {
            $router->prefix($this->getKey())->group(function (Router $router) use ($request): void {
                $this->resolveFields($request)->registerRoutes($request, $router);
                $this->resolveActions($request)->registerRoutes($request, $router);
            });
        }
    }

    /**
     * {@inheritdoc}
     */
    public function routes(Router $router): void
    {
        if ($this->asSubResource) {
            $router->get('{rootResource}', [BelongsToManyController::class, 'index']);
            $router->post('{rootResource}', [BelongsToManyController::class, 'store']);
            $router->get('{rootResource}/create', [BelongsToManyController::class, 'create']);
            $router->get('{rootResource}/{rootRelated}', [BelongsToManyController::class, 'show']);
            $router->get('{rootResource}/{rootRelated}/edit', [BelongsToManyController::class, 'edit']);
            $router->patch('{rootResource}/{rootRelated}', [BelongsToManyController::class, 'update']);
            $router->delete('{rootResource}/{rootRelated}', [BelongsToManyController::class, 'destroy']);
        } else {
            parent::routes($router);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function toInput(RootRequest $request, Model $model): array
    {
        return array_merge(parent::toInput($request, $model), [
            'async' => $this->async,
            'multiple' => true,
            'related_name' => $this->getRelatedName(),
            'url' => sprintf('%s/%s', $this->getUri(), $model->getKey()),
        ]);
    }

    /**
     * Get the create representation of the field.
     *
     * @param  \Cone\Root\Http\Requests\CreateRequest  $request
     * @param  \Illuminate\Database\Eloquent\Model  $model
     * @return array
     */
    public function toCreate(CreateRequest $request, Model $model): array
    {
        return array_merge($this->defaultToCreate($request, $model), [
            'title' => __('Attach :model', ['model' => $this->getRelatedName()]),
        ]);
    }
}
