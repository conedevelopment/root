<?php

namespace Cone\Root\Filters;

use Cone\Root\Columns\Columns;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;

class Sort extends Filter
{
    /**
     * The sortable columns.
     */
    protected Columns $columns;

    /**
     * Create a new filter instance.
     */
    public function __construct(Columns $columns)
    {
        $this->columns = $columns;
    }

    /**
     * {@inheritdoc}
     */
    public function apply(Request $request, Builder $query, mixed $value): Builder
    {
        return $query;
    }
}
