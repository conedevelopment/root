<?php

namespace Cone\Root\Table;

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
     * Filter the columns that are available for the current request and model.
     */
    public function authorized(Request $request, Model $model = null): static
    {
        return $this->filter->authorized($request, $model)->values();
    }

    /**
     * Filter the searchable columns.
     */
    public function searchable(Request $request): static
    {
        return $this->filter->isSearchable($request);
    }

    /**
     * Filter the sortable columns.
     */
    public function sortable(Request $request): static
    {
        return $this->filter->isSortable($request);
    }
}
