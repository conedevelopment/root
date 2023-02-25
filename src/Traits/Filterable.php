<?php

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
            if ($this->hasNamedScope($name)) {
                $this->callNamedScope($name, [$query, $value]);
            }
        }

        return $query;
    }
}
