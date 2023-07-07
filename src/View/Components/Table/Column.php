<?php

namespace Cone\Root\View\Components\Table;

use Cone\Root\Table\Column as TableColumn;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
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
    public function __construct(TableColumn $column, Model $model)
    {
        $this->column = $column;
        $this->model = $model;
    }

    /**
     * Get the view / view contents that represent the component.
     */
    public function render(): View
    {
        return $this->view('root::components.table.column', [
            'value' => App::call(function (Request $request): mixed {
                return $this->column->resolveFormat($request, $this->model);
            }),
        ]);
    }
}
