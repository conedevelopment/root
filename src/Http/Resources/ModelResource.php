<?php

namespace Cone\Root\Http\Resources;

use Cone\Root\Http\Requests\ResourceRequest;
use Cone\Root\Support\Collections\Fields;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\URL;

class ModelResource extends JsonResource
{
    /**
     * Map the abilities for the model.
     *
     * @param  \Cone\Root\Http\Requests\ResourceRequest  $request
     * @return array
     */
    protected function mapAbilities(ResourceRequest $request): array
    {
        $policy = Gate::getPolicyFor($this->resource);

        return array_reduce(
            ['view', 'update', 'delete', 'restore', 'forceDelete'],
            function (array $stack, string $ability) use ($request, $policy): array {
                return array_merge($stack, [
                    $ability => is_null($policy) || $request->user()->can($ability, $this->resource),
                ]);
            },
            []
        );
    }

    /**
     * Map the URL for the model.
     *
     * @param  \Cone\Root\Http\Requests\ResourceRequest  $request
     * @return string
     */
    protected function mapUrl(ResourceRequest $request): string
    {
        $key = $request->resource()->getKey();

        return $this->resource->exists
            ? URL::route(sprintf('root.%s.show', $key), $this->resource)
            : URL::route(sprintf('root.%s.index', $key));
    }

    /**
     * Get the resource display representation of the model.
     *
     * @param  \Cone\Root\Http\Requests\ResourceRequest  $request
     * @param  \Cone\Root\Support\Collections\Fields  $fields
     * @return array
     */
    public function toDisplay(ResourceRequest $request, Fields $fields): array
    {
        return array_merge($this->toArray($request), [
            'fields' => $fields->mapToDisplay($request, $this->resource)->toArray(),
        ]);
    }

    /**
     * Get the resource form representation of the model.
     *
     * @param  \Cone\Root\Http\Requests\ResourceRequest  $request
     * @param  \Cone\Root\Support\Collections\Fields  $fields
     * @return array
     */
    public function toForm(ResourceRequest $request, Fields $fields): array
    {
        $fields = $fields->mapToForm($request, $this->resource)->toArray();

        return array_merge($this->toArray($request), [
            'data' => array_column($fields, 'value', 'name'),
            'fields' => $fields,
        ]);
    }

    /**
     * Transform the resource into an array.
     *
     * @param  \Cone\Root\Http\Requests\ResourceRequest  $request
     * @return array
     */
    public function toArray($request): array
    {
        return [
            'abilities' => $this->mapAbilities($request),
            'id' => $this->resource->getKey(),
            'trashed' => in_array(SoftDeletes::class, class_uses_recursive($this->resource)) && $this->resource->trashed(),
            'url' => $this->mapUrl($request),
        ];
    }
}
