<?php

namespace Cone\Root\Fields;

use Cone\Root\Http\Controllers\HasManyController;
use Cone\Root\Http\Requests\CreateRequest;
use Cone\Root\Http\Requests\IndexRequest;
use Cone\Root\Http\Requests\ResourceRequest;
use Cone\Root\Http\Requests\RootRequest;
use Cone\Root\Http\Requests\ShowRequest;
use Cone\Root\Http\Requests\UpdateRequest;
use Cone\Root\Http\Resources\RelatedResource;
use Cone\Root\Traits\ResolvesFilters;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Routing\Router;
use Illuminate\Support\Facades\URL;

class HasMany extends HasOne
{
    use ResolvesFilters;

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
        $model->saved(function (Model $model) use ($request): void {
            $relation = $this->getRelation($model);

            $value = $this->getValueForHydrate($request, $model);

            $this->hydrate($request, $model, $value);

            $relation->saveMany(
                $model->getRelation($relation->getRelationName())
            );
        });
    }

    /**
     * {@inheritdoc}
     */
    public function hydrate(RootRequest $request, Model $model, mixed $value): void
    {
        $relation = $this->getRelation($model);

        $results = $this->resolveQuery($request, $model)->findMany((array) $value);

        $model->setRelation($relation->getRelationName(), $results);
    }

    /**
     * Map the related models.
     *
     * @param  \Cone\Root\Http\Requests\ResourceRequest  $request
     * @param  \Illuminate\Database\Eloquent\Model  $model
     * @return array
     */
    public function mapItems(ResourceRequest $request, Model $model): array
    {
        $filters = $this->resolveFilters($request)->available($request);

        $query = $this->getRelation($model)->getQuery();

        $items = $filters->apply($request, $query)
                        ->latest()
                        ->paginate($request->input('per_page'))
                        ->withQueryString()
                        ->through(function (Model $related) use ($request, $model): array {
                            $related->setRelation('parent', $model);

                            return (new RelatedResource($related))->toDisplay(
                                $request, $this->resolveFields($request)->available($request, $model, $related)
                            );
                        })
                        ->toArray();

        return array_merge($items, [
            'query' => $filters->mapToQuery($request, $query),
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function toInput(RootRequest $request, Model $model): array
    {
        return array_merge(parent::toInput($request, $model), [
            'multiple' => true,
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function routes(Router $router): void
    {
        parent::routes($router);

        $router->get('{rootResource}', [HasManyController::class, 'index']);
        $router->post('{rootResource}', [HasManyController::class, 'store']);
        $router->get('{rootResource}/create', [HasManyController::class, 'create']);
        $router->get('{rootResource}/{related}', [HasManyController::class, 'show']);
        $router->get('{rootResource}/{related}/edit', [HasManyController::class, 'edit']);
        $router->patch('{rootResource}/{related}', [HasManyController::class, 'update']);
        $router->delete('{rootResource}/{related}', [HasManyController::class, 'destroy']);
    }

    /**
     * Get the sub resource representation of the field.
     *
     * @param  \Cone\Root\Http\Requests\ResourceRequest  $request
     * @param  \Illuminate\Database\Eloquent\Model  $model
     * @return array
     */
    public function toSubResource(ResourceRequest $request, Model $model): array
    {
        return [
            'resource' => $request->resource()->toArray(),
            'parent' => [
                'url' => URL::route(sprintf('root.%s.show', $request->resource()->getKey()), $model),
            ],
            'field' => [
                'url' => URL::to(sprintf('%s/%s', $this->getUri(), $model->getKey())),
                'name' => $this->label,
                'related_name' => $this->getRelatedName(),
            ],
        ];
    }

    /**
     * Get the index representation of the field.
     *
     * @param  \Cone\Root\Http\Requests\IndexRequest  $request
     * @param  \Illuminate\Database\Eloquent\Model  $model
     * @return array
     */
    public function toIndex(IndexRequest $request, Model $model): array
    {
        return array_merge($this->toSubResource($request, $model), [
            'items' => $this->mapItems($request, $model),
            'title' => $this->label,
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
        $related = $this->getRelation($model)->getRelated()->setRelation('parent', $model);

        return array_merge($this->toSubResource($request, $model), [
            'model' => (new RelatedResource($related))->toForm(
                $request, $this->resolveFields($request)->available($request, $model, $related)
            ),
            'title' => __('Create :model', ['model' => $this->label]),
        ]);
    }

    /**
     * Get the show representation of the field.
     *
     * @param  \Cone\Root\Http\Requests\ShowRequest  $request
     * @param  \Illuminate\Database\Eloquent\Model  $model
     * @param  \Illuminate\Database\Eloquent\Model  $related
     * @return array
     */
    public function toShow(ShowRequest $request, Model $model, Model $related): array
    {
        $related->setRelation('parent', $model);

        return array_merge($this->toSubResource($request, $model), [
            'model' => (new RelatedResource($related))->toDisplay(
                $request, $this->resolveFields($request)->available($request, $model, $related)
            ),
            'title' => __('Create :model', ['model' => $this->label]),
        ]);
    }

    /**
     * Get the edit representation of the field.
     *
     * @param  \Cone\Root\Http\Requests\UpdateRequest  $request
     * @param  \Illuminate\Database\Eloquent\Model  $model
     * @param  \Illuminate\Database\Eloquent\Model  $related
     * @return array
     */
    public function toEdit(UpdateRequest $request, Model $model, Model $related): array
    {
        $related->setRelation('parent', $model);

        return array_merge($this->toSubResource($request, $model), [
            'model' => (new RelatedResource($related))->toForm(
                $request, $this->resolveFields($request)->available($request, $model, $related)
            ),
            'title' => __('Edit :model: :id', ['model' => $this->getRelatedName(), 'id' => $related->getKey()]),
        ]);
    }
}
