<?php

namespace Cone\Root\Filters;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Http\Request;

class TrashStatus extends Filter
{
    /**
     * Apply the filter on the query.
     */
    public function apply(Request $request, Builder $query, mixed $value): Builder
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
    public function options(Request $request): array
    {
        return [
            'available' => __('Available'),
            'trashed' => __('Trashed'),
            'all' => __('All'),
        ];
    }
}
