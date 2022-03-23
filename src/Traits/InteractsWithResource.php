<?php

namespace Cone\Root\Traits;

use Cone\Root\Http\Requests\ResourceRequest;
use Cone\Root\Resources\Resource;
use Cone\Root\Support\Collections\Fields;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\URL;

trait InteractsWithResource
{
    /**
     * Map the abilities for the model.
     *
     * @param  \Cone\Root\Http\Requests\ResourceRequest  $request
     * @return array
     */
    public function mapAbilities(ResourceRequest $request): array
    {
        $policy = $request->resource()->getPolicy();

        return array_reduce(
            ['view', 'update', 'delete', 'restore', 'forceDelete'],
            function (array $stack, string $ability) use ($request, $policy): array {
                return array_merge($stack, [
                    $ability => is_null($policy) || $request->user()->can($ability, $this),
                ]);
            },
            []
        );
    }

    /**
     * Map the URL for the model.
     *
     * @param  \Cone\Root\Http\Requests\ResourceRequest  $request
     * @return array
     */
    public function mapUrls(ResourceRequest $request): array
    {
        $key = $request->resource()->getKey();

        $actions = array_fill_keys(['show', 'update', 'edit', 'destroy'], null);

        foreach ($actions as $action => $value) {
            $actions[$action] = URL::route(sprintf('root.%s.%s', $key, $action), $this);
        }

        return $actions;
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
        return [
            'abilities' => $this->mapAbilities($request),
            'fields' => $fields->mapToDisplay($request, $this)->toArray(),
            'id' => $this->getKey(),
            'trashed' => in_array(SoftDeletes::class, class_uses_recursive($this)) && $this->trashed(),
            'urls' => $this->mapUrls($request),
        ];
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
        $fields = $fields->mapToForm($request, $this)->toArray();

        return [
            'abilities' => $this->mapAbilities($request),
            'data' => array_column($fields, 'value', 'name'),
            'fields' => $fields,
            'id' => $this->getKey(),
            'urls' => $this->mapUrls($request),
        ];
    }

    /**
     * Get the resource representation of the model.
     *
     * @return \Cone\Root\Resources\Resource
     */
    public static function toResource(): Resource
    {
        return new Resource(static::class);
    }
}
