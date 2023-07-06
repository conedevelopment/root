<?php

namespace Cone\Root\View\Components\Table;

use Cone\Root\Table\Columns;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class Table extends Component
{
    /**
     * The table items.
     */
    protected LengthAwarePaginator $items;

    /**
     * The table columns.
     */
    protected Columns $columns;

    /**
     * Create a new component instance.
     */
    public function __construct(LengthAwarePaginator $items, Columns $columns)
    {
        $this->items = $items;
        $this->columns = $columns;
    }

    /**
     * Get the view / view contents that represent the component.
     */
    public function render(): View
    {
        return $this->view('root::components.table.table', [
            'items' => $this->items,
            'columns' => $this->columns,
        ]);
    }
}
