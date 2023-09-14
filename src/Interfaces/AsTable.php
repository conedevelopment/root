<?php

namespace Cone\Root\Interfaces;

use Cone\Root\Table\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;

interface AsTable
{
    /**
     * Convert the object to a table using the request and the query.
     */
    public function toTable(Request $request, Builder $query): Table;
}
