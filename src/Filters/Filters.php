<?php

namespace Cone\Root\Filters;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;

class Filters extends Collection
{
    /**
     * Register the given filters.
     */
    public function register(array|Filter $filters): static
    {
        foreach (Arr::wrap($filters) as $filter) {
            $this->push($filter);
        }

        return $this;
    }

    /**
     * Apply the filters on the query.
     */
    public function apply(Request $request, Builder $query): Builder
    {
        $this->filter(static function (Filter $filter) use ($request): bool {
            return $request->has($filter->getRequestKey());
        })->each(static function (Filter $filter) use ($query, $request): void {
            $filter->apply($request, $query, $filter->getValue($request));
        });

        return $query;
    }

    /**
     * Filter the renderable filters.
     */
    public function renderable(): static
    {
        return $this->filter(static function (Filter $filter): bool {
            return $filter instanceof RenderableFilter;
        });
    }

    /**
     * Filter the functional filters.
     */
    public function functional(): static
    {
        return $this->reject(static function (Filter $filter): bool {
            return $filter instanceof RenderableFilter;
        });
    }

    /**
     * Filter the active filters.
     */
    public function active(Request $request): static
    {
        return $this->filter->isActive($request);
    }

    /**
     * Map the filters to an array.
     */
    public function mapToData(Request $request): array
    {
        return $this->mapWithKeys(static function (Filter $filter) use ($request): array {
            return [$filter->getKey() => $filter->getValue($request)];
        })->toArray();
    }
}
