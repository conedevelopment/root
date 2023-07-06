<?php

namespace Cone\Root\View\Components\Table;

use Cone\Root\Table\Columns;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Model;
use Illuminate\View\Component;

class Row extends Component
{
    /**
     * The row model.
     */
    protected Model $model;

    /**
     * The row columns.
     */
    protected Columns $columns;

    /**
     * Create a new component instance.
     */
    public function __construct(Model $model, Columns $columns)
    {
        $this->model = $model;
        $this->columns = $columns;
    }

    /**
     * Get the view / view contents that represent the component.
     */
    public function render(): View
    {
        return $this->view('root::components.table.row', [
            'model' => $this->model,
            'columns' => $this->columns,
        ]);
    }
}
