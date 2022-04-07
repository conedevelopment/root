<?php

namespace Cone\Root\Traits;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;

trait Filterable
{
    /**
     * Apply all the relevant filters on the query.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Database\Eloquent\Builder
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
