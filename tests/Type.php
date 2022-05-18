<?php

namespace Cone\Root\Tests;

use Cone\Root\Filters\Filter;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;

class Type extends Filter
{
    public function apply(Request $request, Builder $query, mixed $value): Builder
    {
        return $query;
    }

    public function options(Request $request): array
    {
        return [];
    }
}
