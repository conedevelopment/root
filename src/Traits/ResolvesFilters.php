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
     *
     * @var \Closure|null
     */
    protected ?Closure $filtersResolver = null;

    /**
     * The resolved filters.
     *
     * @var \Cone\Root\Support\Collections\Filters|null
     */
    protected ?Filters $resolvedFilters = null;

    /**
     * Define the filters for the resource.
     *
     * @param  \Cone\Root\Http\Requests\RootRequest  $request
     * @return array
     */
    public function filters(RootRequest $request): array
    {
        return [];
    }

    /**
     * Set the filters resolver.
     *
     * @param  array|\Closure  $filters
     * @return $this
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
     *
     * @param  \Cone\Root\Http\Requests\RootRequest  $request
     * @return \Cone\Root\Support\Collections\Filters
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
     *
     * @param  \Cone\Root\Http\Requests\RootRequest  $request
     * @param  \Cone\Root\Filters\Filter  $filter
     * @return void
     */
    protected function resolveFilter(RootRequest $request, Filter $filter): void
    {
        //
    }
}
