<?php

namespace Cone\Root\Support\Collections;

use Cone\Root\Filters\Filter;
use Cone\Root\Resources\Resource;
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
        return $this->filter(static function (Filter $filter) use ($request): bool {
                        return $filter->authorized($request);
                    })
                    ->values();
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
     * Call the resolved callbacks on the filters.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Cone\Root\Resources\Resource  $resource
     * @param  string|null  $key
     * @return void
     */
    public function resolved(Request $request, Resource $resource, ?string $key = null): void
    {
        $this->each(static function (Filter $filter) use ($request, $resource, $key): void {
            $filter->resolved($request, $resource, sprintf('%s:%s', $key, $filter->getKey()));
        });
    }
}
