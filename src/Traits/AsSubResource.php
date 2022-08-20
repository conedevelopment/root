<?php

namespace Cone\Root\Traits;

use Cone\Root\Http\Requests\CreateRequest;
use Cone\Root\Http\Requests\IndexRequest;
use Cone\Root\Http\Requests\ResourceRequest;
use Cone\Root\Http\Requests\ShowRequest;
use Cone\Root\Http\Requests\UpdateRequest;
use Cone\Root\Http\Resources\ModelResource;
use Cone\Root\Http\Resources\RelatedResource;
use Cone\Root\Traits\ResolvesFields;
use Cone\Root\Traits\ResolvesFilters;
use Illuminate\Database\Eloquent\Model;

trait AsSubResource
{
    use MapsAbilities;
    use ResolvesActions;
    use ResolvesBreadcrumbs;
    use ResolvesFields;
    use ResolvesFilters;

    /**
     * Indicates wheter the relation is a sub-resource.
     *
     * @var bool
     */
    protected bool $asSubResource = false;

    /**
     * Set the sub-resource property.
     *
     * @param  bool  $value
     * @return $this
     */
    public function asSubResource(bool $value = true): static
    {
        $this->asSubResource = $value;

        $this->component = 'SubResource';

        return $this;
    }

    /**
     * Resolve the resource model for a bound value.
     *
     * @param  \Cone\Root\Http\Requests\RootRequest  $request
     * @param  string  $id
     * @return \Illuminate\Database\Eloquent\Model
     */
    public function resolveRouteBinding(ResourceRequest $request, string $id): Model
    {
        return $this->getRelation($request->route('rootResource'))->findOrFail($id);
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

        $relation = $this->getRelation($model);

        $query = $filters->apply($request, $relation->getQuery())->latest();

        $items = $relation->paginate($request->input('per_page'))
                        ->withQueryString()
                        ->setPath($this->getUri())
                        ->through(function (Model $related) use ($request, $model): array {
                            return $this->mapItem($request, $model, $related)->toDisplay(
                                $request, $this->resolveFields($request)->available($request, $model, $related)
                            );
                        })
                        ->toArray();

        return array_merge($items, [
            'query' => $filters->mapToQuery($request, $query),
        ]);
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
        $related->setRelation('parent', $model);

        return new RelatedResource($related);
    }

    /**
     * Get the mappable abilities.
     *
     * @return array
     */
    public function getAbilities(): array
    {
        return ['viewAny', 'create'];
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
            'field' => [
                'abilities' => $this->mapAbilities(
                    $request,
                    $this->mapItem($request, $model, $this->getRelation($model)->getRelated())->resource,
                ),
                'url' => sprintf('%s/%s', $this->getUri(), $model->getKey()),
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
            'breadcrumbs' => $this->resolveBreadcrumbs($request, $model)
                                ->merge([sprintf('%s/%s', $this->getUri(), $model->getKey()) => $this->label])
                                ->toArray(),
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
            'breadcrumbs' => $this->resolveBreadcrumbs($request, $model)
                                ->merge([
                                    sprintf('%s/%s', $this->getUri(), $model->getKey()) => $this->label,
                                    sprintf('%s/%s/create', $this->getUri(), $model->getKey()) => __('Create'),
                                ])
                                ->toArray(),
            'model' => $this->mapItem($request, $model, $related)->toForm(
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
            'breadcrumbs' => $this->resolveBreadcrumbs($request, $model)
                                ->merge([
                                    sprintf('%s/%s', $this->getUri(), $model->getKey()) => $this->label,
                                    sprintf('%s/%s/%s', $this->getUri(), $model->getKey(), $related->getKey()) => $related->getKey(),
                                ])
                                ->toArray(),
            'model' => $this->mapItem($request, $model, $related)->toDisplay(
                $request, $this->resolveFields($request)->available($request, $model, $related)
            ),
            'title' => __(':model: :id', ['model' => $this->getRelatedName(), 'id' => $this->resolveDisplay($request, $related)]),
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
            'breadcrumbs' => $this->resolveBreadcrumbs($request, $model)
                                ->merge([
                                    sprintf('%s/%s', $this->getUri(), $model->getKey()) => $this->label,
                                    sprintf('%s/%s/%s', $this->getUri(), $model->getKey(), $related->getKey()) => $related->getKey(),
                                    sprintf('%s/%s/%s/edit', $this->getUri(), $model->getKey(), $related->getKey()) => __('Edit'),
                                ])
                                ->toArray(),
            'model' => $this->mapItem($request, $model, $related)->toForm(
                $request, $this->resolveFields($request)->available($request, $model, $related)
            ),
            'title' => __('Edit :model: :id', ['model' => $this->getRelatedName(), 'id' => $this->resolveDisplay($request, $related)]),
        ]);
    }
}
