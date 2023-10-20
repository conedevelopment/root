<?php

namespace Cone\Root\Columns;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;

class Columns extends Collection
{
    /**
     * Register the given columns.
     */
    public function register(array|Column $columns): static
    {
        foreach (Arr::wrap($columns) as $column) {
            $this->push($column);
        }

        return $this;
    }

    /**
     * Filter the searchable columns.
     */
    public function searchable(): Collection
    {
        return $this->filter->isSearchable();
    }

    /**
     * Filter the sortable columns.
     */
    public function sortable(): Collection
    {
        return $this->filter->isSortable();
    }

    /**
     * Map the columns to cells for the given model.
     */
    public function mapToHeads(Request $request): array
    {
        return $this->map->toHead($request)->all();
    }

    /**
     * Map the columns to cells for the given model.
     */
    public function mapToCells(Request $request, Model $model): array
    {
        return $this->map->toCell($request, $model)->all();
    }
}
