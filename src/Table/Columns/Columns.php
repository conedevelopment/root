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
     * Make a new column instance.
     */
    public function column(string $column, string $label, string $key = null): mixed
    {
        $instance = $column($this->table, $label, $key);

        $this->columns->push($instance);

        return $instance;
    }

    /**
     * Make a new ID column.
     */
    public function id(string $label = 'ID', string $key = null): Text
    {
        return $this->column(Text::class, $label, $key);
    }

    /**
     * Make a new text column.
     */
    public function text(string $label, string $key = null): Text
    {
        return $this->column(Text::class, $label, $key);
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
