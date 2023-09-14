<?php

namespace Cone\Root\Table\Columns;

use Cone\Root\Table\Cells\Text as TextCell;
use Illuminate\Database\Eloquent\Model;

class Text extends Column
{
    /**
     * {@inheritdoc}
     */
    public function newCell(Model $model): TextCell
    {
        return new TextCell($this, $model);
    }
}
