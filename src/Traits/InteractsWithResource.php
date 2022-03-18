<?php

namespace Cone\Root\Traits;

use Cone\Root\Resources\Resource;
use Cone\Root\Support\Collections\Fields;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

trait InteractsWithResource
{
    /**
     * Map the abilities for the model.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function mapAbilities(Request $request): array
    {
        $policy = Gate::getPolicyFor(static::class);

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
     * Get the resource display representation of the model.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Cone\Root\Support\Collections\Fields  $fields
     * @return array
     */
    public function toDisplay(Request $request, Fields $fields): array
    {
        return [
            'abilities' => $this->mapAbilities($request),
            'fields' => $fields->mapToDisplay($request, $this)->toArray(),
            'id' => $this->getKey(),
            'trashed' => in_array(SoftDeletes::class, class_uses_recursive($this)) && $this->trashed(),
        ];
    }

    /**
     * Get the resource form representation of the model.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Cone\Root\Support\Collections\Fields  $fields
     * @return array
     */
    public function toForm(Request $request, Fields $fields): array
    {
        $fields = $fields->mapToForm($request, $this)->toArray();

        return [
            'abilities' => $this->mapAbilities($request),
            'data' => array_column($fields, 'value', 'name'),
            'fields' => $fields,
            'id' => $this->getKey(),
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
