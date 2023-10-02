<?php

namespace Cone\Root\Columns;

use Cone\Root\Columns\Cells\RowSelect as Cell;
use Illuminate\Database\Eloquent\Model;

class RowSelect extends Column
{
    /**
     * The Blade template.
     */
    protected string $template = 'root::columns.select-all';

    /**
     * {@inheritdoc}
     */
    public function newCell(Model $model): Cell
    {
        return new Cell($this, $model);
    }
}
