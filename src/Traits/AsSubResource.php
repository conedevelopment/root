<?php

declare(strict_types = 1);

namespace Cone\Root\Traits;

use Cone\Root\Http\Requests\CreateRequest;
use Cone\Root\Http\Requests\IndexRequest;
use Cone\Root\Http\Requests\ResourceRequest;
use Cone\Root\Http\Requests\RootRequest;
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
     * Map the related models.
     *
     * @param  \Cone\Root\Http\Requests\IndexRequest  $request
     * @param  \Illuminate\Database\Eloquent\Model  $model
     * @return array
     */
    public function mapItems(IndexRequest $request, Model $model): array
    {
        $filters = $this->resolveFilters($request)->available($request);

        $relation = $this->getRelation($model);

        $query = $filters->apply($request, $relation->getQuery())->latest();

        $items = $relation->paginate($request->input('per_page'))
                        ->withQueryString()
                        ->setPath($this->resolveUri($request))
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
     * Get the model for the breadcrumbs.
     *
     * @param  \Cone\Root\Http\Requests\ResourceRequest  $request
     * @return \Illuminate\Databse\Eloquent\Model
     */
    public function getModelForBreadcrumbs(ResourceRequest $request): Model
    {
        return $request->route($this->getRouteKeyName());
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
     * Resolve the breadcrumbs for the given request.
     *
     * @param  \Cone\Root\Http\Requests\RootRequest  $request
     * @return array
     */
    public function resolveBreadcrumbs(RootRequest $request): array
    {
        $breadcrumbs = $request->resource()->resolveBreadcrumbs(UpdateRequest::createFrom($request));

        $breadcrumbs[$this->resolveUri($request)] = $this->label;

        if ($request instanceof CreateRequest) {
            $breadcrumbs[$this->resolveUri($request).'/create'] = __('Create');
        }

        if ($request instanceof ShowRequest || $request instanceof UpdateRequest) {
            $related = $this->getModelForBreadcrumbs($request);

            $breadcrumbs[$this->resolveUri($request)."/{$related->getKey()}"] = $related->getKey();
        }

        if ($request instanceof UpdateRequest) {
            $related = $this->getModelForBreadcrumbs($request);

            $breadcrumbs[$this->resolveUri($request)."/{$related->getKey()}/edit"] = __('Edit');
        }

        return $breadcrumbs;
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
            'breadcrumbs' => $this->resolveBreadcrumbs($request),
            'field' => [
                'abilities' => $this->mapAbilities(
                    $request,
                    $this->mapItem($request, $model, $this->getRelation($model)->getRelated())->resource,
                ),
                'url' => $this->resolveUri($request),
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
            'model' => $this->mapItem($request, $model, $related)->toForm(
                $request, $this->resolveFields($request)->available($request, $model, $related)
            ),
            'title' => __('Edit :model: :id', ['model' => $this->getRelatedName(), 'id' => $this->resolveDisplay($request, $related)]),
        ]);
    }
}
