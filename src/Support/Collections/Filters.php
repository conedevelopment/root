<?php

declare(strict_types = 1);

namespace Cone\Root\Support\Collections;

use Cone\Root\Filters\Filter;
use Cone\Root\Http\Requests\RootRequest;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;

class Filters extends Collection
{
    /**
     * Filter the filters that are available for the given request.
     *
     * @param  \Cone\Root\Http\Requests\RootRequest  $request
     * @return static
     */
    public function available(RootRequest $request): static
    {
        return $this->filter->authorized($request)->values();
    }

    /**
     * Apply the filters on the query.
     *
     * @param  \Cone\Root\Http\Requests\RootRequest  $request
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function apply(RootRequest $request, Builder $query): Builder
    {
        $this->filter(static function (Filter $filter) use ($request): bool {
            return $request->has($filter->getKey());
        })->each(static function (Filter $filter) use ($query, $request): void {
            $filter->apply($request, $query, $request->input($filter->getKey()));
        });

        return $query;
    }

    /**
     * Map the filters to form.
     *
     * @param  \Cone\Root\Http\Requests\RootRequest  $request
     * @return \Illuminate\Support\Collection
     */
    public function mapToForm(RootRequest $request): Collection
    {
        return $this->reject->functional()->map->toInput($request)->values()->toBase();
    }

    /**
     * Map the filters into their query representation.
     *
     * @param  \Cone\Root\Http\Requests\RootRequest  $request
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return array
     */
    public function mapToQuery(RootRequest $request, Builder $query): array
    {
        return $this->reduce(static function (array $values, Filter $filter) use ($request): array {
            return array_replace($values, [$filter->getKey() => $filter->default($request)]);
        }, [
            'page' => (int) $request->query('page', 1),
            'per_page' => (int) $request->query('per_page', $query->getModel()->getPerPage()),
        ]);
    }
}
