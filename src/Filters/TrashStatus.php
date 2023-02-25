<?php

namespace Cone\Root\Filters;

use Cone\Root\Http\Requests\RootRequest;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletes;

class TrashStatus extends Filter
{
    /**
     * Apply the filter on the query.
     */
    public function apply(RootRequest $request, Builder $query, mixed $value): Builder
    {
        if (! in_array(SoftDeletes::class, class_uses_recursive($query->getModel()))) {
            return $query;
        }

        switch ($value) {
            case 'all':
                return $query->withTrashed();
            case 'trashed':
                return $query->onlyTrashed();
            default:
                return $query;
        }
    }

    /**
     * Get the filter options.
     */
    public function options(RootRequest $request): array
    {
        return [
            'available' => __('Available'),
            'trashed' => __('Trashed'),
            'all' => __('All'),
        ];
    }
}
