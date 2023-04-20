<?php

namespace Cone\Root\Tests\Filters;

use Cone\Root\Filters\Filter;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;

class Published extends Filter
{
    public function apply(Request $request, Builder $query, mixed $value): Builder
    {
        return $query;
    }

    public function options(Request $request): array
    {
        return array_merge(parent::options($request), [
            'all' => 'All',
            'published' => 'Published',
            'draft' => 'Draft',
        ]);
    }
}
