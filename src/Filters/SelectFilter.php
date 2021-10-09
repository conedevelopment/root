<?php

namespace Cone\Root\FIlters;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;

class SelectFilter extends Filter
{
    /**
     * Apply the filter on the query.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @param  \Illuminate\Http\Request  $request
     * @param  mixed  $value
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function apply(Builder $query, Request $request, mixed $value): Builder
    {
        //

        return parent::apply($query, $request, $value);
    }
}
