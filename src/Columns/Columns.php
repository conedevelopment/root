<?php

namespace Cone\Root\Columns;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Illuminate\Support\Traits\ForwardsCalls;

class Columns
{
    use ForwardsCalls;

    /**
     * The columns collection.
     */
    protected Collection $columns;

    /**
     * Create a new table instance.
     */
    public function __construct(array $columns = [])
    {
        $this->columns = new Collection($columns);
    }

    /**
     * Register the given columns.
     */
    public function register(array|Column $columns): static
    {
        foreach (Arr::wrap($columns) as $column) {
            $this->columns->push($column);
        }

        return $this;
    }

    /**
     * Filter the searchable columns.
     */
    public function searchable(): Collection
    {
        return $this->columns->filter->isSearchable();
    }

    /**
     * Filter the sortable columns.
     */
    public function sortable(): Collection
    {
        return $this->columns->filter->isSortable();
    }

    /**
     * Map the columns to cells for the given model.
     */
    public function mapToHeads(Request $request): array
    {
        return $this->columns->map->toHead($request)->all();
    }

    /**
     * Map the columns to cells for the given model.
     */
    public function mapToCells(Request $request, Model $model): array
    {
        return $this->columns->map->toCell($request, $model)->all();
    }

    /**
     * Handle the dynamic method call.
     */
    public function __call($method, $parameters): mixed
    {
        return $this->forwardCallTo($this->columns, $method, $parameters);
    }
}
