<?php

namespace Cone\Root\Table\Columns;

use Cone\Root\Table\Cells\RowSelect as Cell;
use Illuminate\Database\Eloquent\Model;

class RowSelect extends Column
{
    /**
     * The Blade template.
     */
    protected string $template = 'root::table.columns.select-all';

    /**
     * {@inheritdoc}
     */
    public function newCell(Model $model): Cell
    {
        return new Cell($this, $model);
    }
}
