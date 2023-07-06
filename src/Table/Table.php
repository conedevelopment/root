<?php

namespace Cone\Root\Table;

use Cone\Root\Support\Collections\Actions;
use Cone\Root\Support\Collections\Filters;
use Illuminate\Database\Eloquent\Builder;

class Table
{
    /**
     * The blade component.
     */
    protected string $component = 'root::table.table';

    /**
     * The query instance.
     */
    protected Builder $query;

    /**
     * The columns.
     */
    protected Columns $columns;

    /**
     * The filters.
     */
    protected Filters $filters;

    /**
     * The actions.
     */
    protected Actions $actions;

    /**
     * Create a new table instance.
     */
    public function __construct(Builder $query, Columns $columns, Filters $filters, Actions $actions)
    {
        $this->query = $query;
        $this->columns = $columns;
        $this->filters = $filters;
        $this->actions = $actions;
    }
}
