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
            $filter->apply($request, $query, $request->input($filter->getKey()));
        });

        return $query;
    }

    /**
     * Handle the dynamic method call.
     */
    public function __call($method, $parameters): mixed
    {
        return $this->forwardCallTo($this->filters, $method, $parameters);
    }
}
