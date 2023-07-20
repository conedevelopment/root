<?php

namespace Cone\Root\Table;

use Illuminate\Database\Eloquent\Model;

class TextColumn extends Column
{
    /**
     * Convert the column to a cell.
     */
    public function toCell(Model $model): TextCell
    {
        return new TextCell($this, $model);
    }
}
