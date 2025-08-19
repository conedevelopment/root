<?php

declare(strict_types=1);

namespace Cone\Root\Filters;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Http\Request;

class TrashStatus extends Select
{
    /**
     * Apply the filter on the query.
     */
    public function apply(Request $request, Builder $query, mixed $value): Builder
    {
        if (! in_array(SoftDeletes::class, class_uses_recursive($query->getModel()))) {
            return $query;
        }

        return match ($value) {
            'all' => $query->withTrashed(),
            'trashed' => $query->onlyTrashed(),
            default => $query,
        };
    }

    /**
     * Determine if the filter is active.
     */
    public function isActive(Request $request): bool
    {
        return parent::isActive($request)
            && $this->getValue($request) !== 'available';
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
