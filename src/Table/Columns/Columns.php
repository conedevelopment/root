<?php

namespace Cone\Root\Table\Columns;

use Cone\Root\Table\Table;
use Illuminate\Support\Collection;
use Illuminate\Support\Traits\ForwardsCalls;

class Columns
{
    use ForwardsCalls;

    /**
     * The parent table instance.
     */
    public readonly Table $table;

    /**
     * The columns collection.
     */
    protected Collection $columns;

    /**
     * Create a new table instance.
     */
    public function __construct(Table $table, array $columns = [])
    {
        $this->table = $table;
        $this->columns = new Collection($columns);
    }

    /**
     * Make a new text column.
     */
    public function text(string $label, string $key): Text
    {
        $column = new Text($this->table, $label, $key);

        $this->columns->push($column);

        return $column;
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
     * Handle the dynamic method call.
     */
    public function __call($method, $parameters): mixed
    {
        return $this->forwardCallTo($this->columns, $method, $parameters);
    }
}
