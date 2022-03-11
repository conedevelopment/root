<?php

namespace Cone\Root\Filters;

use Cone\Root\Filters\SelectFilter;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Http\Request;

class TrashStatus extends SelectFilter
{
    /**
     * Apply the filter on the query.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @param  mixed  $value
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function apply(Request $request, Builder $query, mixed $value): Builder
    {
        if (! in_array(SoftDeletes::class, class_uses_recursive($query->getModel()))) {
            return parent::apply($request, $query, $value);
        }

        switch ($value) {
            case 'all':
                return parent::apply($request, $query, $value)->withTrashed();
            case 'trashed':
                return parent::apply($request, $query, $value)->onlyTrashed();
            default:
                return parent::apply($request, $query, $value);
        }
    }

    /**
     * Get the filter options.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
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