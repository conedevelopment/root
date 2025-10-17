<?php

declare(strict_types=1);

namespace Cone\Root\Traits;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;

trait Filterable
{
    /**
     * Apply all the relevant filters on the query.
     */
    public function scopeFilter(Builder $query, Request $request): Builder
    {
        foreach ($request->except('filter') as $name => $value) {
            if ($this->hasNamedScope($name) && ! empty($value)) {
                $this->callNamedScope($name, [$query, $value]);
            }
        }

        return $query;
    }
}
