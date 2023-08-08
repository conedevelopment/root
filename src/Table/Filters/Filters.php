<?php

namespace Cone\Root\Table\Filters;

use Cone\Root\Table\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Traits\ForwardsCalls;

class Filters
{
    use ForwardsCalls;

    /**
     * The parent table instance.
     */
    public readonly Table $table;

    /**
     * The filters collection.
     */
    protected Collection $filters;

    /**
     * Create a new filters instance.
     */
    public function __construct(Table $table, array $filters = [])
    {
        $this->table = $table;
        $this->filters = new Collection($filters);
    }

    /**
     * Make a new filter instance.
     */
    public function filter(string $filter, ...$params): Filter
    {
        $instance = new $filter($this->table, ...$params);

        $this->push($instance);

        return $instance;
    }

    /**
     * Apply the filters on the query.
     */
    public function apply(Request $request): Builder
    {
        $query = $this->table->resolveQuery($request);

        $this->filters->filter(static function (Filter $filter) use ($request): bool {
            return $request->has($filter->getKey());
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
