<?php

namespace Cone\Root\Table;

use Closure;
use Cone\Root\Resources\ModelValueHandler;
use Illuminate\Database\Eloquent\Model;

abstract class Column extends ModelValueHandler
{
    /**
     * Indicates if the field is sortable.
     */
    protected bool|Closure $sortable = false;

    /**
     * Indicates if the field is searchable.
     */
    protected bool|Closure $searchable = false;

    /**
     * The table instance.
     */
    protected Table $table;

    /**
     * Create a new column instance.
     */
    public function __construct(Table $table, string $label, string $name = null)
    {
        parent::__construct($label, $name);

        $this->table = $table;
    }

    /**
     * Set the sortable attribute.
     */
    public function sortable(bool|Closure $value = true): static
    {
        $this->sortable = $value;

        return $this;
    }

    /**
     * Determine if the field is sortable.
     */
    public function isSortable(): bool
    {
        if ($this->sortable instanceof Closure) {
            return call_user_func($this->sortable);
        }

        return $this->sortable;
    }

    /**
     * Set the searachable attribute.
     */
    public function searchable(bool|Closure $value = true): static
    {
        $this->searchable = $value;

        return $this;
    }

    /**
     * Determine if the field is searchable.
     */
    public function isSearchable(): bool
    {
        if ($this->searchable instanceof Closure) {
            return call_user_func($this->searchable);
        }

        return $this->searchable;
    }

    /**
     * Convert the column to a cell.
     */
    abstract public function toCell(Model $model): Cell;
}
