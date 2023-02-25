<?php

namespace Cone\Root\Traits;

use Closure;
use Cone\Root\Filters\Filter;
use Cone\Root\Http\Requests\RootRequest;
use Cone\Root\Support\Collections\Filters;

trait ResolvesFilters
{
    /**
     * The filters resolver callback.
     */
    protected ?Closure $filtersResolver = null;

    /**
     * The resolved filters.
     */
    protected ?Filters $resolvedFilters = null;

    /**
     * Define the filters for the resource.
     */
    public function filters(RootRequest $request): array
    {
        return [];
    }

    /**
     * Set the filters resolver.
     */
    public function withFilters(array|Closure $filters): static
    {
        if (is_array($filters)) {
            $filters = static function (RootRequest $request, Filters $collection) use ($filters): Filters {
                return $collection->merge($filters);
            };
        }

        $this->filtersResolver = $filters;

        return $this;
    }

    /**
     * Resolve the filters.
     */
    public function resolveFilters(RootRequest $request): Filters
    {
        if (is_null($this->resolvedFilters)) {
            $filters = Filters::make($this->filters($request));

            if (! is_null($this->filtersResolver)) {
                $filters = call_user_func_array($this->filtersResolver, [$request, $filters]);
            }

            $this->resolvedFilters = $filters->each(function (Filter $filter) use ($request): void {
                $this->resolveFilter($request, $filter);
            });
        }

        return $this->resolvedFilters;
    }

    /**
     * Handle the resolving event on the filter instance.
     */
    protected function resolveFilter(RootRequest $request, Filter $filter): void
    {
        //
    }
}
