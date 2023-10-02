<?php

namespace Cone\Root\Columns\Cells;

class RowSelect extends Cell
{
    /**
     * The Blade template.
     */
    protected string $template = 'root::columns.cells.row-select';

    /**
     * {@inheritdoc}
     */
    public function toArray(): array
    {
        return array_merge(parent::toArray(), [
            'model' => $this->getModel(),
        ]);
    }
}
