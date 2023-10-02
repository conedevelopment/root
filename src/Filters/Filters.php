<?php

namespace Cone\Root\Filters;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Illuminate\Support\Traits\ForwardsCalls;

class Filters
{
    use ForwardsCalls;

    /**
     * The filters collection.
     */
    protected Collection $filters;

    /**
     * Create a new filters instance.
     */
    public function __construct(array $filters = [])
    {
        $this->filters = new Collection($filters);
    }

    /**
     * Register the given filters.
     */
    public function register(array|Filter $filters): static
    {
        foreach (Arr::wrap($filters) as $filter) {
            $this->filters->push($filter);
        }

        return $this;
    }

    /**
     * Apply the filters on the query.
     */
    public function apply(Request $request, Builder $query): Builder
    {
        $this->filters->filter(static function (Filter $filter) use ($request): bool {
            return $request->has($filter->getRequestKey());
        })->each(static function (Filter $filter) use ($query, $request): void {
            $filter->apply($request, $query, $filter->getValue($request));
        });

        return $query;
    }

    /**
     * Filter the renderable filters.
     */
    public function renderable(): Collection
    {
        return $this->filters->reject->isFunctional();
    }

    /**
     * Filter the functional filters.
     */
    public function functional(): Collection
    {
        return $this->filters->filter->isFunctional();
    }

    /**
     * Filter the active filters.
     */
    public function active(Request $request): Collection
    {
        return $this->filters->filter->isActive($request);
    }

    /**
     * Map the filters to an array.
     */
    public function mapToData(Request $request): array
    {
        return $this->filters
            ->mapWithKeys(static function (Filter $filter) use ($request): array {
                return [$filter->getKey() => $filter->getValue($request)];
            })
            ->toArray();
    }

    /**
     * Handle the dynamic method call.
     */
    public function __call($method, $parameters): mixed
    {
        return $this->forwardCallTo($this->filters, $method, $parameters);
    }
}
