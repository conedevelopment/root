<?php

namespace Cone\Root\Support\Collections;

use Cone\Root\Filters\Filter;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;

class Filters extends Collection
{
    /**
     * Filter the filters that are available for the given request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return static
     */
    public function available(Request $request): static
    {
        return $this->filter->authorized($request)->values();
    }

    /**
     * Apply the filters on the query.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function apply(Request $request, Builder $query): Builder
    {
        $this->filter(static function (Filter $filter) use ($request): bool {
            return $request->has($filter->getKey());
        })->each(static function (Filter $filter) use ($query, $request): void {
            $filter->apply($request, $query, $request->input($filter->getKey()));
        });

        return $query;
    }

    /**
     * Map the filters into their query representation.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function mapToQuery(Request $request): array
    {
        return $this->reduce(static function (array $query, Filter $filter) use ($request): array {
            return array_merge($query, [$filter->getKey() => $filter->default($request)]);
        }, [
            'page' => $request->query('page', 1),
            'per_page' => $request->query('per_page', 15),
        ]);
    }
}
