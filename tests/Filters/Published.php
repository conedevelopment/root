<?php

namespace Cone\Root\Tests\Filters;

use Cone\Root\Filters\Filter;
use Cone\Root\Http\Requests\RootRequest;
use Illuminate\Database\Eloquent\Builder;

class Published extends Filter
{
    public function apply(RootRequest $request, Builder $query, mixed $value): Builder
    {
        return $query;
    }

    public function options(RootRequest $request): array
    {
        return array_merge(parent::options($request), [
            'all' => 'All',
            'published' => 'Published',
            'draft' => 'Draft',
        ]);
    }
}
