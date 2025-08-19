<?php

declare(strict_types=1);

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
     * Filter the filters that are available for the given request.
     */
    public function authorized(Request $request): static
    {
        return $this->filter->authorized($request)->values();
    }

    /**
     * Apply the filters on the query.
     */
    public function apply(Request $request, Builder $query): Builder
    {
        $this->each(static function (Filter $filter) use ($query, $request): void {
            if ($filter->isActive($request)) {
                $filter->apply($request, $query, $filter->getValue($request));
            }
        });

        return $query;
    }

    /**
     * Filter the renderable filters.
     */
    public function renderable(): static
    {
        return $this->filter(static fn (Filter $filter): bool => $filter instanceof RenderableFilter);
    }

    /**
     * Filter the functional filters.
     */
    public function functional(): static
    {
        return $this->reject(static fn (Filter $filter): bool => $filter instanceof RenderableFilter);
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
        return $this->mapWithKeys(static fn (Filter $filter): array => [$filter->getKey() => $filter->getValue($request)])->toArray();
    }
}
