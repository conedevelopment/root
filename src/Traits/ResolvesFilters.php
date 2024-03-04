<?php

namespace Cone\Root\Traits;

use Closure;
use Cone\Root\Filters\Filter;
use Cone\Root\Filters\Filters;
use Cone\Root\Support\Filters as ValueFilters;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;

trait ResolvesFilters
{
    /**
     * The filters collection.
     */
    protected ?Filters $filters = null;

    /**
     * The filters resolver callback.
     */
    protected ?Closure $filtersResolver = null;

    /**
     * Define the filters for the object.
     */
    public function filters(Request $request): array
    {
        return [
            //
        ];
    }

    /**
     * Set the filters resolver callback.
     */
    public function withFilters(Closure $callback): static
    {
        $this->filtersResolver = $callback;

        return $this;
    }

    /**
     * Resolve the filters collection.
     */
    public function resolveFilters(Request $request): Filters
    {
        if (is_null($this->filters)) {
            $filters = new Filters($this->filters($request));

            $filters->when(! is_null($this->filtersResolver), function (Filters $filters) use ($request): void {
                $filters->register(
                    Arr::wrap(call_user_func_array($this->filtersResolver, [$request]))
                );
            });

            $this->filters = ValueFilters::apply(static::class.'.filters', $filters, $this);

            $this->filters->each(function (Filter $filter) use ($request): void {
                $this->resolveFilter($request, $filter);
            });
        }

        return $this->filters;
    }

    /**
     * Handle the callback for the filter resolution.
     */
    protected function resolveFilter(Request $request, Filter $filter): void
    {
        //
    }
}
