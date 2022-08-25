<?php

namespace Cone\Root\Http\Resources;

use Cone\Root\Http\Requests\ResourceRequest;
use Cone\Root\Support\Collections\Fields;
use Cone\Root\Traits\MapsAbilities;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Http\Resources\Json\JsonResource;

class ModelResource extends JsonResource
{
    use MapsAbilities;

    /**
     * Get the mappable abilities.
     *
     * @return array
     */
    public function getAbilities(): array
    {
        return ['view', 'update', 'delete', 'restore', 'forceDelete'];
    }

    /**
     * Map the URL for the model.
     *
     * @param  \Cone\Root\Http\Requests\ResourceRequest  $request
     * @return string
     */
    protected function mapUrl(ResourceRequest $request): string
    {
        return $this->resource->exists
            ? sprintf('%s/%s', $request->resource()->getUri(), $this->resource->getKey())
            : $request->resource()->getUri();
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
            'abilities' => $this->mapAbilities($request, $this->resource),
            'exists' => $this->resource->exists,
            'id' => $this->resource->getKey(),
            'trashed' => in_array(SoftDeletes::class, class_uses_recursive($this->resource)) && $this->resource->trashed(),
            'url' => $this->mapUrl($request),
        ];
    }
}
