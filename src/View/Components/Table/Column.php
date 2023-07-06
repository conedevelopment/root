<?php

namespace Cone\Root\View\Components\Table;

use Cone\Root\Table\Column as TableColumn;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Model;
use Illuminate\View\Component;

class Column extends Component
{
    /**
     * The row model.
     */
    protected Model $model;

    /**
     * The column instance.
     */
    protected TableColumn $column;

    /**
     * Create a new component instance.
     */
    public function __construct(Model $model, TableColumn $column)
    {
        $this->model = $model;
        $this->column = $column;
    }

    /**
     * Get the view / view contents that represent the component.
     */
    public function render(): View
    {
        return $this->view('root::components.table.column', [
            'model' => $this->model,
            'column' => $this->column,
        ]);
    }
}
