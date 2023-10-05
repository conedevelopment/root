<?php

namespace Cone\Root\Columns;

use Cone\Root\Columns\Cells\RowActions as Cell;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;

class RowActions extends Column
{
    /**
     * The Blade template.
     */
    protected string $template = 'root::columns.actions';

    /**
     * {@inheritdoc}
     */
    public function newCell(Model $model): Cell
    {
        return new Cell($this, $model);
    }

    /**
     * Convert the column to a cell.
     */
    public function toCell(Model $model): Cell
    {
        return $this->newCell($model)->value(function (Request $request, Model $model): string {
            return $this->table->modelUrl($model);
        });
    }
}
