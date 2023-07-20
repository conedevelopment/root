<?php

namespace Cone\Root\Table\Columns;

use Cone\Root\Table\Cells\Text as TextCell;
use Illuminate\Database\Eloquent\Model;

class Text extends Column
{
    /**
     * Convert the column to a cell.
     */
    public function toCell(Model $model): TextCell
    {
        return new TextCell($this, $model);
    }
}
