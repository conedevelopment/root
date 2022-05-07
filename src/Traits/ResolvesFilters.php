<?php

namespace Cone\Root\Traits;

use Closure;
use Cone\Root\Support\Collections\Filters;
use Illuminate\Http\Request;

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
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function filters(Request $request): array
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
            $filters = static function (Request $request, Filters $collection) use ($filters): Filters {
                return $collection->merge($filters);
            };
        }

        $this->filtersResolver = $filters;

        return $this;
    }

    /**
     * Resolve the filters.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Cone\Root\Support\Collections\Filters
     */
    public function resolveFilters(Request $request): Filters
    {
        if (is_null($this->resolvedFilters)) {
            $filters = Filters::make($this->filters($request));

            if (! is_null($this->filtersResolver)) {
                $filters = call_user_func_array($this->filtersResolver, [$request, $filters]);
            }

            $this->resolvedFilters = $filters->each->mergeAuthorizationResolver(function (...$parameters): bool {
                return $this->authorized(...$parameters);
            });
        }

        return $this->resolvedFilters;
    }
}
