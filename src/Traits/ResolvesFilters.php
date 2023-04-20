<?php

namespace Cone\Root\Traits;

use Closure;
use Cone\Root\Filters\Filter;
use Cone\Root\Support\Collections\Filters;
use Illuminate\Http\Request;

trait ResolvesFilters
{
    /**
     * The filters resolver callback.
     */
    protected ?Closure $filtersResolver = null;

    /**
     * The resolved filters.
     */
    protected ?Filters $filters = null;

    /**
     * Define the filters for the resource.
     */
    public function filters(Request $request): array
    {
        return [];
    }

    /**
     * Set the filters resolver.
     */
    public function withFilters(array|Closure $filters): static
    {
        $this->filtersResolver = is_array($filters) ? fn (): array => $filters : $filters;

        return $this;
    }

    /**
     * Resolve the filters.
     */
    public function resolveFilters(Request $request): Filters
    {
        if (is_null($this->filters)) {
            $this->filters = Filters::make()->register($this->filters($request));

            if (! is_null($this->filtersResolver)) {
                $this->filters->register(call_user_func_array($this->filtersResolver, [$request]));
            }

            $this->filters->each(function (Filter $filter) use ($request): void {
                $this->resolveFilter($request, $filter);
            });
        }

        return $this->filters;
    }

    /**
     * Handle the resolving event on the filter instance.
     */
    protected function resolveFilter(Request $request, Filter $filter): void
    {
        //
    }
}
